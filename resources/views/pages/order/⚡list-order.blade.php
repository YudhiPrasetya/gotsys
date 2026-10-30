<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use App\Services\OrderService;
use App\Models\Order;

new #[Layout('layouts::dashboard')] #[Lazy] class extends Component
{
    private OrderService $orderService;

    public function boot(OrderService $orderService){
        $this->orderService = $orderService;
    }

    use WithPagination;

    public array $headers = [
        ['index' => 'id', 'label' => '#'],
        ['index' => 'customer.name', 'label' => 'Customer'],
        ['index' => 'product_name', 'label' => 'Product'],
        ['index' => 'quantity', 'label' => 'Qty'],
        ['index' => 'deadline', 'label' => 'Deadline'],
        ['index' => 'status_process', 'label' => 'Status Process'],
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
        return [ $this->headers,
            // 'rows' => Order::with(['customer:id,name'])->where('status_process', 'pending')->paginate(10)->withQueryString()
            'rows' => $this->orderService->orderList()
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
                ['label' => 'Order', 'link' => '/order']
            ]" />
        {{-- </x-card> --}}
    </div>

    <x-card minimize color="purple" accent>
        <x-slot:header class="flex justify-between gap-2">
            <x-icon name="list-bullet" xl />
            <h1 class="text-2xl font-bold">List of Orders</h1>
        </x-slot:header>

        @can('Order-create')
            <x-button class="mb-2" sm text="Add New" color="purple" icon="plus-circle" href="{{ route('create-order') }}" wire:navigate.hover />
        @endcan

        <x-table :$headers :$rows compact striped paginate>
            @interact('column_deadline', $row)
                {{ $row->deadline->format('Y-m-d') }}
            @endinteract

            @interact('column_action', $row)
                @can('Order-edit')
                    <x-button.circle sm color="green" icon="pencil-square" href="{{ route('edit-order', [$row->id]) }}" wire:navigate.hover />
                @endcan
            @endinteract
        </x-table>
    </x-card>
</div>
