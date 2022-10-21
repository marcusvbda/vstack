<?php

namespace marcusvbda\vstack\Fields;

class HasOneOrMany extends Field
{
    public $options = [];
    public function __construct($op = [])
    {
        $this->options = $op;
        $this->options["type"] = "has-one-many";
        parent::processFieldOptions();
    }

    public function getView($type = "input")
    {
        if ($type == "view") {
            return $this->getViewOnlyValue();
        }

        if (@$this->options["hide"]) {
            return $this->view = "";
        }

        $label = data_get($this->options, "label", "");
        $description = data_get($this->options, "description", "");
        $resource = data_get($this->options, "resource", "");
        $field = data_get($this->options, "field", "");
        $children = data_get($this->options, "children", []);
        $table = data_get($this->options, "table", ["name" => ["label" => "Nome"]]);
        $limit = data_get($this->options, "limit", "infinite");
        $field = data_get($this->options, "field", "");
        $eval = data_get($this->options, "eval", " ");
        $disabled = data_get($this->options, "disabled", false) ? "true" : "false";
        $info = $this->makeFieldsList($table, $limit, $field, $resource, $children);
        $info = json_encode($info);

        return $this->view = view("vStack::resources.field.hasonemany", compact(
            "resource",
            "eval",
            "field",
            "info",
            "label",
            "description",
            "disabled"
        ))->render();
    }

    private function makeFieldsList($table, $limit, $field, $resource, $children = [])
    {
        $mounted_resource = app()->make($resource);
        $fields = [
            "table" => $table,
            "limit" => $limit,
            "field" => $field,
            "resource" => $resource,
            "resource_id" => $mounted_resource->id,
            "fields" => [],
            "children" => [],
            "label" => $mounted_resource->label(),
            "singular_label" => $mounted_resource->singularLabel(),
        ];
        foreach ($mounted_resource->tree_fields() as $field) {
            $fields["fields"][] = $field;
        }
        foreach ($children as $child) {
            $child_field = data_get($child, "field", "");
            $child_limit = data_get($child, "limit", "infinite");
            $child_respurce = data_get($child, "resource");
            $table_children = data_get($child, "table", ["name" => ["label" => "Nome"]]);
            $child_children = data_get($child, "children", []);
            $fields["children"][] = $this->makeFieldsList($table_children, $child_limit, $child_field, $child_respurce, $child_children);
        }
        return $fields;
    }
}
