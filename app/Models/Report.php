<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ReportStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Recipient::class);
    }

    protected function casts(): array
    {
        return [
            'status' => ReportStatusEnum::class,
        ];
    }
}
