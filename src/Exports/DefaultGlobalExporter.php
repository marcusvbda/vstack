<?php

namespace marcusvbda\vstack\Exports;

use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
};


class DefaultGlobalExporter implements FromCollection, WithHeadings
{
    public function __construct($headers, $data = [])
    {
        $this->letters = range('A', 'Z');
        $this->headers = $headers;
        $this->data = $data;
        $this->last_collumn = $this->letters[count($this->headings()) - 1];
    }


    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return $this->headers;
    }
}
