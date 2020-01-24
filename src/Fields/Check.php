<?php

namespace marcusvbda\vstack\Fields;

class Check extends Field
{
    public $options = [];
    public function __construct($op = [])
    {
        $this->options = $op;
        $this->options["type"] = "check";
        parent::processFieldOptions();
    }

    public function getView()
    {
        $view = "";
        $label          = $this->options["label"];
        $field          = $this->options["field"];
        $active_color   = @$this->options["active_color"] ? $this->options["active_color"] : "green";
        $inactive_color = @$this->options["inactive_color"] ? $this->options["inactive_color"] : "red";
        $active_text    = @$this->options["active_text"] ? $this->options["active_text"] : "";
        $inactive_text  = @$this->options["inactive_text"] ? $this->options["inactive_text"] : "";
        $disabled       = @$this->options["disabled"] ? "true" : "false";
        $description    = @$this->options["description"];
        if (!@$this->options["hide"])
            $view = "<div class='form-group d-flex align-items-center justify-content-center row mb-3'>
                        <label class='col-sm-2 col-form-label'>$label</label>
                        <div class='col-sm-10 pl-0'>                                                   
                            <el-switch             
                                :disabled='$disabled'                               
                                class='ml-3'                          
                                v-model='form.$field'                 
                                active-color='$active_color'          
                                inactive-color='$inactive_color'      
                                active-text='$active_text'            
                                inactive-text='$inactive_text'>       
                            </el-switch> 
                            ".(!$description ? "" : "<br><small style='color:gray;' class='mt-1 pl-3'>$description</small>")."
                        </div>                             
                    </div>";
        return $this->view = $view;
    }
}
