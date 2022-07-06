<?php

namespace marcusvbda\vstack\Filters;

use  marcusvbda\vstack\Filter;

class FilterByOption extends Filter
{
    public $component   = "select-filter";
    public $label       = "";
    public $index = "";
    public $field = "";
    public $placeholder = "";
    public $multiple = false;

    public function __construct($options)
    {
        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }
        if (!$this->index) {
            $this->index = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', @$this->field ? @$this->field : @$this->column));
        }
        parent::__construct();
    }

    public function apply($query, $value)
    {
        $ids = explode(",", $value);
        return $query->whereIn(@$this->field ? @$this->field : @$this->column, $ids);
    }
}
