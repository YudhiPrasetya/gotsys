<?php

namespace App\Interfaces;

use App\Models\Order;

interface ProgressOrderInterface
{
    public function getProgressOrders(int $quantity, ?string $search, array $sort);

    public function getProgressOrder(int $id);

    public function getProgressOrderByOrderId(int $orderId);

    public function add(array $progressOrder);

    public function updateProgressOrderQty(int $orderId, int $psoId, int $qty, Order $order);
}
