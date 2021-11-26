<v-html-editor class='mb-3'  
    description='{{$description}}'
    placeholder='{{$placeholder}}'
    label='{{$label}}'                                                              
    mode='{{$mode}}'         
    direction='{{ $direction }}'                                                    
    :errors='{{"errors.$field ? errors.$field : null"}}'  
	:default='@json($default)'    
	:show_btns='@json($show_btns)'    
	v-model='{{"form.$field"}}'   
    {!! $eval !!}                                       
/>