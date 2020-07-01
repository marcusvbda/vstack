<?php

namespace marcusvbda\vstack\Fields;

class Summernote extends Field
{
    public $options = [];
    public $view = "";
    public function __construct($op = [])
    {
        $this->options = $op;
        $this->options["type"] = "summernote";
        parent::processFieldOptions();
    }

    public function getView()
    {
        if (@$this->options["hide"]) return $this->view = "";
        $label          = $this->options["label"];
        $field          = $this->options["field"];
        $type           = $this->options["type"];
        $placeholder    = $this->options["placeholder"];
        $disabled       = @$this->options["disabled"] ? "true" : "false";
        $uploadroute    = @$this->options["upload_route"] ? $this->options["upload_route"] : Config("vstack.default_upload_route");
        $height         = @$this->options["height"] ? $this->options["height"] : 150;

        return $this->view = view("vStack::resources.field.summernote", compact(
            "uploadroute",
            "disabled",
            "label",
            "type",
            "field",
            "height",
            "placeholder"
        ))->render();
    }
}
