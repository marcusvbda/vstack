<?php

namespace marcusvbda\vstack\Exports;

use Maatwebsite\Excel\Concerns\{
	FromQuery,
	WithHeadings,
	WithMapping,
	ShouldAutoSize
};

class GlobalExporter implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
	public function __construct($resource, $columns, $ids)
	{
		ini_set('memory_limit', '-1');
		set_time_limit(-1);
		$this->columns = $columns;
		$this->ids = $ids;
		$this->resource = $resource;
	}

	public function query()
	{
		return $this->resource->model->whereIn("id", $this->ids)->orderBy("id", "desc");
	}

	public function headings(): array
	{
		return (array_filter(array_map(function ($key) {
			if ($this->columns[$key]["enabled"]) return $this->columns[$key]["label"];
		}, array_keys($this->columns))));
	}

	public function map($row): array
	{
		$result = (array_filter(array_map(function ($key)  use ($row) {
			return $this->resource->getColumnIndex($this->resource->export_columns(),$row,$key);
		}, array_keys($this->columns))));
		return $result;
	}
}
