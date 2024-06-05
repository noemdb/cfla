<?php

namespace App\Livewire;

use App\Models\app\Educational\DebateCompetition;
use Livewire\Component;
use Livewire\Attributes\On;

class OrderStatusNotification extends Component
{

    public DebateCompetition $order;

    // protected $listeners = ['echo:orders.1.OrderStatusUpdated' => 'notify'];

    #[On('echo:orders')]
    public function notify($data)
    {
        dd($data);
        $this->dispatch('order-status-updated', ['order_id' => $data['order_id'], 'status' => $data['status']]);
    }

    public function render()
    {
        return view('livewire.order-status-notification');
    }    
}
