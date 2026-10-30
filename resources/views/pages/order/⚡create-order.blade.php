<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Validate;
use TallStackUi\Traits\Interactions;

use App\Services\CustomerService;
use App\Services\OrderService;
use App\Services\ProcessSequenceOrderService;

use App\Models\Customer;
use App\Models\Order;

use Carbon\Carbon;

use App\Events\OrderAdded;
use App\Events\OrderPendingAdded;

new #[Layout('layouts::dashboard')] #[Lazy] class extends Component
{
    private CustomerService $customerService;
    private OrderService $orderService;
    private ProcessSequenceOrderService $processSequenceOrderService;

    public function boot(CustomerService $customerService, OrderService $orderService, ProcessSequenceOrderService $processSequenceOrderService){
        $this->customerService = $customerService;
        $this->orderService = $orderService;
        $this->processSequenceOrderService = $processSequenceOrderService;
    }

    use Interactions;

    #[Validate('required')]
    public int $selectedCustomer;

    #[Validate('required')]
    public string $product = '';

    #[Validate('required')]
    public int $qty;

    #[Validate('required')]
    public string $deadline = '';

    public $customers;

    public int $orderId;

    public function mount(){
        // $this->customers = Customer::select(['id', 'name'])->get();
        $this->customers = $this->customerService->getCustomerForSelect();
    }

    public function placeholder(): string{
        return <<<'HTML'
        <div>
            <x-card skeleton="4" />
        </div>
        HTML;
    }

    public function saveAndCreate(){
        if($this->save()){
            $this->toast()
                ->timeout(2)
                ->position('top-center')
                ->success('Success', 'New order added succesfully!')
                ->hook([
                'timeout' => [
                    'method' => 'clearModel'
                ]
            ])
            ->send();
        };
    }

    private function save(){
        $this->validate();

        // $this->orderId = Order::insertGetId([
        //     'customer_id' => $this->selectedCustomer,
        //     'product_name' => $this->product,
        //     'quantity' => $this->qty,
        //     'deadline' => $this->deadline,
        //     'created_at' => Carbon::now('Asia/Jakarta'),
        //     'updated_at' => Carbon::now('Asia/Jakarta')
        // ]);

        $newOrder = [
            'customer_id' => $this->selectedCustomer,
            'product_name' => $this->product,
            'quantity' => $this->qty,
            'deadline' => $this->deadline,
            'created_at' => Carbon::now('Asia/Jakarta'),
            'updated_at' => Carbon::now('Asia/Jakarta')
        ];

        // Insert new order
        $insertedOrderId = $this->orderService->addNewOrder($newOrder);
        // dump($insertedOrder);

        $this->orderId = $insertedOrderId;
        $updated = $this->processSequenceOrderService->addNewProcessSequenceOrder($this->orderId);

        OrderAdded::dispatch(Order::count());
        OrderPendingAdded::dispatch(Order::where('status_process', 'pending')->count());

        return $updated;

    }

    public function clearModel(){
        $this->selectedCustomer = 0;
        $this->product = '';
        $this->qty = 0;
        $this->deadline = '';
    }

    public function exit(){
        $this->redirect('/order', navigate: true);
    }

    public function cancel(){
        $this->clearModel();
    }

    public function close(){
        $this->exit();
    }

    public function saveAndExit(){
        if($this->save()){
            $this->dialog()
            ->question('Confirmation', 'New order added successfully. Do you want to edit the process sequence order?')
            ->confirm('Yes', 'redirectToProcessSequenceOrder', $this->orderId)
            ->cancel('No', 'exit')
            ->send();
        };

    }

    public function redirectToProcessSequenceOrder(int $id){
        $this->redirectRoute('edit-ProcessSequenceOrder', $id);
    }

};
?>

<div class="flex items-center justify-center">
    <div class="w-2/3 flex flex-col gap-4">
        <x-breadcrumbs separator="icon:chevron-right" :items="[
            ['label' => 'Home', 'link' => '/', 'icon' => 'home'],
            ['label' => 'Data Master', 'icon' => 'circle-stack'],
            ['label' => 'Create order', 'link' => '/order']
        ]" />

        <x-card color="indigo" accent minimize loading="saveAndCreate" delay="longer">
            <x-slot:header class="flex justify-between gap-2">
                <x-icon name="user-plus" xl />
                <h1 class="text-2xl font-bold">Create new order</h1>
            </x-slot:header>

                <div class="flex flex-col gap-6" class="mb-4">
                    <x-select.styled :options="$customers" select="label:name|value:id" wire:model="selectedCustomer" label="Customer" hint="Select a customer" placeholder="Select a customer" searchable />
                    <x-input wire:model="product" label="Product" hint="Input product name" />
                    <x-number wire:model="qty" label="Quantity" hint="Quantity product" />
                    <x-date wire:model="deadline" label="Deadline" hint="Product finish deadline" />
                    <x-errors title="Ups! There are :count validation errors:" color="orange" />
                </div>

                <x-slot:footer>
                    <x-button color="green" wire:click="saveAndCreate" loading="saveAndCreate" spinner="dots" text="Save and create again" />
                    <x-button color="purple" wire:click="saveAndExit" loading="saveAndExit" spinner="bars" text="Save and exit" />
                    <x-button color="primary" outline wire:click="cancel" loading="cancel" spinner="wave" text="Cancel" />
                    <x-button color="zinc" outline wire:click="close" loading="close" spinner="ping" text="Close" />

            </x-slot:footer>
        </x-card>
    </div>
</div>
