<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ReportStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property ReportStatusEnum $status
 */
class Report extends Model
{
    use HasFactory;

    protected $table = 'reports';
    protected $guarded = ['id'];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReportStatusEnum::class,
        ];
    }
}
