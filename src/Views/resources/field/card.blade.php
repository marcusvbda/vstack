<resource-crud-card :form="form" append_content='{{ @$append_content }}' label="{{ @$label }}"
    description="{{ @$description }} " :advanced="{{ json_encode(@$advanced ? true : false) }}"
    index="{{ $index }}" {!! $eval !!}>
    <div class="row-responsive-table">
        <table class="table table-crud mb-0 w-full">
            <tbody>{!! $inputs !!}</tbody>
        </table>
    </div>
</resource-crud-card>
