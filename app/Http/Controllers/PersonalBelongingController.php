<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogHelper;
use App\Models\PersonalBelonging;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PersonalBelongingController extends Controller
{
    /**
     * Display a listing of personal belongings.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $belongings = PersonalBelonging::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('personal-belongings/Index', [
            'belongings' => $belongings,
        ]);
    }

    /**
     * Store a newly created personal belonging.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $belonging = PersonalBelonging::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'quantity' => $validated['quantity'],
            'unit' => $validated['unit'],
            'notes' => $validated['notes'] ?? null,
            'is_packed_departure' => false,
            'is_packed_return' => false,
        ]);

        ActivityLogHelper::log(
            'member',
            'create_personal_belonging',
            "User added personal belonging item '{$belonging->name}'."
        );

        return back()->with('success', 'Barang bawaan pribadi berhasil ditambahkan.');
    }

    /**
     * Update the specified personal belonging.
     */
    public function update(Request $request, PersonalBelonging $personalBelonging): RedirectResponse
    {
        $user = $request->user();

        if ($personalBelonging->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak untuk mengubah barang ini.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $personalBelonging->update([
            'name' => $validated['name'],
            'quantity' => $validated['quantity'],
            'unit' => $validated['unit'],
            'notes' => $validated['notes'] ?? null,
        ]);

        ActivityLogHelper::log(
            'member',
            'update_personal_belonging',
            "User updated personal belonging item '{$personalBelonging->name}'."
        );

        return back()->with('success', 'Barang bawaan pribadi berhasil diperbarui.');
    }

    /**
     * Remove the specified personal belonging.
     */
    public function destroy(Request $request, PersonalBelonging $personalBelonging): RedirectResponse
    {
        $user = $request->user();

        if ($personalBelonging->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus barang ini.');
        }

        $name = $personalBelonging->name;
        $personalBelonging->delete();

        ActivityLogHelper::log(
            'member',
            'delete_personal_belonging',
            "User deleted personal belonging item '{$name}'."
        );

        return back()->with('success', 'Barang bawaan pribadi berhasil dihapus.');
    }

    /**
     * Toggle the packed status of a personal belonging.
     */
    public function togglePacked(Request $request, PersonalBelonging $personalBelonging): RedirectResponse
    {
        $user = $request->user();

        if ($personalBelonging->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak untuk mengakses barang ini.');
        }

        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(['departure', 'return'])],
        ]);

        $type = $validated['type'];

        if ($type === 'departure') {
            $personalBelonging->update([
                'is_packed_departure' => ! $personalBelonging->is_packed_departure,
            ]);
        } else {
            $personalBelonging->update([
                'is_packed_return' => ! $personalBelonging->is_packed_return,
            ]);
        }

        return back();
    }
}
