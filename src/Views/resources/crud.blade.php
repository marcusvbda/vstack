@extends('templates.admin')
@section('title', $resource->label())
@php
    $cards = $data['fields'];
    $routes = [];
    $raw_type = \marcusvbda\vstack\Vstack::getPageType(@$data['page_type']);
    if (in_array($raw_type, ['clone', 'edit'])) {
        $current_route = $resource->route() . '/' . @$content->code . '/' . $raw_type;
    } else {
        $current_route = $resource->route() . '/' . $raw_type;
    }
@endphp
@section('breadcrumb')
    @include('vStack::resources.partials._breadcrumb')
@endsection
@section('content')
    @include(@$raw_type == 'edit' ? $resource->editBlade() : $resource->createBlade())
@endsection
