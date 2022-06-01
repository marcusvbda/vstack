<div class="card w-100">
    <div class='card-header d-flex flex-row align-items-center justify-content-between'>
        <div>
            @php 
                $code = \Hashids::encode($row->id);
                $content = $row_data["content"];
            @endphp
            @if($resource->canView())
                <div>
                    <b>
                        <a href="{{$resource->route().'/'.$code}}" class="link">
                            <div class="d-flex flex-column">
                                {!! $content['code'] !!}
                            </div>
                        </a>
                    </b>
                </div>
            @else
            <div>
                <div class="d-flex flex-column">
                    @foreach($row_data as $_row)
                        <div>
                            {!! $content['code'] !!}
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        <resource-crud-buttons :data="{{json_encode($crud_buttons)}}" id="{{$row->id}}"></resource-crud-buttons>
    </div>
    <div class="card-body">
        <div class="d-flex flex-column">
            @foreach($content as $key => $value)
                @if($key != 'id')
                    <div class="d-flex flex-row">
                        <div class="d-flex flex-column">
                            <div>
                                <b>{{$key}}</b>
                            </div>
                            <div>
                                {{$value}}
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>