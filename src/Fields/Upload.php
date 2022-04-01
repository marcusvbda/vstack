<?php

namespace marcusvbda\vstack\Fields;

class Upload extends Field
{
    public $options = [];
    public $view = "";
    public function __construct($op = [])
    {
        $this->options = $op;
        $this->options["type"] = "upload";
        parent::processFieldOptions();
    }

    public function getView($type = "input")
    {
        if (@$this->options["hide"]) {
            return $this->view = "";
        }

        if ($type == "view") {
            return $this->getViewOnlyValue();
        }

        $field     = $this->options["field"];
        $preview   = !@$this->options["preview"] ? "undefined" : "true";
        $multiple  = @$this->options["multiple"] ? "true" : "false";
        $accept    = @$this->options["accept"] ? $this->options["accept"] : "";
        $limit     = @$this->options["limit"] ? ($multiple == "true" ? $this->options["limit"] : 1) : ($multiple == "true" ? 5 : 1);
        $uploadroute    = @$this->options["upload_route"] ? $this->options["upload_route"] : Config("vstack.default_upload_route");
        $label = $this->options["label"];
        $description = @$this->options["description"] ?? "";
        $sizelimit = @$this->options["file_size_limit"] ? $this->options["file_size_limit"] : 0;
        $eval = " " . (@$this->options["eval"] ? trim($this->options["eval"]) : "") . " ";


        return $this->view = view("vStack::resources.field.upload", compact(
            "label",
            "uploadroute",
            "field",
            "multiple",
            "preview",
            "limit",
            "accept",
            "description",
            "sizelimit",
            "eval"
        ))->render();
    }
}
