<div>
	<br>
	@php
		$columns = $model->DTcolumns;
		if(isset($model->DToptions['functions'])){
			$tests = $model->DToptions['functions'];
		}else{
			$tests['create'] = false;
			$tests['edit'] = false;
			$tests['delete'] = false;

		}
		if(isset($model->DToptions['customs'])){
			$custom = $model->DToptions['customs'];
		}
		$link = $model->DToptions['link'];
	@endphp

	@if($tests['create'])

		@include((!isset($custom['create']['html']) || is_null($custom['create']['html'])) ? 'BuTTER.components.html.jsonformcreate' : $custom['create']['html'])

	@endif

	<table class="table cell-border display is-striped is-hoverable is-fullwidth" id="tuble">
			<thead>
			<tr>
				@foreach($columns as $column)
					<th>{{$column}}</th>
				@endforeach
				@if($tests['edit'] || $tests['delete'])
				<th>Action</th>
				@endif
			</tr>
			</thead>
		</table>


	@if($tests['edit'])
	
		@include((!isset($custom['edit']['html']) || is_null($custom['edit']['html'])) ? 'BuTTER.components.html.jsonformedit' : $custom['edit']['html'])

	@endif

	@if($tests['delete'])

		@include((!isset($custom['delete']['html']) || is_null($custom['delete']['html'])) ? 'BuTTER.components.html.jsonformdelete' : $custom['delete']['html'])	

	@endif
</div>

@push('js')
<script>
$(function() {
	var selected = [];
	var table = $('#tuble').DataTable({
		processing: true,
		serverSide: true,
		ajax: { url: '{{$link}}' },
		columns: [
				@foreach($columns as $column => $name)
					{ data: '{{$column}}', name: '{{$column}}'},
				@endforeach
				@if($tests['edit'] || $tests['delete'])
				{ data: null,
				orderable: false,
				width: '20%',
				className: "center",
				defaultContent: 
					@if($tests['edit'])
						@include((!isset($custom['edit']['button']) || is_null($custom['edit']['button'])) ? 'BuTTER.components.js.tablebuttons.edit' : $custom['edit']['button'])
					@endif
					@if($tests['edit'] and $tests['delete'])
					+
					@endif
					@if($tests['delete'])
						@include((!isset($custom['delete']['button']) || is_null($custom['delete']['button'])) ? 'BuTTER.components.js.tablebuttons.delete' : $custom['delete']['button'])
					@endif}
				@endif
			]
	});
	$('#tuble tbody').on('click', 'tr', function () {

		$('.is-selected').toggleClass('is-selected');
		
		$(this).toggleClass('is-selected');

	} );
	var selid = 0;
	@if($tests['edit'])
	
		@include((!isset($custom['edit']['js']) || is_null($custom['edit']['js'])) ? 'BuTTER.components.js.jsonformedit' : $custom['edit']['js'])
	
	@endif
	@if($tests['delete'])

		@include((!isset($custom['delete']['js']) || is_null($custom['delete']['js'])) ? 'BuTTER.components.js.jsonformdelete' : $custom['delete']['js'])

	@endif

	@if($tests['create'])
	
		@include((!isset($custom['create']['js']) || is_null($custom['create']['js'])) ? 'BuTTER.components.js.jsonformcreate' : $custom['create']['js'])

	@endif
});
</script>

@endpush