@extends("templates.admin")
@section('title',$resource->label())
<?php 
$has_summernote = false;
foreach($resource->fields() as $cards)
{
    foreach($cards->inputs as $field)
    {
        if($field->options["type"]=="summernote")
        {
            $has_summernote = true;
            break;
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
                    <li class="breadcrumb-item"><a href="{{route('admin.home')}}" class="link">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{$resource->route()}}" class="link">{{$resource->label()}}</a></li>
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
<resource-crud :data="{{json_encode($data)}}"></resource-crud>
@endsection