@php
	if($extra['column'])
		$divtype = 'column';
	else
		$divtype = 'field';
@endphp
<div class="{{$divtype}}">
	<label class='label' for='title'>@if($extra['title'])
{{$extra['title']}}
@else
{{$name}}
@endif</label>

	<div class='control'>
		<textarea name='{{$name}}' class='textarea' @if($extra['required']) required @endif></textarea>
	</div>
	<p class='help {{$extra['helpsup']}}'>{{$extra['help']}}</p>
</div>