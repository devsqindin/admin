@extends('BuTTER.layout')

@section('content')

	<h1 class='title'>Add {{$item->info['displayName']}}</h1>

	@include('BuTTER.components.input')

	<br>
	
@endsection
