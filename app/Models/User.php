<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'user_accounts';

    protected $fillable = [
        'username',
        'password',
        'name',
        'role_id',
        'phone_number',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at'
    ];
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
