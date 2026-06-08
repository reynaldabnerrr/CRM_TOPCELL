<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'sales';

    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'unit_name',
        'department',
        'warehouse',
        'customer_name',
        'phone_number',
        'sales_person',
        'payment_method',
        'status',
        'amount',
        'notes',
        'followup_h1_date',
        'followup_h7_date',
        'followup_1month_date',
        'followup_h1_status',
        'followup_h7_status',
        'followup_1month_status',
        'followup_h1_notes',
        'followup_h7_notes',
        'followup_1month_notes',
        'last_followup_at',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'followup_h1_date' => 'date',
            'followup_h7_date' => 'date',
            'followup_1month_date' => 'date',
            'amount' => 'decimal:2',
            'last_followup_at' => 'datetime',
        ];
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class, 'invoice_number', 'invoice_number');
    }
}
