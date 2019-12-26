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
                        <div class="link-sortable">{{isset($value["label"]) ? @$value["label"] : $value}}</div>
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
                                        <div><a href="{{$resource->route().'/'.$row->code.'?'.http_build_query(['params'=>$params])}}" class="link"><b>{!! $text ? $text : '-' !!}</a></b></div>
                                    @else
                                        <div>{!! $text ? $text : '-' !!}</div>
                                    @endif
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