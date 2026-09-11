<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Validate;
use TallStackUi\Traits\Interactions;

use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use App\Models\User;

new #[Layout('layouts::dashboard')] #[Lazy] class extends Component
{
    use Interactions;

    #[Validate('required|unique:users,name')]
    public string $name = '';

    #[Validate('required|unique:users,username')]
    public string $userName = '';

    #[Validate('required|email|unique:users,email')]
    public string $email = '';

    #[Validate('required|min:5')]
    public string $password = '';

    #[Validate('required|same:password')]
    public string $passwordConfirmation = '';

    #[Validate('required')]
    public $selectedRole = '';

    public $roles;

    public function mount(){
        $this->getRoles();
    }

    public function getRoles(){
        $this->roles = Role::orderBy('id')->get();
    }

    public function saveAndExit(){
        if($this->save()){
            $this->toast()
            ->position('top-center')
            ->timeout(1)
            ->success('Success', 'New user added successfully')
            ->hook([
                'timeout' => [
                    'method' => 'exit'
                ]
            ])->send();
        };
    }

    public function exit(){
        $this->redirect('/users', navigate: true);
    }

    public function close(){
        $this->exit();
    }

    private function save(){
        try{
            $this->validate();
            $newUser = User::create([
                'name' => $this->name,
                'username' => $this->userName,
                'email' => $this->email,
                'email_verified_at' => Carbon::now('Asia/Jakarta'),
                'password' => bcrypt($this->password)
            ]);
            if($newUser){
                $newUser->assignRole($this->selectedRole);
            }
            return true;
        }catch(\Exception $err){
            throw $err;
        }

    }

    public function placeholder(): string{
        return <<<'HTML'
        <div>
            <x-card skeleton="4" header="Create new user" footer rounded="xl" />
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
            ['label' => 'Create User', 'link' => '/user/create']
        ]" />

        <x-card color="primary" accent minimize>
            <x-slot:header class="flex justify-between gap-2">
                <x-icon name="user-plus" xl />
                <h1 class="text-2xl font-bold">Create new user</h1>
            </x-slot:header>

            <div class="flex grow gap-3">
                <div class="flex flex-1 flex-col gap-3">
                    <x-input label="Name *" icon="users" wire:model="name" hint="Input your name" />
                    <x-input label="User Name *" icon="user-circle" wire:model="userName" hint="Input your user name" />
                    <x-input email label="Email *" icon="envelope" wire:model="email" hint="Input your email" />
                </div>

                <div class="flex flex-1 flex-col gap-3">
                    <x-password label="Password *" icon="lock-closed" wire:model="password" hint="Input your best password" />
                    <x-password label="Confirm Password *" icon="lock-closed" wire:model="passwordConfirmation" hint="Confirm your password" />
                    <x-select.styled :options="$roles" select="label:name|value:id" wire:model="selectedRole" label="Role" hint="Select a role" placeholder="Select a role" searchable />
                </div>

            </div>

            <div class="mt-3">
                <x-errors list-numeric close color="pink" />
            </div>

            <x-slot:footer>
                <x-button md icon="user-plus" color="purple" wire:click="saveAndExit" loading="saveAndExit" spinner="bars" text="Save and exit" />
                <x-button md icon="arrow-left-start-on-rectangle" color="zinc" outline wire:click="close" loading="close" spinner="ping" text="Close" />
            </x-slot:footer>
        </x-card>
    </div>
</div>
