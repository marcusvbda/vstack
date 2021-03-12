@extends("templates.admin")
@section('title',$resource->label())
@php
	$current = $data['page_type']." de ".$resource->singularLabel();
    $current_route = $resource->route()."/".@$content->code;
@endphp
@section('breadcrumb')
@include("vStack::resources.partials._breadcrumb")
@endsection
@section('content')
    @if(@$resource->beforeViewSlot())
        {!! @$resource->beforeViewSlot() !!}
    @endif
    <resource-view 
        :data="{{json_encode($data)}}" 
        :form="{{json_encode($content)}}" 
        redirect="{{$current_route}}" 
    >
        <template slot="title">
            <h4>@if( @$resource->icon() ) <span class="{{$resource->icon()}} mr-2"></span> @endif {{$data["page_type"]}} de {{$resource->singularLabel()}}</h4>
        </template>
	</resource-view>
	@if(@$resource->afterViewSlot())
        {!! @$resource->afterViewSlot() !!}
    @endif
@endsection
