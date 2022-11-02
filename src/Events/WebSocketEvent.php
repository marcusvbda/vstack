<?php

namespace marcusvbda\vstack\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebSocketEvent implements ShouldBroadcastNow
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public $data;
	public $broadcastQueue;

	public function __construct(String $channel, String $event, $data)
	{
		$this->broadcastQueue = config('vstack.queue.event-broadcasts', 'event-broadcasts');
		$this->data = $data;
		$this->event = $event;
		$this->channel = $channel;
	}

	public function broadcastOn()
	{
		return new PrivateChannel($this->channel);
	}

	public function broadcastWith()
	{
		return $this->data;
	}

	public function broadcastAs()
	{
		return  $this->event;
	}
}