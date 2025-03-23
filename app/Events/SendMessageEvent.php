<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    private array $arr;
    public $msg;

    
    public function __construct($message,$msg)
    {
        $this->arr = $message;
        $this->msg = $msg;
    }
    public function broadcastOn()
    {
        // return new PrivateChannel('chat');
        return ['tekramapp'];
    }
    // public function broadcastWith()
    // {
    //     return $this->arr;
    //     // return $this->arr;
    // }

    public function broadcastAs()
  {
      return 'my-event';
  }
}
