<div class="w-100" data-aos="fade-up">
	@if(@$resource->beforeViewSlot())
	{!! @$resource->beforeViewSlot() !!}
@endif
</div>
<resource-view 
	:data="{{json_encode($data)}}" 
	:form="{{json_encode($content)}}" 
	redirect="{{$current_route}}" 
>
	<template slot="title">
		<h4>@if( @$resource->icon() ) <span class="{{$resource->icon()}} mr-2"></span> @endif {{$data["page_type"]}} de {{$resource->singularLabel()}}</h4>
	</template>
</resource-view>
@if(@$resource->afterViewSlot())
	{!! @$resource->afterViewSlot() !!}
@endif