<?php

namespace marcusvbda\vstack;

class Filter
{
    public $view;
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
            default:
                dd($this);
                break;
        }
    }

    public function applyFilter($query, $data)
    {
        $value = @$data[$this->index] ? @$data[$this->index] : null;
        if ($value && @$value != "null" && @$value != "false") $query = $this->apply($query, $value);
        return $query;
    }

    private function makeViewTextField()
    {
        $index         = $this->index;
        $placeholder   = @$this->placeholder ? $this->placeholder : "";
        $this->view =  "<el-input clearable size='medium' v-model='filter.$index' 
                            @change='makeNewRoute' 
                            type='text' class='w-100'      
                            placeholder='$placeholder'
                            v-bind:class='{\"withFilter\": filter.$index}'>
                        </el-input>";
    }

    private function makeViewSelectField()
    {
        $index         = $this->index;
        $placeholder   = @$this->placeholder ? $this->placeholder : "";
        $view = "<el-select 
                    v-model='filter.$index' size='medium' class='w-100' @change='makeNewRoute'
                    filterable placeholder='$placeholder'
                    v-bind:class='{\"withFilter\" : filter.$index}'>
                    <el-option label=\"\" value=\"\"></el-option>";
        foreach ($this->options as $option) {
            $value = $option->value;
            $label = $option->label;
            $view .= "<el-option :value='$value' label='$label'></el-option>";
        }
        $view .= "</el-select>";
        $this->view = $view;
    }

    private function makeViewCheckField()
    {
        $index = $this->index;
        $text  = @$this->text ? $this->text : "";
        $this->view =  "<el-checkbox size='medium' class='d-flex align-items-center'
                            v-model='filter.$index' @change='makeNewRoute' >$text
                        </el-checkbox>";
    }

    private function makeViewDateField()
    {
        $index = $this->index;
        $placeholder = @$this->placeholder ? $this->placeholder : "";
        $this->view  =  "<el-date-picker size='medium' class='w-100' clearable
                            v-model='filter.$index' @change='makeNewRoute' type='date'
                            :format=\"'dd/MM/yyyy'\"
                            value-format='yyyy-MM-dd'
                            placeholder='$placeholder'>
                        </el-date-picker>";
    }

    private function makeViewRangeDateField()
    {
        $index             = $this->index;
        $end_placeholder   = @$this->end_placeholder ? $this->end_placeholder : "";
        $start_placeholder = @$this->start_placeholder ? $this->start_placeholder : "";
        $this->view =  "<el-date-picker size='medium' class='w-100' clearable
                            v-model='filter.$index' @change='makeNewRoute' type='daterange'
                            :format=\"'dd/MM/yyyy'\"
                            value-format='yyyy-MM-dd'
                            end-placeholder='$end_placeholder'
                            start-placeholder='$start_placeholder'>
                        </el-date-picker>";
    }
}
