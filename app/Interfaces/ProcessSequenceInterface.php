<?php

namespace App\Interfaces;

interface ProcessSequenceInterface
{
    public function getProcessSequences();
    public function getProcessSequence(int $id);
    // public function createCustomer(array $newCustomer);
    public function updateProcessSequence(array $processSequence);
    public function deleteProcessSequences(int $id);
}
