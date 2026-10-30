<?php

namespace App\Services;

use App\Interfaces\ProgressOrderInterface;
use App\Models\Order;

class ProgressOrderService
{
    public ProgressOrderInterface $progressOrderInterface;

    /**
     * Create a new class instance.
     */
    public function __construct(ProgressOrderInterface $progressOrderInterface)
    {
        $this->progressOrderInterface = $progressOrderInterface;
    }

    public function getProgressOrders($quantity, $search, $sort){
        return $this->progressOrderInterface->getProgressOrders($quantity, $search, $sort);
    }

    public function getProgressOrder(int $id){
        return $this->progressOrderInterface->getProgressOrder($id);
    }

    public function getProgressOrderByOrderId(int $orderId){
        return $this->progressOrderInterface->getProgressOrderByOrderId($orderId);
    }

    public function addNewProgressOrder(array $progressOrder){
        return $this->progressOrderInterface->add($progressOrder);
    }

    public function updateProgressOrderQty(int $orderId, int $psoId, int $qty, Order $order){
        return $this->progressOrderInterface->updateProgressOrderQty($orderId, $psoId, $qty, $order);
    }
}
