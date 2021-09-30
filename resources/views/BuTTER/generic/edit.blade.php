@extends('BuTTER.layout')

@section('content')

	<h1 class='title'>Edit {{$item->displayName()}} </h1>

	@include('BuTTER.components.patch')

	<br>

@endsection
