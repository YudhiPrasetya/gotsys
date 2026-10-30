<?php

namespace App\Interfaces;

interface OrderInterface
{
    public function getOrders();
    public function getOrder(int $id);
    public function addOrder(array $order);
    public function updateOrder(array $order);
    public function deleteOrder(int $id);
}
