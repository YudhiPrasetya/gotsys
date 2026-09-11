<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Validate;

use TallStackUi\Traits\Interactions;
use App\Models\Customer;
use App\Models\Order;

use Carbon\Carbon;

new #[Layout('layouts::dashboard')] #[Lazy] class extends Component
{
    use Interactions;

    public $order;

    public int $idOrder = 0;

    #[Validate('required')]
    public int $selectedCustomer;

    #[Validate('required')]
    public string $product = '';

    #[Validate('required')]
    public int $qty;

    #[Validate('required')]
    public string $deadline = '';

    public $customers;

    public function mount(int $id){
        // find selected order
        $this->idOrder = $id;
        $this->order = Order::find($id);
        $this->selectedCustomer = $this->order->customer_id;
        $this->product = $this->order->product_name;
        $this->qty = $this->order->quantity;
        $this->deadline = $this->order->deadline;

        // get all customers
        $this->customers = Customer::all();
    }

    public function placeholder(): string{
        return <<<'HTML'
        <div>
            <x-card skeleton="4" />
        </div>
        HTML;
    }

    public function saveAndExit(){
        $this->save();
    }

    private function save(){
        $this->validate();

        $updated = $this->order->update([
            'id' => $this->idOrder,
            'customer_id' => $this->selectedCustomer,
            'product_name' => $this->product,
            'quantity' => $this->qty,
            'deadline' => $this->deadline,
            'updated_at' => Carbon::now('Asia/Jakarta')
        ]);
        if($updated){
            $this->toast()
            ->timeout(2)
            ->success('Success', 'Data order updated successfully')
            ->hook([
                'timeout' => [
                    'method' => 'exit'
                ]
            ])
            ->send();
        }
    }

    public function exit(){
        $this->redirect('/order', navigate: true);
    }

    public function close(){
        $this->exit();
    }

};
?>

<div class="flex items-center justify-center">
    <div class="w-2/3 flex flex-col gap-4">
        <x-breadcrumbs lg separator="icon:chevron-right" :items="[
            ['label' => 'Home', 'link' => '/', 'icon' => 'home'],
            ['label' => 'Data Master', 'icon' => 'circle-stack'],
            ['label' => 'Edit order']
        ]" />

        <x-card color="indigo" accent minimize>
            <x-slot:header class="flex justify-between gap-2">
                <x-icon name="pencil-square" xl />
                <h1 class="text-2xl font-bold">Edit order</h1>
            </x-slot:header>

                <div class="flex flex-col gap-6" class="mb-4">
                    <x-select.styled :options="$customers" select="label:name|value:id" wire:model="selectedCustomer" label="Customer" hint="Select a customer" placeholder="Select a customer" searchable />
                    <x-input wire:model="product" label="Product" hint="Input product name" />
                    <x-number wire:model="qty" label="Quantity" hint="Quantity product" />
                    <x-date wire:model="deadline" label="Deadline" hint="Product finish deadline" />

                    <x-errors title="Ups! There are :count validation errors:" color="orange" />
                </div>

                <x-slot:footer>
                    <x-button color="purple" wire:click="saveAndExit" loading="saveAndExit" spinner="bars" text="Save and exit" />
                    <x-button color="zinc" outline wire:click="close" loading="close" spinner="ping" text="Close" />

            </x-slot:footer>
        </x-card>
    </div>
</div>
