<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Computed;

use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

new #[Layout('layouts::dashboard')] class extends Component
{
    use WithPagination;

    public ?int $quantity = 10;
    public ?string $search = null;
    public array $sort = [
        'column' => 'id',
        'direction' => 'asc',
    ];

    #[Computed]
    public function getUsers(){
        return User::query()
                ->when($this->search, function(Builder $query){
                    return $query->where('name', 'like', "%{$this->search}%")
                                 ->orWhere('username', 'like', "%%{$this->search}");
                })
                ->orderBy(...array_values($this->sort))
                ->paginate($this->quantity)
                ->withQueryString();

    }

    public array $headers = [
        ['index' => 'id', 'label' => '#'],
        ['index' => 'name', 'label' => 'Name'],
        ['index' => 'username', 'label' => 'User Name'],
        ['index' => 'email', 'label' => 'Email'],
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
            'rows' => $this->getUsers()
        ];
    }
};
?>

<div>
    <div class="max-w-1/3 mb-4">
        <x-breadcrumbs separator="icon:chevron-right" :items="[
            ['label' => 'Home', 'link' => '/', 'icon' => 'home'],
            ['label' => 'Settings', 'icon' => 'cog-6-tooth'],
            ['label' => 'Users', 'link' => '/users']
        ]" />
    </div>

    <x-card minimize color="lime" accent>
        <x-slot:header class="flex justify-between gap-2">
            <x-icon name="list-bullet" lg />
            <h1 class="text-2xl font-bold">List of Users</h1>
        </x-slot:header>

        @can('User-create')
            <x-button class="mb-4" sm text="Add New" color="purple" icon="plus-circle" href="{{ route('create-user') }}" wire:navigate.hover />
        @endcan

        <x-table :$headers :$rows :quantity="[2,5,10,15,20,25]" :$sort compact striped filter paginate loading>
            @interact('column_action', $row)
                @can('User-edit')
                    <x-button.circle sm color="green" icon="pencil-square" href="{{ route('edit-role', [$row->id]) }}" wire:navigate.hover />
                @endcan
            @endinteract
        </x-table>
    </x-card>
</div>
