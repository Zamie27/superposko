<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
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
        
        $members = User::where('host_id', $hostId)->orWhere('id', $hostId)->orderBy('name', 'asc')->get();
        $recapQuery = Attendance::with('user')
            ->where('host_id', $hostId)
            ->whereYear('date', $selectedYear)
            ->whereMonth('date', $selectedMonth)
            ->orderBy('date', 'asc')
            ->get();

        // Get host settings
        $hostUser = User::find($hostId);
        $hostLat = $hostUser?->attendance_lat;
        $hostLng = $hostUser?->attendance_lng;
        $hostRadius = $hostUser?->attendance_radius ?? 100; // default 100m

        $recap = $recapQuery->map(function ($attendance) use ($hostLat, $hostLng, $hostRadius) {
            $attendance->is_outside_radius = false;
            
            if ($hostLat && $hostLng && $attendance->latitude && $attendance->longitude) {
                // Haversine formula
                $earthRadius = 6371000; // in meters
                $dLat = deg2rad($attendance->latitude - $hostLat);
                $dLon = deg2rad($attendance->longitude - $hostLng);
                $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($hostLat)) * cos(deg2rad($attendance->latitude)) * sin($dLon / 2) * sin($dLon / 2);
                $c = 2 * asin(sqrt($a));
                $distance = $earthRadius * $c;

                if ($distance > $hostRadius) {
                    $attendance->is_outside_radius = true;
                }
            }
            return $attendance;
        });

        return Inertia::render('attendance/Index', [
            'todayAttendance' => $todayAttendance,
            'recap' => $recap,
            'members' => $members,
            'daysInMonth' => $daysInMonth,
            'isLeader' => $isLeader,
            'hostPosko' => [
                'name' => $hostUser?->name ?? 'Posko KKN',
                'group_number' => $hostUser?->group_number ?? 1,
            ],
            'supportInfo' => [
                'instagram' => Setting::get('footer_instagram', '@kuukok.id'),
                'whatsapp' => Setting::get('footer_phone', '+62 851-7173-9232'),
            ],
            'settings' => [
                'lat' => $hostLat,
                'lng' => $hostLng,
                'radius' => $hostRadius,
            ],
            'filters' => [
                'month' => (int) $selectedMonth,
                'year' => (int) $selectedYear,
            ]
        ]);
    }

    public function scanQr(Request $request): Response|\Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();

        if ($todayAttendance) {
            return redirect()->route('attendance.index')->with('error', 'Anda sudah mengisi absensi hari ini.');
        }

        return Inertia::render('attendance/ScanQr', [
            'user' => $user,
        ]);
    }

    public function storeScanQr(Request $request): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $user = auth()->user();
        $hostId = $user->host_id ?? $user->id;

        $alreadySubmitted = Attendance::where('user_id', $user->id)
            ->whereDate('date', today())
            ->exists();

        if ($alreadySubmitted) {
            return redirect()->route('attendance.index')->with('error', 'Anda sudah mengisi absensi hari ini.');
        }

        $lat = $request->input('latitude');
        $lng = $request->input('longitude');

        // Fallback to host location if GPS coordinates not provided
        if (is_null($lat) || is_null($lng)) {
            $hostUser = User::find($hostId);
            $lat = $hostUser?->attendance_lat ?? 0;
            $lng = $hostUser?->attendance_lng ?? 0;
        }

        $village = null;
        $district = null;
        $regency = null;
        $province = null;

        if ($lat && $lng) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'SuperPosko/1.0 (github.com/Zamie27/superposko)',
                ])->timeout(8)->get('https://nominatim.openstreetmap.org/reverse', [
                    'format' => 'jsonv2',
                    'lat' => $lat,
                    'lon' => $lng,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['address'])) {
                        $addr = $data['address'];
                        $village = $addr['village'] ?? $addr['suburb'] ?? $addr['hamlet'] ?? $addr['neighbourhood'] ?? $addr['city_district'] ?? null;
                        $district = $addr['county'] ?? $addr['municipality'] ?? $addr['city_district'] ?? null;
                        $regency = $addr['city'] ?? $addr['town'] ?? $addr['region'] ?? null;
                        $province = $addr['state'] ?? $addr['province'] ?? null;
                    }
                }
            } catch (\Exception $e) {
            }
        }

        $nowWib = Carbon::now('Asia/Jakarta');
        Attendance::create([
            'user_id' => $user->id,
            'host_id' => $hostId,
            'date' => $nowWib->toDateString(),
            'time' => $nowWib->format('H:i:s'),
            'latitude' => $lat,
            'longitude' => $lng,
            'village' => $village,
            'district' => $district,
            'regency' => $regency,
            'province' => $province,
            'status' => 'Hadir',
            'notes' => 'Absen via QR Code Posko',
        ]);

        return redirect()->route('attendance.index')->with('success', 'Absensi Hadir berhasil dicatat via QR Code!');
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

    public function updateSettings(Request $request): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $user = auth()->user();
        if (!in_array($user->role, ['ketua', 'wakil', 'sekretaris'])) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'radius' => ['nullable', 'integer', 'min:10', 'max:5000'],
        ]);

        $hostId = $user->host_id ?? $user->id;
        $host = User::find($hostId);
        
        if ($host) {
            $host->update([
                'attendance_lat' => $request->lat,
                'attendance_lng' => $request->lng,
                'attendance_radius' => $request->radius,
            ]);
        }

        return back()->with('success', 'Pengaturan lokasi absensi berhasil diperbarui.');
    }

    /**
     * Generate & Download QR Poster directly from MinIO storage bucket under client/{group_slug}/image/qr_poster.png.
     */
    public function downloadQrPoster(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $user = auth()->user();
        $hostId = $user->host_id ?? $user->id;
        $hostUser = User::find($hostId);

        $rawGroup = $hostUser?->group_number ?: "Kelompok {$hostId}";
        if (is_numeric($rawGroup)) {
            $groupNameStr = "Kelompok {$rawGroup}";
        } else {
            $groupNameStr = $rawGroup;
        }

        $groupSlug = Str::slug($groupNameStr, '_');
        $disk = env('FILESYSTEM_DISK', 's3');
        $storagePath = "client/{$groupSlug}/image/qr_poster.png";

        // 1. If already generated & stored in MinIO bucket, stream direct download
        try {
            if (Storage::disk($disk)->exists($storagePath)) {
                return Storage::disk($disk)->download($storagePath, "Poster-QR-Absensi-{$groupSlug}.png", [
                    'Content-Type' => 'image/png',
                ]);
            }
        } catch (\Throwable $e) {
            // File does not exist yet in S3/MinIO
        }

        // 2. Generate poster image using PHP GD
        $width = 1000;
        $height = 1250;
        $img = imagecreatetruecolor($width, $height);

        $bg = imagecolorallocate($img, 255, 255, 255);
        $dark = imagecolorallocate($img, 15, 23, 42);
        $primary = imagecolorallocate($img, 2, 132, 199);
        $skyBg = imagecolorallocate($img, 240, 249, 255);
        $skyBorder = imagecolorallocate($img, 186, 230, 253);
        $grayText = imagecolorallocate($img, 100, 116, 139);

        // Background
        imagefill($img, 0, 0, $bg);
        imagesetthickness($img, 8);
        imagerectangle($img, 16, 16, $width - 16, $height - 16);

        // Logo SuperPosko
        $logoPath = public_path('logo_superposko.png');
        if (file_exists($logoPath)) {
            $logo = @imagecreatefrompng($logoPath);
            if ($logo) {
                $lw = imagesx($logo);
                $lh = imagesy($logo);
                $targetLw = 240;
                $targetLh = (int) (($targetLw * $lh) / $lw);
                imagecopyresampled($img, $logo, 60, 60, 0, 0, $targetLw, $targetLh, $lw, $lh);
                imagedestroy($logo);
            }
        }

        // Badge "OFFICIAL ABSENSI"
        imagefilledrectangle($img, $width - 290, 60, $width - 60, 108, $skyBg);
        imagesetthickness($img, 2);
        imagerectangle($img, $width - 290, 60, $width - 60, 108, $skyBorder);
        imagestring($img, 5, $width - 265, 75, 'OFFICIAL ABSENSI', $primary);

        // Separator line
        imagesetthickness($img, 2);
        imageline($img, 60, 145, $width - 60, 145, $border = imagecolorallocate($img, 241, 245, 249));

        // Heading Titles
        $titleStr = 'Scan QR di bawah untuk absen';
        imagestring($img, 5, (int) (($width - (strlen($titleStr) * 10)) / 2), 220, $titleStr, $dark);

        $groupDisplayStr = strtoupper($groupNameStr);
        imagestring($img, 5, (int) (($width - (strlen($groupDisplayStr) * 10)) / 2), 270, $groupDisplayStr, $primary);

        // QR Code Box
        $scanQrUrl = route('attendance.scan_qr', ['host_id' => $hostId]);
        $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=500x500&margin=10&data='.urlencode($scanQrUrl);

        $qrX = (int) (($width - 500) / 2);
        $qrY = 340;
        imagesetthickness($img, 6);
        imagerectangle($img, $qrX - 10, $qrY - 10, $qrX + 510, $qrY + 510, $dark);

        $qrContent = @file_get_contents($qrApiUrl);
        if ($qrContent) {
            $qrImg = @imagecreatefromstring($qrContent);
            if ($qrImg) {
                imagecopyresampled($img, $qrImg, $qrX, $qrY, 0, 0, 500, 500, imagesx($qrImg), imagesy($qrImg));
                imagedestroy($qrImg);
            }
        }

        // Subtitle Text
        $subStr1 = 'Arahkan kamera smartphone Anda ke QR Code ini';
        $subStr2 = 'untuk otomatis merekam presensi harian posko.';
        imagestring($img, 5, (int) (($width - (strlen($subStr1) * 10)) / 2), 920, $subStr1, $grayText);
        imagestring($img, 5, (int) (($width - (strlen($subStr2) * 10)) / 2), 950, $subStr2, $grayText);

        // Save image to temporary file
        $tempPath = tempnam(sys_get_temp_dir(), 'qr_poster_').'.png';
        imagepng($img, $tempPath);
        imagedestroy($img);

        // Upload to MinIO bucket
        try {
            Storage::disk($disk)->put($storagePath, file_get_contents($tempPath));
        } catch (\Throwable $e) {
            // Ignore upload exception if offline
        }

        // Return streamed attachment download from MinIO / disk
        try {
            if (Storage::disk($disk)->exists($storagePath)) {
                @unlink($tempPath);

                return Storage::disk($disk)->download($storagePath, "Poster-QR-Absensi-{$groupSlug}.png", [
                    'Content-Type' => 'image/png',
                ]);
            }
        } catch (\Throwable $e) {
            // Fallback to local response download
        }

        return response()->download($tempPath, "Poster-QR-Absensi-{$groupSlug}.png", [
            'Content-Type' => 'image/png',
        ])->deleteFileAfterSend(true);
    }
}
