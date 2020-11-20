<?php 
    Session::forget('breadcrumb');
	Session::push('breadcrumb',["Dashboard" => asset('admin')]);
	Session::push('breadcrumb',[$resource->label() => @$resource->route()]);
	if($report_mode) Session::push('breadcrumb',['Relatório de '.$resource->label() => @$resource->report_route()]);
?>
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <?php $bc = Session::get('breadcrumb');?>
                    @foreach($bc as $rows)
                    @foreach($rows as $key=>$value)
                    <li class="breadcrumb-item">
                        <a href="{{$value}}" class="link">{{$key}}</a>
                    </li>
                    @endforeach
                    @endforeach
                </ol>
            </nav>
        </nav>
    </div>
</div>