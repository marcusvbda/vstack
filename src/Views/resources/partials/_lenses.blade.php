<div class="d-flex flex-row mb-1" style="font-size:14px;">
    <?php 
        $current_len = @$_GET["current_len"] ? @$_GET["current_len"] : "all";
        $lenses = $resource->lenses(); 
    ?>
    @if($current_len=="all")   
        <b>Todos</b>
    @else 
        <?php
            $query = request()->query();
            foreach($lenses as $len_key=>$len_value) if(isset($query[$len_value["field"]])) unset($query[$len_value["field"]]);
            if(isset($query["current_len"])) unset($query["current_len"]);
            $route = route("resource.index",array_merge(["resource"=>$resource->id],$query));
        ?>
        <a href="{{$route}}">Todos</a>
    @endif
    @foreach($lenses as $len_key=>$len_value)
        <?php
            $query = request()->query();
            $query[$len_value["field"]] = $len_value["value"];
            $query["current_len"] = $len_key;
            $route = route("resource.index",array_merge(["resource"=>$resource->id],$query));
        ?>
        <div class="mx-2" style="opacity:.5;">|</div>
        @if($current_len==$len_key)  
        <b>{!! $len_key !!}</b>
        @else
            <a href="{{$route}}">{!! $len_key !!}</a>
        @endif
    @endforeach
</div>