@extends('templates.admin')
@php
    $page_title = $data['resource']['import_settings']['page_title'];
    $resource = ResourcesHelpers::find($data['resource']['resource_id']);
    $raw_type = 'import';
@endphp
@section('title', $page_title)
@section('breadcrumb')
    @include('vStack::resources.partials._breadcrumb')
@endsection
@section('content')
    <resource-import :data="{{ json_encode($data) }}"></resource-import>
@endsection
