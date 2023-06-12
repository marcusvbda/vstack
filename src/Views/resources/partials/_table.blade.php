<?php
$crud_buttons = [
    "code"         => null,
    "can_view"     => $resource->canView(),
    "can_update"   => $resource->canUpdate(),
    "can_delete"   => $resource->canDelete(),
    "route"        => null
];

$order_by = @$_GET["order_by"];
$order_type = @$_GET["order_type"] == "desc" ? "asc" : "desc";
$table = $resource->table();
$table_keys = array_keys($table);
?>

@include($resource->viewListBlade())