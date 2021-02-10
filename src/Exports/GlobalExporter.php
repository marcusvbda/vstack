<?php

namespace marcusvbda\vstack\Exports;

use Maatwebsite\Excel\Concerns\{
	FromCollection,
	WithHeadings,
	WithMapping,
	ShouldAutoSize
};
use  marcusvbda\vstack\Controllers\VstackController;

class GlobalExporter implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
	// public $counter = 0;
	// public $start = null;
	public $controller = null;
	public function __construct($resource, $columns, $ids)
	{
		// $this->start = microtime(true);
		// echo "__construct " . PHP_EOL;
		ini_set('memory_limit', '-1');
		set_time_limit(-1);
		$this->columns = $columns;
		$this->ids = $ids;
		$this->resource = $resource;
		$this->controller = new VstackController;
	}

	public function collection()
	{
		// echo "collection " . PHP_EOL;
		return $this->resource->model->whereIn("id", $this->ids)->orderBy("id", "desc")->cursor();
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