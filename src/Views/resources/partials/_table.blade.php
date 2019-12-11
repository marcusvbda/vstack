<div class="table-responsive-sm">
    <table class="table table-striped hovered resource-table table-hover mb-0">
        <thead>
            <tr>
                <?php
                    $order_type = @$_GET["order_type"]=="desc" ? "asc" : "desc";
                    $order_by = @$_GET["order_by"];
                    $table = $resource->table();
                ?>
                @foreach($table as $key=>$value)
                    <?php
                        $size = isset($value['size']) ? $value['size'] : 'auto';
                        $sortable_index = isset($value['sortable_index']) ? $value['sortable_index'] : $key;
                    ?>
                    <th width="{{$size}}">
                        @if(@$value["sortable"]!==false)
                            <a href="{{ResourcesHelpers::sortLink($resource->route(),request()->query(), $sortable_index,$order_type)}}" 
                                class="d-flex flex-row align-items-center link-sortable">
                                <div class="link">{{isset($value["label"]) ? @$value["label"] : $value}}</div>
                                <div class="ml-auto d-flex flex-row">
                                    <span class="sort-icon el-icon-arrow-down @if($order_type=='asc' && $order_by==$sortable_index ) active @endif"></span>
                                    <span class="sort-icon el-icon-arrow-up @if($order_type=='desc' && $order_by==$sortable_index) active @endif"></span>
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
                    @foreach($table as $key=>$value)
                        <td>
                            <div class="d-flex flex-column">
                                <?php 
                                    $indexes = explode("->",$key);
                                    $_val = $row;
                                    foreach($indexes as $i) {
                                        $_val = @$_val->{$i}; 
                                    }
                                    $text = @$_val ? $_val : @$row->{$value};
                                ?>
                                @if($show_btns) 
                                    @if($resource->canView())
                                        <div><a href="{{$resource->route().'/'.$row->code}}" class="link"><b>{!! $text ? $text : '-' !!}</a></b></div>
                                    @else
                                        <div>{!! $text ? $text : '-' !!}</div>
                                    @endif
                                    <?php
                                        $crud_buttons = [
                                            "code"         => $row->code,
                                            "can_view"     => $resource->canView(),
                                            "can_update"   => $resource->canUpdate(),
                                            "can_delete"   => $resource->canDelete(),
                                            "route"        => $resource->route()."/".$row->code
                                        ];
                                    ?>
                                    <resource-crud-buttons :data="{{json_encode($crud_buttons)}}" id="{{$row->id}}"></resource-crud-buttons>
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