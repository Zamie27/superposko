<?php

namespace App\Helpers;

use App\Models\User;

class RoleConfig
{
    /**
     * Role definitions with display labels and maximum capacity per posko.
     * A capacity of 0 means unlimited.
     *
     * @var array<string, array{label: string, capacity: int}>
     */
    private const ROLES = [
        'ketua' => ['label' => 'Ketua', 'capacity' => 0],
        'wakil' => ['label' => 'Wakil Ketua', 'capacity' => 1],
        'sekretaris' => ['label' => 'Sekretaris', 'capacity' => 0],
        'bendahara' => ['label' => 'Bendahara', 'capacity' => 2],
        'logistik' => ['label' => 'Logistik & Konsumsi', 'capacity' => 2],
        'pdd' => ['label' => 'PDD (Publikasi, Dokumentasi, Desain)', 'capacity' => 3],
        'humas' => ['label' => 'Humas', 'capacity' => 3],
        'acara' => ['label' => 'Acara', 'capacity' => 3],
        'perlengkapan' => ['label' => 'Perlengkapan', 'capacity' => 3],
        'dpl' => ['label' => 'DPL (Dosen Pembimbing Lapangan)', 'capacity' => 0],
        'anggota' => ['label' => 'Anggota Biasa', 'capacity' => 0],
    ];

    /**
     * Get the display label for a given role.
     */
    public static function getRoleLabel(string $role): string
    {
        return self::ROLES[$role]['label'] ?? ucfirst($role);
    }

    /**
     * Get the maximum capacity for a given role (0 = unlimited).
     */
    public static function getRoleCapacity(string $role): int
    {
        return self::ROLES[$role]['capacity'] ?? 0;
    }

    /**
     * Get all role definitions.
     *
     * @return array<string, array{label: string, capacity: int}>
     */
    public static function getAllRoles(): array
    {
        return self::ROLES;
    }

    /**
     * Get all valid role keys.
     *
     * @return string[]
     */
    public static function getRoleKeys(): array
    {
        return array_keys(self::ROLES);
    }

    /**
     * Get available roles for a posko with current slot counts.
     * Returns each role with its label, capacity, current count, and whether it's available.
     *
     * @param int $hostId The host/owner user ID
     * @param int|null $excludeUserId Exclude this user from the count (useful when editing a member's role)
     * @return array<string, array{label: string, capacity: int, current: int, available: bool}>
     */
    public static function getAvailableRoles(int $hostId, ?int $excludeUserId = null): array
    {
        $query = User::where(function ($q) use ($hostId) {
            $q->where('host_id', $hostId)->orWhere('id', $hostId);
        });

        if ($excludeUserId !== null) {
            $query->where('id', '!=', $excludeUserId);
        }

        $roleCounts = $query->selectRaw('role, count(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();

        $result = [];
        foreach (self::ROLES as $key => $config) {
            $current = $roleCounts[$key] ?? 0;
            $capacity = $config['capacity'];
            $result[$key] = [
                'label' => $config['label'],
                'capacity' => $capacity,
                'current' => $current,
                'available' => $capacity === 0 || $current < $capacity,
            ];
        }

        return $result;
    }
}
