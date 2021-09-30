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
	<div>
		<div class='select'>
			<select name='{{$name}}' @if($extra['required']) required @endif>
			@if(count($connectlist->all()))
				@for($i=0;$i<count($connectlist->all());$i++)
					@php
						$hold = $connectlist->all();
						$show = $extra['foreignuse'];
						$namex = $hold[$i]->$show;
						$id = $hold[$i]->id;
					@endphp
					<option value='{{$id}}'
					@if($fitem->id == $id)
						selected
					@endif
					>{{$namex}}</option>
				@endfor
			@else
				<option>{{$extra['foreignEmpty']}}</option>
			@endif
			</select>
		</div>
		@if($extra['foreignAdd'])
			<a href="{{$extra['foreignAddLink']}}"><button class='button is-link' type='button'>+</button></a>
		@endif
	</div>
</div>