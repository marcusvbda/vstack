@extends("templates.admin")
@section('title',$data["resource"]["label"])
@section('breadcrumb')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{asset('admin')}}" class="link">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{$data["resource"]["route"]}}" class="link">{{$data["resource"]["label"]}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Importação de {{$data["resource"]["label"]}}</li>
                </ol>
            </nav>
        </nav>
    </div>
</div>
@endsection
@section('content')
<resource-import :data="{{json_encode($data)}}"></resource-import>
@endsection