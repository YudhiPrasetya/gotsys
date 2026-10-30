<?php

namespace App\Services;

use App\Interfaces\OrderInterface;

class OrderService
{
    private OrderInterface $orderInterface;
    /**
     * Create a new class instance.
     */
    public function __construct(OrderInterface $orderInterface)
    {
        $this->orderInterface = $orderInterface;
    }

    /**
     * Summary of orderList
     * Function: orderList
     * Description: list of orders
     * @return mixed
     */
    public function orderList(){
        return $this->orderInterface->getOrders();
    }

    /**
     * Summary of getOrder
     * Function: getOrder
     * Description: get order by id
     * @param int  $id
     * @return mixed
     */
    public function getOrder(int $id){
        return $this->orderInterface->getOrder($id);
    }

    /**
     * Summary of addNewOrder
     * Function: addNewOrder
     * Description: add new order
     * @param array $order
     * @return mixed
     */
    public function addNewOrder(array $order){
        return $this->orderInterface->addOrder($order);
    }

    /**
     * Summary of updateOrder
     * Function: updateOrder
     * Description: update selected order
     * @param array $order
     * @return mixed
     *
     */
    public function updateOrder(array $order){
        return $this->orderInterface->updateOrder($order);
    }

    /**
     * Summary of deleteOrder
     * Function: deleteOrder
     * description: delete order
     * @param int $id
     * @return mixed
     */
    public function deleteOrder(int $id){
        return $this->orderInterface->deleteOrder($id);
    }
}
