<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginToken extends Model
{
    protected $fillable = [
        'telegram_id',
        'token',
        'expires_at'
    ];
}
