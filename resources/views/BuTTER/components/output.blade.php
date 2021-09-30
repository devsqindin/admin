<div class='box'>
	@php
		$catr = $model->atr;
		$col = false
	@endphp

	@if(count($catr))
		@foreach($catr as $name=>$info)
			@php
				$satr = $model->atr[$name];
				$type = $info['type'];
				$extra = $model->fillDetails($name)->form;
			@endphp
			@if($extra['OnShow'])
					@if($col and $extra['newcolumn'])
						</div>
						@php
							$col = false;
						@endphp
					@endif
					@if(!$col and $extra['column'])
						<div class='columns'>
						@php
							$col = true;
						@endphp
					@endif
					@if($col and !$extra['column'])
						</div>
						@php
							$col = false;
						@endphp
					@endif
				@if(!$extra['override'])
					@include('BuTTER.components.showlocal.'.$type)
				@else
					@include('BuTTER.components.showlocal.'.$extra['replacement'])
				@endif
			@endif
		@endforeach
		@if($col)
			</div>
			@php
				$col = false
			@endphp
		@endif
		@if(isset($model->foreign))
			@php
				$foreign = $model->foreign;
			@endphp
			@foreach ($foreign as $name=>$info)
				@php
					$extra = $model->fillDetails($name,true)->form;
					$id = $item->$name;
					$fitem = $connect[$name]->find($id);
				@endphp
				@include('BuTTER.components.showlocal.foreign.'.$extra['foreignType'])
			@endforeach
		@endif
	@else
		Define $atr (attributtes array) on the model class
	@endif

	<div class='field is-grouped'>
			<div class='control'>
				<a class='button is-link' href="{{$link}}/{{$item->id}}/edit">
					<span class='icon'><i class='fas fa-edit'></i></span>
					<span>Edit</span>
				</a>
			</div>

			<div class='control'>
				<form method="POST" action='{{$link}}/{{$item->id}}'>

					@method('DELETE')
					@csrf

					<button class='button is-danger'><span class='icon is-small'>
						<i class='fas fa-times'></i></span><span>Delete</span>
					</button>
				</form>
			</div>

			<div class='control'>
				<a class='button' href="{{$link}}">
					Back
				</a>
			</div>
		</div>
</div>
