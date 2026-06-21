<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Helpers\ActivityLogHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
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
     * Display a listing of the contacts.
     */
    public function index(Request $request): Response
    {
        $hostId = $this->getHostId();
        $search = $request->input('search');
        $category = $request->input('category');

        $contacts = Contact::query()
            ->where('host_id', $hostId)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('role_title', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%");
                });
            })
            ->when($category, function ($query, $category) {
                $query->where('category', $category);
            })
            ->orderBy('category', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return Inertia::render('contacts/Index', [
            'contacts' => $contacts,
            'filters' => $request->only(['search', 'category']),
        ]);
    }

    /**
     * Store a newly created contact in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $hostId = $this->getHostId();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role_title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', Rule::in(['aparat_desa', 'kesehatan', 'keamanan', 'akademik', 'mitra'])],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validated['phone']) {
            $phone = preg_replace('/[^0-9]/', '', $validated['phone']);
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            } elseif (!str_starts_with($phone, '62')) {
                $phone = '62' . $phone;
            }
            $validated['phone'] = $phone;
        }

        $contact = Contact::create(array_merge($validated, [
            'host_id' => $hostId,
        ]));

        ActivityLogHelper::log(
            'member',
            'create_contact',
            "User added contact {$contact->name} ({$contact->role_title}) to the contact book."
        );

        return back()->with('success', 'Kontak berhasil ditambahkan.');
    }

    /**
     * Update the specified contact in storage.
     */
    public function update(Request $request, Contact $contact): RedirectResponse
    {
        $hostId = $this->getHostId();

        if ($contact->host_id !== $hostId) {
            abort(403, 'Anda tidak berhak memperbarui kontak ini.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role_title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', Rule::in(['aparat_desa', 'kesehatan', 'keamanan', 'akademik', 'mitra'])],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validated['phone']) {
            $phone = preg_replace('/[^0-9]/', '', $validated['phone']);
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            } elseif (!str_starts_with($phone, '62')) {
                $phone = '62' . $phone;
            }
            $validated['phone'] = $phone;
        }

        $contact->update($validated);

        ActivityLogHelper::log(
            'member',
            'update_contact',
            "User updated contact {$contact->name} details."
        );

        return back()->with('success', 'Kontak berhasil diperbarui.');
    }

    /**
     * Remove the specified contact from storage.
     */
    public function destroy(Contact $contact): RedirectResponse
    {
        $hostId = $this->getHostId();

        if ($contact->host_id !== $hostId) {
            abort(403, 'Anda tidak berhak menghapus kontak ini.');
        }

        $oldName = $contact->name;
        $contact->delete();

        ActivityLogHelper::log(
            'member',
            'delete_contact',
            "User deleted contact {$oldName}."
        );

        return back()->with('success', 'Kontak berhasil dihapus.');
    }
}
