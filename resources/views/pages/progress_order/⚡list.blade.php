<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Computed;

use App\Services\ProgressOrderService;

use App\Models\ProgressOrder;
use App\Models\ProcessSequence;
use App\Models\ProcessSequenceOrder;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

new #[Layout('layouts::dashboard')] #[Lazy] class extends Component
{
    private ProgressOrderService $progressOrderService;

    public function boot(ProgressOrderService $progressOrderService){
        $this->progressOrderService = $progressOrderService;
    }

    use WithPagination;

    public ?int $quantity = 10;
    public ?string $search = null;
    public array $sort = [
        'column' => 'id',
        'direction' => 'asc',
    ];

    public function progressOrders(){
        // return Order::query()
        //                 ->when($this->search, function(Builder $query){
        //                     return $query->where('product_name', 'like', "%{$this->search}%");
        //                 })
        //                 ->where('status_process', 'In Progress')
        //                 ->with(['customer', 'progressOrders', 'processSequenceOrders'])
        //                 ->orderBy(...array_values($this->sort))
        //                 ->paginate($this->quantity)
        //                 ->withQueryString();

        return $this->progressOrderService->getProgressOrders($this->quantity, $this->search, $this->sort);

    }

    public array $headers= [
        ['index' => 'id', 'label' => '#'],
        ['index' => 'customer.name', 'label' => 'Customer'],
        ['index' => 'product_name', 'label' => 'Product'],
        ['index' => 'quantity', 'label' => 'Qty'],
        ['index' => 'deadline', 'label' => 'Deadline'],
        ['index' => 'progressOrder', 'label' => 'Process Sequence'],
        ['index' => 'action', 'label' => 'Action']
    ];

    public function placeholder(): string{
        return <<<'HTML'
        <div>
            <x-table :$headers skeleton="10" />
        </div>
        HTML;
    }

    public function with(){
        return[
            $this->headers,
            'rows' => $this->progressOrders()
        ];
    }
};
?>

<div>
    <div class="max-w-1/3 mb-4">
        <x-breadcrumbs separator="icon:chevron-right" :items="[
            ['label' => 'Home', 'link' => '/', 'icon' => 'home'],
            ['label' => 'Transaction', 'icon' => 'arrow-path-rounded-square'],
            ['label' => 'Progress order', 'link' => '/progress_order']
        ]" />
    </div>

    <x-card minimize color="purple" accent>
        <x-slot:header class="flex justify-between gap-2">
            <x-icon name="list-bullet" xl />
            <h1 class="text-2xl font-bold">Progress Orders</h1>
        </x-slot:header>

        @can('ProgressOrder-create')
            <x-button class="mb-2" sm text="Add New" color="purple" icon="plus-circle" href="{{ route('create-ProgressOrder') }}" wire:navigate.hover />
        @endcan

        <x-table :$headers :$rows :quantity="[2,5,10,15,20,25]" striped :$sort filter paginate loading>
            @interact('column_deadline', $row)
                {{ $row->deadline->format('Y-m-d') }}
            @endinteract

            @interact('column_progressOrder', $row)
                @php
                    $order = Order::find($row->id);
                    $rstPSO = ProcessSequenceOrder::where('order_id', $row->id)->orderBy('ordinal', 'asc')->get()->toArray();
                    $rstPO = ProgressOrder::where('order_id', $row->id)->get()->toArray();

                    $rstMatch = array_filter($rstPSO, fn($item) => in_array($item['process_sequence_id'], array_column($rstPO, 'process_sequence_id')));
                    $rstMatch = collect($rstMatch)->map(function($item) use($row, $order){
                        $ps = ProcessSequence::find($item['process_sequence_id']);
                        $po = ProgressOrder::where('order_id', $row->id)->where('process_sequence_id', $item['process_sequence_id'])->first();
                        return [
                            'title' => $ps->process_name,
                            'description' => $po->qty,
                            'date' => Carbon::parse($po->updated_at, 'Asia/Jakarta')->toDateString(),
                            'color' => $order->quantity == $po->qty ? 'green' :'yellow',
                            'icon' => $order->quantity == $po->qty ? 'check' : 'arrow-path'
                        ];
                    })->toArray();

                    $rstNotMatch = array_filter($rstPSO, fn($item) => !in_array($item['process_sequence_id'], array_column($rstPO, 'process_sequence_id')));
                    $rstNotMatch = collect($rstNotMatch)->map(function($item){
                        return [
                            'title' => $item['process_name'],
                            'description' => 0,
                            'date' => "0000-00-00",
                            'color' => 'yellow',
                            'icon' => 'arrow-path'
                        ];
                    })->toArray();

                    $process = [];
                    $arrMerge = array_merge($rstMatch, $rstNotMatch);
                    $process = $arrMerge;

                    // $process = [];
                    // foreach($row->order->processSequenceOrders as $p){
                    //     $description = $row->process_sequence_id == $p->process_sequence_id ? $row->qty : 0;
                    //     $date = $row->process_sequence_id == $p->process_sequence_id ? Carbon::parse($row->updated_at, 'Asia/Jakarta')->toDateString() : '00-00-00';
                    //     $data = [
                    //         'title' => $p->process_name,
                    //         'description' => $description,
                    //         'date' => $date,
                    //         'color' => $row->order->quantity == $row->qty ? 'green' : 'yellow',
                    //         'icon' => $row->order->quantity == $row->qty ? 'check' : ''
                    //     ];
                    //     $process[] = $data;
                    // }
                    // $process = [];
                    // foreach($row->processSequenceOrders as $pso){
                    //     foreach($row->progressOrders as $po){
                    //         $description = $pso->process_sequence_id == $po->process_sequence_id ? $po->qty : 0;
                    //         $date = $pso->process_sequence_id == $po->process_sequence_id ? Carbon::parse($po->updated_at, 'Asia/Jakarta')->toDateString() : '0000-00-00';
                    //         $data = [
                    //             'title' => $pso->process_name,
                    //             'description' => $description,
                    //             'date' => $date,
                    //             'color' => $row->quantity == $po->qty ? 'green' : 'yellow',
                    //             'icon' => $row->quantity == $po->qty ? 'check' : 'arrow-path'
                    //         ];
                    //         $process[] = $data;
                    //     }
                    // }
                    // dump($process);

                    // foreach($row->processSequenceOrders as $p){
                    //     dump($p);
                    // }

                @endphp
                <x-timeline horizontal :items="$process" />
            @endinteract

            @interact('column_action', $row)
                @can('ProgressOrder-edit')
                    <x-button.circle sm color="green" icon="pencil-square" href="{{ route('edit-ProgressOrder', [$row->id]) }}" wire:navigate.hover />
                @endcan
            @endinteract
        </x-table>
    </x-card>
</div>
