<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
// use Illuminate\Broadcasting\PresenceChannel;
// use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
// use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\Customer;

class AddCustomer implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Customer $newCustomer;

    /**
     * Create a new event instance.
     */
    public function __construct(Customer $newCustomer)
    {
        $this->newCustomer = $newCustomer;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        // return [
        //     new PrivateChannel('channel-name'),
        // ];

        return [
            new Channel('add-customer')
        ];
    }

    public function broadcastWith(): array
    {
        $this->newCustomer->load('orders');
        return [
            'newCustomer' => $this->newCustomer
        ];
    }

    public function broadcastAs(){
        return 'customer.added';
    }

}
