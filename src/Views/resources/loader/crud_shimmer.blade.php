@php
    $show_crud_rcard = $resource->showCrudRightCard();
@endphp
<div id="cud-loader">
    <div class="row">
        <div class="col-sm-12 @if($show_crud_rcard) col-md-9  @endif d-flex flex-row">
            <div class="shimmer" style="height: 600px;width: 100%;"></div>
        </div>
        @if($show_crud_rcard)
            <div class="col-sm-12 col-md-3 d-flex flex-row">
                <div class="shimmer" style="height: 190px;width: 100%;"></div>
            </div>
        @endif
    </div>
</div>