@php 
	$breadcrumbs = $resource->breadcrumbLabels();
	$bc = [
		"Dashboard" => "/admin",
		$breadcrumbs["list"] => @$resource->route()
	];
	if(@$report_mode) {
		$bc[$breadcrumbs["report"]] = @$resource->report_route();
	}
	if(@$raw_type) {
		$bc[$breadcrumbs[$raw_type]] = $current_route;
	}
@endphp
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    @foreach($bc as $key=>$value)
						<li class="breadcrumb-item">
							<a href="{{$value}}" class="link">{{$key}}</a>
						</li>
                    @endforeach
                </ol>
            </nav>
        </nav>
    </div>
</div>
