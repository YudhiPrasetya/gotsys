<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProcessSequence extends Model
{
    protected $fillable = [
        // 'order',
        'process_name',
        'alias',
        'description'
    ];

    public function processSequenceOrder(): HasOne{
        return $this->hasOne(ProcessSequenceOrder::class);
    }
}
