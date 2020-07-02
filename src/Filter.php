<?php

namespace marcusvbda\vstack;

class Filter
{
    public $view;
    public $options = [];
    public function __construct()
    {
        $this->makeView();
    }

    private function makeView()
    {
        switch ($this->component) {
            case "text-filter":
                $this->makeViewTextField();
                break;
            case "select-filter":
                $this->makeViewSelectField();
                break;
            case "check-filter":
                $this->makeViewCheckField();
                break;
            case "date-filter":
                $this->makeViewDateField();
                break;
            case "rangedate-filter":
                $this->makeViewRangeDateField();
                break;
            case "custom-filter":
                $this->makeCustomField();
                break;
            default:
                return $this->component;
                break;
        }
    }

    public function applyFilter($query, $data)
    {
        $value = @$data[$this->index] ? @$data[$this->index] : null;
        if ($value && @$value != "null" && @$value != "false") $query = $this->apply($query, $value);
        return $query;
    }

    private function makeCustomField()
    {
        $this->view =  @$this->element;
    }

    private function makeViewTextField()
    {
        $index         = $this->index;
        $placeholder   = @$this->placeholder ? $this->placeholder : "";
        $this->view =  view("vStack::resources.filter.text", compact(
            "index",
            "placeholder"
        ))->render();
    }

    private function makeViewSelectField()
    {
        $index         = $this->index;
        $placeholder   = @$this->placeholder ? $this->placeholder : "";
        $options   = @$this->options ? $this->options : [];
        $this->view = view("vStack::resources.filter.select", compact(
            "index",
            "placeholder",
            "options"
        ))->render();
    }

    private function makeViewCheckField()
    {
        $index = $this->index;
        $text  = @$this->text ? $this->text : "";
        $this->view = view("vStack::resources.filter.check", compact(
            "index",
            "text"
        ))->render();
    }

    private function makeViewDateField()
    {
        $index = $this->index;
        $placeholder = @$this->placeholder ? $this->placeholder : "";
        $this->view = view("vStack::resources.filter.date", compact(
            "index",
            "placeholder"
        ))->render();
    }

    private function makeViewRangeDateField()
    {
        $index             = $this->index;
        $end_placeholder   = @$this->end_placeholder ? $this->end_placeholder : "";
        $start_placeholder = @$this->start_placeholder ? $this->start_placeholder : "";
        $this->view = view("vStack::resources.filter.rangedate", compact(
            "index",
            "end_placeholder",
            "start_placeholder"
        ))->render();
    }
}
