<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

use App\Models\Customer;
use App\Models\Order;
use App\Models\ProcessSequenceOrder;
use App\Models\ProcessSequence;
use App\Models\ProgressOrder;

use Carbon\Carbon;

use TallStackUi\Traits\Interactions;

new #[Layout('layouts::dashboard')] #[Lazy] class extends Component
{
    use WithPagination, Interactions;

    public array $chartOrders = [];

    public int $customerCount = 0;

    public function mount(){
        $this->generateInProgressOrdersChart();
        $this->customerCount = Customer::count();
    }

    #[Computed]
    public function countCustomers(){
        return Customer::count();
    }

    #[Computed]
    public function countOrders(){
        return Order::count();
    }

    #[Computed]
    public function countPendingOrders(){
        return Order::where('status_process', 'pending')->count();
    }

    #[Computed]
    public function countRunningOrders(){
        return Order::where('status_process', 'In Progress')->count();
    }

    #[Computed]
    public function countFinishOrders(){
        return Order::where('status_process', 'Finish')->count();
    }

    #[Computed]
    public function getProgressOrders(){
        return Order::query()
                        ->where('status_process', 'In Progress')
                        ->with(['customer', 'progressOrders', 'processSequenceOrders'])
                        ->paginate(5)
                        ->withQueryString();

    }

    #[Computed]
    public function generateInProgressOrdersChart(){
        $inProgressOrders = Order::where('status_process', 'In Progress')->with(['customer', 'progressOrders', 'processSequenceOrders'])->get()->toArray();

        foreach($inProgressOrders as $index => $inProgressOrder){
            $series = [];
            foreach($inProgressOrder['process_sequence_orders'] as $pso){
                if(in_array($pso['process_sequence_id'], array_column($inProgressOrder['progress_orders'], 'process_sequence_id'))){
                    $series = array_column($inProgressOrder['progress_orders'], 'qty');
                }else{
                    $series[] = 0;
                }
            }
            // dump(array_column($inProgressOrder['progress_orders'], 'process_sequence_id'));
            $this->chartOrders[] = [
                'orderId' => $inProgressOrder['id'],
                'customer' => $inProgressOrder['customer']['name'],
                'product' => $inProgressOrder['product_name'],
                'quantity' => $inProgressOrder['quantity'],
                'labels' => array_column($inProgressOrder['process_sequence_orders'], 'process_name'),
                'series' => $series
            ];
        }
        // foreach($this->chartOrders as $co){
        //     dump( $co['series']);
        //     dump($co['labels']);
        // }
    }

    public array $headers= [
        ['index' => 'id', 'label' => '#'],
        ['index' => 'customer.name', 'label' => 'Customer'],
        ['index' => 'product_name', 'label' => 'Product'],
        ['index' => 'quantity', 'label' => 'Qty'],
        // ['index' => 'deadline', 'label' => 'Deadline'],
        ['index' => 'progressOrder', 'label' => 'Progress']
    ];

    public function with(){
        return[
            $this->headers,
            'rows' => $this->getProgressOrders()
        ];
    }

    public function placeholder(){
        return <<<'HTML'
            <div>
                <div class="mb-4">
                    <x-stats skeleton />
                </div>

                <div class="mb-4">
                    <x-chart skeleton="10" bar />
                </div>

                <div class="mb-4">
                    <x-card skeleton header="Progress Orders" />
                </div>
            </div>
            HTML;
    }

    #[On('echo:orders,OrderUpdated')]
    public function handleOrderUpdated($event){
        $this->generateInProgressOrdersChart();
    }

    #[On('echo:customer-added,.customer.added')]
    public function handleAddCustomer($event){
        $this->customerCount = $event['totalCustomers'];
    }

};
?>

<div>
    <div class="flex justify-center gap-6 mb-4">
        <x-stats title="Customers" icon="swatch" color="pink" :number="$this->customerCount" animated duration="3"/>
        <x-stats title="Total Orders" icon="swatch" color="pink" :number="$this->countOrders" animated duration="3"/>
        <x-stats title="Total Pending Orders" icon="swatch" color="pink" :number="$this->countPendingOrders" animated duration="3"/>
        <x-stats title="Total Running Orders" icon="swatch" color="pink" :number="$this->countRunningOrders" animated duration="3"/>
        <x-stats title="Total Finish Orders" icon="swatch" color="pink" :number="$this->countFinishOrders" animated duration="3"/>
    </div>

    <div class="mb-4 flex gap-4 justify-center">
            @foreach($this->chartOrders as $co)
                <div class="flex w-100">
                    <x-card color="purple" accent minimize>
                        <x-slot:header class="flex justify-between gap-2">
                            <x-icon name="list-bullet" xl />
                            <h1 class="text-xl font-bold">Progress Orders in Chart</h1>
                        </x-slot:header>

                        <x-chart :series="$co['series']"
                                    :labels="$co['labels']"
                                    fit="thin"
                                    color="emerald"
                                    grid tooltip
                                >
                            <x-slot:header>
                                <div class="flex flex-col gap-3 items-center justify-center">
                                    <div>
                                        Product: {{ $co['product'] }}
                                    </div>
                                    <div>
                                        Customer: {{ $co['customer'] }}
                                    </div>
                                    <div>
                                        Quantity: {{ $co['quantity'] }}
                                    </div>
                                </div>
                            </x-slot:header>
                        </x-chart>
                    </x-card>
                </div>
            @endforeach
    </div>

    <div class="flex mb-4">
        <x-card color="violet" accent minimize>
            <x-slot:header class="flex justify-between gap-2">
                <x-icon name="list-bullet" xl />
                <h1 class="text-2xl font-bold">Progress Orders</h1>
            </x-slot:header>

            <x-table :$headers :$rows striped loading compact>
                @interact('column_deadline', $row)
                    {{ $row->deadline->format('Y-m-d') }}
                @endinteract

                @interact('column_progressOrder', $row)
                    @php
                        $order = Order::find($row->id);
                        $rstPSO = ProcessSequenceOrder::where('order_id', $row->id)->get()->toArray();
                        $rstPO = ProgressOrder::where('order_id', $row->id)->get()->toArray();

                        $rstMatch = array_filter($rstPSO, fn($item) => in_array($item['process_sequence_id'], array_column($rstPO, 'process_sequence_id')));
                        $rstMatch = collect($rstMatch)->map(function($item) use($row, $order){
                                $ps = ProcessSequence::find($item['process_sequence_id']);
                                $po = ProgressOrder::where('order_id', $row->id)->where('process_sequence_id', $item['process_sequence_id'])->first();
                                return [
                                    'title' => $ps->process_name,
                                    'description' => $po->qty,
                                    'date' => Carbon::parse($item['updated_at'], 'Asia/Jakarta')->toDateString(),
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


                    @endphp
                    <x-timeline horizontal :items="$process" />
                @endinteract
            </x-table>
        </x-card>
    </div>


</div>
