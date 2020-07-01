<v-tags multiple 
    :allowcreate='true' 
    :required='{{$required}}' 
    v-model='{{"form.$field"}}' 
    label='{{$label}}' 
    :disabled='{{$disabled}}' 
    :unique='{{$unique}}'                    
    placeholder='{{$placeholder}}' 
    :errors='{{"errors.$field ? errors.$field : false"}}'   
/>