<?php
$crud_buttons = [
    "code"         => null,
    "can_view"     => $resource->canView(),
    "can_update"   => $resource->canUpdate(),
    "can_delete"   => $resource->canDelete(),
    "route"        => null
];

function getTextRow($key,$row) {
    $indexes = explode("->",$key);
    $_val = $row;
    foreach($indexes as $i) $_val = @$_val->{$i}; 
    $text = @$_val ? $_val : @$row->{$value};
    return $text;
}
$list_types = @$resource->listType() ? $resource->listType() : ["table"];
$show_btns = true;
$order_by = @$_GET["order_by"];
$order_type = @$_GET["order_type"]=="desc" ? "asc" : "desc";
$list_type = @$_GET["list_type"] ? $_GET["list_type"] : $list_types[0];
$table = $resource->table();
$table_keys = array_keys($table);
// dd("resource-table-style-".$resource->id,request()->cookie("resource-table-style-".$resource->id));
?>

<select-table-style id="{{$resource->id}}" :list_type="{{json_encode($list_types)}}"
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
                    <tr>
                        <?php $show_btns = true; ?>
                        @foreach($table_keys as $key)
                        <td @if($show_btns) width="{{$size}}" @endif>
                            <div class="d-flex flex-column">
                                <?php $text = getTextRow($key,$row);?>
                                @if($show_btns)
                                @if($resource->canView())
                                <div><a href="{{$resource->route().'/'.$row->code}}" class="link"><b>{!! $text ? $text :
                                            '-' !!}</a></b></div>
                                @else
                                <div>{!! $text ? $text : '-' !!}</div>
                                @endif
                                <?php
                                                    $crud_buttons['code'] = $row->code;
                                                    $crud_buttons['route'] = $resource->route()."/".$row->code;
                                                ?>
                                <resource-crud-buttons :data="{{json_encode($crud_buttons)}}" id="{{$row->id}}">
                                </resource-crud-buttons>
                                @else
                                <div>{!! $text ? $text : '-' !!}</div>
                                @endif
                            </div>
                        </td>
                        <?php $show_btns = false; ?>
                        @endforeach
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
                                            $crud_buttons['code'] = $row->code;
                                            $crud_buttons['route'] = $resource->route()."/".$row->code;
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
