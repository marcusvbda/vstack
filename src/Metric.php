<?php

namespace marcusvbda\vstack;
use Illuminate\Http\Request;

class Metric
{
    public $label;
    public $type;
    public $view;

    public function __construct()
    {
        $this->view = $this->initView();
        $this->processView();
    }

    public function label()
    {
        return "";
    }

    public function sublabel()
    {
        return "";
    }

    private function initView()
    {
        if(in_array($this->type,["custom-content"]))
        {
            return  "<div class='".$this->width()." mb-3'>
                        <div class='card d-flex flex-column justify-content-between p-3 h-100 h-100'>
                            __content__here__
                        </div>
                    </div>";
        }
        if(in_array($this->type,["group-chart"]))
        {
            return  "<div class='".$this->width()." mb-3'>
                        <div class='card d-flex flex-column justify-content-between p-3 h-100 h-100'>
                            __content__here__
                        </div>
                    </div>";
        }
        if(in_array($this->type,["trend-counter"]))
        {
            return  "<div class='".$this->width()." mb-3'>
                        <div class='card d-flex flex-column justify-content-between p-3 h-100 h-100'>
                            __content__here__
                        </div>
                    </div>";
        }
        if(in_array($this->type,["trend-chart"]))
        {
            return  "<div class='".$this->width()." mb-3'>
                        <div class='card d-flex flex-column justify-content-between p-0 h-100 h-100'>
                            __content__here__
                        </div>
                    </div>";
        }
    }

    public function width()
    {
        return "col-md-4 col-sm-12";
    }

    public function processView()
    {
        $view = str_replace ("__content__here__",$this->makeContent(),$this->view);
        $this->view = $view;
    }

    private function makeContent()
    {
        switch ($this->type) 
        {
            case 'custom-content':
                return $this->customContent();
                break;
            case 'group-chart':
                return $this->groupChartContent();
                break;
            case 'trend-counter':
                return $this->trendCounterContent();
                break;
            case 'trend-chart':
                return $this->trendChartContent();
                break;
            default:
                return $this->type;
                break;
        }
    }

    private function groupChartContent()
    {
        return "<metric-piechart :time='time' :route='calculate_route'>
                    <template slot='label'>".$this->label()."</template>
                    <template slot='sublabel'>".$this->sublabel()."</template>
                </metric-piechart>";
    }

    private function customContent()
    {
        return "<metric-custom-content :time='time' :route='calculate_route'>
                    <template slot='label'>".$this->label()."</template>
                    <template slot='sublabel'>".$this->sublabel()."</template>
                </metric-custom-content>";
    }

    private function trendChartContent()
    {
        return "<metric-trendchart :ranges='ranges' :time='time' :route='calculate_route'>".$this->label()."</metric-trendchart>";
    }

    private function trendCounterContent()
    {
        return "<metric-trend-counter :ranges='ranges' :time='time' :route='calculate_route'>".$this->label()."</metric-trend-counter>";
    }

    public function ranges()
    {
        return "date-interval";
    }

    public function calculate(Request $request)
    {
        return [];
    }

    public function updateTime()
    {
        return 60;
    }

    public function uriKey()
    {
        return 'metric_key_here';
    }
}
