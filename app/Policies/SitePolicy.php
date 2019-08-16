<?php

namespace App\Policies;

use App\User;
use App\Site;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;

class SitePolicy
{
    use HandlesAuthorization;

    private $is_admin;

    public function __construct() {
        $this->is_admin = Gate::allows('admin');
    }

    /**
     * Determine whether the user can view the site.
     *
     * @param  \App\User  $user
     * @param  \App\Site  $site
     * @return mixed
     */
    public function view(User $user, Site $site)
    {
        // verifica se o número usp em questão é responsável ou adminstrador de site
        $all = $site->owner . ',' . $site->numeros_usp . ',' . config('sites.admins');
        if(in_array($user->codpes,explode(",",$all))) {
            return true; 
        }
        return false;
    }

    /**
     * Determine whether the user can create sites.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the site.
     *
     * @param  \App\User  $user
     * @param  \App\Site  $site
     * @return mixed
     */
    public function update(User $user, Site $site)
    {
        return ($user->codpes == $site->owner) || $this->is_admin;
    }

    /**
     * Determine whether the user can delete the site.
     *
     * @param  \App\User  $user
     * @param  \App\Site  $site
     * @return mixed
     */
    public function delete(User $user, Site $site)
    {
        return ($user->codpes == $site->owner) || $this->is_admin;
    }

}
