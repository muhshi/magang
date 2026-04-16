<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Aturan;
use Illuminate\Auth\Access\HandlesAuthorization;

class AturanPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Aturan');
    }

    public function view(AuthUser $authUser, Aturan $aturan): bool
    {
        return $authUser->can('View:Aturan');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Aturan');
    }

    public function update(AuthUser $authUser, Aturan $aturan): bool
    {
        return $authUser->can('Update:Aturan');
    }

    public function delete(AuthUser $authUser, Aturan $aturan): bool
    {
        return $authUser->can('Delete:Aturan');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Aturan');
    }

    public function restore(AuthUser $authUser, Aturan $aturan): bool
    {
        return $authUser->can('Restore:Aturan');
    }

    public function forceDelete(AuthUser $authUser, Aturan $aturan): bool
    {
        return $authUser->can('ForceDelete:Aturan');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Aturan');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Aturan');
    }

    public function replicate(AuthUser $authUser, Aturan $aturan): bool
    {
        return $authUser->can('Replicate:Aturan');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Aturan');
    }

}