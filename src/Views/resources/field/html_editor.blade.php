<v-html-editor class='mb-3'  
    description='{{$description}}'
    label='{{$label}}'
    uploadroute='{{$uploadroute}}'                                                                  
    mode='{{$mode}}'                                                             
    :errors='{{"errors.$field ? errors.$field : null"}}'  
	:default='@json($default)'    
	v-model='{{"form.$field"}}'                                      
/>