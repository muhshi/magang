<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Internship;
use Illuminate\Auth\Access\HandlesAuthorization;

class InternshipPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Internship');
    }

    public function view(AuthUser $authUser, Internship $internship): bool
    {
        return $authUser->can('View:Internship');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Internship');
    }

    public function update(AuthUser $authUser, Internship $internship): bool
    {
        return $authUser->can('Update:Internship');
    }

    public function delete(AuthUser $authUser, Internship $internship): bool
    {
        return $authUser->can('Delete:Internship');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Internship');
    }

    public function restore(AuthUser $authUser, Internship $internship): bool
    {
        return $authUser->can('Restore:Internship');
    }

    public function forceDelete(AuthUser $authUser, Internship $internship): bool
    {
        return $authUser->can('ForceDelete:Internship');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Internship');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Internship');
    }

    public function replicate(AuthUser $authUser, Internship $internship): bool
    {
        return $authUser->can('Replicate:Internship');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Internship');
    }

}