<?php

namespace marcusvbda\vstack\Exports;

use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
};

class GlobalExporter implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
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
        $placeholder = '          -          ';
        $resource_columns = $this->resource->export_columns();
        $result = (array_filter(array_map(function ($key)  use ($row, $resource_columns, $placeholder) {
            if ($this->columns[$key]["enabled"]) {
                if (!@$resource_columns[$key]["handle"]) {
                    if (strpos($key, "->") === false) return @$row->{$key} ? $row->{$key} : $placeholder;
                    $value = $row;
                    $_runner = explode("->", $key);
                    foreach ($_runner as $idx) $value = @$value->{$idx};
                    return ($value ? $value : $placeholder);
                }
                $value = $resource_columns[$key]["handle"]($row);
                return (@$value ? $value : $placeholder);
            }
        }, array_keys($this->columns))));
        return $result;
    }
}
