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
    public $handle = null;

    public function __construct($options)
    {
        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }

        if (!$this->handle) {
            $this->handle = function ($query, $value) {
                if (!$this->index) {
                    $this->index = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0',  @$this->field ? @$this->field : @$this->column));
                }
                return function ($query, $value) {
                    $ids = explode(",", $value);
                    $query->where(@$this->field ? @$this->field : @$this->column, "like", "%$value%");
                };
            };
        }
        if ($this->index == "") $this->index = $this->field;

        parent::__construct();
    }

    public function apply($query, $value)
    {
        $handle = $this->handle;
        return $handle($query, $value);
    }
}
