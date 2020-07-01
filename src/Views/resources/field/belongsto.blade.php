<v-select 
    v-model='{{"form.$field"}}' 
    list_model='{{$model}}' 
    label='{{$label}}' 
    :disabled='{{$disabled}}'   
    description='{{$description}}'  
    v-show='{{$visible}}'             
    placeholder='{{$placeholder}}' 
    route_list='{{$route_list}}' 
    :option_list='{{$options}}' 
    :errors='{{"errors.$field ? errors.$field : false"}}'     
/>