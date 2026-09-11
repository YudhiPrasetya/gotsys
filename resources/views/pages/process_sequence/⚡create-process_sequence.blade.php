<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Lazy;
use TallStackUi\Traits\Interactions;
use App\Models\ProcessSequence;

new #[Layout('layouts::dashboard')] #[Lazy] class extends Component
{
    use Interactions;

    #[Validate('required')]
    public string $processName = '';

    public string $alias = '';

    public string $description = '';

    public function placeholder(): string{
        return <<<'HTML'
        <div>
            <x-card skeleton="3" />
        </div>
        HTML;
    }

    public function saveAndCreate(){
        $this->save();
        // $this->messageSuccess();
        $this->toast()
             ->timeout(2)
             ->position('top-center')
             ->success('Success', 'New process sequence data added succesfully!')
             ->hook([
                'timeout' => [
                    'method' => 'clearModel'
                ]
             ])
             ->send();
    }

    private function save(){
        $this->validate();

        ProcessSequence::create([
            'process_name' => $this->processName,
            'alias' => $this->alias,
            'description' => $this->description
        ]);
        // $this->messageSuccess();
    }

    public function clearModel(){
        $this->processName = '';
        $this->alias = '';
        $this->description = '';
    }

    public function exit(){
        $this->redirect('/process_sequence', navigate: true);
    }

    public function cancel(){
        $this->clearModel();
    }

    public function close(){
        $this->exit();
    }

    public function saveAndExit(){
        $this->save();
        $this->toast()
             ->position('top-center')
             ->timeout(2)
             ->success('Success', 'New process sequence data added succesfully!')
             ->hook([
                'timeout' => [
                    'method' => 'exit'
                ]
             ])
             ->send();
    }
};
?>

<div class="flex items-center justify-center">
    <div class="w-2/3 flex flex-col gap-4">
        {{-- <div class="max-w-1/3"> --}}
            <x-breadcrumbs separator="icon:chevron-right" :items="[
                ['label' => 'Home', 'link' => '/', 'icon' => 'home'],
                ['label' => 'Data Master', 'icon' => 'circle-stack'],
                ['label' => 'Create Process Sequence', 'link' => '/process_sequence/create']
            ]" />
        {{-- </div> --}}

        <x-card color="indigo" accent minimize>
            <x-slot:header class="flex justify-between gap-2">
                {{-- <div class=""> --}}
                    <x-icon name="arrow-path-rounded-square" xl />
                    <h1 class="text-2xl font-bold">Create new Process Sequence</h1>
                {{-- </div> --}}
            </x-slot:header>
            <div class="flex flex-col gap-6">
                <x-input wire:model="processName" label="Process Name" hint="Input process name" />
                <x-input wire:model="alias" label="Process Name Alias" hint="Input process name alias" />
                <x-textarea wire:model="description" label="Description" hint="Input description of the process" />
            </div>
            <x-slot:footer>
                <x-button color="green" wire:click="saveAndCreate" loading="saveAndCreate" spinner="dots" text="Save and create again" />
                <x-button color="purple" wire:click="saveAndExit" loading="saveAndExit" spinner="bars" text="Save and exit" />
                <x-button color="primary" outline wire:click="cancel" loading="cancel" spinner="wave" text="Cancel" />
                <x-button color="zinc" outline wire:click="close" loading="close" spinner="ping" text="Close" />
            </x-slot:footer>
        </x-card>
    </div>
</div>
