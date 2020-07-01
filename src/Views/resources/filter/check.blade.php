<el-checkbox 
    size='medium' 
    class='d-flex align-items-center'
    v-model='{{"filter.$index"}}' 
    @change='makeNewRoute' >
    {!! $text !!}}
</el-checkbox>