@extends("templates.admin")
@section('title',$resource->label())
@section('breadcrumb')
@include("vStack::resources.partials._breadcrumb")
@endsection
@section('content')
@php $user = Auth::user(); @endphp
@include($resource->indexBlade())
