@extends('layouts.admin')

@push('js')
<script src="{{asset('plugins/crud.js')}}"></script>
<script type="text/javascript">
$(function(){
})
</script>
@endpush

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
    		<div class="card">
    			<div class="card-body">
    				@include('forms.pergunta')
    			</div>
    		</div>
    	</div>
    </div>
</div>
@endsection