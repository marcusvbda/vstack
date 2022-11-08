<?php

namespace marcusvbda\vstack\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebSocketEvent implements ShouldBroadcastNow
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public $data;
	public $broadcastQueue;
	public $private;

	public function __construct(String $channel, String $event, $data,$private = true)
	{
		$this->broadcastQueue = config('vstack.queue.event-broadcasts', 'event-broadcasts');
		$this->data = $data;
		$this->event = $event;
		$this->channel = $channel;
		$this->private = $private;
	}

	public function broadcastOn()
	{
		return $this->private ? new PrivateChannel($this->channel) : new Channel($this->channel);
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