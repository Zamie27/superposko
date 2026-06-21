<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\ActivityLogHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminDocumentationConfigController extends Controller
{
    /**
     * Display a listing of hosts and their Immich configurations.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');

        $hosts = User::query()
            ->where('role', 'host')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('university', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/DocumentationConfigs', [
            'hosts' => $hosts,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Update the Immich configuration for a host.
     */
    public function update(Request $request, User $host): JsonResponse
    {
        if ($host->role !== 'host') {
            return response()->json([
                'success' => false,
                'message' => 'User tersebut bukan merupakan Host.',
            ], 400);
        }

        $validated = $request->validate([
            'immich_api_key' => ['nullable', 'string'],
            'immich_email' => ['nullable', 'email', 'max:255'],
            'immich_password' => ['nullable', 'string', 'max:255'],
        ]);

        $host->update([
            'immich_api_key' => $validated['immich_api_key'],
            'immich_email' => $validated['immich_email'],
            'immich_password' => $validated['immich_password'],
        ]);

        \Illuminate\Support\Facades\Cache::forget('immich_storage_' . $host->id);

        ActivityLogHelper::log(
            'settings',
            'update_host_documentation_config',
            "Admin updated Immich documentation credentials for host {$host->name} ({$host->email})."
        );

        return response()->json([
            'success' => true,
            'message' => "Konfigurasi Immich untuk host {$host->name} berhasil disimpan.",
        ]);
    }
}
