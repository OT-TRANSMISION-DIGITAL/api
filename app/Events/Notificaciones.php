<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Notificaciones implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $message;
  public $tecnico_id;

  public function __construct($message, $tecnico_id)
  {
      $this->message = $message;
      $this->tecnico_id = $tecnico_id;
  }

  public function broadcastOn()
  {
      return ['notificaciones'];
  }

  public function broadcastAs()
  {
      return 'notificaciones';
  }
}