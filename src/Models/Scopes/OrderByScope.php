<?php

namespace marcusvbda\vstack\Models\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class OrderByScope implements Scope
{
    private $table = "";
    private $column = "";
    private $type = "";

    public function __construct($table, $column = "id", $type = "desc")
    {
        $this->table = $table;
        $this->column = $column;
        $this->type = $type;
    }

    public function apply(Builder $builder, Model $model)
    {
        @$builder->orderBy($this->table . "." . $this->column, $this->type);
    }
}
