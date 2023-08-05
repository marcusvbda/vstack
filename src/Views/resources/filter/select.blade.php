<el-select v-model='{{ "filter.$index" }}' size='medium' class='w-full' @change='showConfirm' filterable clearable
    placeholder='{{ $placeholder }}' :multiple='@json($multiple)'
    v-bind:class='{"withFilter" : {{ "filter.$index" }} }'>
    @foreach ($options as $option)
        @php
            $val = data_get($option, 'value');
            $valueIndex = is_numeric($val) ? ':value' : 'value';
        @endphp
        <el-option {{ $valueIndex }}='{{ strval($val) }}' label='{{ data_get($option, 'label') }}'>
            {!! @$option->html !!}
        </el-option>
    @endforeach
</el-select>
