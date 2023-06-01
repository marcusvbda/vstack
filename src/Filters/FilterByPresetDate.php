<?php

namespace marcusvbda\vstack\Filters;

use  marcusvbda\vstack\Filter;
use Carbon\Carbon;

class FilterByPresetDate extends Filter
{
    public $component   = "custom-filter";
    public $label       = "Data de Criação";
    public $index = "created_at";
    public $field = "created_at";
    public $element = "";
    public $action = null;
    public $column = null;
    public $joins = [];

    public $_options = [
        "hoje" => ["Hoje", "getToday"],
        "ontem" => ["Ontem", "getYesterday"],
        "ultimos 5 dias" => ["Últimos 5 dias", "getLastFiveDays"],
        "ultimos 7 dias" => ["Últimos 7 dias", "getLastSevenDays"],
        "ultimos 14 dias" => ["Últimos 14 dias", "getLastFourteenDays"],
        "ultimos 15 dias" => ["Últimos 15 dias", "getLastFifteenDays"],
        "ultimos 30 dias" => ["Últimos 30 dias", "getLastThirthDays"],
        "esta semana" => ["Esta semana", "getThisWeek"],
        "este mes" =>  ["Este mês", "getThisMonth"],
        "este ano" =>  ["Este ano", "getThisYear"],
        "todos" =>  ["Todos os Resultados", "getAll"],
    ];

    public function queryBetweenDates($column, $dates)
    {
<<<<<<< HEAD
        $end_of_filter = (new \Carbon\Carbon($dates[1]))->endOfDay();
        return "$column >= '$dates[0]' and $column <= '$end_of_filter'";
=======
        return "DATE($column) >= DATE('$dates[0]') and DATE($column) <= DATE('$dates[1]')";
>>>>>>> f3f4219b266368600aa696dfd36600cca789a0a0
    }


    public function __construct($options = [])
    {
        foreach ($options as $key => $value) {
            $this->$key = $value;
        }

        if (!$this->index) {
            $this->index = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0',  @$this->field ? @$this->field : @$this->column));
        }

        $this->element = "
            <custom-preset-date-filter @on-submit='showConfirm' index='" . $this->index . "' :filter='filter' :options='" . json_encode($this->_options) . "'/>
        ";

        if (!@$this->action) {
            $this->action = function ($query, $value, $dates) {
                if ($value == "todos") {
                    return $query;
                }
                return  $query->whereRaw($this->queryBetweenDates(@$this->field ? @$this->field : @$this->column, $dates));
            };
        }
        parent::__construct();
    }


    public function apply($query, $value)
    {
        $action = $this->action;
        $dates = $this->getDates($value);
        return  $action($query, $value, $dates);
    }

    protected function getDates($value)
    {
        $values = explode(",", $value);
        if (in_array($values[0], array_keys($this->_options))) {
            $action = data_get($this->_options, "$value.1");
            return $this->{$action}();
        }
        return $values;
    }

    protected function getAll()
    {
        return [null, null];
    }

    protected function getLastThirthDays()
    {
        $starts = Carbon::today()->subDays(30);
        $ends = Carbon::today();
        return [$starts, $ends];
    }

    protected function getLastFourteenDays()
    {
        $starts = Carbon::today()->subDays(14);
        $ends = Carbon::today();
        return [$starts, $ends];
    }

    protected function getLastFifteenDays()
    {
        $starts = Carbon::today()->subDays(15);
        $ends = Carbon::today();
        return [$starts, $ends];
    }

    protected function getLastFiveDays()
    {
        $starts = Carbon::today()->subDays(5);
        $ends = Carbon::today();
        return [$starts, $ends];
    }

    protected function getLastSevenDays()
    {
        $starts = Carbon::today()->subDays(7);
        $ends = Carbon::today();
        return [$starts, $ends];
    }

    protected function getYesterday()
    {
        $yesterday = Carbon::today()->subDays(1);
        return [$yesterday, $yesterday];
    }

    protected function getToday()
    {        
        $today = Carbon::today();
        $today_end = Carbon::today();
        return [$today, $today_end];
    }

    protected function getThisWeek()
    {
        return [
            Carbon::today()->startOfWeek()->subDays(1),
            Carbon::today()->endOfWeek(),
        ];
    }

    protected function getThisYear()
    {
        $year = Carbon::today()->format("Y");
        return [
            Carbon::create("01-01-$year"),
            Carbon::create("31-12-$year")
        ];
    }

    protected function getThisMonth()
    {
        return [
            Carbon::today()->startOfMonth(),
            Carbon::today()->endOfMonth(),
        ];
    }

    protected function format($date)
    {
        return $date->format("Y-m-d");
    }

    public static function getAllDates()
    {
        $filter = new FilterByPresetDate;
        $options_dates = [];
        foreach ($filter->_options as $key => $value) {
            $options_dates[$key] = $filter->{$value[1]}();
        }
        return $options_dates;
    }
}
