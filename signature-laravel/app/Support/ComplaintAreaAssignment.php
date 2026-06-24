<?php

namespace App\Support;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ComplaintAreaAssignment
{
    public static function assignableRoleNames(): array
    {
        return [
            'Junior Engineer',
            'Senior Engineer',
            'Unloading Technician',
        ];
    }

    public static function userManagesAreaAssignments(?User $user = null): bool
    {
        $user = $user ?? auth()->user();

        return $user && $user->hasAnyRole(['Admin', 'Super Admin']);
    }

    public static function userSeesAllComplaints(?User $user = null): bool
    {
        $user = $user ?? auth()->user();

        return $user && $user->hasAnyRole(['Admin', 'Super Admin']);
    }

    /**
     * @return Collection<int, int>
     */
    public static function assignedAreaIdsForUser(?User $user = null): Collection
    {
        $user = $user ?? auth()->user();

        if (! $user) {
            return collect();
        }

        if ($user->relationLoaded('complaintAreas')) {
            return $user->complaintAreas->pluck('id');
        }

        return $user->complaintAreas()->pluck('areas.id');
    }

    public static function userHasAreaAssignments(?User $user = null): bool
    {
        return self::assignedAreaIdsForUser($user)->isNotEmpty();
    }

    public static function applyVisibleScope(Builder $query, ?User $user = null): Builder
    {
        $user = $user ?? auth()->user();

        if (! $user || self::userSeesAllComplaints($user)) {
            return $query;
        }

        $areaIds = self::assignedAreaIdsForUser($user);
        $teamMemberIds = User::where('created_by', $user->id)->pluck('id')->toArray();

        return $query->where(function ($q) use ($areaIds, $teamMemberIds, $user) {
            $q->where('created_by', $user->id)
                ->orWhereIn('created_by', $teamMemberIds);

            if ($areaIds->isNotEmpty()) {
                $q->orWhereHas('contract', function ($contractQuery) use ($areaIds) {
                    $contractQuery->whereIn('area_id', $areaIds);
                });
            }
        });
    }

    public static function userCanViewComplaint(Complaint $complaint, ?User $user = null): bool
    {
        $user = $user ?? auth()->user();

        if (! $user) {
            return false;
        }

        if (self::userSeesAllComplaints($user)) {
            return true;
        }

        $teamMemberIds = User::where('created_by', $user->id)->pluck('id')->toArray();
        if ($complaint->created_by === $user->id || in_array($complaint->created_by, $teamMemberIds, true)) {
            return true;
        }

        $areaIds = self::assignedAreaIdsForUser($user);
        if ($areaIds->isEmpty()) {
            return false;
        }

        $complaint->loadMissing('contract');

        return $complaint->contract && $areaIds->contains($complaint->contract->area_id);
    }

    public static function ensureCanViewComplaint(Complaint $complaint, ?User $user = null): void
    {
        if (! self::userCanViewComplaint($complaint, $user)) {
            abort(403);
        }
    }

    public static function userCanActOnComplaint(Complaint $complaint, ?User $user = null): bool
    {
        $user = $user ?? auth()->user();

        if (! $user) {
            return false;
        }

        if (self::userSeesAllComplaints($user)) {
            return true;
        }

        if ($complaint->relationLoaded('assignees')) {
            if ($complaint->assignees->contains('id', $user->id)) {
                return true;
            }
        } elseif ($complaint->assignees()->where('users.id', $user->id)->exists()) {
            return true;
        }

        return self::userCanViewComplaint($complaint, $user);
    }

    public static function userCanAssignComplaint(Complaint $complaint, ?User $user = null): bool
    {
        $user = $user ?? auth()->user();

        if (! $user) {
            return false;
        }

        if (self::userSeesAllComplaints($user)) {
            return true;
        }

        $teamMemberIds = User::where('created_by', $user->id)->pluck('id')->toArray();

        return $complaint->created_by === $user->id || in_array($complaint->created_by, $teamMemberIds, true);
    }

    public static function assignableUsers()
    {
        return User::query()
            ->where('is_active', true)
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', self::assignableRoleNames());
            })
            ->with(['complaintAreas', 'roles'])
            ->orderBy('name')
            ->get();
    }
}
