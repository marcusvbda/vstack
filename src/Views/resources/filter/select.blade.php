<el-select 
    v-model='{{"filter.$index"}}' 
    size='medium' 
    class='w-100' 
    @change='makeNewRoute'
    filterable 
    clearable
    placeholder='{{$placeholder}}'
    v-bind:class='{"withFilter" : {{"filter.$index"}} }'>
    @foreach ($options as $option) 
        <el-option :value='{{strval($option->value)}}' label='{{$option->label}}'/>
    @endforeach
</el-select>
