<?php

namespace marcusvbda\vstack\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\{
	FromCollection,
	WithHeadings,
	WithMapping,
	ShouldAutoSize
};
use  marcusvbda\vstack\Controllers\VstackController;
use  marcusvbda\vstack\Controllers\ResourceController;

class GlobalExporter implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
	// public $counter = 0;
	// public $start = null;
	public $controller = null;
	public $resource_controller = null;
	public function __construct($resource, $columns, $filter)
	{
		// $this->start = microtime(true);
		// echo "__construct " . PHP_EOL;
		ini_set('memory_limit', '-1');
		set_time_limit(-1);
		$this->columns = $columns;
		$this->filter = $filter;
		$this->resource = $resource;
		$this->controller = new VstackController;
		$this->resource_controller = new ResourceController;
	}

	public function collection()
	{
		$request = new Request();
		$request->merge($this->filter);
		return ($this->resource_controller->getData($this->resource, $request))->orderBy("id", "desc")->select("*")->cursor();
	}

	public function headings(): array
	{
		// echo "headings " . PHP_EOL;
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
		// echo "map " . ++$this->counter . "  " . (microtime(true) - $this->start) . PHP_EOL;
		return $result;
	}
}