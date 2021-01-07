<el-date-picker size='medium' class='w-100' clearable v-model='{{"filter.$index"}}' @change='makeNewRoute'
    type='date' :format="'dd/MM/yyyy'" :value-format="'{{ $value_format }}'" placeholder='{{$placeholder}}'>
</el-date-picker>
