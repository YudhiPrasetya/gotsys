<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Validate;
use TallStackUi\Traits\Interactions;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

new #[Layout('layouts::dashboard')] #[Lazy] class extends Component
{
    use Interactions;

    public $permissions;
    public $groupedPermission = [];

    #[Validate('required')]
    public ?string $name = '';

    #[Validate('required')]
    public array $roles = [];
    // public array $allOptions = [];

    public bool $selectAllRoles = false;

    public function mount(){
        $this->getPermissions();

    }

    public function getPermissions(){
        $this->permissions = Permission::orderBy('id')->get();

        foreach($this->permissions as $p){
            $parts = explode('-', $p->name);
            $prefix = $parts[0];
            $value = $p->name;

            $labelAndValue = [
                'label' => $value,
                'value' => $value,
            ];
            $this->groupedPermission[$prefix][] = $labelAndValue;
            // $this->allOptions[] = $labelAndValue;
        }

        // foreach($this->groupedPermission as $gp){
        //     dump($gp);
        // }

    }

    public function updatedSelectAllRoles($value){
        $this->roles = $value ? $this->groupedPermission : [];
    }

    public function updatedRoles(){
        // dump($this->roles);
        $this->selectAllRoles = count($this->roles) === count($this->groupedPermission);
    }

    public function saveAndExit(){
        if($this->save()){
            $this->toast()
            ->position('top-center')
            ->timeout(1)
            ->success('Success', 'Role and permissions added successfully!')
            ->hook([
                'timeout' => [
                    'method' => 'exit'
                ]
            ])->send();
        }

        // $this->save();
    }

    public function exit(){
        $this->redirect('/roles', navigate: true);
    }

    private function save(){
        $this->validate();

        Role::create(['name' => $this->name]);
        $role = Role::findByName($this->name);

        $role->givePermissionTo($this->roles);

        return true;
    }

    public function cancel(){
        $this->selectAllRoles = false;
        $this->roles = [];
    }

    public function placeholder(): string{
        return <<<'HTML'
        <div>
            <x-card skeleton="3" header="Add new permission" footer image round="xl" />
        </div>
        HTML;
    }
};
?>

<div class="flex items-center justify-center">
    <div class="w-2/3 flex flex-col gap-4">
        <x-breadcrumbs separator="icon:chevron-right" :items="[
            ['label' => 'Home', 'link' => '/', 'icon' => 'home'],
            ['label' => 'Settings', 'icon' => 'cog-6-tooth'],
            ['label' => 'Create Role', 'link' => '/roles/create']
        ]" />

        <x-card color="lime" accent minimize>
            <x-slot:header class="flex justify-between gap-2">
                <x-icon name="user-plus" xl />
                <h1 class="text-2xl font-bold">Create new role</h1>
            </x-slot:header>
            <div class="flex flex-col gap-3">
                <div class="w-max-1/2">
                    <x-input label="Name *" wire:model="name" hint="Input role name" />
                </div>

                <div class="flex gap-2 items-center justify-items-center">
                    <hr class=" flex-1 border-t-gray-600 border-b-gray-700 w-50"></hr>
                    <p class="text-lg">Select Roles</p>
                    <hr class="flex-1 border-t-gray-600 border-b-gray-700 w-50" />
                </div>
                <div class="flex flex-col gap-3">
                    <div class="my-3">
                        <x-checkbox wire:model.live="selectAllRoles" label="Select All Roles" color="green" />
                    </div>

                    @foreach($this->groupedPermission as $p)
                        @php
                            $part = explode('-', $p[0]['label']);
                            $prefix = $part[0];
                        @endphp
                        <div class="mb-3">
                            <x-checkbox.group label="{{ $prefix }}" card :columns="3" :options="$p" color="green" wire:model.live="roles" />
                        </div>
                    @endforeach
                </div>
            </div>
            <x-slot:footer>
                <x-button sm color="purple" wire:click="saveAndExit" loading="saveAndExit" spinner="bars" text="Save and exit" :disabled="empty($roles)" />
                <x-button sm color="primary" outline wire:click="cancel" loading="cancel" spinner="wave" text="Cancel" :disabled="empty($roles)" />
                <x-button sm color="zinc" outline wire:click="close" loading="close" spinner="ping" text="Close" />
            </x-slot:footer>
        </x-card>
    </div>
</div>
