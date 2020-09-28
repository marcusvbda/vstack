<?php
$crud_buttons = [
    "code"         => null,
    "can_view"     => $resource->canView(),
    "can_update"   => $resource->canUpdate(),
    "can_delete"   => $resource->canDelete(),
    "route"        => null
];

$list_types = @$resource->listType() ? $resource->listType() : ["table"];
$show_btns = true;
$order_by = @$_GET["order_by"];
$order_type = @$_GET["order_type"]=="desc" ? "asc" : "desc";
$list_type = @$_GET["list_type"] ? $_GET["list_type"] : $list_types[0];
$session_list_type = @session()->get($resource->id . "_list_type");
if(in_array($session_list_type,$list_types)) $list_type = $session_list_type;
$table = $resource->table();
$table_keys = array_keys($table);
?>

<select-table-style id="{{$resource->id}}" :list_type="{{json_encode($list_types)}}" :has_lenses="{{json_encode(count($resource->lenses())>0)}}"
    selected_list_type="{{$list_type}}">
    <template slot="lenses">
        <div class="col-md-9 col-sm-12">
            @if($resource->lenses())
            @include("vStack::resources.partials._lenses")
            @endif
        </div>
    </template>
    <template slot="content">
        @if($list_type == "table")
        <div class="table-responsive-sm">
            <table class="table table-striped hovered resource-table table-hover mb-0">
                <thead>
                    <tr>
                        @foreach($table as $key=>$value)
                        <?php
                            $size = !$show_btns ? (isset($value['size']) ? $value['size'] : 'auto') : '390px';
                            $sortable_index = isset($value['sortable_index']) ? $value['sortable_index'] : $key;
                            $show_btns = false;
                        ?>
                        <th width="{{$size}}">
                            @if(@$value["sortable"]!==false)
                            <a href="{{ResourcesHelpers::sortLink($resource->route(),request()->query(), $sortable_index,$order_type)}}"
                                class="d-flex flex-row align-items-center link-sortable">
                                <div class="link">{{isset($value["label"]) ? @$value["label"] : $value}}</div>
                                <div class="ml-auto d-flex flex-row">
                                    <span
                                        class="sort-icon el-icon-arrow-down @if($order_type=='asc' && $order_by==$sortable_index ) active @endif"></span>
                                    <span
                                        class="sort-icon el-icon-arrow-up @if($order_type=='desc' && $order_by==$sortable_index) active @endif"></span>
                                </div>
                            </a>
                            @else
                            <div class="link-sortable">{{isset($value["label"]) ? @$value["label"] : $value}}</div>
                            @endif
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                    <?php 
                        $code = \Hashids::encode($row->id);
                    ?>
                    <tr is="get-resource-content" :cols={{count($table_keys)}} row_code="{{$code}}"
                        resource_route="{{$resource->route()}}" resource_id="{{$resource->id}}" row_id="{{$row->id}}"
                        type="resourceTableContent" :can_view="{{json_encode($resource->canView())}}"
                        :can_delete="{{json_encode($resource->canDelete())}}"
                        :can_update="{{json_encode($resource->canUpdate())}}">
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-4">
            @foreach($data->chunk(3) as $chunk)
            <div class="row">
                @foreach($chunk as $row)
                <div class="col-lg-4 col-sm-12 mb-3  d-flex align-items-stretch">
                    <?php
                        $code = \Hashids::encode($row->id);
                        $crud_buttons['code'] = $code;
                        $crud_buttons['route'] = $resource->route()."/".$code;
                    ?>
                    @include($resource->listCardView())
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
        @endif
    </template>
</select-table-style>
