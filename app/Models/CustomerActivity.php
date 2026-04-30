<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerActivity extends Model
{
    protected $fillable = ['customer_id', 'pppoe_user', 'action', 'ip_address', 'description'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
