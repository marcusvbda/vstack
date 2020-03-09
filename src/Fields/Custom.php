<?php

namespace marcusvbda\vstack\Fields;

class Custom extends Field
{
    public $options = [];
    public $view = "";
    public function __construct($view = "", $op = [])
    {
        $this->options = $op;
        $this->options["type"] = "custom";
        $this->options["view"] = $view;
        parent::processFieldOptions();
    }

    public function getView()
    {
        $view = @$this->options["view"];
        return $this->view = $view;
    }
}
