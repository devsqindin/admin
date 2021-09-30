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
  <label class="radio">
    <input type="radio" name="{{$name}}" value=1>
    {{$extra['b-choice'][0]}}
  </label>
  <label class="radio">
    <input type="radio" name="{{$name}}" value=0>
    {{$extra['b-choice'][1]}}
  </label>
  <p class='help {{$extra['helpsup']}}'>{{$extra['help']}}</p>
</div>