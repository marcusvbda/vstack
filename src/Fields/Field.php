<?php

namespace marcusvbda\vstack\Fields;

class Field
{
    public $options = [];
    public $view    = "";

    public function processFieldOptions()
    {
        @$this->options["type"]                 = $this->options["type"] ? $this->options["type"] : "text";
        @$this->options["label"]                = $this->options["label"] ? $this->options["label"] : "";
        @$this->options["field"]                = $this->options["field"] ? $this->options["field"] : "field";
        @$this->options["required"]             = $this->options["required"] ? $this->options["required"] : false;
        @$this->options["placeholder"]          = $this->options["placeholder"] ? $this->options["placeholder"] : "";
        @$this->options["minlength"]            = $this->options["minlength"] ? $this->options["minlength"] : 0;
        @$this->options["max"]                  = $this->options["max"] ? $this->options["max"] : null;
        @$this->options["min"]                  = $this->options["min"] ? $this->options["min"] : null;
        @$this->options["mask"]                 = $this->options["mask"] ? $this->options["mask"] : null;
        @$this->options["value"]                = $this->options["value"] ? $this->options["value"] : null;
        @$this->options["default"]              = $this->options["default"] ? $this->options["default"] : null;
        @$this->options["append"]               = $this->options["append"] ? $this->options["append"] : null;
        @$this->options["prepend"]              = $this->options["prepend"] ? $this->options["prepend"] : null;
        @$this->options["rules"]                = $this->options["rules"] ? $this->options["rules"] : '';
        @$this->options["mask"]                 = $this->options["mask"] ? $this->options["mask"] : '';
        @$this->options["description"]          = $this->options["description"] ? $this->options["description"] : '';
    }
}
