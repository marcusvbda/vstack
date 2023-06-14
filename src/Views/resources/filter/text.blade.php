@if (!@$mask)
    <el-input clearable size='medium' v-model='{{ "filter.$index" }}' @change='showConfirm' type='text' class='w-full'
        placeholder='{{ $placeholder }}' v-bind:class='{"withFilter": {{ "filter.$index" }}}'>
    </el-input>
@else
    <div class="w-full el-input el-input--medium el-input--suffix" v-bind:class='{"withFilter": {{ "filter.$index" }}}'>
        <input type="text" autocomplete="off" v-model='{{ "filter.$index" }}' @change='showConfirm'
            placeholder='{{ $placeholder }}' v-mask='{{ json_encode($mask) }}' class="el-input__inner">
    </div>
@endif
