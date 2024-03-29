<?php

namespace marcusvbda\vstack\Exports;

use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    ShouldAutoSize
};


class DefaultGlobalExporter implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct($headers, $data = [])
    {
        $this->letters = range('A', 'Z');
        $this->headers = $headers;
        $this->data = $data;
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
