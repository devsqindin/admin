@php
	$name = $extra['foreignuse'];
@endphp
@php
	if($extra['column'])
		$divtype = 'column';
	else
		$divtype = 'field';
@endphp
<div class="{{$divtype}} {{$extra['divsize']}}">
	<h2 class='label'>@if($extra['title'])
{{$extra['title']}}
@else
{{$name}}
@endif</h2>
	<div>{{$fitem->$name}}</div>
</div>