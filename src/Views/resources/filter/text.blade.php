@if(!@$mask) 
    <el-input 
        clearable size='medium' 
        v-model='{{"filter.$index"}}' 
        @change='makeNewRoute'
        type='text' class='w-100'      
        placeholder='{{$placeholder}}'
        v-bind:class='{"withFilter": {{"filter.$index"}}}'>
    </el-input>
@else
<div  class="w-100 el-input el-input--medium el-input--suffix" v-bind:class='{"withFilter": {{"filter.$index"}}}'>
    <input type="text" 
    autocomplete="off" 
    v-model='{{"filter.$index"}}'
    @change='makeNewRoute'
    type='text'
    placeholder='{{$placeholder}}'
    v-mask='{{json_encode($mask)}}'
        class="el-input__inner">
</div>
@endif
