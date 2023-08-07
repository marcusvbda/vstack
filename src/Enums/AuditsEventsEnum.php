<?php

namespace marcusvbda\vstack\Enums;

enum AuditsEventsEnum: string
{
    case created = "Criado";
    case updated = "Atualizado";
    case deleted = "Deletado";
    case restored = "Restaurado";
    case forceDeleted = "Deletado permanentemente";

    public static function badge($name)
    {
        $case = collect(static::cases())->where("name", $name)->first();
        $type = match ($name) {
            "created" => "info",
            "updated" => "warning",
            "deleted" => "primary",
            "restored" => "success",
            "forceDeleted" => "danger",
        };
        return makeBadge($type, $case->value);
    }
}
