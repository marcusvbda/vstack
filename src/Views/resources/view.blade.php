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
	@include($resource->viewBlade())
@endsection
