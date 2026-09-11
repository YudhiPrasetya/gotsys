<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessSequenceOrder extends Model
{
    protected $fillable = [
        'order_id', 'process_sequence_id', 'process_name', 'ordinal'
    ];

    public function order(): BelongsTo{
        return $this->belongsTo(Order::class);
    }

    public function processSequence(): BelongsTo{
        return $this->belongsTo(ProcessSequence::class);
    }
}
