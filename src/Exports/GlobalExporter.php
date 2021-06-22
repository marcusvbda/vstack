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
use marcusvbda\vstack\Models\ResourceConfig;
use App\User;

class GlobalExporter implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	public $counter = 0;
	public $start = null;
	public $controller = null;
	public $resource_controller = null;
	public $config = null;
	public $context = null;

	public function __construct($resource, $columns, $config_id)
	{
		$this->start = microtime(true);
		ini_set('memory_limit', '-1');
		set_time_limit(-1);
		$this->columns = $columns;
		$this->resource = $resource;
		$this->controller = new VstackController;
		$this->resource_controller = new ResourceController;
		$this->config = ResourceConfig::findOrFail($config_id);
		$this->context = (object)[
			"user" => User::find($this->config->data->user_id)
		];
	}

	public function query()
	{
		return $this->resource->getModelInstance()->whereIn('id', array(DB::raw($this->config->data->raw_sql)))->select("*");
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
		$cx = $this->context;
		$result = (array_filter(array_map(function ($key)  use ($row, $cx) {
			if ($this->columns[$key]["enabled"]) {
				return $this->controller->getColumnIndex($this->resource->export_columns($cx), $row, $key);
			}
		}, array_keys($this->columns))));
		return $result;
	}
}