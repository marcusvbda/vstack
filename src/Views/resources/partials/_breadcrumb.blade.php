<?php
$breadcrumbs = $resource->breadcrumbLabels();
$prepend = config("vstack.prepend_breadcrumb", ["Dashboard" => "/admin"]);
$bc[] = [
	"title" => $breadcrumbs["list"],
	"route" => @$resource->route()
];
$bc = array_merge($prepend, $bc);
if (@$report_mode) {
	$bc[] = [
		"title" => $breadcrumbs["report"],
		"route" => @$resource->report_route()
	];
}
if (@$raw_type) {
	$bc[] = [
		"title" => $breadcrumbs[$raw_type],
		"route" => @$resource->route($raw_type)
	];
}
?>
<vstack-breadcrumb :items='@json($bc)'></vstack-breadcrumb>