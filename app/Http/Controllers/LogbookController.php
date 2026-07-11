<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogHelper;
use App\Helpers\HostRoleHelper;
use App\Models\Logbook;
use App\Models\ProgramKerja;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class LogbookController extends Controller
{
    /**
     * Display a listing of the logbooks and program kerjas.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        $logbooksQuery = Logbook::with('user:id,name,email')
            ->where('host_id', $hostId);

        // If not admin/leadership role, restrict personal logbooks to own entries
        if (! HostRoleHelper::canAdminister($user)) {
            $logbooksQuery->where(function ($query) use ($user) {
                $query->where('scope', 'group')
                    ->orWhere(function ($q) use ($user) {
                        $q->where('scope', 'personal')
                          ->where('user_id', $user->id);
                    });
            });
        }

        $logbooks = $logbooksQuery->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (Logbook $log) {
                return [
                    'id' => $log->id,
                    'user_id' => $log->user_id,
                    'title' => $log->title,
                    'description' => $log->description,
                    'date' => $log->date->format('Y-m-d'),
                    'activity_type' => $log->activity_type,
                    'scope' => $log->scope,
                    'image_path' => $log->image_path,
                    'user' => $log->user,
                ];
            });

        $programKerjas = ProgramKerja::with(['pic:id,name,email', 'finances' => function ($query) {
                $query->select('id', 'program_kerja_id', 'title', 'type', 'amount', 'date')->orderBy('date', 'desc');
            }])
            ->withSum(['finances as spent' => function ($query) {
                $query->where('type', 'expense');
            }], 'amount')
            ->withSum(['finances as earned' => function ($query) {
                $query->where('type', 'income');
            }], 'amount')
            ->where('host_id', $hostId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($proker) {
                return [
                    'id' => $proker->id,
                    'host_id' => $proker->host_id,
                    'pic_id' => $proker->pic_id,
                    'name' => $proker->name,
                    'category' => $proker->category,
                    'description' => $proker->description,
                    'progress' => $proker->progress,
                    'budget' => (float) $proker->budget,
                    'spent' => (float) ($proker->spent ?? 0),
                    'earned' => (float) ($proker->earned ?? 0),
                    'status' => $proker->status,
                    'pic' => $proker->pic,
                    'finances' => $proker->finances->map(function ($f) {
                        return [
                            'id' => $f->id,
                            'title' => $f->title,
                            'type' => $f->type,
                            'amount' => (float) $f->amount,
                            'date' => $f->date->format('Y-m-d'),
                        ];
                    }),
                ];
            });

        $members = User::where('host_id', $hostId)
            ->orWhere('id', $hostId)
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'email']);

        return Inertia::render('logbook/Index', [
            'logbooks' => $logbooks,
            'programKerjas' => $programKerjas,
            'members' => $members,
            'canWriteFinance' => HostRoleHelper::canWriteFinance($user),
            'isHostOrSekretaris' => HostRoleHelper::isHostOrSekretaris($user),
            'canWriteGroupLogbook' => HostRoleHelper::canWriteGroupLogbook($user),
            'authUserId' => $user->id,
        ]);
    }

    /**
     * Store a newly created program kerja.
     */
    public function storeProker(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! HostRoleHelper::canWriteFinance($user)) {
            abort(403, 'Anda tidak memiliki hak untuk mengelola Program Kerja.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', Rule::in(['fisik', 'non_fisik', 'keagamaan', 'kesehatan', 'pendidikan', 'tambahan'])],
            'description' => ['nullable', 'string', 'max:1000'],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'budget' => ['required', 'numeric', 'min:0'],
            'pic_id' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['required', 'string', Rule::in(['planned', 'in_progress', 'completed'])],
        ]);

        $hostId = $user->host_id ?? $user->id;

        $picId = $validated['pic_id'] ?? null;
        if (empty($picId) || $picId === 'null') {
            $picId = null;
        }

        $proker = ProgramKerja::create([
            'host_id' => $hostId,
            'pic_id' => $picId,
            'name' => $validated['name'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'progress' => $validated['progress'],
            'budget' => $validated['budget'],
            'status' => $validated['status'],
        ]);

        ActivityLogHelper::log(
            'member',
            'create_proker',
            "User added program kerja '{$proker->name}'."
        );

        return back()->with('success', 'Program Kerja berhasil ditambahkan.');
    }

    /**
     * Update the specified program kerja.
     */
    public function updateProker(Request $request, ProgramKerja $proker): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        // Allowed to edit: finance role (Host/Sekretaris/Bendahara) OR the assigned PIC of the proker
        $isPic = $proker->pic_id === $user->id;
        if ($proker->host_id !== $hostId || (! HostRoleHelper::canWriteFinance($user) && ! $isPic)) {
            abort(403, 'Anda tidak memiliki hak untuk mengelola Program Kerja ini.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', Rule::in(['fisik', 'non_fisik', 'keagamaan', 'kesehatan', 'pendidikan', 'tambahan'])],
            'description' => ['nullable', 'string', 'max:1000'],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'budget' => ['required', 'numeric', 'min:0'],
            'pic_id' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['required', 'string', Rule::in(['planned', 'in_progress', 'completed'])],
        ]);

        $picId = $validated['pic_id'] ?? null;
        if (empty($picId) || $picId === 'null') {
            $picId = null;
        }

        $proker->update([
            'pic_id' => $picId,
            'name' => $validated['name'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'progress' => $validated['progress'],
            'budget' => $validated['budget'],
            'status' => $validated['status'],
        ]);

        ActivityLogHelper::log(
            'member',
            'update_proker',
            "User updated program kerja '{$proker->name}'."
        );

        return back()->with('success', 'Program Kerja berhasil diperbarui.');
    }

    /**
     * Remove the specified program kerja.
     */
    public function destroyProker(Request $request, ProgramKerja $proker): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        if ($proker->host_id !== $hostId || ! HostRoleHelper::canWriteFinance($user)) {
            abort(403, 'Anda tidak memiliki hak untuk mengelola Program Kerja ini.');
        }

        $name = $proker->name;
        $proker->delete();

        ActivityLogHelper::log(
            'member',
            'delete_proker',
            "User deleted program kerja '{$name}'."
        );

        return back()->with('success', 'Program Kerja berhasil dihapus.');
    }

    /**
     * Store a newly created logbook.
     */
    public function storeLogbook(Request $request): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:5000'],
            'activity_type' => ['required', 'string', Rule::in(['internal', 'community'])],
            'scope' => ['required', 'string', Rule::in(['personal', 'group'])],
            'image' => ['nullable', 'image', 'max:5120'], // Max 5MB
        ]);

        if ($validated['scope'] === 'group' && ! HostRoleHelper::canWriteGroupLogbook($user)) {
            abort(403, 'Hanya Ketua, Wakil, dan Sekretaris yang dapat membuat Logbook Kelompok.');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('logbooks', 'public');
        }

        $logbook = Logbook::create([
            'host_id' => $hostId,
            'user_id' => $user->id,
            'date' => $validated['date'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'activity_type' => $validated['activity_type'],
            'scope' => $validated['scope'],
            'image_path' => $imagePath,
        ]);

        ActivityLogHelper::log(
            'member',
            'create_logbook',
            "User recorded a logbook entry: '{$logbook->title}' for date '{$logbook->date->format('Y-m-d')}'."
        );

        return back()->with('success', 'Catatan logbook berhasil ditambahkan.');
    }

    /**
     * Update the specified logbook.
     */
    public function updateLogbook(Request $request, Logbook $logbook): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        $isAuthor = $logbook->user_id === $user->id;
        $isHostOrSekretaris = HostRoleHelper::isHostOrSekretaris($user);

        if ($logbook->host_id !== $hostId || (! $isAuthor && ! $isHostOrSekretaris)) {
            abort(403, 'Anda tidak memiliki hak untuk memperbarui catatan logbook ini.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:5000'],
            'activity_type' => ['required', 'string', Rule::in(['internal', 'community'])],
            'scope' => ['required', 'string', Rule::in(['personal', 'group'])],
            'image' => ['nullable', 'image', 'max:5120'], // Max 5MB
        ]);

        if ($validated['scope'] === 'group' && ! HostRoleHelper::canWriteGroupLogbook($user)) {
            abort(403, 'Hanya Ketua, Wakil, dan Sekretaris yang dapat mengelola Logbook Kelompok.');
        }

        $imagePath = $logbook->image_path;
        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('logbooks', 'public');
        }

        $logbook->update([
            'title' => $validated['title'],
            'date' => $validated['date'],
            'description' => $validated['description'],
            'activity_type' => $validated['activity_type'],
            'scope' => $validated['scope'],
            'image_path' => $imagePath,
        ]);

        ActivityLogHelper::log(
            'member',
            'update_logbook',
            "User updated logbook entry '{$logbook->title}'."
        );

        return back()->with('success', 'Catatan logbook berhasil diperbarui.');
    }

    /**
     * Remove the specified logbook.
     */
    public function destroyLogbook(Request $request, Logbook $logbook): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        $isAuthor = $logbook->user_id === $user->id;
        $isHostOrSekretaris = HostRoleHelper::isHostOrSekretaris($user);

        if ($logbook->host_id !== $hostId || (! $isAuthor && ! $isHostOrSekretaris)) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus catatan logbook ini.');
        }

        if ($logbook->image_path) {
            Storage::disk('public')->delete($logbook->image_path);
        }

        $title = $logbook->title;
        $logbook->delete();

        ActivityLogHelper::log(
            'member',
            'delete_logbook',
            "User deleted logbook entry '{$title}'."
        );

        return back()->with('success', 'Catatan logbook berhasil dihapus.');
    }
}
