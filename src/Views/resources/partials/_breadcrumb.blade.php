@php 
	$breadcrumbs = $resource->breadcrumbLabels();
	$prepend = config("vstack.prepend_breadcrumb",["Dashboard" => "/admin"]);
	$bc = [
		$breadcrumbs["list"] => @$resource->route()
	];
	$bc = array_merge($prepend,$bc);
	if(@$report_mode) {
		$bc[$breadcrumbs["report"]] = @$resource->report_route();
	}
	if(@$raw_type) {
		$bc[$breadcrumbs[$raw_type]] = $current_route;
	}
@endphp
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" id="breadcrumb">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    @foreach($bc as $key=>$value)
						<li class="breadcrumb-item" id="breadcrumb-item-{{ $key }}">
							<a href="{{$value}}" class="link" id="breadcrumb-item-link">{{$key}}</a>
						</li>
                    @endforeach
                </ol>
            </nav>
        </nav>
    </div>
</div>
