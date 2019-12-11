@extends("templates.admin")
@section('title',"Cards Customizados")
@section('content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.home')}}" class="link">Dashboard</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="{{$resource->route()}}">{{$resource->label()}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Cards Customizados</li>
                </ol>
            </nav>
        </nav>
    </div>
</div>

<div class="row mb-3 mt-2">
    <div class="col-12 d-flex flex-row align-items-center">
        <h4 class="mb-1"><b class="el-icon-data-line mr-2"></b> Cards Customizados</h4> 
        <div class="d-flex flex-row">
            @if(count($cards)>0)
                <a href="{{$resource->route()."/custom-cards/create"}}" class="btn btn-primary btn-sm-block btn-sm cursor-pointer ml-4 px-3 pr-4">
                    <span class="el-icon-plus mr-2"></span>Cadastrar
                </a>
            @endif
        </div>
    </div>
</div>

@if(count($cards)<=0)
    <div class="row mt-5">
        <div class="col-12">
            <div class="row d-flex justify-content-center">
                <div class="col-md-4 col-sm-12 d-flex flex-column text-center d-flex flex-column">
                    <h1><span class="el-icon-data-line mb-3" style="font-size: 200px;opacity:.3;"></span></h1>
                    <h4>Adicione seu primeiro card customizado</h4>
                    <span>Os cards customizados podem ajudar você a visualizar e mensurar melhor seus números</span>
                    <div>
                        <a class="btn btn-primary btn-sm-block mt-3 cursor-pointer" href="{{$resource->route()."/custom-cards/create"}}"><span class="el-icon-plus mr-2"></span>Cadastrar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="table-responsive-sm">
        <table class="table table-striped hovered resource-table table-hover mb-0">
            <thead>
                <tr><th></th></tr>
            </thead>
            <tbody>
                @foreach($cards as $row)
                    <tr>
                        <td>
                            <div class="d-flex flex-column">
                                <div>
                                    <preview-resource-card :metric="{{json_encode($row)}}" :key="{{$row->id}}" />
                                </div>
                                <?php
                                    $crud_buttons = [
                                        "code"         => $row->code,
                                        "can_view"     => false,
                                        "can_update"   => true,
                                        "can_delete"   => true,
                                        "route"        => $resource->route()."/custom-cards/".$row->code
                                    ];
                                ?>
                                <resource-crud-buttons :data="{{json_encode($crud_buttons)}}" id="{{$row->id}}"></resource-crud-buttons>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="row mt-3 d-flex align-items-center">
        @if($cards->total()>0)
            <div class="col-md-6 col-sm-12">{!! $resource->resultsFoundLabel() !!} {{ $cards->total() }}</div>
            <div class="col-md-6 col-sm-12 d-flex justify-content-end">
                {{$cards->appends(request()->query())->links()}}
            </div>
        @endif
    </div>
@endif
@endsection