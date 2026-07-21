<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogHelper;
use App\Helpers\HostRoleHelper;
use App\Models\ProkerDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProkerDocumentController extends Controller
{
    /**
     * Resolve the current user's host/posko ID.
     */
    protected function getHostId(): int
    {
        $user = auth()->user();

        return $user->host_id ?? $user->id;
    }

    /**
     * Display a listing of the documents.
     */
    public function index(Request $request): Response
    {
        $hostId = $this->getHostId();
        $search = $request->input('search');
        $category = $request->input('category');

        $documents = ProkerDocument::query()
            ->with('uploader:id,name')
            ->where('host_id', $hostId)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($category, function ($query, $category) {
                $query->where('category', $category);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'title' => $doc->title,
                    'description' => $doc->description,
                    'category' => $doc->category,
                    'file_name' => $doc->file_name,
                    'file_size' => $doc->formatted_size,
                    'mime_type' => $doc->mime_type,
                    'uploaded_by' => $doc->uploader->name ?? 'Unknown',
                    'created_at' => $doc->created_at->diffForHumans(),
                ];
            });

        return Inertia::render('repository/Index', [
            'documents' => $documents,
            'filters' => $request->only(['search', 'category']),
            'canWrite' => HostRoleHelper::canWritePublicRelations(auth()->user()),
        ]);
    }

    /**
     * Store a newly created document in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! HostRoleHelper::canWritePublicRelations(auth()->user())) {
            abort(403, 'Anda tidak memiliki hak untuk mengunggah dokumen.');
        }

        $hostId = $this->getHostId();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'string', Rule::in(['proposal', 'lpj', 'perizinan', 'notulensi', 'desain', 'lainnya'])],
            'file' => ['required', 'file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,png,jpg,jpeg'],
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getClientMimeType();
        $fileSize = $file->getSize();

        // Store file securely in S3/MinIO under client/{groupSlug}/documents folder
        $hostUser = \App\Models\User::find($hostId);
        $groupSlug = \Illuminate\Support\Str::slug($hostUser?->group_number ?: "kelompok-{$hostId}", '_');
        $disk = env('FILESYSTEM_DISK', 's3');
        $path = $file->store("client/{$groupSlug}/documents", $disk);

        $document = ProkerDocument::create([
            'host_id' => $hostId,
            'uploaded_by' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'file_path' => $path,
            'file_name' => $originalName,
            'file_size' => $fileSize,
            'mime_type' => $mimeType,
        ]);

        ActivityLogHelper::log(
            'member',
            'upload_document',
            "User uploaded document {$document->title} ({$document->file_name}) to repository."
        );

        return back()->with('success', 'Dokumen berhasil diunggah.');
    }

    /**
     * Download the specified document.
     */
    public function download(ProkerDocument $document): StreamedResponse
    {
        $hostId = $this->getHostId();

        if ($document->host_id !== $hostId) {
            abort(403, 'Anda tidak berhak mengunduh dokumen ini.');
        }

        $disk = env('FILESYSTEM_DISK', 's3');
        try {
            if (! Storage::disk($disk)->exists($document->file_path)) {
                abort(404, 'Berkas fisik tidak ditemukan di server.');
            }

            return Storage::disk($disk)->download($document->file_path, $document->file_name, [
                'Content-Disposition' => 'attachment; filename="'.$document->file_name.'"',
            ]);
        } catch (\Throwable $e) {
            abort(404, 'Berkas fisik tidak ditemukan di server.');
        }
    }

    /**
     * View/stream the specified document inline in the browser.
     */
    public function view(ProkerDocument $document): \Symfony\Component\HttpFoundation\Response
    {
        $hostId = $this->getHostId();

        if ($document->host_id !== $hostId) {
            abort(403, 'Anda tidak berhak melihat dokumen ini.');
        }

        $disk = env('FILESYSTEM_DISK', 's3');
        try {
            if (! Storage::disk($disk)->exists($document->file_path)) {
                abort(404, 'Berkas fisik tidak ditemukan di server.');
            }

            return Storage::disk($disk)->response($document->file_path, $document->file_name, [
                'Content-Disposition' => 'inline; filename="'.$document->file_name.'"',
            ]);
        } catch (\Throwable $e) {
            abort(404, 'Berkas fisik tidak ditemukan di server.');
        }
    }

    /**
     * Remove the specified document from storage.
     */
    public function destroy(ProkerDocument $document): RedirectResponse
    {
        if (! HostRoleHelper::canWritePublicRelations(auth()->user())) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus dokumen.');
        }

        $hostId = $this->getHostId();

        if ($document->host_id !== $hostId) {
            abort(403, 'Anda tidak berhak menghapus dokumen ini.');
        }

        $disk = env('FILESYSTEM_DISK', 's3');
        if (Storage::disk($disk)->exists($document->file_path)) {
            Storage::disk($disk)->delete($document->file_path);
        }

        $oldTitle = $document->title;
        $document->delete();

        ActivityLogHelper::log(
            'member',
            'delete_document',
            "User deleted document {$oldTitle}."
        );

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }
}
