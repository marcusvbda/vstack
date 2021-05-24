@extends("templates.admin")
@section('title',$resource->label())
@php
	$cards = $data["fields"];
	$routes = [];
	$raw_types = ["Edição" => "edit","Cadastro" => "create", "Clonagem" => "clone"];
	$raw_type = $raw_types[$data["page_type"]];
	$current = $data['page_type']." de ".$resource->singularLabel();
	if(in_array($raw_type,["clone","edit"])) {
		$current_route = $resource->route()."/".@$content->code."/".$raw_type;
	} else {
		$current_route = $resource->route()."/".$raw_type;
	}
@endphp
@section('breadcrumb')
@include("vStack::resources.partials._breadcrumb")
@endsection
@section('content')
@include(@$data["page_type"] == 'edit' ? $resource->editBlade() : $resource->createBlade() )
@endsection
