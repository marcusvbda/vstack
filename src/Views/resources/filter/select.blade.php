<el-select 
    v-model='{{"filter.$index"}}' 
    size='medium' 
	class='w-100' 
	@change='showConfirm'
    filterable 
    clearable
    placeholder='{{$placeholder}}'
    :multiple='@json($multiple)'
    v-bind:class='{"withFilter" : {{"filter.$index"}} }'>
    @foreach ($options as $option) 
        <el-option :value='{{strval($option->value)}}' label='{{$option->label}}'>
            {!! @$option->html !!}
        </el-option>
    @endforeach
</el-select>
