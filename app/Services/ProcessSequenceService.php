<?php

namespace App\Services;

use App\Interfaces\ProcessSequenceInterface;

class ProcessSequenceService
{
    public ProcessSequenceInterface $processSequenceInterface;
    /**
     * Create a new class instance.
     */
    public function __construct(ProcessSequenceInterface $processSequenceInterface)
    {
        $this->processSequenceInterface = $processSequenceInterface;
    }

    /**
     * Summary of processSequenceList
     * Function: processSequenceList
     * Description: Fetch all process sequences
     * @return mixed
     */
    public function processSequenceList(){
        return $this->processSequenceInterface->getProcessSequences();
    }

    /**
     * Summary of getProcessSequence
     * Function: getProcessSequence
     * Description: get process sequence by id
     * @param int $id
     * @return mixed
     */
    public function getProcessSequence(int $id){
        return $this->processSequenceInterface->getProcessSequence($id);
    }

    /**
     * Summary of updateProcessSequence
     * Function: updateProcessSequence
     * @param array $processSequenve
     * @return mixed
     */
    public function updateProcessSequence(array $processSequenve){
        return $this->processSequenceInterface->updateProcessSequence($processSequenve);
    }

    /**
     * Summary of deleteProcessSequence
     * Function: deleteProcessSequence
     * @param int $id
     * @return mixed
     */
    public function deleteProcessSequence(int $id){
        return $this->processSequenceInterface->deleteProcessSequences($id);
    }


}
