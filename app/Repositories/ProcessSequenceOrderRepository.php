<?php

namespace App\Repositories;

use App\Interfaces\ProcessSequenceOrderInterface;
use App\Models\ProcessSequence;
use App\Models\ProcessSequenceOrder;
use Carbon\Carbon;

class ProcessSequenceOrderRepository implements ProcessSequenceOrderInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Summary of add
     * Function: add
     * Description: add new process sequence for current product order
     * @param int $orderId
     * @return bool
     */
    public function add(int $orderId){
        $processSequenceOrders = ProcessSequence::all()->map(function ($ps) use($orderId) {
            return [
                'order_id' => $orderId,
                'process_sequence_id' => $ps->id,
                'process_name' => $ps->process_name,
                'ordinal' => $ps->id-1,
                'created_at' => Carbon::now('Asia/Jakarta'),
                'updated_at' => Carbon::now('Asia/Jakarta')
            ];
        })->toArray();

        return ProcessSequenceOrder::insert($processSequenceOrders);

    }

    /**
     * Summary of getProcessSequenceOrder
     * Function: getProcessSequenceOrder
     * Description: get process sequence selected order by order id
     * @param int $orderId
     * @return \Illuminate\Support\Collection<int, \stdClass>
     */
    public function getProcessSequenceOrder(int $orderId){
        return ProcessSequenceOrder::with('processSequence')->where('order_id', $orderId)->orderBy('ordinal')->get();
    }

    /**
     * Summary of deleteProcessSequenceOrder
     * Function: deleteProcessSequenceOrder
     * Description: delete process sequence order for selected order
     * @param int $id
     * @return bool|int|mixed|null
     */
    public function deleteProcessSequenceOrder(int $id){
        return ProcessSequenceOrder::find($id)->delete();
    }

    public function updateOrdinal(int $orderId, int $id, int $position){
        $tasks = ProcessSequenceOrder::where('order_id', $orderId)->orderBy('ordinal')->get();

        $movedTask = $tasks->firstWhere('id', $id);
        $tasks = $tasks->reject(fn($task) => $task->id == $id)->values();

        $tasks->splice($position, 0, [$movedTask]);
        foreach($tasks as $index => $task){
            ProcessSequenceOrder::where('id', $task->id)->update(['ordinal' => $index]);
        }

        return true;
    }
}
