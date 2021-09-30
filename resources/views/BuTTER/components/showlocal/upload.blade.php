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
    

    <img src="{{$item->folderPath().$item->$name}}" height="300" width='300'>

</div>
