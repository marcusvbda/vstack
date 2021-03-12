@php 
	$bc = [
		"Dashboard" => "/admin",
		$resource->label() => @$resource->route()
	];
	if(@$report_mode) {
		$bc['Relatório de '.$resource->label()] = @$resource->report_route();
	}
	if(@$data["page_type"]) {
		$bc[$current] =  $current_route;
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
