<el-input 
    clearable size='medium' 
    v-model='{{"filter.$index"}}' 
    @change='makeNewRoute'
    type='text' class='w-100'      
    placeholder='{{$placeholder}}'
    v-bind:class='{"withFilter": {{"filter.$index"}}}'>
</el-input>
