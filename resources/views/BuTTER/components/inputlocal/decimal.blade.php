@php
	if($extra['column'])
		$divtype = 'column';
	else
		$divtype = 'field';
@endphp
<div class="{{$divtype}} {{$extra['divsize']}}">
	<label class='label' for='title'>@if($extra['title'])
{{$extra['title']}}
@else
{{$name}}
@endif</label>

	<div class='control'>
		<input type="number" class='input' 
		@if(!is_null($extra['min']))
		min='{{$extra['min']}}'
		@endif 
		@if(!is_null($extra['max']))
		max='{{$extra['max']}}'
		@endif 
		@if(!is_null($extra['step']))
		step='{{$extra['step']}}'
		@endif 
		placeholder="{{$extra['placeholder']}}" name="{{$name}}" @if($extra['required']) required @endif>
	</div>
	<p class='help {{$extra['helpsup']}}'>{{$extra['help']}}</p>
</div>