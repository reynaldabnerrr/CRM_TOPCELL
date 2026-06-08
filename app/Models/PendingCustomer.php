<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PendingCustomer extends Model
{
    use HasFactory;

    protected $table = 'pending_customers';

    protected $fillable = [
        'name',
        'phone_number',
        'entry_date',
        'status_id',
        'notes',
        'followup_h1_date',
        'followup_h1_last_date',
        'followup_h7_date',
        'followup_h7_last_date',
        'followup_h1month_date',
        'followup_h1month_last_date',
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'followup_h1_date' => 'date',
            'followup_h1_last_date' => 'date',
            'followup_h7_date' => 'date',
            'followup_h7_last_date' => 'date',
            'followup_h1month_date' => 'date',
            'followup_h1month_last_date' => 'date',
        ];
    }

    public function status()
    {
        return $this->belongsTo(PendingCustomerStatus::class, 'status_id');
    }
}
