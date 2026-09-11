<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressOrderDetail extends Model
{
    protected $fillable = [
        'progress_order_id', 'process_sequence_id', 'qty'
    ];

    public function progressOrder(): BelongsTo{
        return $this->belongsTo(ProgressOrder::class);
    }

}
