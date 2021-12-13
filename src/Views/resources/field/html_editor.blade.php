<v-grapes 
    description='{{$description}}'
    label='{{$label}}'
    {!! $eval !!}  
    v-model='{{"form.$field"}}' 
    :errors='{{"errors.$field ? errors.$field : null"}}'     
    :default='@json($default)'
    mode='{{$mode}}'  
    :blocks='@json($blocks)'       
    :settings='@json($settings)'       
    :height='{{ $height }}'       
/>
