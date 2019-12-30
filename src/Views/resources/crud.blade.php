@extends("templates.admin")
@section('title',$resource->label())
<?php 
$has_summernote = false;
$cards = $data["fields"];
for($i=0;$i<count($cards);$i++)
{
    $card = $cards[$i];
    for($y=0;$y<count($card->inputs);$y++)
    {
        $field = $card->inputs[$y];
        if($field->options["type"]=="summernote")
        {
            $has_summernote = true;
            break;
        }
        if($field->options["type"]=="resource-field")
        {
            if($data["page_type"]!="Edição") unset($data["fields"][$i]);
        }
    }
}
?>
@if($has_summernote)
    <link rel="stylesheet" type="text/css" href="http://cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/codemirror.min.css" />
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/codemirror.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/mode/xml/xml.js"></script>
@endif
@section('breadcrumb')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{asset('admin')}}" class="link">Dashboard</a></li>
                    @if(@$_GET["params"]["redirect_back"])
                        <li class="breadcrumb-item">
                            <a href="{{asset('/admin/'.$_GET["params"]["redirect_back"]) }}" class="link">
                                Página Anterior
                            </a>
                        </li>
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

<div class="row mt-2">
    <div class="col-12">
        <div class="d-flex flex-row justify-content-between mb-3">
            <h4>@if( @$resource->icon() ) <span class="{{$resource->icon()}} mr-2"></span> @endif {{$data["page_type"]}} de {{$resource->singularLabel()}}</h4>
        </div>
    </div>
</div>
<resource-crud :params="{{json_encode($params)}}" :data="{{json_encode($data)}}"></resource-crud>
@endsection