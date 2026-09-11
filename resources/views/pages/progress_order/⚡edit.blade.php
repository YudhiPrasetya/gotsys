<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use TallStackUi\Traits\Interactions;

use App\Models\Customer;
use App\Models\Order;
use App\Models\ProgressOrder;
use App\Models\ProcessSequenceOrder;
use App\Models\ProcessSequence;

use Carbon\Carbon;

new #[Layout('layouts::dashboard')] #[Lazy] class extends Component
{
    use Interactions;

    public $orderId;
    public $selectedOrder;
    // public $processSequenceOrders;
    public $customer;

    // public $progressOrder = null;

    public ?int $orderQty = 0;

    public $processSequenceOrders;
    public $progressOrders;
    public array $maxInputProcess = [];

    public int $qtyInputPrevProcess = 0;

    #[Validate('required')]
    public $selectedProcessSequenceOrder;

    #[Validate('required')]
    public $qtyInput;

    public string $deadline = '';

    public function mount(int $id){
        $this->selectedOrder = Order::find($id);
        $this->orderId = $id;
        $this->deadline = $this->selectedOrder->deadline;
        $this->customer = Customer::find($this->selectedOrder->customer_id);
        $this->processSequenceOrders = ProcessSequenceOrder::where('order_id', $this->orderId)->get();
        $this->progressOrders = ProgressOrder::where('order_id', $id)->get()->toArray();
        // dump($this->progressOrders);

        $this->getProcessMaxInput();
    }

    private function getProcessMaxInput(){
        $rstPSO = collect($this->processSequenceOrders)->toArray();
        $rstPO = collect($this->progressOrders)->toArray();

        $rstMatch = array_filter($rstPSO, fn($item) => in_array($item['process_sequence_id'], array_column($rstPO, 'process_sequence_id')));
        $rstMatch = collect($rstMatch)->map(function($item){
            $ps = ProcessSequence::find($item['process_sequence_id']);
            $po = ProgressOrder::where('order_id', $this->orderId)->where('process_sequence_id', $item['process_sequence_id'])->first();
            return [
                'process_id' => $item['process_sequence_id'],
                'title' => $ps->process_name,
                'description' => $po->qty,
                'date' => Carbon::parse($po->updated_at, 'Asia/Jakarta')->toDateString(),
                'qty_input' => $po->qty,
                'max_input' => $this->selectedOrder->quantity - $po->qty,
                'color' => $this->selectedOrder->quantity == $po->qty ? 'green' :'yellow',
                'icon' => $this->selectedOrder->quantity == $po->qty ? 'check' : 'arrow-path'
            ];
        })->toArray();

        $rstNotMatch = array_filter($rstPSO, fn($item) => !in_array($item['process_sequence_id'], array_column($rstPO, 'process_sequence_id')));
        $rstNotMatch = collect($rstNotMatch)->map(function($item){
            return [
                'process_id' => $item['process_sequence_id'],
                'title' => $item['process_name'],
                'description' => 0,
                'date' => "0000-00-00",
                'qty_input' => 0,
                'max_input' => $this->selectedOrder->quantity,
                'color' => 'yellow',
                'icon' => 'arrow-path'
            ];
        })->toArray();

        $arrMerge = array_merge($rstMatch, $rstNotMatch);

        $this->maxInputProcess = $arrMerge;
    }

    public function checkPreviousProcess(){
        $this->qtyInputPrevProcess = 0;
        $this->qtyInput = 0;
        if($this->selectedProcessSequenceOrder > 1){
            $prevProcess = $this->selectedProcessSequenceOrder - 1;
            $findIdx = array_search($prevProcess, array_column($this->maxInputProcess, 'process_id'));
            $this->qtyInputPrevProcess = $this->maxInputProcess[$findIdx]['qty_input'] - $this->maxInputProcess[$findIdx+1]['qty_input'];

            if($this->qtyInputPrevProcess == 0){
                $this->dialog()
                ->warning('Warning', 'The previous process (' . $this->maxInputProcess[$findIdx]['title'] . ') not inputed yet at all.' )
                ->send();
            }else{
                $qtyInputSelectedProcess = $this->maxInputProcess[$findIdx+1]['qty_input'];
                if($qtyInputSelectedProcess == $this->selectedOrder->quantity){
                    $this->dialog()
                    ->info('Info', 'The selected process (' . $this->maxInputProcess[$findIdx+1]['title'] . ') has been done or full.' )
                    ->send();
                }
            }
        }else{
            $this->qtyInputPrevProcess = $this->maxInputProcess[0]['max_input'];
        }
    }

    public function saveAndExit(){
        $this->validate();

        $idx = array_search($this->selectedProcessSequenceOrder, array_column($this->maxInputProcess, 'process_id'));
        $input = $this->maxInputProcess[$idx]['qty_input'];
        $psoId = $this->maxInputProcess[$idx]['process_id'];
        if($input == 0){
            // insert new progress order
            $status = ProgressOrder::create([
                'order_id' => $this->orderId,
                'process_sequence_id' => $psoId,
                'qty' => $this->qtyInput
            ]);
        }else{
            // update
            $status = ProgressOrder::where('order_id', $this->orderId)
                                                   ->where('process_sequence_id', $psoId)
                                                   ->increment('qty', $this->qtyInput);
        }

        if($status){
            $this->toast()
            ->success('Success', 'Progress order updated successfully')
            ->timeout(1)
            ->position('top-center')
            ->hook([
                'timeout' => [
                    'method' => 'exit'
                ]
            ])->send();
        }
    }

    public function close(){
        $this->exit();
    }

    public function exit(){
        $this->redirect('/progress_order', navigate: true);
    }

    public function placeholder(): string{
        return <<<'HTML'
        <div>
            <x-card skeleton="4" />
        </div>
        HTML;
    }

};
?>

<div class="flex justify-center">
    <div class="max-w-3/4 mb-4 flex flex-col gap-4">
        <x-breadcrumbs separator="icon:chevron-right" :items="[
            ['label' => 'Home', 'link' => '/', 'icon' => 'home'],
            ['label' => 'Transaction', 'icon' => 'arrow-path-rounded-square'],
            ['label' => 'Progress order', 'link' => '/progress_order'],
            ['label' => 'Edit Progress Order'],
        ]" />


        <x-card minimize color="violet" accent>
            <x-slot:header class="flex justify-between gap-2">
                <x-icon name="list-bullet" lg />
                <h1 class="text-2xl font-bold">Edit Progress Order</h1>
            </x-slot:header>

            <div class="flex grow gap-3 mb-5">
                <div class="flex flex-1 flex-col gap-4" class="mb-4">
                    <x-input value="{{ $this->customer->name }}" label="Customer" hint="Customer Name" disabled />
                    <x-input value="{{ $this->selectedOrder->product_name }}" label="Product" hint="Product name" disabled />
                </div>
                <div class="flex flex-1 flex-col gap-4" class="mb-4">
                    <x-number value="{{ $this->selectedOrder->quantity }}" label="Quantity" hint="Quantity product" disabled />
                    <x-date format="YYYY-MM-DD" wire:model="deadline" label="Deadline" hint="Product finish deadline" readonly />
                </div>
            </div>

            <div class="my-8">
                <x-timeline horizontal :items="$maxInputProcess" />
            </div>

            <div class="flex grow gap-4 items-center mb-3">
                <div class="flex-1">
                    <x-select.styled
                        :options="$this->processSequenceOrders"
                        select="label:process_name|value:process_sequence_id"
                        wire:model.live="selectedProcessSequenceOrder"
                        :change="$this->checkPreviousProcess()"
                        label="Process"
                        hint="Please select a process"
                        placeholder="Please select a process"
                        searchable
                    />
                </div>
                <x-number
                    wire:model="qtyInput"
                    label="Qty Input"
                    hint="Input Qty"
                    min="0"
                    :max="$this->qtyInputPrevProcess"
                />

            </div>

            <x-errors />

            <x-slot:footer end>
                <x-button text="Save" icon="plus" color="pink" wire:click="saveAndExit" loading="saveAndExit" spinner="bars" />
                <x-button text="Cancel" color="olive" wire:click="cancel" loading="cancel" spinner="bars" />
                <x-button text="Exit" color="mauve" wire:click="close" loading="close" spinner="bars" />
            </x-slot:footer>
        </x-card>
    </div>
</div>
