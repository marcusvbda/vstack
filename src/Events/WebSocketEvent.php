<?php

namespace marcusvbda\vstack\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebSocketEvent implements ShouldBroadcast
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public $channel;
	public $data;
	public $namespace;
	public $broadcastQueue;

	public function __construct($channel, $namespace, $data)
	{
		$this->broadcastQueue = config('vstack.queue.event-broadcasts', 'event-broadcasts');
		$this->data = $data;
		$this->namespace = $namespace;
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
		return  $this->namespace;
	}
}