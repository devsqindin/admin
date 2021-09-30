@extends('BuTTER.layout')

@section('content')

	<h1 class='title'>{{$model->info['displayName']}}</h1>

	@include('BuTTER.components.listdatatables')

	<br>

@endsection
