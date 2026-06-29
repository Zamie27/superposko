<?php

namespace App\Helpers;

use App\Models\User;

class HostRoleHelper
{
    /**
     * Check if user is host or has sekretaris role (full access).
     */
    public static function isHostOrSekretaris(User $user): bool
    {
        return is_null($user->host_id) || $user->role === 'sekretaris';
    }

    /**
     * Check if user has permission to write/modify Kas & Keuangan, Inventaris, and Logistik.
     * Allowed: Host, Sekretaris, Bendahara.
     */
    public static function canWriteFinance(User $user): bool
    {
        return self::isHostOrSekretaris($user) || $user->role === 'bendahara';
    }

    /**
     * Check if user has permission to write/modify Buku Kontak, Repository Proker, and Dokumentasi.
     * Allowed: Host, Sekretaris, PDD.
     */
    public static function canWritePublicRelations(User $user): bool
    {
        return self::isHostOrSekretaris($user) || $user->role === 'pdd';
    }

    /**
     * Check if user has permission to view Immich credentials / integration panel.
     * Allowed: Host, Sekretaris, PDD.
     */
    public static function canManageImmich(User $user): bool
    {
        return self::isHostOrSekretaris($user) || $user->role === 'pdd';
    }

    /**
     * Check if user has permission to manage members.
     * Allowed: Host, Sekretaris.
     */
    public static function canManageMembers(User $user): bool
    {
        return self::isHostOrSekretaris($user);
    }
}
