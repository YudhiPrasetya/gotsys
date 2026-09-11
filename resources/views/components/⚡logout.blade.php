<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use TallStackUi\Traits\Interactions;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts::dashboard')] class extends Component
{
    use Interactions;

    public function mount(){
        $this->dialog()
        ->question('Confirmation', 'Are you sure want to logout?')
        ->confirm('Yes', 'logout')
        ->cancel('No')
        ->send();
    }

    public function logout(){
        Auth::logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();

        return $this->redirect('/login', navigate: true);
    }
};
?>

<div>
    {{-- @can('Dashboard-access')
        <a href="/" wire:navigate.hover>To Dashboard</a>
    @endcan

    <x-button wire:click="logout" text="Logout" icon="arrow-left-start-on-rectangle" color="zink" light /> --}}
</div>
