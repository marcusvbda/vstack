<?php

namespace marcusvbda\vstack\Jobs;

use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use marcusvbda\vstack\Events\WebSocketEvent;
use marcusvbda\vstack\Models\ResourceConfig;
use marcusvbda\vstack\Services\SendMail;
use marcusvbda\vstack\Vstack;

class GlobalJob implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	private $config = null;
	private $resource = null;
	private $file_id = null;
	public function __construct($config_id, $resource, $file_id)
	{
		$this->queue = Vstack::queue_resource_export();
		$this->config = ResourceConfig::findOrFail($config_id);
		$this->resource = $resource;
		$this->file_id = $file_id;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$user = User::findOrFail($this->config->data->user_id);
		$route = route('resource.export_download_intercept', ['resource' => $this->resource->id, 'file' => $this->file_id]);
		$_data = $this->config->data;
		$_data->status = "ready";
		$_data->microtime->end = microtime(true);
		$_data->due_date = Carbon::now()->addDays(1);
		$this->config->data = $_data;
		$this->config->save();
		$resource = $this->resource;
		$html = view($resource->exportNotificationView(), compact('user', 'resource', 'route'))->render();
		broadcast(new WebSocketEvent("App.User." . $this->config->data->user_id, "notifications.exporting_status." . $this->config->id, [
			"config" => $this->config
		]));
		SendMail::to($user->email, "Relatório de " . $resource->label(), $html);
	}
}