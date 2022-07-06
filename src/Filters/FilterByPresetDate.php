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
    public $action = null;
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
    ];


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
                return  $query->whereRaw(queryBetweenDates(@$this->field ? @$this->field : @$this->column, $dates));
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

    protected function getLastThirthDays()
    {
        $starts = $this->format(Carbon::now()->subDays(30));
        $ends = $this->format(Carbon::now());
        return [$starts, $ends];
    }

    protected function getLastFourteenDays()
    {
        $starts = $this->format(Carbon::now()->subDays(14));
        $ends = $this->format(Carbon::now());
        return [$starts, $ends];
    }

    protected function getLastFifteenDays()
    {
        $starts = $this->format(Carbon::now()->subDays(15));
        $ends = $this->format(Carbon::now());
        return [$starts, $ends];
    }

    protected function getLastFiveDays()
    {
        $starts = $this->format(Carbon::now()->subDays(5));
        $ends = $this->format(Carbon::now());
        return [$starts, $ends];
    }

    protected function getLastSevenDays()
    {
        $starts = $this->format(Carbon::now()->subDays(7));
        $ends = $this->format(Carbon::now());
        return [$starts, $ends];
    }

    protected function getYesterday()
    {
        $yesterday = $this->format(Carbon::now()->subDays(1));
        return [$yesterday, $yesterday];
    }

    protected function getToday()
    {
        $today = $this->format(Carbon::now());
        return [$today, $today];
    }

    protected function getThisWeek()
    {
        return [
            $this->format(Carbon::now()->startOfWeek()->subDays(1)),
            $this->format(Carbon::now()->endOfWeek()->subDays(1)),
        ];
    }

    protected function getThisYear()
    {
        $year = Carbon::now()->format("Y");
        return [
            $this->format(Carbon::create("01-01-$year")),
            $this->format(Carbon::create("31-12-$year"))
        ];
    }

    protected function getThisMonth()
    {
        return [
            $this->format(Carbon::now()->startOfMonth()),
            $this->format(Carbon::now()->endOfMonth()),
        ];
    }

    protected function format($date)
    {
        return $date->format("Y-m-d");
    }

    protected static function getAllDates()
    {
        $filter = new FilterByPresetDate;
        $options_dates = [];
        foreach ($filter->_options as $key => $value) {
            $options_dates[$key] = $filter->{$value}();
        }
        return $options_dates;
    }
}
