<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Computed;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Builder;

new #[Layout('layouts::dashboard')] #[Lazy] class extends Component
{
    use WithPagination;

    // public $permissions;
    public ?int $id = 0;
    public ?int $quantity = 10;
    public ?string $search = null;

    #[Computed]
    public function getPermissions(){
        return Permission::query()
               ->when($this->search, function(Builder $query){
                    return $query->where('name', 'like', "%{$this->search}%");
               })
               ->orderBy(...array_values($this->sort))
               ->paginate($this->quantity)
               ->withQueryString();
    }

    public array $sort = [
        'column' => 'id',
        'direction' => 'asc'
    ];

    public array $headers = [
        ['index' => 'id', 'label' => '#', 'sortable' => true],
        ['index' => 'name', 'label' => 'Name', 'sortable' => true],
        ['index' => 'action'],
    ];

    public function placeholder(){
        return <<<'HTML'
        <div>
            <x-table :$headers skeleton='20' />
        </div>
        HTML;
    }

    public function with(): array{
        return [
            $this->headers,
            'rows' => $this->getPermissions()
        ];
    }

};
?>

<div>
    <div class="max-w-1/3 mb-4">
        {{-- <x-card> --}}
            <x-breadcrumbs separator="icon:chevron-right" :items="[
                ['label' => 'Home', 'link' => '/', 'icon' => 'home'],
                ['label' => 'Settings', 'icon' => 'cog-6-tooth'],
                ['label' => 'Permissions', 'link' => '/permission']
            ]" />
        {{-- </x-card> --}}
    </div>

    <x-card minimize color="violet">
        <x-slot:header class="flex justify-between gap-2">
            <x-icon name="list-bullet" lg />
            <h1 class="text-2xl font-bold">List of Permissions</h1>
        </x-slot:header>

        @can('Permission-create')
            <x-button class="mb-4" sm text="Add New" color="purple" icon="plus-circle" href="{{ route('create-permission') }}" wire:navigate.hover />
        @endcan

        <x-table :$headers :$rows :quantity="[2,5,10,15,20,25]" :$sort compact striped filter paginate loading>
            @interact('column_action', $row)
                @can('Permission-edit')
                    <x-button.circle sm color="green" icon="pencil-square" href="{{ route('edit-permission', [$row->id]) }}" wire:navigate.hover />
                @endcan
            @endinteract
        </x-table>
    </x-card>


</div>
