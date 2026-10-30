<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use TallStackUi\Traits\Interactions;
use App\Services\CustomerService;
use App\Models\Customer;

new #[Layout('layouts::dashboard')] class extends Component
{
    private CustomerService $customerService;

    public function boot(CustomerService $customerService){
        $this->customerService = $customerService;
    }

    use Interactions;

    public Customer $customer;
    public int $customerId = 0;

    #[Validate('required')]
    public string $customerName = '';

    #[Validate('required')]
    public string $customerAddress = '';

    public string $prefixPhone = '';

    public string $customerPhone = '';

    public function mount($id){
        // $this->customer = Customer::find($id);
        $this->customer = $this->customerService->getCustomer($id);

        $this->customerId = $id;
        $this->customerName = $this->customer->name;
        $this->customerAddress = $this->customer->address;
        $this->prefixPhone = $this->customer->prefix_phone;
        $this->customerPhone = $this->customer->phone;
    }

    public function saveAndExit(){
        // $updated = $this->customer->update([
        //     'id' => $this->customerId,
        //     'name' => $this->customerName,
        //     'address' => $this->customerAddress,
        //     'prefix_phone' => $this->prefixPhone,
        //     'phone' => $this->customerPhone
        // ]);
        $customer = [
            'id' => $this->customerId,
            'name' => $this->customerName,
            'address' => $this->customerAddress,
            'prefix_phone' => $this->prefixPhone,
            'phone' => $this->customerPhone
        ];
        $updated = $this->customerService->updateCustomer($customer);

        if($updated){
            $this->toast()
            ->timeout(2)
            ->success('Success', 'Data customer updated successfully')
            ->hook([
                'timeout' => [
                    'method' => 'exit'
                ]
            ])
            ->send();
        }
    }

    public function exit(){
        $this->redirect('/customer', navigate: true);
    }

    public function close(){
        $this->exit();
    }
};
?>

<div class="flex items-center justify-center">
    <div class="w-2/3 flex flex-col gap-4">
        {{-- <div class="max-w-1/3"> --}}
            <x-breadcrumbs separator="icon:chevron-right" :items="[
                ['label' => 'Home', 'link' => '/', 'icon' => 'home'],
                ['label' => 'Data Master', 'icon' => 'circle-stack'],
                ['label' => 'Edit customer']
            ]" />
        {{-- </div> --}}

        <x-card color="purple" accent minimize>
            <x-slot:header class="flex justify-between gap-2">
                {{-- <div class=""> --}}
                    <x-icon name="pencil-square" xl />
                    <h1 class="text-2xl font-bold">Edit customer</h1>
                {{-- </div> --}}
            </x-slot:header>
            <div class="flex flex-col gap-6">
                <x-input wire:model="customerName" label="Customer Name" hint="Input customer name" />
                <x-textarea label="Customer Address *" hint="Input cutomer address" wire:model="customerAddress" />
                <x-input.select label="Customer Cellphone" hint="Input customer cellphone" wire:model="customerPhone" icon="phone" clearable>
                    <x-slot:left>
                        <x-select.styled placeholder="select prefix phone number" :options="['+02', '+62']" wire:model.live="prefixPhone" />
                    </x-slot:left>
                </x-input.select>
            </div>
            <x-slot:footer>
                <x-button color="purple" wire:click="saveAndExit" loading="saveAndExit" spinner="bars" text="Save and exit" />
                <x-button color="zinc" outline wire:click="close" loading="close" spinner="ping" text="Close" />
            </x-slot:footer>
        </x-card>
    </div>
</div>
