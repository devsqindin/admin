@php
	if($extra['column'])
		$divtype = 'column';
	else
		$divtype = 'field';
@endphp
<div class="{{$divtype}} {{$extra['divsize']}}">
	<label class='label'>@if($extra['title'])
{{$extra['title']}}
@else
{{$name}}
@endif</label>
	<div>
		{{$item->$name}}
	</div>
</div>