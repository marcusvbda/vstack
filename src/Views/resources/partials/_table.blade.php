<?php
$crud_buttons = [
    "code"         => null,
    "can_view"     => $resource->canView(),
    "can_update"   => $resource->canUpdate(),
    "can_delete"   => $resource->canDelete(),
    "route"        => null
];
$list_type = \marcusvbda\vstack\Models\ResourceConfig::where("data->user_id", $user->id)->where("resource", $resource->id)->where("config", 'list_type')->first();
$list_types = @$resource->listType() ? $resource->listType() : ["table"];
$order_by = @$_GET["order_by"];
$order_type = @$_GET["order_type"]=="desc" ? "asc" : "desc";
$session_list_type = @$list_type->data->type ? $list_type->data->type : $list_types[0];
if(in_array($session_list_type,$list_types)) $list_type = $session_list_type;
$table = $resource->table();
$table_keys = array_keys($table);
?>

@include($resource->viewListBlade())
