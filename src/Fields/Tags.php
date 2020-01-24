<?php

namespace marcusvbda\vstack\Fields;

class Tags extends Field
{
    public $options = [];
    public $view = "";
    public function __construct($op = [])
    {
        $this->options = $op;
        parent::processFieldOptions();
    }

    public function getView()
    {
        $view = "";
        $label          = $this->options["label"];
        $field          = $this->options["field"];
        $disabled       = @$this->options["disabled"] ? "true" : "false";
        $description    = $this->options["description"];
        if (!@$this->options["hide"])
            $view = "<v-tags class='mb-3'  
                        :disabled='$disabled'                                                                  
                        label='$label'                                                                 
                        description='$description'                                                              
                        v-model='form.$field'                                                            
                        :errors='errors.$field ? errors.$field : false'                                  
                    />";
        return $this->view = $view;
    }
}
