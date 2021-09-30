@extends('layouts.admin')

@push('css')
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
@endpush

@section('content')
<div class="card" id="dvGrid">
  <div class="card-header" style="background-color: #fff; position: absolute; top: -90px; right: 0px; border: 0px;">
    <button type="button" id="btNewRegister" class="btn btn-success">Novo Motivo</button>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <table class="table cell-border display is-striped is-hoverable is-fullwidth" id="tuble">
        <thead>
        <tr>
            @foreach($columns as $column)
            <th>{{$column}}</th>
            @endforeach
            <th>Ações</th>
        </tr>
        </thead>
    </table>
  </div>
  <!-- /.card-body -->
</div>
<div class="card" id="dvForm" style="display:none;">
  <!-- /.card-header -->
  <button type="button" class="btn btn-warning" id="btShowGrid">Voltar ao Grid</button>
  <div class="card-body">
    @include('forms.motivo')
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
<script src="{{asset('plugins/crud.js')}}"></script>
<script>
$(function() {
    var selected = [];
    var table = $('#tuble').DataTable({
        processing: true,
        serverSide: true,
        ajax: { url: '/api/motivos' },
        "oLanguage": {
            "sUrl": "/pt_BR.txt"
        },
        columns: [
            @foreach($columns as $column => $name)
            { data: '{{$column}}', name: '{{$column}}'},
            @endforeach
            @if(!Auth()->user()->temPermissao('faq','leitura'))
            { 
            data: null,
            orderable: false,
            width: '20%',
            className: "center",
            defaultContent: 
            `<button type="button" class="btn btedit btn-warning">Editar</button>
            <button type="button" class="btn btdelete btn-danger">Apagar</button>`
            }
            @else
            { 
            data: null,
            orderable: false,
            width: '20%',
            className: "center",
            defaultContent: 
            `<button type="button" class="btn btedit btn-warning">Visualizar</button>`
            }
            @endif
        ]
    });
    
    $('#tuble tbody').on('click', 'tr', function () {
        $('.is-selected').toggleClass('is-selected');
        $(this).toggleClass('is-selected');
    });

    var selid = 0;
    
    $('#btNewRegister').click(function(){
        $('#dvGrid').hide();
        if (typeof $.posEdit !== 'undefined' && $.isFunction($.posEdit)) {
            $.posEdit(null);
        }
        $('form').each(function(vv){
            $(this)[0].reset();
        })
        $('input[name="id"]').val('');
        // permissao
        $('.chkacesso').prop('checked',true);
        $('.chkleitura').prop('checked',false);
        $('#dvForm').fadeIn();
    });

    $.acShowGrid = function() {
        $('#dvForm').hide();
        $('label.error').hide();
        table.ajax.reload();
        $('#dvGrid').fadeIn();
    }

    $('#btShowGrid').click(function(){
        $.acShowGrid();
    });


    table.on('click', '.btedit', function () {
        selid = table.row($(this).closest('tr')).data().id;
        console.log(selid);
        $.getJSON("/api/motivo/"+selid, function(data){
            $('#dvGrid').hide();
            $('#theForm')[0].reset();
            $.each(data, function(kk,vv){
                // formatar valores
                if (kk.indexOf('valor') > -1 || kk.indexOf('preco') > -1) {
                    data[kk] = moeda.formatar(vv);
                }
            })
            console.log(data);
            var permissoes = $.extend( {}, data.item.permissoes );
            data.item.permissoes = null
            $('#theForm').populate(data.item);
            @if(Auth()->user()->temPermissao('motivos','leitura'))
            $('#theForm input, #theForm select').attr('disabled','disabled')
            @endif
            // marcar acessos e leituras
            $.each(permissoes, function(kk,vv){
                if (vv.acesso) {
                    console.log('#acesso_'+vv.tela)
                    $('#acesso_'+vv.tela).prop('checked',true);
                }
                if (vv.leitura) {
                    $('#leitura_'+vv.tela).prop('checked',true);
                }
            });
            if (typeof $.posEdit !== 'undefined' && $.isFunction($.posEdit)) {
                $.posEdit(data.item);
            }
            $('#dvForm').fadeIn();
        });
    });

    @if(!Auth()->user()->temPermissao('motivos','leitura'))
    table.on('click', '.btdelete', function () {
        selid = table.row($(this).closest('tr')).data().id;
        bootbox.confirm({
            message: "Deseja mesmo apagar esse motivo?",
            buttons: {
                cancel: {
                    label: 'Não',
                    className: 'btn-danger'
                },
                confirm: {
                    label: 'Sim',
                    className: 'btn-success'
                }
            },
            callback: function (result) {
                if (result) {
                    $.post("/api/motivo/"+selid+"/apagar",
                    {_method: 'DELETE', id: selid}
                    ,function(data){
                        if (data.status == 'success') {
                            bootbox.alert('Motivo removido com sucesso!', function(){
                                $.acShowGrid();
                            });
                        }
                    });
                }
            }
        });
    });
    
    $('#theForm').ajaxForm({
        beforeSubmit: function(arr,$form,options) {
            // senha

            if ($('#inp-password').val() && $('#inp-rep_password').val()) {
                var password = $('#inp-password').val();
                var rep_password = $('#inp-rep_password').val();
                var StrongPass = /^(?=\S*?[A-Z])(?=\S*?[0-9])\S{6,}$/;

                if (password != rep_password) {
                  alert('Senhas diferentes');
                  $('#inp-password').focus();
                  return false;
                } else if (password.length < 6) {
                  alert('Senha precisa ter no mínimo 6 caracteres');
                  $('#inp-password').focus();
                  return false;
                } else if (!StrongPass.test(password)) {
                  alert('Sua senha deve conter no mínimo 6 caracteres, sendo 1 letra maiúscula e 1 número.');
                  $('#inp-password').focus();
                  return false;
                }
            }

            // formata valor
            $.each(arr, function(kk,vv){
                if (vv['name'].indexOf('valor') > -1 || vv['name'].indexOf('preco') > -1) {
                    arr[kk]['value'] = parseFloat(arr[kk]['value'].replace('.','').replace(',','.'));
                }
            })
            console.log(arr);
            if (!$('#theForm').valid()) {
              return false; 
            }

        },
        success: function() {
            bootbox.alert('Motivo salvo com sucesso', function(){
                $.acShowGrid();
            });
        }
    });
    @endif

});
</script>
@endpush