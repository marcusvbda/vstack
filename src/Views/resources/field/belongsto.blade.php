<v-select 
    v-model='{{'form.'.$field}}' 
	label='{{$label}}' 
    :disabled='{{$disabled}}'   
    description='{{$description}}'  
	v-show='{{$visible}}'             
    placeholder='{{$placeholder}}' 
    :multiple='@json($multiple)' 
    route_list='{{$route_list}}' 
    :option_list='{{$options}}'  
	all_options_label="{{ $all_options_label }}"
	list_model='{{$model}}'
	:errors='{{"errors.$field ? errors.$field : false"}}'     
/>