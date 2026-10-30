<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Lazy;
use TallStackUi\Traits\Interactions;
use App\Services\ProcessSequenceService;
use App\Models\ProcessSequence;

new #[Layout('layouts::dashboard')] #[Lazy] class extends Component
{
    private ProcessSequenceService $processSequenceService;

    public function boot(ProcessSequenceService $processSequenceService){
        $this->processSequenceService = $processSequenceService;
    }
    use Interactions;

    public ProcessSequence $processSequence;
    public int $processId = 0;

    #[Validate('required')]
    public string $processName = '';

    public string $alias = '';

    public string $description = '';

    public function placeholder(): string{
        return <<<'HTML'
        <div>
            <x-card skeleton="4" />
        </div>
        HTML;
    }

    public function mount($id){
        // $this->processSequence = ProcessSequence::find($id);
        $this->processSequence = $this->processSequenceService->getProcessSequence($id);

        $this->processId = $id;
        $this->processName = $this->processSequence->process_name;
        $this->alias = $this->processSequence->alias;
        $this->description = $this->processSequence->description;

    }

    public function saveAndExit(){
        $updated = $this->processSequence->update([
            'id' => $this->processId,
            'process_name' => $this->processName,
            'alias' => $this->alias,
            'description' => $this->description,
        ]);
        $updateProcessSequence = [
            'id' => $this->processId,
            'process_name' => $this->processName,
            'alias' => $this->alias,
            'description' => $this->description,
        ];
        if($this->processSequenceService->updateProcessSequence($updateProcessSequence)){
            $this->toast()
                ->timeout(2)
                ->success('Success', 'Data process sequence updated successfully')
                ->hook([
                    'timeout' => [
                        'method' => 'exit'
                ]
            ])
            ->send();
        }
    }

    public function exit(){
        $this->redirect('/process_sequence', navigate: true);
    }

    public function close(){
        $this->exit();
    }
};
?>

<div class="flex items-center justify-center">
    <div class="w-2/3 flex flex-col gap-4">
        {{-- <div class="max-w-1/3"> --}}
            <x-breadcrumbs separator="icon:chevron-right" :items="[
                ['label' => 'Home', 'link' => '/', 'icon' => 'home'],
                ['label' => 'Data Master', 'icon' => 'circle-stack'],
                ['label' => 'Edit Process Sequence']
            ]" />
        {{-- </div> --}}

        <x-card color="indigo" accent minimize>
            <x-slot:header class="flex justify-between gap-2">
                <x-icon name="arrow-path-rounded-square" xl />
                <h1 class="text-2xl font-bold">Edit Process Sequence</h1>
            </x-slot:header>
            <div class="flex flex-col gap-6">
                <x-input wire:model="processName" label="Process Name" hint="Input process name" />
                <x-input wire:model="alias" label="Process Name Alias" hint="Input process name alias" />
                <x-textarea wire:model="description" label="Description" hint="Input description of the process" />
            </div>
            <x-slot:footer>
                <x-button color="purple" wire:click="saveAndExit" loading="saveAndExit" spinner="bars" text="Save and exit" />
                <x-button color="zinc" outline wire:click="close" loading="close" spinner="ping" text="Close" />
            </x-slot:footer>
        </x-card>
    </div>
</div>
