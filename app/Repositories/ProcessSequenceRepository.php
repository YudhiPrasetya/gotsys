<?php

namespace App\Repositories;

use App\Interfaces\ProcessSequenceInterface;
use App\Models\ProcessSequence;

class ProcessSequenceRepository implements ProcessSequenceInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getProcessSequences(){
        return ProcessSequence::query()->orderBy('id', 'asc')->paginate(5)->withQueryString();
    }
    public function getProcessSequence(int $id){
        return ProcessSequence::find($id);
    }
    public function updateProcessSequence(array $processSequence){
        return ProcessSequence::updateOrInsert(['id' => $processSequence['id']], function($exists) use($processSequence){
            if($exists){
                return [
                    'process_name' => $processSequence['process_name'],
                    'alias' => $processSequence['alias'],
                    'description' => $processSequence['description'],
                ];
            }
            return [
                'process_name' => $processSequence['process_name'],
                'alias' => $processSequence['alias'],
                'description' => $processSequence['description'],
            ];
        });
    }
    public function deleteProcessSequences(int $id){
        return ProcessSequence::find($id)?->delete();
    }
}
