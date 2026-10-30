<?php

namespace App\Interfaces;

interface ProgressOrderDetailInterface
{
    public function addNewProgressOrderDetail(int $orderId, int $psoId, int $qty);
}
