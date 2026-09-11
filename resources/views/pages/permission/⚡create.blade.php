<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Validate;

use TallStackUi\Traits\Interactions;
use Spatie\Permission\Models\Permission;

new #[Layout('layouts::dashboard')] #[Lazy] class extends Component
{
    use Interactions;

    #[Validate('required')]
    public string $name ='';

    public bool $permissionModal = false;

    public array $notSavedPermissions = [];
    // public int $listId = 0;

    public function placeholder(): string{
        return <<<'HTML'
        <div>
            <x-card skeleton="3" header="Add new permission" footer image round="xl" />
        </div>
        HTML;
    }

    public function addPermission(){
        $this->notSavedPermissions[] = [ 'name' => $this->name];
        $this->name = '';
    }

    public function deleteListItem(string $name){
        $this->notSavedPermissions = array_filter($this->notSavedPermissions, function($subArray) use($name){
            return $subArray['name'] !== $name;
        });
    }

    public function saveAndExit(){
        if($this->save()){
            $this->toast()
            ->timeout(1)
            ->success('Success', 'All new permissions data has been added successfullly')
            ->position('top-center')
            ->hook([
                'timeout' => [
                    'method' => 'exit'
                ]
            ])->send();
        }
    }

    public function exit(){
        $this->redirect('/permission', navigate: true);
    }

    private function save(){
        $data = array_column($this->notSavedPermissions, 'name');
        try{
            foreach($data as $d){
                Permission::firstOrCreate(['name' => $d]);
            }
            return true;
        }catch(\Exception $e){
            throw $e;
            return false;
        }
    }

    public function close(){
        $this->exit();
    }

    public function cancel(){
        $this->notSavedPermissions = [];
    }

};
?>

<div>
    <div class="w-1/3 flex flex-col gap-4">
        <x-breadcrumbs separator="icon:chevron-right" :items="[
            ['label' => 'Home', 'link' => '/', 'icon' => 'home'],
            ['label' => 'Data Master', 'icon' => 'circle-stack'],
            ['label' => 'Create permission', 'link' => '/permission/create']
        ]" />

        <x-card color="lime" accent minimize>
            <x-slot:header class="flex justify-between gap-2">
                <x-icon name="user-plus" xl />
                <h1 class="text-2xl font-bold">Create new permission</h1>
            </x-slot:header>
            <div class="flex flex-col gap-3">
                <x-list label="Permissions are not saved yet" wire:model.live="notSavedPermissions" :items="$notSavedPermissions">
                    @interact('item_action', $item)
                        <x-button sm color="red" icon="trash" wire:click="deleteListItem('{{ $item['name'] }}')" />
                    @endinteract
                </x-list>
                <div class="flex gap-1 items-end">
                    <div class="grow">
                        <x-button block sm class="flex flex-0" md icon="plus" color="blue" light text="Add permission" wire:click="$toggle('permissionModal')" />
                    </div>
                    <div class="flex-none">
                        <x-tooltip position="right" text="For begin, click the 'Add permission' button" icon="information-circle" />
                    </div>
                </div>
            </div>
            <x-slot:footer>
                <x-button sm color="purple" wire:click="saveAndExit" loading="saveAndExit" spinner="bars" text="Save and exit" :disabled="empty($notSavedPermissions)" />
                <x-button sm color="primary" outline wire:click="cancel" loading="cancel" spinner="wave" text="Cancel" :disabled="empty($notSavedPermissions)" />
                <x-button sm color="zinc" outline wire:click="close" loading="close" spinner="ping" text="Close" />
            </x-slot:footer>
        </x-card>
    </div>

    <x-modal sm title="Add new permission" center persistent wire="permissionModal">
        <div class="flex gap-2 items-end">
            <div class="grow">
                <x-input label="Permission Name" wire:model.live="name" />
            </div>
            <div class="flex-none">
                <x-button sm icon="plus" text="Add" :disabled="empty($name)" wire:click="addPermission"/>
            </div>
        </div>
    </x-modal>
</div>
