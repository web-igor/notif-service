<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'recipient_id' => 'required|int|exists:users,id',
            'from_date'    => 'required|date_format:Y-m-d|before_or_equal:to_date',
            'to_date'      => 'required|date_format:Y-m-d|after_or_equal:from_date',
        ];
    }
}
