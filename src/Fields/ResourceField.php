<?php

namespace marcusvbda\vstack\Fields;

class ResourceField extends Field
{
    public $options = [];
    public $view = "";
    public function __construct($op = [])
    {
        $this->options = $op;
        $this->options["type"] = "resource-field";
        parent::processFieldOptions();
    }

    public function getView()
    {
        $view = "";
        $resource = @$this->options["resource"] ? $this->options["resource"] : "";
        $this->options["field"] = $resource;
        $params = json_encode(@$this->options["params"] ? $this->options["params"] : []);
        if (!@$this->options["hide"])
            $view = "<resource-field     
                        resource='$resource' 
                        :params='resourceData.$resource'
                    />";
        return $this->view = $view;
    }
}
