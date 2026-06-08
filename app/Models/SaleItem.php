<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleItem extends Model
{
    use HasFactory;

    protected $table = 'sale_items';

    protected $fillable = [
        'invoice_number',
        'item_code',
        'item_name',
        'vendor',
        'quantity',
        'unit',
        'purchase_price',
        'selling_price',
        'discount',
        'tax',
        'serial_number',
        'total_revenue',
        'profit',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'purchase_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'total_revenue' => 'decimal:2',
            'profit' => 'decimal:2',
        ];
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'invoice_number', 'invoice_number');
    }
}

