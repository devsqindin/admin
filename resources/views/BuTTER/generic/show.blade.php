@extends('BuTTER.layout')

@section('content')

	<h1 class='title'>{{$item->displayName()}}</h1>

	@include('BuTTER.components.output')
	
	<br>

@endsection