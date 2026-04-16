<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Logbook;
use Illuminate\Auth\Access\HandlesAuthorization;

class LogbookPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Logbook');
    }

    public function view(AuthUser $authUser, Logbook $logbook): bool
    {
        return $authUser->can('View:Logbook');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Logbook');
    }

    public function update(AuthUser $authUser, Logbook $logbook): bool
    {
        return $authUser->can('Update:Logbook');
    }

    public function delete(AuthUser $authUser, Logbook $logbook): bool
    {
        return $authUser->can('Delete:Logbook');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Logbook');
    }

    public function restore(AuthUser $authUser, Logbook $logbook): bool
    {
        return $authUser->can('Restore:Logbook');
    }

    public function forceDelete(AuthUser $authUser, Logbook $logbook): bool
    {
        return $authUser->can('ForceDelete:Logbook');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Logbook');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Logbook');
    }

    public function replicate(AuthUser $authUser, Logbook $logbook): bool
    {
        return $authUser->can('Replicate:Logbook');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Logbook');
    }

}