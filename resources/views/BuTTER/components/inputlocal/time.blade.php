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
		<input type="time" class='input' name="{{$name}}" @if($extra['required']) required @endif>
	</div>
	<p class='help {{$extra['helpsup']}}'>{{$extra['help']}}</p>
</div>