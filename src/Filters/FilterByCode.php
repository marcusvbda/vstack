<?php

namespace marcusvbda\vstack\Filters;

use marcusvbda\vstack\Filter;

class FilterByCode extends Filter
{
    public $component = 'text-filter';
    public $label =  'Código';
    public $placeholder = '';
    public $index = 'code';

    public function apply($query, $value)
    {
        $value = str_replace('#', '', strtoupper($value));
        $hashed_id = \Hashids::decode($value);

        return $query->where('id', @$hashed_id[0] ? @$hashed_id[0] : $value);
    }
}
