<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogHelper;
use App\Helpers\HostRoleHelper;
use App\Models\DutyRoster;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ScheduleController extends Controller
{
    /**
     * Display weekly duty rosters and agenda events.
     */
    public function index(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        // Fetch posko members (including host) to assign duties
        $members = User::where('host_id', $hostId)
            ->orWhere('id', $hostId)
            ->where('role', '!=', 'admin')
            ->select('id', 'name', 'role')
            ->get();

        // Fetch duty rosters grouped/categorized by day
        $rosters = DutyRoster::where('host_id', $hostId)
            ->with('user:id,name,role')
            ->get()
            ->map(function ($roster) {
                return [
                    'id' => $roster->id,
                    'day_of_week' => $roster->day_of_week,
                    'task_name' => $roster->task_name,
                    'user_id' => $roster->user_id,
                    'user_name' => $roster->user->name ?? 'Unknown',
                ];
            });

        // Fetch events
        $events = Event::where('host_id', $hostId)
            ->with('creator:id,name')
            ->orderBy('start_time', 'asc')
            ->get()
            ->map(function (Event $event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'start_time' => $event->start_time->toIso8601String(),
                    'end_time' => $event->end_time ? $event->end_time->toIso8601String() : null,
                    'location' => $event->location,
                    'created_by_name' => $event->creator->name ?? 'Unknown',
                ];
            });

        return Inertia::render('management/schedule/Index', [
            'members' => $members,
            'rosters' => $rosters,
            'events' => $events,
            'canManage' => HostRoleHelper::isHostOrSekretaris($user),
        ]);
    }

    /**
     * Assign a member to a duty roster.
     */
    public function storeRoster(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! HostRoleHelper::isHostOrSekretaris($user)) {
            abort(403, 'Hanya Host dan Sekretaris yang dapat mengelola jadwal piket.');
        }

        $validated = $request->validate([
            'day_of_week' => ['required', 'string', Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])],
            'task_name' => ['required', 'string', 'max:255'],
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $hostId = $user->host_id ?? $user->id;

        // Verify target user belongs to same posko
        $targetUser = User::where('id', $validated['user_id'])->first();
        if (! $targetUser || ($targetUser->host_id !== $hostId && $targetUser->id !== $hostId)) {
            abort(403, 'Akses ditolak.');
        }

        // Validate that user is not already assigned to another task on the same day
        $exists = DutyRoster::where('host_id', $hostId)
            ->where('day_of_week', $validated['day_of_week'])
            ->where('user_id', $validated['user_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['user_id' => 'Anggota ini sudah memiliki tugas piket di hari yang sama.']);
        }

        $roster = DutyRoster::create([
            'host_id' => $hostId,
            'day_of_week' => $validated['day_of_week'],
            'task_name' => $validated['task_name'],
            'user_id' => $validated['user_id'],
        ]);

        ActivityLogHelper::log(
            'member',
            'assign_duty',
            "User assigned {$targetUser->name} to duty task '{$validated['task_name']}' on ".ucfirst($validated['day_of_week'])
        );

        return back()->with('success', 'Jadwal piket berhasil ditambahkan.');
    }

    /**
     * Delete a duty roster assignment.
     */
    public function destroyRoster(Request $request, DutyRoster $roster): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        if ($roster->host_id !== $hostId || ! HostRoleHelper::isHostOrSekretaris($user)) {
            abort(403, 'Hanya Host dan Sekretaris yang dapat menghapus jadwal piket.');
        }

        $taskName = $roster->task_name;
        $day = $roster->day_of_week;
        $roster->delete();

        ActivityLogHelper::log(
            'member',
            'remove_duty',
            "User removed duty task '{$taskName}' on ".ucfirst($day)
        );

        return back()->with('success', 'Jadwal piket berhasil dihapus.');
    }

    /**
     * Store a newly created event.
     */
    public function storeEvent(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! HostRoleHelper::isHostOrSekretaris($user)) {
            abort(403, 'Hanya Host dan Sekretaris yang dapat menambahkan agenda.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_time' => ['required', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:start_time'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        $hostId = $user->host_id ?? $user->id;

        $event = Event::create([
            'host_id' => $hostId,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'] ?? null,
            'location' => $validated['location'] ?? null,
            'created_by' => $user->id,
        ]);

        $eventDate = \Illuminate\Support\Carbon::parse($event->start_time)->format('d M Y');
        $locationStr = $event->location ? " di {$event->location}" : "";
        \App\Helpers\PushNotificationHelper::sendToHostGroup(
            $hostId,
            'Agenda Baru Kelompok KKN',
            "Telah ditambahkan agenda kegiatan baru: '{$event->title}' pada tanggal {$eventDate}{$locationStr}.",
            '/management/schedule',
            $user->id
        );

        ActivityLogHelper::log(
            'member',
            'create_event',
            "User created event '{$event->title}'."
        );

        return back()->with('success', 'Agenda berhasil ditambahkan.');
    }

    /**
     * Update an event.
     */
    public function updateEvent(Request $request, Event $event): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        if ($event->host_id !== $hostId || ! HostRoleHelper::isHostOrSekretaris($user)) {
            abort(403, 'Hanya Host dan Sekretaris yang dapat memperbarui agenda.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_time' => ['required', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:start_time'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        $event->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'] ?? null,
            'location' => $validated['location'] ?? null,
        ]);

        ActivityLogHelper::log(
            'member',
            'update_event',
            "User updated event '{$event->title}'."
        );

        return back()->with('success', 'Agenda berhasil diperbarui.');
    }

    /**
     * Delete an event.
     */
    public function destroyEvent(Request $request, Event $event): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        if ($event->host_id !== $hostId || ! HostRoleHelper::isHostOrSekretaris($user)) {
            abort(403, 'Hanya Host dan Sekretaris yang dapat menghapus agenda.');
        }

        $title = $event->title;
        $event->delete();

        ActivityLogHelper::log(
            'member',
            'delete_event',
            "User deleted event '{$title}'."
        );

        return back()->with('success', 'Agenda berhasil dihapus.');
    }
}
