@extends("templates.admin")
@section('title',$resource->label())
@section('breadcrumb')
<?php 
    $routes = [];
    $current = $data['page_type']." de ".$resource->singularLabel();
    $current_route = $resource->route()."/".@$content->code;
    Session::push("breadcrumb",[$current=>$current_route]);
    $bc = Session::get("breadcrumb");
    Session::forget('breadcrumb');
	$indexes = [];
    foreach($bc as $row)
    {
        foreach($row as $key=>$value)
        {
            if(!in_array($key,$indexes))
            {
                $indexes[] = $key;
                Session::push("breadcrumb",[$key=>$value]);
                $routes[] = $value;
            }
            if($key==$current) {
                break 2;
            };
        }
    }
    $bc = Session::get("breadcrumb");
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
                                <a  href="{{$value}}" class="link">{{$key}}</a>
                            </li>
                        @endforeach
                    @endforeach
                </ol>
            </nav>
        </nav>
    </div>
</div>
@endsection
@section('content')
    @if(@$resource->beforeViewSlot())
        {!! @$resource->beforeViewSlot() !!}
    @endif
    <resource-view 
        :data="{{json_encode($data)}}" 
        redirect="{{$current_route}}" 
        :breadcrumb="{{json_encode($routes)}}" 
    >
        <template slot="title">
            <h4>@if( @$resource->icon() ) <span class="{{$resource->icon()}} mr-2"></span> @endif {{$data["page_type"]}} de {{$resource->singularLabel()}}</h4>
        </template>
    </resource-view>
@endsection