<div class="card w-100">
    <div class='card-header d-flex flex-row align-items-center justify-content-between'>
        <div>
            <?php  
                $code = \Hashids::encode($row->id);
            ?>
            @if($resource->canView())
            <div>
                <b>
                    <a href="{{$resource->route().'/'.$code}}" class="link">
                        <get-resource-content :w="{{250}}" :h="{{20}}" resource_id="{{$resource->id}}"
                            row_id="{{$row->id}}" table_key="{{$table_keys[0]}}" type="resourceTableContent">
                        </get-resource-content>
                    </a>
                </b>
            </div>
            @else
            <div>
                <get-resource-content :w="{{250}}" :h="{{20}}" resource_id="{{$resource->id}}" row_id="{{$row->id}}"
                    table_key="{{$table_keys[0]}}" type="resourceTableContent">
                </get-resource-content>
            </div>
            @endif
        </div>
        <resource-crud-buttons :data="{{json_encode($crud_buttons)}}" id="{{$row->id}}"></resource-crud-buttons>
    </div>
    <div class="card-body">
        <div class="row  d-flex flex-row flex-wrap">
            @for($i = 1; $i < count($table_keys);$i++) <div class="col-md-6 col-sm-12 pb-4">
                <get-resource-content :w="{{250}}" :h="{{20}}" resource_id="{{$resource->id}}" row_id="{{$row->id}}"
                    table_key="{{$table_keys[$i]}}" type="resourceTableContent">
                </get-resource-content>
        </div>
        @endfor
    </div>
</div>
</div>