<div class='box'>
	<form method="POST" action='{{$link}}/{{$item->id}}' enctype="multipart/form-data">
		@method('PATCH')
		@csrf

		@php
			$catr = $model->atr;
			$col = false
		@endphp

		@if(count($catr))
			@foreach ($catr as $name=>$info)
				@php
					$satr = $model->atr[$name];
					$type = $info['type'];
					$extra = $model->fillDetails($name)->form;
				@endphp
				@if($extra['OnEdit'])
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
						@include('BuTTER.components.editlocal.'.$type)
					@else
						@include('BuTTER.components.editlocal.'.$extra['replacement'])
					@endif
				@endif
			@endforeach
		@else
			Define $atr (attributtes array) on the model class
		@endif
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
					$connectlist = $connect[$name];
					$fitem = $connectlist->find($id);
				@endphp
				@include('BuTTER.components.editlocal.foreign.'.$extra['foreignType'])
			@endforeach
		@endif
		<div class='is-grouped'>
			<button type="submit" class='button is-success'><span class='icon is-small'>
				<i class='fas fa-check'></i></span><span>Save</span></button>
			<a class='button is-danger' href="{{$link}}">Back</a>
		</div>
	</form>
</div>