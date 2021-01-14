<?php

namespace marcusvbda\vstack\Commands;

use Illuminate\Console\Command;
use marcusvbda\vstack\Models\ResourceConfig;
use Storage;
use File;

class clearResourceExport extends Command
{
	protected $signature = 'vstack:clear-resource-export';

	public function __construct()
	{
		parent::__construct();
	}

	public function handle()
	{
		$old_reports = ResourceConfig::where("config", "like", "report_export_%")
			->whereIn("data->status", ["ready", "error"])
			->whereRaw("DATE(json_unquote(json_extract(data,'$.due_date'))) <= DATE(CURDATE())")
			->get();
		foreach ($old_reports as $old_report) {
			$filename = $old_report->data->path . $old_report->data->file_id . '.' . $old_report->data->file_extension;
			$filepath = storage_path('app' . $filename);
			if (File::exists($filepath)) Storage::delete($filename);
		}
		ResourceConfig::whereIn("id", $old_reports->pluck("id"))->delete();
	}
}