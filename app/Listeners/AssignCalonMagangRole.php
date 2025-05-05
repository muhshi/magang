<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;

class AssignCalonMagangRole
{
    public function handle(Registered $event): void
    {
        $user = $event->user;
        $user->assignRole('Calon Magang');
    }
}
