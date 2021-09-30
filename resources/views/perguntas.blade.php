@extends('layouts.admin')

@push('css')
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
@endpush

@section('content')
<div class="card">
<!--   <div class="card-header">
    <h3 class="card-title">DataTable with default features</h3>
  </div> -->
  <!-- /.card-header -->
  <div class="card-body">
    @foreach($categs as $categ)
    <h5 style="font-weight:bold;color:blue;">{{$categ->pergunta}} <small>(<a href="{{URL::to('/')}}/admin/pergunta/{{$categ->id}}" style="color:blue;text-decoration:underline;">editar</a>)</small></h5>
    <table id="tuble{{$categ->id}}" class="table table-bordered table-striped">
      <thead>
      <tr>
        <th style="width:40%">Pergunta</th>
        <th>Resposta</th>
        <th></th>
      </tr>
      </thead>
    </table>
    <hr/>
    @endforeach
  </div>
  <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection

@push('js')
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script>
  $(function () {
    
    @foreach($categs as $categ)
    var table{{$categ->id}} = $('#tuble{{$categ->id}}').DataTable({
        processing: true,
        serverSide: true,
        ajax: { url: '{{URL::to('/')}}/api/faq/perguntas/{{$categ->id}}' },
        "oLanguage": {
            "sUrl": "/pt_BR.txt"
        },
        "ordering": false,
        paging: false,
        //"order": [[ 2, "asc" ]],
        // "columnDefs": [
        //     {
        //         "targets": [ 2 ],
        //         "visible": false,
        //         "searchable": false
        //     },
        // ],
        columns: [
            @foreach($columns as $column)
            { data: '{{$column['name']}}', name: '{{$column['name']}}'{!!isset($column['render'])?', render: '.$column['render']:''!!}},
            @endforeach
            //{data: 'parentid' , name: 'parentid'},
            @if(!Auth()->user()->temPermissao('faq','leitura'))
            { 
            data: null,
            orderable: false,
            width: '20%',
            className: "center",
            defaultContent: 
            `<button type="button" class="btn btn-sm btedit btn-warning">Editar</button>
            <button type="button" class="btn btn-sm btdelete btn-danger">Apagar</button>`
            }
            @endif
        ]
    });
    @if(!Auth()->user()->temPermissao('faq','leitura'))
    table{{$categ->id}}.on('click', '.btedit', function () {
        selid = table{{$categ->id}}.row($(this).closest('tr')).data().id;
        location.href='{{URL::to('/')}}/admin/pergunta/'+selid
    });
    @endif
    @endforeach

  });
</script>
@endpush