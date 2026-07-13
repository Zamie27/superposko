<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceController extends Controller
{
    protected string $url = '';
    protected string $apiKey = '';

    protected function resolveConfig(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        // Get global Immich Server URL
        $this->url = rtrim(Setting::get('immich_url', config('services.immich.url', '')), '/');

        // Resolve host user
        $hostId = $user->host_id ?? $user->id;
        $host = User::find($hostId);

        if ($host) {
            $this->apiKey = ($host->immich_api_key ?: config('services.immich.api_key')) ?? '';
        }

        return ! empty($this->apiKey) && ! empty($this->url);
    }

    public function index(): Response
    {
        $user = auth()->user();
        $hostId = $user->host_id ?? $user->id;

        // Retrieve today's attendance for the current user
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();

        // Get all attendances for the host (for recap view)
        $isLeader = in_array($user->role, ['ketua', 'wakil', 'sekretaris']);
        
        $recap = [];
        if ($isLeader) {
            $recap = Attendance::with('user')
                ->where('host_id', $hostId)
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return Inertia::render('attendance/Index', [
            'todayAttendance' => $todayAttendance,
            'recap' => $recap,
            'isLeader' => $isLeader,
            'hasImmichConfig' => $this->resolveConfig()
        ]);
    }

    public function store(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $user = auth()->user();
        $hostId = $user->host_id ?? $user->id;

        if (! $this->resolveConfig()) {
            return back()->with('error', 'API Key Immich belum dikonfigurasi oleh ketua.');
        }

        $request->validate([
            'photo' => ['required', 'image', 'max:10240'], // 10MB max
        ]);

        // Check if already submitted today
        $alreadySubmitted = Attendance::where('user_id', $user->id)
            ->whereDate('date', today())
            ->exists();
            
        if ($alreadySubmitted) {
            return back()->with('error', 'Anda sudah mengisi absensi hari ini.');
        }

        /** @var UploadedFile $file */
        $file = $request->file('photo');

        $deviceId = 'SuperPosko-Web-Absensi';
        $deviceAssetId = Str::uuid()->toString();
        $now = now()->toIso8601String();

        try {
            $stream = fopen($file->getPathname(), 'r');
            if ($stream === false) {
                throw new \RuntimeException('Gagal membuka file.');
            }

            $response = Http::timeout(300)->withHeaders([
                'x-api-key' => $this->apiKey,
                'Accept' => 'application/json',
            ])->attach(
                'assetData', $stream, $file->getClientOriginalName()
            )->post("{$this->url}/api/assets", [
                'deviceId' => $deviceId,
                'deviceAssetId' => $deviceAssetId,
                'fileCreatedAt' => $now,
                'fileModifiedAt' => $now,
                'isFavorite' => 'false',
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                $immichAssetId = $responseData['id'] ?? $responseData['duplicateId'] ?? null;

                if (! $immichAssetId) {
                    return back()->with('error', 'Gagal mendapatkan ID foto dari Immich.');
                }

                // Save Attendance record
                Attendance::create([
                    'user_id' => $user->id,
                    'host_id' => $hostId,
                    'immich_asset_id' => $immichAssetId,
                    'date' => today(),
                    'time' => now()->format('H:i:s'),
                    'status' => 'hadir'
                ]);

                return back()->with('success', 'Absensi berhasil dicatat.');
            }

            return back()->with('error', 'Gagal mengunggah foto ke Immich: '.$response->body());
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengunggah: '.$e->getMessage());
        }
    }
}
