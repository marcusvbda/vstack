<?php
$crud_buttons = [
    "code"         => null,
    "can_view"     => $resource->canView(),
    "can_update"   => $resource->canUpdate(),
    "can_delete"   => $resource->canDelete(),
    "route"        => null
];

$list_types = @$resource->listType() ? $resource->listType() : ["table"];
$order_by = @$_GET["order_by"];
$order_type = @$_GET["order_type"]=="desc" ? "asc" : "desc";
$list_type = @$_GET["list_type"] ? $_GET["list_type"] : $list_types[0];
$session_list_type = @session()->get($resource->id . "_list_type");
if(in_array($session_list_type,$list_types)) $list_type = $session_list_type;
$table = $resource->table();
$table_keys = array_keys($table);
?>

@include($resource->viewListBlade())
