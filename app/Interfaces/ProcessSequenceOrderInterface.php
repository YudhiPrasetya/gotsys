<?php

namespace App\Interfaces;

interface ProcessSequenceOrderInterface
{
    public function add(int $orderId);

    public function getProcessSequenceOrder(int $orderId);

    public function deleteProcessSequenceOrder(int $id);

    public function updateOrdinal(int $orderId, int $id, int $position);
}
