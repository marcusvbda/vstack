{{-- <div>
    <div class="card mb-4">
        <div class="card-body">
@if(@$label)
                <div class="row d-flex justify-content-center mb-3">
                    <div class="col-12">
                        <h4>{!! $label !!}</h4>
                    </div>
                </div>
@endif

            <div class="row mb-3">
                <div class="col-12">
                    <div class="row-responsive-table">
                        <table class="table table-striped">
                            <tbody>{!! $inputs !!}</tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div> --}}
<resource-crud-card label="{{ @$label }}" :advanced="{{ json_encode(@$advanced ? true : false) }}">
    <div class="row-responsive-table">
        <table class="table table-striped">
            <tbody>{!! $inputs !!}</tbody>
        </table>
    </div>
</resource-crud-card>
