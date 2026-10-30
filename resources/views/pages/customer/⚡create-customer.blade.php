<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Validate;
use TallStackUi\Traits\Interactions;
use App\Services\CustomerService;
use App\Models\Customer;

use App\Events\CustomerAdded;

new #[Layout('layouts::dashboard')] #[Lazy] class extends Component
{
    private CustomerService $customerService;

    public function boot(CustomerService $customerService){
        $this->customerService = $customerService;
    }

    use Interactions;

    #[Validate('required')]
    public string $customerName = '';

    #[Validate('required')]
    public string $customerAddress = '';

    public string $prefixPhone = '';

    public string $customerPhone = '';

    public function placeholder(): string{
        return <<<'HTML'
        <div>
            <x-card skeleton="3" />
        </div>
        HTML;
    }

    public function saveAndCreate(){
        $this->save();
        // $this->messageSuccess();
        $this->toast()
             ->timeout(2)
             ->position('top-center')
             ->success('Success', 'New customer data added succesfully!')
             ->hook([
                'timeout' => [
                    'method' => 'clearModel'
                ]
             ])
             ->send();
    }

    private function save(){
        $this->validate();

        // Customer::create([
        //     'name' => $this->customerName,
        //     'address' => $this->customerAddress,
        //     'prefix_phone' => $this->prefixPhone,
        //     'phone' => $this->customerPhone
        // ]);
        $newCustomer = [
            'id' => 0,
            'name' => $this->customerName,
            'address' => $this->customerAddress,
            'prefix_phone' => $this->prefixPhone,
            'phone' => $this->customerPhone
        ];
        $updated = $this->customerService->updateCustomer($newCustomer);

        CustomerAdded::dispatch(Customer::count());
        return $updated;
    }

    public function clearModel(){
        $this->customerName = '';
        $this->customerAddress = '';
        $this->prefixPhone = '';
        $this->customerPhone = '';
    }

    public function exit(){
        $this->redirect('/customer', navigate: true);
    }

    public function cancel(){
        $this->clearModel();
    }

    public function close(){
        $this->exit();
    }

    public function saveAndExit(){
        $this->save();
        $this->toast()
             ->position('top-center')
             ->timeout(2)
             ->success('Success', 'New customer data added succesfully!')
             ->hook([
                'timeout' => [
                    'method' => 'exit'
                ]
             ])
             ->send();
    }

};
?>

<div class="flex items-center justify-center">
    <div class="w-2/3 flex flex-col gap-4">
        {{-- <div class="max-w-1/3"> --}}
            <x-breadcrumbs separator="icon:chevron-right" :items="[
                ['label' => 'Home', 'link' => '/', 'icon' => 'home'],
                ['label' => 'Data Master', 'icon' => 'circle-stack'],
                ['label' => 'Create customer', 'link' => '/customer/create']
            ]" />
        {{-- </div> --}}

        <x-card color="indigo" accent minimize>
            <x-slot:header class="flex justify-between gap-2">
                {{-- <div class=""> --}}
                    <x-icon name="user-plus" xl />
                    <h1 class="text-2xl font-bold">Create new customer</h1>
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
                <x-button color="green" wire:click="saveAndCreate" loading="saveAndCreate" spinner="dots" text="Save and create again" />
                <x-button color="purple" wire:click="saveAndExit" loading="saveAndExit" spinner="bars" text="Save and exit" />
                <x-button color="primary" outline wire:click="cancel" loading="cancel" spinner="wave" text="Cancel" />
                <x-button color="zinc" outline wire:click="close" loading="close" spinner="ping" text="Close" />
            </x-slot:footer>
        </x-card>
    </div>
</div>
