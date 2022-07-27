<div class="d-flex flex-row my-1" style="font-size:14px;" id="resource-lenses">
    @php
        $current_len = @$_data["current_len"] ? @$_data["current_len"] : "all";
        $lenses = $resource->lenses(); 
    @endphp
    @if($current_len=="all")   
        <b>Todos</b>
    @else 
        @php
            $query = request()->all();
            foreach($lenses as $len_key=>$len_value) {
                if(isset($query[$len_value["field"]])) {
                    unset($query[$len_value["field"]]);
                }
            }
            if(isset($query["current_len"])) {
                unset($query["current_len"]);
            }
            $route = secure_url(route("resource.index",array_merge(["resource"=>$resource->id],$query)));
        @endphp
        <a href="{{$route}}">Todos</a>
    @endif
    @foreach($lenses as $len_key=>$len_value)
        @php
            $query = request()->query();
			$field = $len_value["field"];
			$other_fields = array_filter($lenses,function($row) use ($field){
				return $row["field"] != $field;
			});
            $query[$len_value["field"]] = $len_value["value"];
			$query["current_len"] = $len_key;
			foreach($other_fields as $other) {
				if(@$query[$other["field"]]) {
                    unset($query[$other["field"]]);
                }
			}
            $route = secure_url("resource.index",array_merge(["resource"=>$resource->id],$query)));
        @endphp
        <div class="mx-2" style="opacity:.5;">|</div>
        @if($current_len==$len_key)  
            <b id="resource-lenses-{{ $len_key }}">{!! $len_key !!}</b>
        @else
            <a id="resource-lenses-{{ $len_key }}" href="{{$route}}">
                {!! $len_key !!}
            </a>
        @endif
    @endforeach
</div>
