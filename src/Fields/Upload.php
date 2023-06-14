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
        $is_image = !@$this->options["style"] ? true : $this->options["style"] == "gallery";
        $is_image = $is_image ? 'true' : 'false';

        $auto_set_name = @$this->options["auto_set_name"] !== null ? $this->options["auto_set_name"] : true;
        $auto_set_name = $auto_set_name ? 'true' : 'false';
        $slot_top = @$this->options["slot_top"] ? $this->options["slot_top"] : "";
        $slot_bottom = @$this->options["slot_bottom"] ? $this->options["slot_bottom"] : "";

        $aspect_ratio = @$this->options["aspect_ratio"] ? $this->options["aspect_ratio"] : 0;
        $crop_image = (@$this->options["crop_image"] && $is_image != "true") ? $this->options["crop_image"] : false;
        $crop_image = $crop_image ? 'true' : 'false';


        $show_url = @$this->options["show_url"] ? $this->options["show_url"] : false;
        $show_url = $show_url ? 'true' : 'false';

        return $this->view = view("vStack::resources.field.upload", compact(
            "aspect_ratio",
            "crop_image",
            "show_url",
            "label",
            "uploadroute",
            "field",
            "multiple",
            "preview",
            "limit",
            "accept",
            "description",
            "sizelimit",
            "eval",
            "is_image",
            "auto_set_name",
            "slot_top",
            "slot_bottom"
        ))->render();
    }
}
