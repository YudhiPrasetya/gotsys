<?php

namespace App\Services;

use App\Interfaces\ProcessSequenceOrderInterface;

class ProcessSequenceOrderService
{
    public ProcessSequenceOrderInterface $processSequenceOrderInterface;
    /**
     * Create a new class instance.
     */
    public function __construct(ProcessSequenceOrderInterface $processSequenceOrderInterface)
    {
        $this->processSequenceOrderInterface = $processSequenceOrderInterface;
    }

    /**
     * Summary of addNewProcessSequenceOrder
     * Function: addNewProcessSequenceOrder
     * Description: add new process sequence for selected order
     * @param int $orderId
     * @return mixed
     */
    public function addNewProcessSequenceOrder(int $orderId){
        return $this->processSequenceOrderInterface->add($orderId);
    }

    public function getProcessSequenceOrder(int $orderId){
        return $this->processSequenceOrderInterface->getProcessSequenceOrder($orderId);
    }

    public function deleteProcessSequenceOrder(int $id){
        return $this->processSequenceOrderInterface->deleteProcessSequenceOrder($id);
    }

    public function updatePosition(int $orderId, int $id, int $position){
        return $this->processSequenceOrderInterface->updateOrdinal($orderId, $id, $position);
    }
}
