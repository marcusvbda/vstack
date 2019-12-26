@extends("templates.admin")
@section('title',$resource->label())
@section('breadcrumb')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.home')}}" class="link">Dashboard</a></li>
                    @if(@$_GET["params"]["redirect_back"])
                        <li class="breadcrumb-item"><a href="{{$_GET["params"]["redirect_back"]}}" class="link">{{$_GET["params"]["redirect_back"]}}</a></li>
                    @else
                        @if($resource->canViewList())
                            <li class="breadcrumb-item"><a href="{{$resource->route()}}" class="link">{{$resource->label()}}</a></li>
                        @endif
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">{{$data["page_type"]}} de {{$resource->singularLabel()}}</li>
                </ol>
            </nav>
        </nav>
    </div>
</div>
@endsection
@section('content')
    <?php $data["params"] = @$_GET["params"]; ?>
    <resource-view :data="{{json_encode($data)}}" >
        <template slot="title">
            <h4>@if( @$resource->icon() ) <span class="{{$resource->icon()}} mr-2"></span> @endif {{$data["page_type"]}} de {{$resource->singularLabel()}}</h4>
        </template>
    </resource-view>
@endsection