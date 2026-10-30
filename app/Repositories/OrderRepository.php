<?php

namespace App\Repositories;

use App\Interfaces\OrderInterface;
use App\Models\Order;

class OrderRepository implements OrderInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Summary of getOrders
     * Function: getOrders
     * Description: Fetch all orders that has pending status_process
     * @return \Illuminate\Pagination\LengthAwarePaginator<int, Order>
     */
    public function getOrders(){
        return Order::with(['customer:id,name'])->where('status_process', 'pending')->paginate(10)->withQueryString();
    }

    /**
     * Summary of getOrder
     * Function: getOrder
     * Description: get order by id
     * @param int $id
     * @return Order|\Illuminate\Database\Eloquent\Builder<Order>
     */
    public function getOrder(int $id){
        // return Order::find($id);
        return Order::with(['customer:id,name'])->find($id);
    }

    /**
     * Summary of addOrder
     * Function: addOrder
     * Description: add new order
     * @param array $order
     * @return int
     */
    public function addOrder(array $order)
    {
        return Order::insertGetId($order);
    }

    /**
     * Summary of updateOrder
     * Function: updateOrder
     * Description: update selected order
     * @param array $order
     * @return int
     */
    public function updateOrder(array $order){
        return Order::update($order);

        // return Order::updateOrInsert(['id' => $order['id']], function($exists) use($order){
        //     if($exists){
        //         return [
        //             'customer_id' => $order['customer_id'],
        //             'product_name' => $order['product_name'],
        //             'quantity' => $order['quantity'],
        //             'deadline' => $order['deadline'],
        //             'status_process' => $order['status_process']
        //         ];
        //     }
        //     return [
        //         'customer_id' => $order['customer_id'],
        //         'product_name' => $order['product_name'],
        //         'quantity' => $order['quantity'],
        //         'deadline' => $order['deadline'],
        //         'status_process' => $order['status_process']
        //     ];
        // });
    }

    /**
     * Summary of deleteOrder
     * Function: deleteOrder
     * Description: delete order
     * @param int $id
     * @return bool|int|mixed|null
     */
    public function deleteOrder(int $id){
        return Order::find($id)?->delete();
    }
}
