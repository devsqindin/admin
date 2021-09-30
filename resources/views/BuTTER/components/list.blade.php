<div>
	<br>
	@if (count($items))
		<p></p>

			@foreach ($items as $item)
			
			<a class='button is-rounded' href='{{$link}}/{{$item->id}}'>{{$item->displayName()}}</a>

			@endforeach	
			<a href="{{$link}}/create">
				<button class='button is-success is-rounded'>
					<span class='icon is-small'>
						<i class='fas fa-plus'></i>
					</span>
				</button>
			</a>

	@else
		
		<p>
			<h2 class="label">Empty</h2>
		</p>
		<a href="{{$link}}/create">
			<button class='button is-success is-rounded'>
				<span class='icon is-small'>
					<i class='fas fa-plus'></i>
				</span>
			</button>
		</a>
	@endif
	<br>
	<br>
	<br>
</div>