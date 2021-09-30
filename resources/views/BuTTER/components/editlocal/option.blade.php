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
	<div>
		<div class='select'>
			<select name='{{$name}}' @if($extra['required']) required @endif>
				<option value=''>...</option>
				@for($i=0;$i<count($extra['d-option']);$i++)
					<option @if($item->$name==$extra['d-option'][$i])
						selected
						@endif
					value='{{$extra['d-option'][$i]}}'>{{$extra['d-choice'][$i]}}</option>
				@endfor
			</select>
		</div>
	</div>
</div>