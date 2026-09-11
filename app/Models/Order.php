<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'customer_id', 'product_name', 'quantity', 'deadline', 'status_process'
    ];

    protected function casts(): array{
        return [
            'deadline' => 'date:Y-m-d',
        ];
    }

    public function customer(): HasOne{
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function processSequenceOrders(): HasMany{
        return $this->hasMany(ProcessSequenceOrder::class);
    }

    public function progressOrders(): HasMany{
        return $this->hasMany(ProgressOrder::class);
    }
}
