<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;

use App\Models\Order;
use App\Models\ProgressOrder;
use App\Models\ProgressOrderDetail;
use App\Models\ProcessSequenceOrder;
use TallStackUi\Traits\Interactions;

use Carbon\Carbon;

new #[Layout('layouts::dashboard')] #[Lazy] class extends Component
{
    use Interactions;

    #[Validate('required')]
    public $selectedOrder;

    public array $firstProcess = [];

    #[Validate('required')]
    public $selectedProcess;

    public string $customer = '';
    public string $product = '';


    public int $qty = 0;

    public string $deadline = '';

    #[Validate('required')]
    public $qtyInput;

    #[Computed]
    public function orders(){
        $parent = Order::where('status_process', 'pending')->pluck('id')->toArray();
        $child = ProgressOrder::pluck('order_id')->toArray();
        $parent = array_diff($parent, $child);
        $parent = collect($parent)->values()->all();

        return Order::with(['customer:id,name'])->whereIn('id', $parent)->get()->map(fn($order) => [
            'id' => $order->id,
            'name' => 'Customer: ' . $order->customer->name . ', Product: ' . $order->product_name . ', Quantity: ' . $order->quantity . ', Deadline: ' . Carbon::parse($order->deadline, 'Asia/Jakarta')->format('Y-m-d')
            // 'label' => $order->product_name
        ])->toArray();

        // return Order::whereIn('id', $parent)->select(['id', 'product_name'])->get()->toArray();
    }

    public function showOrderInfo(){
        $order = Order::with(['customer:id,name'])->find($this->selectedOrder);
        if($order){
            $this->customer = $order->customer->name;
            $this->product = $order->product_name;
            $this->qty = $order->quantity;
            $this->deadline = Carbon::parse($order->deadline, 'Asia/Jakarta')->format('Y-m-d');

            $this->firstProcess =  $this->getFirstProcess($this->selectedOrder);
        }
    }

    private function getFirstProcess(int $idOrder){
        $arr = ProcessSequenceOrder::with(['processSequence:id,process_name'])
                ->where('order_id', $idOrder)
                ->orderBy('ordinal')->first();

        return [
            ['id' => $arr->process_sequence_id, 'name' => $arr->processSequence->process_name,]
        ];

    }

    public function placeholder(): string{
        return <<<'HTML'
        <div>
            <x-card skeleton="4" header="Create new progress order" footer rounded="xl" />
        </div>
        HTML;
    }

    public function checkInputMax(){
        if($this->qtyInput > $this->qty){
            $this->dialog()
            ->warning('Warning', 'The maximum amount of Input qty is ' . (string)$this->qty . '!' )
            ->send();

            $this->qtyInput = 0;
        }
    }

    private function save(){
        // dd($this->selectedOrder, $this->selectedProcess, $this->qtyInput);
        $this->validate();

        $newProgressOrder = ProgressOrder::create([
            'order_id' => $this->selectedOrder,
            'process_sequence_id' => $this->selectedProcess,
            'qty' => $this->qtyInput
        ]);
        if($newProgressOrder){
            if(ProgressOrderDetail::create([
                'progress_order_id' => $newProgressOrder->id,
                'process_sequence_id' => $this->selectedProcess,
                'qty' => $this->qtyInput
            ]))
            return Order::find($this->selectedOrder)->update(['status_process' => 'In Progress']);
        }
    }

    public function saveAndCreate(){
        if($this->save()){
            $this->toast()
            ->timeout(1)
            ->success('Success', 'Data progress order added successfully')
            ->position('top-center')
            ->hook([
                'timeout' =>[
                    'method' => 'clearModel'
                ]
            ])->send();
        }
    }

    public function saveAndExit(){
        if($this->save()){
            $this->toast()
            ->timeout(1)
            ->success('Success', 'Data progress order added successfully')
            ->position('top-center')
            ->hook([
                'timeout' =>[
                    'method' => 'exit'
                ]
            ])->send();
        }
    }

    public function clearModel(){
        $this->selectedOrder = 0;
        $this->firstProcess = [];
        $this->selectedProcess = 0;
        $this->customer = '';
        $this->product = '';
        $this->deadline = '';
        $this->qtyInput = 0;
        $this->qty = 0;

        // $this->orders();
    }

    public function cancel(){
        $this->clearModel();
    }

    public function close(){
        $this->exit();
    }

    public function exit(){
        $this->redirect('/progress_order', navigate: true);
    }



};
?>

<div class="flex items-center justify-center">
    <div class="w-2/3 flex flex-col gap-4">
        <x-breadcrumbs separator="icon:chevron-right" :items="[
            ['label' => 'Home', 'link' => '/', 'icon' => 'home'],
            ['label' => 'Transaction', 'icon' => 'arrow-path-rounded-square'],
            ['label' => 'Create progress order', 'link' => '/progress_order/create']
        ]" />

        <x-card color="indigo" accent minimize>
            <x-slot:header class="flex justify-between gap-2">
                <x-icon name="user-plus" xl />
                <h1 class="text-2xl font-bold">Create new progress order</h1>
            </x-slot:header>

            <div class="flex grow gap-3 mb-3">
                <div class="flex flex-1 flex-col gap-6">
                    <x-select.styled
                        :options="$this->orders"
                        select="label:name|value:id"
                        wire:model.live="selectedOrder"
                        :change="$this->showOrderInfo()"
                        label="Order"
                        placeholder="Select an order"
                        searchable />
                    <x-input
                        wire:model="product"
                        label="Product"
                        disabled />
                    <x-select.styled
                        :options="$this->firstProcess"
                        select="label:name|value:id"
                        wire:model.live="selectedProcess"
                        label="First Process"
                        placeholder="Select a process" searchable />
                </div>

                <div class="flex flex-col gap-6">
                    <x-number wire:model="qty" label="Quantity" disabled />
                    <x-date wire:model="deadline" label="Deadline" disabled />
                    <x-number label="Input qty" wire:model.live="qtyInput" :blur="$this->checkInputMax()"/>
                </div>
            </div>

            <x-errors title="Ups! There are :count validation errors:" color="orange" />
            <x-slot:footer>
                <x-button color="green" wire:click="saveAndCreate" loading="saveAndCreate" spinner="dots" text="Save and create again" />
                <x-button color="purple" wire:click="saveAndExit" loading="saveAndExit" spinner="bars" text="Save and exit" />
                <x-button color="primary" outline wire:click="cancel" loading="cancel" spinner="wave" text="Cancel" />
                <x-button color="zinc" outline wire:click="close" loading="close" spinner="ping" text="Close" />
            </x-slot:footer>
        </x-card>
    </div>
</div>
