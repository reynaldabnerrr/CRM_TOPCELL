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
        'status',
        'notes',
        'last_followup_date',
        'next_followup_date',
    ];

    protected function casts(): array
    {
        return [
            'last_followup_date' => 'date',
            'next_followup_date' => 'date',
        ];
    }
}
