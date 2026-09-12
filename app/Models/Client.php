<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Client extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'clients';
    protected $guarded = ['id'];
    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'password'  => 'hashed',
            'abilities' => 'array',
        ];
    }
}
