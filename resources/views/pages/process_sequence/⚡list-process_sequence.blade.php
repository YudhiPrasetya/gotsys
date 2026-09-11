<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;
use App\Models\ProcessSequence;

new #[Layout('layouts::dashboard')] #[Lazy] class extends Component
{
    use WithPagination, Interactions;

    public array $headers = [
        ['index' => 'id', 'label' => '#'],
        ['index' => 'order', 'label' => 'Process Order'],
        ['index' => 'process_name', 'label' => 'Process Name'],
        ['index' => 'alias', 'label' => 'Alias'],
        ['index' => 'description', 'label' => 'Description'],
        ['index' => 'action']
    ];

    public function placeholder(): string{
        return <<<'HTML'
        <div>
            <x-table :$headers skeleton="10" />
        </div>
        HTML;
    }

    public function with(): array{
        return [
            'headers' => [
                ['index' => 'id', 'label' => '#', 'sortable' => false],
                ['index' => 'process_name', 'label' => 'Process Name', 'sortable' => false],
                ['index' => 'alias', 'label' => 'Alias', 'sortable' => false],
                ['index' => 'description', 'label' => 'Description', 'sortable' => false],
                ['index' => 'action']
            ],
            'rows' => ProcessSequence::query()->paginate(10)->withQueryString()
        ];
    }

    public function delete(string $id){
        // dd($id);
        $this->dialog()->question('Warning!', 'Are you sure want to delete?')
                       ->confirm('Yes', 'deleteProcessSequence', $id)
                       ->cancel('No')
                       ->send();

    }

    public function deleteProcessSequence(string $id){
        ProcessSequence::find($id)?->delete();

        $this->toast()
             ->timeout(2)
             ->success('Deleted', 'Record has been removed')
             ->position('top-center')
             ->hook([
                'timeout' => [
                    'method' => 'redirectToProcessSequence'
                ]
             ])
             ->send();
    }

    public function redirectToProcessSequence(){
        $this->redirect('/process_sequence', navigate: true);
    }

};
?>

<div>
    <div class="max-w-1/3 mb-4">
        {{-- <x-card> --}}
            <x-breadcrumbs separator="icon:chevron-right" :items="[
                ['label' => 'Home', 'link' => '/', 'icon' => 'home'],
                ['label' => 'Data Master', 'icon' => 'circle-stack'],
                ['label' => 'Process Sequence', 'link' => '/process_sequence']
            ]" />
        {{-- </x-card> --}}
    </div>

    <x-card minimize color="purple" accent>
        <x-slot:header class="flex justify-between gap-2">
            <x-icon name="list-bullet" xl />
            <h1 class="text-2xl font-bold">List of Process Sequences</h1>
        </x-slot:header>

        @can('ProcessSequence-create')
            <x-button class="mb-2" sm text="Add New" color="purple" icon="plus-circle" href="{{ route('create-process_sequence') }}" wire:navigate.hover />
        @endcan

        <x-table :$headers :$rows compact striped paginate>
            @interact('column_action', $row)
                @can('ProcessSequence-edit')
                    <x-button.circle sm color="green" icon="pencil-square" href="{{ route('edit-process_sequence', [$row->id]) }}" wire:navigate.hover />
                @endcan

                @can('ProcessSequence-delete')
                    <x-button.circle sm color="red" icon="trash" wire:click="delete('{{ $row->id }}')" />
                @endcan
            @endinteract
        </x-table>
    </x-card>
</div>
