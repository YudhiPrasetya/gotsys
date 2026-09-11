<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressOrder extends Model
{
    protected $fillable = [
        'order_id', 'process_sequence_id', 'qty'
    ];

    public function order(): BelongsTo{
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function processSequence(): BelongsTo{
        return $this->belongsTo(ProcessSequence::class, 'process_sequence_id', 'id');
    }
}
