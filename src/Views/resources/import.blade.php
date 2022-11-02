@extends("templates.admin")
@php
    $page_title = $data["resource"]["import_settings"]["page_title"];
    $label = $data["resource"]["label"];
    $resource_route = $data["resource"]["route"];
@endphp
@section('title',$page_title)
@section('breadcrumb')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{asset('admin')}}" class="link">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{$resource_route}}" class="link">{{$label}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page_title}}</li>
                </ol>
            </nav>
        </nav>
    </div>
</div>
@endsection
@section('content')
<resource-import :data="{{json_encode($data)}}"></resource-import>
@endsection