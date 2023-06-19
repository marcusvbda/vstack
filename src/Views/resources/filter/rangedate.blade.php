<el-date-picker size='medium' class='w-full' clearable v-model='{{ "filter.$index" }}' @change='showConfirm'
    type='daterange' :format="'dd/MM/yyyy'" :value-format="'{{ $value_format }}'"
    end-placeholder='{{ $end_placeholder }}' start-placeholder='{{ $start_placeholder }}'>
</el-date-picker>
