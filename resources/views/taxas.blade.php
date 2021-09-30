@extends('layouts.admin')

@push('css')
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
@endpush

@section('content')
<div class="card">
  <!-- /.card-header -->
  <div class="card-body">
    @include('forms.taxas')
  </div>
  <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection

@push('js')
<script src="{{asset('plugins/crud.js')}}"></script>
@endpush