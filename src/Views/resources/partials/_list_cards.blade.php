<div class="card">
    <div class='card-header d-flex flex-row align-items-center justify-content-between'>
        <div>
            <?php  $text = getTextRow($table_keys[0],$row) ?>
            @if($resource->canView())
                <div><a href="{{$resource->route().'/'.$row->code}}" class="link"><b>{!! $text ? $text : '-' !!}</a></b></div>
            @else
                <div>{!! $text ? $text : '-' !!}</div>
            @endif
        </div>
        <resource-crud-buttons :data="{{json_encode($crud_buttons)}}" id="{{$row->id}}"></resource-crud-buttons>
    </div>
    <div class="card-body">
        <div class="row  d-flex flex-row flex-wrap" >
            @for($i = 1; $i < count($table_keys);$i++)
                <div class="col-md-6 col-sm-12 pb-4">
                    <?php $text = getTextRow($table_keys[$i],$row);?>
                    <div>{!! $text ? $text : '-' !!}</div>
                </div>
            @endfor
        </div>
    </div>
</div>
