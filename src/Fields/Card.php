<?php

namespace marcusvbda\vstack\Fields;

class Card
{
    public $view;
    public $label;
    public $inputs;

    public function __construct($label, $inputs)
    {
        $this->label = $label;
        $this->inputs = $inputs;
        $this->makeView();
    }

    private function mapInputs()
    {
        $views = collect($this->inputs)->map(function ($x) {
            return $x->getView();
        })->toArray();
        return implode("", $views);
    }

    private function makeView()
    {
        $label = $this->label;
        $inputs = $this->mapInputs();
        return $this->view = view("vStack::resources.field.card", compact(
            "label",
            "inputs"
        ))->render();
    }
}
