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
	@for($i=0;$i<count($extra['c-choice']);$i++)
	  <label class="radio">
	    <input type="radio" name="{{$name}}" value={{$extra['c-choice'][$i]}}>
	    {{$extra['c-option'][$i]}}
	  </label>
		@endfor
		<p class='help {{$extra['helpsup']}}'>{{$extra['help']}}</p>
</div>