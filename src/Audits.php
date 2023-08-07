<?php

namespace marcusvbda\vstack;

use marcusvbda\vstack\Enums\AuditsEventsEnum;
use marcusvbda\vstack\Filters\FilterByOption;
use marcusvbda\vstack\Filters\FilterByPresetDate;
use marcusvbda\vstack\Resource;
use OwenIt\Auditing\Models\Audit;
use ResourcesHelpers;

class Audits extends Resource
{
    public $model = Audit::class;

    public function label()
    {
        $parent = ResourcesHelpers::find(request()->resource_id);
        return "Audits de " . $parent->label();
    }

    public function singularLabel()
    {
        $parent = ResourcesHelpers::find(request()->resource_id);
        return "Audit " . $parent->singularLabel();
    }

    public function icon()
    {
        return "el-icon-tickets";
    }

    public function canImport()
    {
        return false;
    }

    public function canCreate()
    {
        return false;
    }

    public function canUpdate()
    {
        return false;
    }

    public function canDelete()
    {
        return false;
    }

    public function search()
    {
        return ["event", "user_type", "auditable_type", "old_value", "new_values", "url", "ip_address", "user_agent"];
    }

    public function getFormatedValuesAttribute()
    {
        $getType = function ($value) {
            if ((str_contains($value, "{") !== false) && (str_contains($value, "}") !== false)) return "json|array";
            if ((str_contains($value, "[") !== false) && (str_contains($value, "]") !== false)) return "json|array";
            return "html|string";
        };

        $old_values = $getType($this->old_values) === "json|array" ?  "<json-viewer :value='" . $this->old_values . "' :preview-mode='true' />" : $this->old_values;
        $new_values = $getType($this->new_values) === "json|array" ?  "<json-viewer :value='" . $this->new_values . "' :preview-mode='true' />" : $this->new_values;

        return "<div class='d-flex flex-column'>
      <b class='mb-2'>Valor antigo :</b>
      {$old_values}
      <br />
      <b class='my-2'>Valor novo :</b>
      {$new_values}
    </div>";
    }

    public function table()
    {
        return [
            "event" => ["label" => "Evento", "handler" => function ($row) {
                return AuditsEventsEnum::badge($row->event);
            }],
            "user->name" => ["label" => "Usuário", "sortable_index" => "user_id"],
            "values" => ["label" => "Valores", "sortable" => false, "handler" => function ($row) {
                $values =  "<small class='text-neutral-400 dark:text-neutral-400 text-xs'>
                                <b>Antes :</b>
                                <code>" . nl2br(json_encode($row->old_values, JSON_PRETTY_PRINT)) . "</code><br><br>
                                <b class='mt-3'>Depois :</b>
                                <code>" . nl2br(json_encode($row->new_values, JSON_PRETTY_PRINT)) . "</code><br>
                            </small>";
                return $values;
            }, "size" => "300px"],
            "agent" => ["label" => "IP/User Agent", "sortable" => false, "handler" => function ($row) {
                return Vstack::makeLinesHtmlAppend($row->ip_address, $row->user_agent);
            }, "size" => "300px"],
            "created_at" => ["label" => "Data", "sortable_index" => "created_at", "handler" => function ($row) {
                return Vstack::makeLinesHtmlAppend($row->created_at->format("d/m/Y H:i:s"), $row->created_at->diffForHumans());
            }],
        ];
    }

    public function filters()
    {
        $filters[] = new FilterByOption([
            "label" => "Evento",
            "column" => "event",
            "options" => Vstack::enumToOptions(AuditsEventsEnum::cases(), true)
        ]);

        $filters[] = new FilterByPresetDate([
            "label" => "Data",
            "column" => "created_at",
        ]);

        return $filters;
    }

    public function prepareQueryToList($query)
    {
        $resource = ResourcesHelpers::find(request()->resource_id);
        $code = request()->code;
        $id = Hashids::decode($code)[0];
        return $query->where("auditable_type", $resource->model::class)->where("auditable_id", $id);
    }
}
