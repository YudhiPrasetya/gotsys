<?php

namespace App\Repositories;

use App\Interfaces\ProgressOrderDetailInterface;
use App\Models\ProgressOrder;
use App\Models\ProgressOrderDetail;

class ProgressOrderDetailRepository implements ProgressOrderDetailInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function addNewProgressOrderDetail(int $orderId, int $psoId, int $qty){
        return ProgressOrderDetail::create([
                'progress_order_id' => ProgressOrder::where('order_id', $orderId)
                                                   ->where('process_sequence_id', $psoId)
                                                   ->first()->id,
                'process_sequence_id' => $psoId,
                'qty' => $qty
        ]);
    }
}
