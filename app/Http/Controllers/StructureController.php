<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StructureController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        // Fetch all members of this posko including the host
        $members = User::where(function ($q) use ($hostId) {
                $q->where('host_id', $hostId)
                  ->orWhere('id', $hostId);
            })
            ->with('customRole') // Eager load custom role details if any
            ->orderBy('name', 'asc')
            ->get();

        return Inertia::render('structure/Index', [
            'members' => $members
        ]);
    }
}
