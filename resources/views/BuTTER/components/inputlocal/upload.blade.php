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

    <div class='file'>
        <label class='file-label'>
            <input class='file-input' type='file' name='{{$name}}' accept='image/*'>
            <span class="file-cta">
                <span class="file-icon">
                    <i class="fas fa-upload"></i>
                </span>
                <span class="file-label">
                    Choose a file...
                </span>
            </span>
        </label>
    </div>
    <p class='help {{$extra['helpsup']}}'>{{$extra['help']}}</p>

</div>
