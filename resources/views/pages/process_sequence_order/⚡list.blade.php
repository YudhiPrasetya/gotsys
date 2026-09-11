<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\WithPagination;
use App\Models\Order;

use Carbon\Carbon;

new #[Layout('layouts::dashboard')] #[Lazy] class extends Component
{
    use WithPagination;

    public array $headers = [
        ['index' => 'id', 'label' => '#'],
        ['index' => 'customer.name', 'label' => 'customer'],
        ['index' => 'product_name', 'label' => 'Product'],
        ['index' => 'quantity', 'label' => 'Quantity'],
        ['index' => 'deadline', 'label' => 'Deadline'],
        ['index' => 'action', 'label' => 'Action'],
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
            $this->headers,
            'rows' => Order::with(['customer:id,name'])->where('status_process', 'pending')->paginate(10)->withQueryString()
        ];
    }


};
?>

<div>
    <div class="max-w-1/3 mb-4">
        {{-- <x-card> --}}
            <x-breadcrumbs separator="icon:chevron-right" :items="[
                ['label' => 'Home', 'link' => '/', 'icon' => 'home'],
                ['label' => 'Data Master', 'icon' => 'circle-stack'],
                ['label' => 'Process Sequence Order', 'link' => '/process_sequenceorder']
            ]" />
        {{-- </x-card> --}}
    </div>

    <x-card minimize color="purple" accent>
        <x-slot:header class="flex justify-between gap-2">
            <x-icon name="list-bullet" xl />
            <h1 class="text-2xl font-bold">List of Orders With Process Sequence Order</h1>
        </x-slot:header>

        <x-table :$headers :$rows compact striped paginate expandable>
            @interact('column_action', $row)
                @can('ProcessSequenceOrder-edit')
                    <x-button text="Edit Process Sequence Order" color="green" icon="pencil-square" href="{{ route('edit-ProcessSequenceOrder', [$row->id]) }}" wire:navigate.hover />
                @endcan
            @endinteract

            @interact('column_deadline', $row)
                {{ Carbon::parse($row->deadline, 'Asia/Jakarta')->format('d-m-Y') }}
            @endinteract

            {{-- @interact('sub_table', $row)
                <x-table :headers="[
                        ['index' => 'processSequenceOrders.processSequence.process_name', 'label' => 'Process Name'],
                        ['index' => 'processSequenceOrders.processSequence.description', 'label' => 'Description'],
                        ['index' => 'processSequenceOrders.processSequence.ordinal', 'label' => 'Position']
                    ]" :rows="[
                        ['property' => 'process_name', 'value' => $row->processSequenceOrders->processSequence->process_name],
                        ['property' => 'description', 'value' => $row->processSequenceOrders->processSequence->description],
                        ['property' => 'ordinal', 'value' => $row->processSequenceOrders->processSequence->ordinal],
                    ]"
                />
            @endinteract --}}
        </x-table>
    </x-card>
</div>
