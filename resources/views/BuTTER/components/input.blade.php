<div class='box'>
	<form method="POST" action='{{$link}}' enctype="multipart/form-data">

		@csrf

		@php
			$catr = $item->atr;
			$col = false
		@endphp

		@if(count($catr))
			@foreach($catr as $name=>$info)
				@php
					$atr = $item->atr[$name];
					$type = $info['type'];
					$extra = $item->fillDetails($name)->form;
				@endphp
				@if($extra['OnCreate'])
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
						@include('BuTTER.components.inputlocal.'.$type)
					@else
						@include('BuTTER.components.inputlocal.'.$extra['replacement'])
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
		@if(isset($item->foreign))
			@php
				$foreign = $item->foreign;
			@endphp
			@foreach ($foreign as $name=>$info)
				@php
					$extra = $item->fillDetails($name,true)->form;
					$id = $item->$name;
					$connectlist = $connect[$name];
					$fitem = $connectlist->find($id);
				@endphp
				@include('BuTTER.components.inputlocal.foreign.'.$extra['foreignType'])
			@endforeach
		@endif
		<div class='is-groupped'>
			<button type="submit" class='button is-success'><span class='icon is-small'>
				<i class='fas fa-plus'></i></span><span>Add</span></button>
			<a class='button is-danger' href="{{$link}}">Back</a>
		</div>
	</form>
</div>