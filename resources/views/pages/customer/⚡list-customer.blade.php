<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\WithPagination;
use App\Models\Customer;

use TallStackUi\Traits\Interactions;

new #[Lazy] #[Layout('layouts::dashboard')] class extends Component
{
    use WithPagination, Interactions;

    public array $headers = [
                    ['index' => 'id', 'label' => '#'],
                    ['index' => 'name', 'label' => 'Customer Name'],
                    ['index' => 'address', 'label' => 'Address'],
                    ['index' => 'phone', 'label' => 'Phone'],
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
                ['index' => 'id', 'label' => '#'],
                ['index' => 'name', 'label' => 'Customer Name'],
                ['index' => 'address', 'label' => 'Address'],
                ['index' => 'phone', 'label' => 'Phone'],
                ['index' => 'action']
            ],
            // 'rows' => Customer::query()->paginate(5)->withQueryString()
            'rows' => Customer::selectRaw("id, name, address, CONCAT(prefix_phone,'',phone) as phone")->paginate(5)->withQueryString()
        ];
    }


    public function delete(string $id){
        // dd($id);
        $this->dialog()->question('Warning!', 'Are you sure want to delete?')
                       ->confirm('Yes', 'deleteCustomer', $id)
                       ->cancel('No')
                       ->send();

    }

    public function deleteCustomer(string $id){
        Customer::find($id)?->delete();

        $this->toast()
             ->timeout(2)
             ->success('Deleted', 'Record has been removed')
             ->position('top-center')
             ->hook([
                'timeout' => [
                    'method' => 'redirectToCustomer'
                ]
             ])
             ->send();
    }

    public function redirectToCustomer(){
        $this->redirect('/customer', navigate: true);
    }
};
?>

<div>
    {{-- <div class="max-w-5/12 mb-4"> --}}
    <div class="max-w-1/3 mb-4">
        {{-- <x-card> --}}
            <x-breadcrumbs separator="icon:chevron-right" :items="[
                ['label' => 'Home', 'link' => '/', 'icon' => 'home'],
                ['label' => 'Data Master', 'icon' => 'circle-stack'],
                ['label' => 'Customers', 'link' => '/customer']
            ]" />
        {{-- </x-card> --}}
    </div>

    <x-card minimize color="lime" accent>
        <x-slot:header class="flex justify-between gap-2">
            <x-icon name="list-bullet" xl />
            <h1 class="text-2xl font-bold">List of Customer</h1>
        </x-slot:header>
        @can('Customer-create')
            <x-button sm text="Add New" color="purple" icon="plus-circle" class="mb-2" href="{{ route('create-customer') }}" wire:navigate.hover />
        @endcan
        <x-table :$headers :$rows compact striped paginate>
            @interact('column_action', $row)
                @can('Customer-edit')
                    <x-button.circle sm color="green" icon="pencil-square" href="{{ route('edit-customer', [$row->id]) }}" wire:navigate.hover />
                @endcan

                @can('Customer-delete')
                    <x-button.circle sm color="red" icon="trash" wire:click="delete('{{ $row->id }}')" />
                @endcan
            @endinteract
        </x-table>
    </x-card>

    {{-- <x-modal title="{{ $this->modalTitle }}" name="customerModal" persistent wire handle>
        <div class="flex flex-col gap-4">
            <input type="hidden" id="idCust" name="idCust" value="{{ $this->idCust }}" wire:model="idCust" />
            <x-input label="Customer Name *" hint="Input cutomer name" wire:model="customerName" />
            <x-textarea label="Customer Address *" hint="Input cutomer name" wire:model="customerAddress" />
            <x-input.select label="Customer Cellphone" hint="Input customer cellphone" wire:model="customerPhone" icon="phone" clearable>
                <x-slot:left>
                    <x-select.native :options="['+02', '+62']" select="label:name|value:id" wire:model="selectedOption" />
                </x-slot:left>
            </x-input.select>
        </div>
        <x-slot:footer end>
            <x-button md color="green" icon="arrow-up-tray" loading="updateCustomer" spinner="dots" wire:click="updateCustomer">
                Save
            </x-button>
            <x-button sm color="primary" icon="x-circle" outline>
                Cancel
            </x-button>
            <x-button sm color="zinc" icon="x-mark" outline>
                Close
            </x-button>
        </x-slot:footer>
    </x-modal> --}}
</div>
