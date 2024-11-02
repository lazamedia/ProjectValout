<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine if the given project can be edited by the user.
     */
    public function update(User $user, Project $project)
    {
        // Izinkan akses jika pengguna adalah admin atau pemilik proyek
        return $user->role === 'admin' || $user->id === $project->user_id;
    }

    /**
     * Determine if the given project can be deleted by the user.
     */
    public function delete(User $user, Project $project)
    {
        // Izinkan akses jika pengguna adalah admin atau pemilik proyek
        return $user->role === 'admin' || $user->id === $project->user_id;
    }
}
