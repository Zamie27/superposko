<?php

namespace App\Http\Controllers;

use App\Models\Aspiration;
use App\Models\DutyRoster;
use App\Models\Event;
use App\Models\Finance;
use App\Models\Inventory;
use App\Models\Logistic;
use App\Models\Poll;
use App\Models\ProgramKerja;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the host/posko dashboard.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        if ($user->role === 'dpl' && is_null($user->host_id)) {
            $allPoskos = User::whereNull('host_id')
                ->whereIn('role', ['host', 'ketua', 'trial'])
                ->select('id', 'name', 'university', 'group_number')
                ->get();

            $pendingRequests = \App\Models\DplMonitoring::with('host:id,name,university,group_number')
                ->where('dpl_id', $user->id)
                ->where('status', 'pending')
                ->get();

            return Inertia::render('Dashboard', [
                'isDplGateway' => true,
                'allPoskos' => $allPoskos,
                'pendingRequests' => $pendingRequests,
                'metrics' => [
                    'finance' => ['balance' => 0, 'total_income' => 0, 'total_expense' => 0],
                    'proker' => ['count' => 0, 'total_pagu' => 0, 'total_spent' => 0],
                    'inventory' => ['count' => 0, 'good_count' => 0],
                    'logistics' => ['count' => 0, 'critical_count' => 0],
                    'members' => ['count' => 0],
                    'voting' => ['active_polls' => 0, 'unresponded_aspirations' => 0],
                ],
                'todayRoster' => [],
                'events' => [],
            ]);
        }

        $hostId = $user->host_id ?? $user->id;

        // 1. Keuangan Metrics
        $totalIncome = (float) Finance::where('host_id', $hostId)
            ->where('type', 'income')
            ->sum('amount');

        $totalExpense = (float) Finance::where('host_id', $hostId)
            ->where('type', 'expense')
            ->sum('amount');

        $balance = $totalIncome - $totalExpense;

        // 2. Program Kerja (Proker) Metrics
        $totalProker = ProgramKerja::where('host_id', $hostId)->count();
        $totalPagu = (float) ProgramKerja::where('host_id', $hostId)->sum('budget');
        $totalProkerSpent = (float) Finance::where('host_id', $hostId)
            ->where('type', 'expense')
            ->whereNotNull('program_kerja_id')
            ->sum('amount');

        // 3. Inventaris & Logistik Metrics
        $totalInventory = Inventory::where('host_id', $hostId)->count();
        $goodInventoryCount = Inventory::where('host_id', $hostId)
            ->where('condition', 'good')
            ->count();

        $totalLogistics = Logistic::where('host_id', $hostId)->count();
        $criticalLogisticsCount = Logistic::where('host_id', $hostId)
            ->whereIn('status', ['low', 'out'])
            ->count();

        // 4. Anggota count
        $totalMembers = User::where('host_id', $hostId)
            ->orWhere('id', $hostId)
            ->count();

        // 5. Voting & Aspirasi Metrics
        $activePolls = Poll::where('host_id', $hostId)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->count();

        $unrespondedAspirations = Aspiration::where('host_id', $hostId)
            ->whereNull('admin_response')
            ->count();

        // 6. Piket Hari Ini (Duty Roster)
        $dayOfWeek = strtolower(now()->englishDayOfWeek); // e.g. "monday"
        $todayRoster = DutyRoster::with('user:id,name,email')
            ->where('host_id', $hostId)
            ->where('day_of_week', $dayOfWeek)
            ->get();

        // 7. Events (Agenda Kegiatan) for calendar
        $events = Event::where('host_id', $hostId)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'start_time' => $event->start_time,
                    'end_time' => $event->end_time,
                    'location' => $event->location,
                ];
            });

        return Inertia::render('Dashboard', [
            'metrics' => [
                'finance' => [
                    'balance' => $balance,
                    'total_income' => $totalIncome,
                    'total_expense' => $totalExpense,
                ],
                'proker' => [
                    'count' => $totalProker,
                    'total_pagu' => $totalPagu,
                    'total_spent' => $totalProkerSpent,
                ],
                'inventory' => [
                    'count' => $totalInventory,
                    'good_count' => $goodInventoryCount,
                ],
                'logistics' => [
                    'count' => $totalLogistics,
                    'critical_count' => $criticalLogisticsCount,
                ],
                'members' => [
                    'count' => $totalMembers,
                ],
                'voting' => [
                    'active_polls' => $activePolls,
                    'unresponded_aspirations' => $unrespondedAspirations,
                ],
            ],
            'todayRoster' => $todayRoster,
            'events' => $events,
        ]);
    }
}
