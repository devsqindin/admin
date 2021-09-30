@php
	foreach($extra['d-option'] as $index => $pie) {
		if($item->$name==$pie)
			$k = $index;
	}
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
	<div>{{$extra['d-choice'][$k]}}</div>
</div>