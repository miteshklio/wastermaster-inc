<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'last_login', 'last_bids_view'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Role relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function role()
    {
        return $this->hasOne('\App\UserRole', 'id', 'role_id');
    }

    /**
     * Check if user is admin
     *
     * @return bool
     */
    public function getIsAdminAttribute()
    {
        // Check if admin
        if($this->role->name === 'Admin') {
            return true;
        }

        return false;
    }
}
