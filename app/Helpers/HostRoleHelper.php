<?php

namespace App\Helpers;

use App\Models\User;

class HostRoleHelper
{
    /**
     * Check if user is the posko Owner (the original registrant).
     * Owner is identified by having no host_id (they ARE the host).
     */
    public static function isOwner(User $user): bool
    {
        return is_null($user->host_id) && $user->role !== 'dpl';
    }

    /**
     * Check if user is in leadership (Owner, Ketua, or Wakil).
     */
    public static function isLeadership(User $user): bool
    {
        return self::isOwner($user) || in_array($user->role, ['ketua', 'wakil'], true);
    }

    /**
     * Check if user can administer the posko (Owner, Ketua, Wakil, or Sekretaris).
     * This is the base "admin-level" check used by most modules.
     */
    public static function canAdminister(User $user): bool
    {
        return self::isLeadership($user) || $user->role === 'sekretaris';
    }

    /**
     * Backward-compatible alias for canAdminister.
     */
    public static function isHostOrSekretaris(User $user): bool
    {
        return self::canAdminister($user);
    }

    /**
     * Check if user can manage members.
     * Allowed: Owner, Ketua, Wakil, Sekretaris.
     */
    public static function canManageMembers(User $user): bool
    {
        if ($user->role === 'lainnya' && $user->customRole) {
            return $user->customRole->permissions['can_manage_members'] ?? false;
        }
        return self::canAdminister($user);
    }

    /**
     * Check if user can write/modify Kas & Keuangan (E-Bendahara).
     * Allowed: Owner, Ketua, Wakil, Sekretaris, Bendahara.
     */
    public static function canWriteFinance(User $user): bool
    {
        if ($user->role === 'lainnya' && $user->customRole) {
            return $user->customRole->permissions['can_write_finance'] ?? false;
        }
        return self::canAdminister($user) || $user->role === 'bendahara';
    }

    /**
     * Check if user can write/modify Logistik & Konsumsi.
     * Allowed: Owner, Ketua, Wakil, Sekretaris, Logistik.
     */
    public static function canWriteLogistic(User $user): bool
    {
        if ($user->role === 'lainnya' && $user->customRole) {
            return $user->customRole->permissions['can_write_logistic'] ?? false;
        }
        return self::canAdminister($user) || $user->role === 'logistik';
    }

    /**
     * Check if user can write/modify Inventaris Barang.
     * Allowed: Owner, Ketua, Wakil, Sekretaris, Perlengkapan, Logistik.
     */
    public static function canWriteInventory(User $user): bool
    {
        if ($user->role === 'lainnya' && $user->customRole) {
            return $user->customRole->permissions['can_write_inventory'] ?? false;
        }
        return self::canAdminister($user) || in_array($user->role, ['perlengkapan', 'logistik'], true);
    }

    /**
     * Check if user can write/modify Buku Kontak.
     * Allowed: Owner, Ketua, Wakil, Sekretaris, Humas.
     */
    public static function canWriteContact(User $user): bool
    {
        if ($user->role === 'lainnya' && $user->customRole) {
            return $user->customRole->permissions['can_write_contact'] ?? false;
        }
        return self::canAdminister($user) || $user->role === 'humas';
    }

    /**
     * Check if user can write/modify Repository Proker & Dokumen.
     * Allowed: Owner, Ketua, Wakil, Sekretaris, Acara, Humas, PDD.
     */
    public static function canWriteProker(User $user): bool
    {
        if ($user->role === 'lainnya' && $user->customRole) {
            return $user->customRole->permissions['can_write_proker'] ?? false;
        }
        return self::canAdminister($user) || in_array($user->role, ['acara', 'humas', 'pdd'], true);
    }

    /**
     * Check if user can write/modify public relations content (Buku Kontak, Proker, Dokumentasi).
     * Backward-compatible with existing usage.
     * Allowed: Owner, Ketua, Wakil, Sekretaris, PDD, Humas, Acara.
     */
    public static function canWritePublicRelations(User $user): bool
    {
        return self::canWriteProker($user);
    }

    /**
     * Check if user can manage Immich documentation integration.
     * Allowed: Owner, Ketua, Wakil, Sekretaris, PDD.
     */
    public static function canManageImmich(User $user): bool
    {
        if ($user->role === 'lainnya' && $user->customRole) {
            return $user->customRole->permissions['can_manage_immich'] ?? false;
        }
        return self::canAdminister($user) || $user->role === 'pdd';
    }

    /**
     * Check if user can write/modify Jadwal & Roster Piket.
     * Allowed: Owner, Ketua, Wakil, Sekretaris, Acara.
     */
    public static function canWriteSchedule(User $user): bool
    {
        if ($user->role === 'lainnya' && $user->customRole) {
            return $user->customRole->permissions['can_write_schedule'] ?? false;
        }
        return self::canAdminister($user) || $user->role === 'acara';
    }

    /**
     * Check if user can write to Logbook Kelompok (group logbook).
     * Allowed: Owner, Ketua, Wakil, Sekretaris.
     */
    public static function canWriteGroupLogbook(User $user): bool
    {
        if ($user->role === 'lainnya' && $user->customRole) {
            return $user->customRole->permissions['can_write_group_logbook'] ?? false;
        }
        return self::canAdminister($user);
    }
}
