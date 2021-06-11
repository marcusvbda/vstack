<?php

namespace marcusvbda\vstack\Exports;

use Maatwebsite\Excel\Concerns\{
	FromQuery,
	WithHeadings,
	WithMapping,
	ShouldAutoSize
};
use  marcusvbda\vstack\Controllers\VstackController;
use  marcusvbda\vstack\Controllers\ResourceController;
use DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Bus\Queueable;
use marcusvbda\vstack\Models\ResourceConfig;

class GlobalExporterQueued implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, ShouldQueue
{
	use Exportable, Queueable;
	public $counter = 0;
	public $start = null;
	public $controller = null;
	public $resource_controller = null;
	public $config_id = null;

	public function __construct($resource, $columns, $config_id)
	{
		$this->start = microtime(true);
		ini_set('memory_limit', '-1');
		set_time_limit(-1);
		$this->columns = $columns;
		$this->resource = $resource;
		$this->controller = new VstackController;
		$this->resource_controller = new ResourceController;
		$this->config_id = $config_id;
	}

	public function query()
	{
		$config = ResourceConfig::findOrFail($this->config_id);
		return $this->resource->getModelInstance()->whereIn('id', array(DB::raw($config->data->raw_sql)))->select("*");
	}

	public function headings(): array
	{
		$this->filtered_columns =  (array_filter(array_map(function ($key) {
			if ($this->columns[$key]["enabled"]) return $this->columns[$key]["label"];
		}, array_keys($this->columns))));
		return $this->filtered_columns;
	}

	public function map($row): array
	{
		$result = [];
		$result = (array_filter(array_map(function ($key)  use ($row) {
			if ($this->columns[$key]["enabled"])  return $this->controller->getColumnIndex($this->resource->export_columns(), $row, $key);
		}, array_keys($this->columns))));
		return $result;
	}
}