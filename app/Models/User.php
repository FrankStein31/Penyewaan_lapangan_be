<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'no_hp',
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'role' => 'string'
    ];
}