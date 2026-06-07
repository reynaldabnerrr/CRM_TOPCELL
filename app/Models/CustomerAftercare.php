<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerAftercare extends Model
{
    use HasFactory;

    protected $table = 'customer_aftercare';

    protected $fillable = [
        'sale_id',
        'customer_name',
        'phone_number',
        'type',
        'scheduled_date',
        'done_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'done_date' => 'date',
        ];
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
