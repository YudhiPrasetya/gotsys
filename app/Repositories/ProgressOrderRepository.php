<?php

namespace App\Repositories;

use App\Interfaces\ProgressOrderInterface;
use App\Models\Order;
use App\Models\ProcessSequenceOrder;
use App\Models\ProgressOrder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProgressOrderRepository implements ProgressOrderInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {

    }

    public function getProgressOrders(int $quantity, ?string $search, array $sort){
        return Order::query()
                        ->when($search, function(Builder $query) use ($search){
                            return $query->where('product_name', 'like', "%{$search}%");
                        })
                        ->where('status_process', 'In Progress')
                        ->with(['customer', 'progressOrders', 'processSequenceOrders'])
                        ->orderBy(...array_values($sort))
                        ->paginate($quantity)
                        ->withQueryString();
    }

    public function getProgressOrder(int $id){
        return ProgressOrder::find($id);
    }

    public function getProgressOrderByOrderId(int $orderId){
        return ProgressOrder::where('order_id', $orderId)->get()->toArray();
    }

    public function add(array $progressOrder){
        return ProgressOrder::create($progressOrder);
    }

    public function updateProgressOrderQty(int $orderId, int $psoId, int $qty, Order $order){
        try{
            DB::beginTransaction();

            ProgressOrder::where('order_id', $orderId)
                                ->where('process_sequence_id', $psoId)
                                ->increment('qty', $qty,
                                            ['updated_at' => Carbon::now('Asia/Jakarta')]);

            $progressOrder = ProgressOrder::where('order_id', $orderId)->get()->toArray();
            $processSequenceOrder = ProcessSequenceOrder::where('order_id', $orderId)->count();
            if($processSequenceOrder == count($progressOrder)){
                foreach($progressOrder as $po){
                    if($po['qty'] == $order->quantity){
                        $this->finishOrder($orderId);
                    }
                }
            }

            DB::commit();
        }catch(\Exception $e){
            report($e);
            DB::rollBack();
        }
    }

    private function finishOrder(int $orderId){
        Order::find($orderId)->update(['status_process' => 'Finish']);
    }
}
