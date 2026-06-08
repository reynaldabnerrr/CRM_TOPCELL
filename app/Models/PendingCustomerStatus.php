<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingCustomerStatus extends Model
{
    protected $table = 'pending_customer_statuses';

    protected $fillable = [
        'name',
    ];

    public $timestamps = false;

    public function customers()
    {
        return $this->hasMany(PendingCustomer::class, 'status_id');
    }
}
