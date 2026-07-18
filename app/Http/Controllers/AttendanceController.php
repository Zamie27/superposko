<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request): Response
    {
        $user = auth()->user();
        $hostId = $user->host_id ?? $user->id;

        // Retrieve today's attendance for the current user (using Asia/Jakarta timezone)
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();

        // Get selected month/year with defaults (Asia/Jakarta timezone)
        $nowWib = Carbon::now('Asia/Jakarta');
        $selectedMonth = $request->query('month', $nowWib->month);
        $selectedYear = $request->query('year', $nowWib->year);

        // Calculate days in the selected month/year
        $daysInMonth = (int) Carbon::create($selectedYear, $selectedMonth, 1)->format('t');

        // Get all attendances for the host (for recap view)
        $isLeader = in_array($user->role, ['ketua', 'wakil', 'sekretaris']);
        
        $recap = [];
        $members = [];
        
        if ($isLeader) {
            $members = User::where('host_id', $hostId)->orWhere('id', $hostId)->get();
            $recap = Attendance::with('user')
                ->where('host_id', $hostId)
                ->whereYear('date', $selectedYear)
                ->whereMonth('date', $selectedMonth)
                ->orderBy('date', 'asc')
                ->get();
        }

        return Inertia::render('attendance/Index', [
            'todayAttendance' => $todayAttendance,
            'recap' => $recap,
            'members' => $members,
            'daysInMonth' => $daysInMonth,
            'isLeader' => $isLeader,
            'filters' => [
                'month' => (int) $selectedMonth,
                'year' => (int) $selectedYear,
            ]
        ]);
    }

    public function store(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $user = auth()->user();
        $hostId = $user->host_id ?? $user->id;

        $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'status' => ['required', 'string', 'in:Hadir,Izin,Sakit,Alfa'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        // Check if already submitted today
        $alreadySubmitted = Attendance::where('user_id', $user->id)
            ->whereDate('date', today())
            ->exists();
            
        if ($alreadySubmitted) {
            return back()->with('error', 'Anda sudah mengisi absensi hari ini.');
        }

        $village = null;
        $district = null;
        $regency = null;
        $province = null;

        try {
            // Reverse geocoding with Nominatim API
            $response = Http::withHeaders([
                'User-Agent' => 'SuperPosko/1.0 (github.com/Zamie27/superposko)',
            ])->timeout(10)->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'jsonv2',
                'lat' => $request->latitude,
                'lon' => $request->longitude,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['address'])) {
                    $addr = $data['address'];
                    $village = $addr['village'] ?? $addr['suburb'] ?? $addr['hamlet'] ?? $addr['neighbourhood'] ?? $addr['city_district'] ?? null;
                    $district = $addr['county'] ?? $addr['municipality'] ?? null;
                    $regency = $addr['city'] ?? $addr['town'] ?? $addr['region'] ?? null;
                    
                    // Fallbacks for common Indonesian mappings in OSM
                    if (!$district && isset($addr['city_district'])) {
                        $district = $addr['city_district'];
                    }

                    $province = $addr['state'] ?? $addr['province'] ?? null;
                }
            }
        } catch (\Exception $e) {
            // Abaikan jika gagal geocode, biarkan null
        }

        $nowWib = Carbon::now('Asia/Jakarta');
        Attendance::create([
            'user_id' => $user->id,
            'host_id' => $hostId,
            'date' => $nowWib->toDateString(),
            'time' => $nowWib->format('H:i:s'),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'village' => $village,
            'district' => $district,
            'regency' => $regency,
            'province' => $province,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Absensi berhasil dicatat.');
    }
}
