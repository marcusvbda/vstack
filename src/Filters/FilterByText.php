<?php

namespace marcusvbda\vstack\Filters;

use  marcusvbda\vstack\Filter;

class FilterByText extends Filter
{
    public $component   = "text-filter";
    public $label       = "";
    public $field = "";
    public $placeholder = "";
    public $index = "";

    public function __construct($options)
    {
        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }
        if (!$this->index) {
            $this->index = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0',  @$this->field ? @$this->field : @$this->column));
        }
        parent::__construct();
    }

    public function apply($query, $value)
    {
        return $query->where(@$this->field ? @$this->field : @$this->column, "like", "%$value%");
    }
}
