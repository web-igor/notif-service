<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipient extends Model
{
    protected $table = 'recipients';
    protected $guarded = ['id'];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
