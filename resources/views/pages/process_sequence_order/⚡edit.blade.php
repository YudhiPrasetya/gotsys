<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use TallStackUi\Traits\Interactions;
use App\Services\OrderService;
use App\Services\ProcessSequenceOrderService;

use App\Models\Order;
use App\Models\ProcessSequence;
use App\Models\ProcessSequenceOrder;
use Carbon\Carbon;

new #[Layout('layouts::dashboard')] class extends Component
{
    private OrderService $orderService;
    private ProcessSequenceOrderService $processSequenceOrderService;

    public function boot(OrderService $orderService, ProcessSequenceOrderService $processSequenceOrderService){
        $this->orderService = $orderService;
        $this->processSequenceOrderService = $processSequenceOrderService;
    }

    use Interactions;

    public $processSequences = [];
    public $processSequenceOrders;
    public int $idOrder;
    public $order;
    public string $customer;
    public string $product;
    public int $quantity;
    public string $deadline;

    public ?array $selectedProcesSequences = [];

    public bool $btnAddDisabled = true;

    public function mount(int $orderId){
        $this->idOrder = $orderId;
        $this->getOrderInfo($orderId);
        $this->getProcessSequenceOrder($this->idOrder);

    }

    public function getOrderInfo(int $id){
        // $this->order = Order::with(['customer:id,name'])->find($id);
        $this->order = $this->orderService->getOrder($id);
        $this->customer = $this->order->customer->name;
        $this->product = $this->order->product_name;
        $this->quantity = $this->order->quantity;
        $this->deadline = Carbon::parse($this->order->deadline, 'Asia/Jakarta')->format('Y-m-d');
        // dd($this->deadline);

    }

    // #[Computed]
    public function getProcessSequenceOrder(int $id){
        // $this->processSequenceOrders = ProcessSequenceOrder::with('processSequence')
        // ->where('order_id', $id)->orderBy('ordinal')->get();

        $this->processSequenceOrders = $this->processSequenceOrderService->getProcessSequenceOrder($id);

        $this->findTheDiff();

    }

    public function findTheDiff(){
        $parentProcessSequenceId = ProcessSequence::pluck('id')->toArray();
        $childProcessSequenceOrderId = $this->processSequenceOrders->pluck('process_sequence_id')->toArray();
        $parentProcessSequenceId = array_diff($parentProcessSequenceId, $childProcessSequenceOrderId);
        $parentProcessSequenceId = collect($parentProcessSequenceId)->values()->all();

        if(count($parentProcessSequenceId) > 0){
            $this->processSequences = ProcessSequence::whereIn('id', $parentProcessSequenceId)->get()->map(function($item){
                return [
                    'label' => $item->process_name,
                    'value' => $item->id,
                    'description' => $item->description
                ];
            });

            // dd($this->processSequences);
        }else{
            $this->processSequences = [];
        }

    }

    public function updateOrdinal($id, $position){

        // $tasks = ProcessSequenceOrder::where('order_id', $this->idOrder)->orderBy('ordinal')->get();

        // $movedTask = $tasks->firstWhere('id', $id);
        // $tasks = $tasks->reject(fn( $task) => $task->id == $id)->values();

        // $tasks->splice($position, 0, [$movedTask]);
        // foreach($tasks as $index => $task){
        //     ProcessSequenceOrder::where('id', $task->id)->update(['ordinal' => $index]);
        // }

        $updated = $this->processSequenceOrderService->updatePosition($this->idOrder, $id, $position);
        if($updated){
            $this->getProcessSequenceOrder($this->idOrder);

            $this->toast()
                ->success('Success', 'Process Sequence Order updated successfully!')
                ->timeout(1)
            ->send();
        }
    }

    public function remove(int $id){
        $this->dialog()
        ->question('Confirmation', 'Are you sure want to delete?')
        ->confirm('Yes', 'delete', $id)
        ->cancel('No')
        ->send();
    }

    public function delete(int $id){
        $deleted = $this->processSequenceOrderService->deleteProcessSequenceOrder($id);
        if($deleted){
            $this->toast()
            ->timeout(2)
            ->position('top-center')
            ->info('Info', 'Data process sequence order has been deleted successfully')
            ->hook([
                'timeout' => [
                    'method' => 'reloadData',
                    'params' => $this->idOrder
                ]
            ])->send();
        }
    }

    public function addProcessSequences(){
        // dd($this->selectedProcesSequences);
        $dataProcessSequences = collect($this->selectedProcesSequences)->map(function($item){
            $ps = ProcessSequence::find($item);
            return [
                'order_id' => $this->idOrder,
                'process_sequence_id' => $item,
                'process_name' => $ps->process_name,
                'ordinal' => $item-1,
                'created_at' => Carbon::now('Asia/Jakarta'),
                'updated_at' => Carbon::now('Asia/Jakarta'),
            ];
        });
        $newData = ProcessSequenceOrder::insert($dataProcessSequences->toArray());
        if($newData){
            $this->selectedProcesSequences = [];

            $this->toast()
            ->position('top-center')
            ->timeout(2)
            ->success('Success', 'Addedd proces sequences order data succeed!')
            ->hook([
                'timeout' => [
                    'method' => 'reloadData',
                    'params' => $this->idOrder
                ]
            ])->send();
        }
    }

    public function reloadData(int $id){
        $this->getProcessSequenceOrder($id);
    }

    public function close(){
        $this->redirect('/process_sequence_order', navigate: true);
    }
};
?>

<div class="flex items-center justify-center">
    <div class="w-2/3 flex flex-col gap-4">
        <x-breadcrumbs separator="icon:chevron-right" :items="[
            ['label' => 'Home', 'link' => '/', 'icon' => 'home'],
            ['label' => 'Data Master', 'icon' => 'circle-stack'],
            ['label' => 'Edit process sequence order', 'link' => route('edit-ProcessSequenceOrder', $this->idOrder)]
        ]" />

        <x-card color="indigo" accent minimize>
            <x-slot:header class="flex justify-between gap-2">
                <x-icon name="numbered-list" xl />
                <h1 class="text-2xl font-bold">Edit process sequence order</h1>
            </x-slot:header>

            <div class="flex gap-2 items-center mb-3">
                <hr class="border-t-gray-600 border-b-gray-700 w-20"></hr>
                <p class="text-lg">Order Info</p>
                <hr class="flex-1 border-t-gray-600 border-b-gray-700" />
            </div>

            <div class="flex gap-4 mb-3">
                <div class="w-1/2 flex flex-col gap-6">
                    <x-input value="{{ $this->customer }}" label="Customer" disabled />
                    <x-input value="{{ $this->product }}" label="Product" disabled />
                </div>

                <div class="w-1/2 flex flex-col gap-6">
                    <x-number value="{{ $this->quantity }}" label="Quantity" disabled />
                    <x-input value="{{ $this->deadline }}" label="Deadline" disabled />
                </div>
            </div>

            <div class="flex gap-2 items-center mb-3">
                <hr class="border-t-gray-600 border-b-gray-700 w-20"></hr>
                <p class="text-lg">Process Sequence Order</p>
                <hr class="flex-1 border-t-gray-600 border-b-gray-700" />
            </div>

            <ul wire:sort="updateOrdinal" class="space-y-2 p-4 bg-gray-500 rounded-lg mb-3">
                @foreach($processSequenceOrders as $task)
                    <!-- Each item needs wire:sort:item and a unique wire:key -->
                    <li
                        wire:sort:item="{{ $task->id }}"
                        wire:key="task-{{ $task->id }}"
                        class="p-1 bg-white shadow rounded border flex items-center justify-between cursor-move select-none"
                    >
                        <div class="flex items-center gap-3">
                            <!-- Visual drag handle anchor -->
                            {{-- <span class="text-gray-400">☰</span> --}}
                            <span class="text-gray-400">↕</span>
                            <span class="text-gray-800 text-lg font-bold">{{ $task->process_name }}</span> -
                            <span class="text-gray-800">{{ $task->processSequence->description }}</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded">
                                Position: {{ $task->ordinal+1 }}
                            </span>
                            @can('ProcessSequenceOrder-delete')
                                <x-button.circle icon="trash" color="orange" loading="remove({{ $task->id }})" wire:click="remove({{ $task->id }})" spinner="wave" />
                            @endcan
                        </div>
                    </li>
                @endforeach
            </ul>

            <x-accordion chevron="left">
                <x-accordion.items title="Add process sequence order" id="add-process-sequence-order" icon="plus-circle">
                    <x-checkbox.group class="mb-3" wire:model.live="selectedProcesSequences" label="Process sequence order" panel :options="$processSequences" />

                    <div class="flex flex-row-reverse gap-3">
                        <x-button :disabled="empty($selectedProcesSequences)" wire:click="addProcessSequences" text="Add" color="green" light icon="plus" loading="addProcessSequences" spinner="wave" />
                    </div>
                </x-accordion.items>
            </x-accordion>

            <div class="flex flex-reverse my-4">
                <x-button md wire:click="close" text="Close" color="red" light icon="x-circle" loading="close" spinner="dots" />
            </div>

        </x-card>
    </div>
</div>
