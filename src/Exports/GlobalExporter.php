<?php

namespace marcusvbda\vstack\Exports;

use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping
};

class GlobalExporter implements FromCollection, WithHeadings, WithMapping
{
    public function __construct($resource, $columns, $data = [])
    {
        ini_set('memory_limit', '-1');
        set_time_limit(-1);
        $this->columns = $columns;
        $this->data = $data;
        $this->resource = $resource;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return (array_filter(array_map(function ($key) {
            if ($this->columns[$key]["enabled"]) return $this->columns[$key]["label"];
        }, array_keys($this->columns))));
    }

    public function map($row): array
    {
        $resource_columns = $this->resource->export_columns();
        $result = (array_filter(array_map(function ($key)  use ($row, $resource_columns) {
            if ($this->columns[$key]["enabled"]) {
                if (!@$resource_columns[$key]["handle"]) return $row->{$key};
                return $resource_columns[$key]["handle"]($row);
            }
        }, array_keys($this->columns))));
        return $result;
    }
}
