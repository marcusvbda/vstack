@extends("templates.admin")
@php
	$raw_types = ["Edição" => "edit","Cadastro" => "create"];
	$current = $data['page_type']." de ".$resource->singularLabel();
    $current_route = $resource->route()."/".@$content->code."/".$raw_types[$data["page_type"]];
@endphp
@section('title',$resource->label())
@section('breadcrumb')
@include("vStack::resources.partials._breadcrumb")
@endsection
@section('content')
<div class="row mt-2">
    <div class="col-12">
        <div class="d-flex flex-row justify-content-between mb-3">
            <h4>@if( @$resource->icon() ) <span class="{{$resource->icon()}} mr-2"></span> @endif {{$data["page_type"]}} de {{$resource->singularLabel()}}</h4>
        </div>
    </div>
</div>
@if(($data["page_type"]=="Edição") && $resource->useTags())
	<resource-tags-input resource='{{$resource->id}}' resource_code='{{@$content->code}}'></resource-tags-input>
@endif
@if(@!$data->id)
    @if(@$resource->beforeCreateSlot())
        {!! @$resource->beforeCreateSlot() !!}
    @endif
@else 
    @if(@$resource->beforeEditSlot())
    {!! @$resource->beforeEditSlot() !!}
    @endif
@endif
<resource-crud 
    :data="{{json_encode($data)}}"  
    :params="{{json_encode($params)}}"  
    redirect="{{$current_route}}" >
    @if(@!$data->id && !@$data["id"])
        @if(@$resource->afterCreateSlot())
            <template slot="aftercreate">
                {!! @$resource->afterCreateSlot() !!}
            </template>
        @endif
    @else
        @if(@$resource->afterEditSlot())
            <template slot="afteredit">
                {!! @$resource->afterEditSlot() !!}
            </template>
        @endif
    @endif
</resource-crud>


@endsection
