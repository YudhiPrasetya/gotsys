<?php

namespace App\Services;

use App\Interfaces\ProgressOrderDetailInterface;

class ProgressOrderDetailService
{
    public ProgressOrderDetailInterface $progressOrderDetailInterface;

    /**
     * Create a new class instance.
     */
    public function __construct(ProgressOrderDetailInterface $progressOrderDetailInterface)
    {
        $this->progressOrderDetailInterface = $progressOrderDetailInterface;
    }

    public function addNewProgressOrderDetail(int $orderId, int $psoId, int $qty){
        return $this->progressOrderDetailInterface->addNewProgressOrderDetail($orderId, $psoId, $qty);
    }
}
