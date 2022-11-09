<v-upload class='mb-3'                                                                     
    label='{{$label}}'        
    description='{{$description}}'        
    uploadroute='{{$uploadroute}}'                                                    
    v-model='{{'form.'.$field}}'   
    :multiple='{{$multiple}}'   
    :preview='{{$preview}}'   
    :limit='{{$limit}}'    
    :is_image="{{ $is_image }}"                                                  
    accept='{{$accept}}'                        
    :sizelimit='{{ $sizelimit }}'             
    :auto_set_name='{{ $auto_set_name }}'                 
    :errors='{{"errors.$field ? errors.$field : false"}}'    
    id="resource-input-upload-{{ $field }}" 
    {!! $eval !!}                          
>
<template #prepend-slot>
    {!! $slot_top !!}
</template>
<template #append-slot>
    {!! $slot_bottom !!}
</template>
</v-upload>