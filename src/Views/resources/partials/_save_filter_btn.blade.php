<?php
	use \marcusvbda\vstack\Models\ResourceConfig;
	$save_filter_data = ResourceConfig::where("data->user_id", @\Auth::user()->id)->where("resource", $resource->id)->where("config", "report_templates")->first();
	$filter_report_limit = $resource->reportLimitTemplates();
?>
<save-resource-filter-btn
	:config_data='@json($save_filter_data)' :filter_report_limit='@json($filter_report_limit)'
>
</save-resource-filter-btn>