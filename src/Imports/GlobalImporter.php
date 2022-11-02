<?php

namespace marcusvbda\vstack\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\HeadingRowImport;
use Excel;

class GlobalImporter implements ToCollection
{
    public function __construct($filepath, $controller, $class, $params)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(-1);
        $this->filepath = $filepath;
        $this->params = $params;
        $this->controller = $controller;
        $this->class = $class;
        $this->result = [];
        $data = Excel::toArray(new HeadingRowImport, $this->getFile());
        $this->headers = @$data[0][0];
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setResult($result)
    {
        return $this->result = $result;
    }

    public function getFile()
    {
        return $this->filepath;
    }

    public function collection(Collection $rows)
    {
        $this->controller = new $this->controller;
        $this->controller->{$this->class}($rows, $this->params, $this);
    }
}
