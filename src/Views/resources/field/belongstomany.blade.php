<v-select 
    multiple 
    v-show='{{$visible}}' 
    :required='{{$required}}'
    v-model='{{"form.$field"}}' 
    list_model='{{$model}}' 
    label='{{$label}}' 
    :disabled='{{$disabled}}'  
    description='{{$description}}'                   
    placeholder='{{$placeholder}}' 
    route_list='{{$route_list}}' 
    :errors='{{"errors.$field ? errors.$field : false"}}'   
/>