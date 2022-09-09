<?php

namespace marcusvbda\vstack\Filters;


use  marcusvbda\vstack\Filter;
use marcusvbda\vstack\Models\TagRelation;
use Auth;

class FilterByTag extends Filter
{
    public $component   = "select-filter";
    public $label       = "";
    public $placeholder = "";
    public $index = "";
    public $multiple = true;
    public $table = "";
    public $model = "";
    public $user = null;
    public $callback = null;

    public function __construct($model, $label = "Tags", $index = "tags", $callback = null)
    {
        $this->label = $label;
        $this->index = $index;
        $this->model = $model;
        $this->callback = $callback;
        $this->table = (new $this->model)->getTable();
        $this->user = Auth::user();
        $this->options = TagRelation::where("resource_tags_relation.model", $this->model)
            ->join("resource_tags", "resource_tags.id", "=", "resource_tags_relation.resource_tag_id")
            ->groupBy("resource_tags_relation.resource_tag_id")
            ->select("resource_tags.id as value", "resource_tags.name as label")
            ->where("resource_tags.tenant_id", $this->user->tenant_id)
            ->where("resource_tags.model", $this->model)
            ->get();
        parent::__construct();
    }

    public function apply($query, $value)
    {
        if ($this->callback) {
            $callback = $this->callback;
            return $callback($query, $value);
        }
        if (!@$value) {
            return $query;
        }

        return $query->whereHas("tags", function ($query) use ($value) {
            $values = explode(",", $value);
            return $query->whereIn("resource_tags.id", $values);
        });
    }
}
