<?php

namespace App\Support;

use App\Models\ProformaInvoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class MsUnloadingAssignment
{
    public static function assignableRoleNames(): array
    {
        return [
            'Junior Engineer',
            'Senior Engineer',
            'Unloading Technician',
            'Admin',
        ];
    }

    public static function userSeesAllMsUnloading(?User $user = null): bool
    {
        $user = $user ?? auth()->user();

        return $user && $user->hasAnyRole(['Admin', 'Super Admin']);
    }

    public static function applyVisibleScope(Builder $query, ?User $user = null): Builder
    {
        $user = $user ?? auth()->user();

        if (! $user || self::userSeesAllMsUnloading($user)) {
            return $query;
        }

        return $query->whereHas('msUnloadingAssignedUsers', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        });
    }

    public static function ensureCanAccessPi(ProformaInvoice $proformaInvoice, ?User $user = null): void
    {
        if (! self::userCanAccessPi($proformaInvoice, $user)) {
            abort(403, 'You are not assigned to this MS Unloading job.');
        }
    }

    public static function userCanAccessPi(ProformaInvoice $proformaInvoice, ?User $user = null): bool
    {
        $user = $user ?? auth()->user();

        if (! $user) {
            return false;
        }

        if (self::userSeesAllMsUnloading($user)) {
            return true;
        }

        if ($proformaInvoice->relationLoaded('msUnloadingAssignedUsers')) {
            return $proformaInvoice->msUnloadingAssignedUsers->contains('id', $user->id);
        }

        return $proformaInvoice->msUnloadingAssignedUsers()
            ->where('users.id', $user->id)
            ->exists();
    }

    public static function assignableUsers()
    {
        return User::query()
            ->where('is_active', true)
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', self::assignableRoleNames());
            })
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
