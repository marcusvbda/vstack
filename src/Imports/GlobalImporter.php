<?php

namespace marcusvbda\vstack\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\HeadingRowImport;
use Excel;

class GlobalImporter implements ToCollection
{
    public function __construct($resource, $fieldlist, $tenant_id, $file)
    {
        $this->fieldlist = $fieldlist;
        $this->tenant_id = $tenant_id;
        $this->file = $file;
        $data = Excel::toArray(new HeadingRowImport, $this->file);
        $header = @$data[0][0];
        $this->header = $header;
        $this->resource = $resource;
        $this->success = false;
        $this->lineErrors = [];
        $this->rows = 0;
    }

    public function getRowCount()
    {
        return $this->rows;
    }

    public function getErrors()
    {
        return $this->lineErrors;
    }

    public function collection(Collection $rows)
    {
        $model = $this->resource->model;
        $fieldlist = $this->fieldlist;
        $header = $this->header;

        foreach ($rows as $key => $row_values) {
            if ($key == 0) continue;
            try {
                $row_values = $row_values->toArray();
                $new = [];
                foreach ($fieldlist as $field => $row_key) {
                    if ($row_key == "_IGNORE_") continue;
                    $value = @$row_values[array_search($row_key, $header)];
                    if (!$value) continue;
                    $new[$field] = $value;
                }
                $new_model = @$new["id"] ? $model->findOrFail($new["id"]) : new $model;
                if (@$this->tenant_id) $new["tenant_id"] = $this->tenant_id;
                $new_model->fill($new);
                $new_model->save();
                unset($new_model, $row_values, $new);
                $this->rows++;
            } catch (\Exception $e) {
                $this->lineErrors[] = [
                    "message" => $e->getMessage(),
                    "line" => $key
                ];
            }
        }
        unlink($this->file);
        $this->success = true;
    }
}
