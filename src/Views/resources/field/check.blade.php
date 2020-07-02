<tr>
    <td>{{$label}}</td>
    <td>
        <div class='d-flex flex-column'>                                                   
            <el-switch             
                :disabled='{{$disabled}}'                               
                class='ml-3'                          
                v-model='{{"form.$field"}}'                 
                active-color='{{$active_color}}'          
                inactive-color='{{$inactive_color}}'      
                active-text='{{$active_text}}'            
                inactive-text='{{$inactive_text}}'>       
            </el-switch> 
            @if(@$description)
                <br><small style='color:gray;' class='mt-1 pl-3'>{!! $description!!}</small>
            @endif
        </div>                             
    </td>                             
</tr>