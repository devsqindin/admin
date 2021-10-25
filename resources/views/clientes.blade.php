@extends('layouts.admin')
@php
function popCombo($arr) {
  foreach($arr as $ak=>$av) {
    $res .= '<option value="'.$ak.'">'.$av.'</option>';
  }
  return $res;
}
@endphp

@push('css')
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<style type="text/css">
  .trbusca th {
    padding: 1px;
    background-color: #ddd;
  }
  .fbusca {
    max-width: 125px;
  }
  .fbusca::placeholder {
    font-size: 12px;
  }
</style>
@endpush

@section('content')
<div class="card" id="dvGrid">
  <div class="card-header" style="background-color: #fff; position: absolute; top: -90px; right: 0px; border: 0px;">
    {{-- <h3 class="card-title">DataTable with default features</h3> --}}
    @if(!Auth()->user()->temPermissao('clientes','leitura'))
      <button type="button" id="btNewRegister" class="btn btn-primary">Novo Cliente (Convite)</button>
      <button type="button" id="btExportCsvClientes" class="btn btn-primary">Exportar CSV</button>
    @endif
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <table id="tuble" class="table table-bordered table-striped">
      <thead>
      <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>CPF</th>
        <th>Renda Comprovada</th>
        <th>Crédito Aprovado</th>
        <th>Limite Total</th>
        <th>Limite Disponível</th>
        <th>Solicitação Crédito</th>
        <th>Situação Faturas</th>
        <th>Situação Cadastro</th>
        <th></th>
      </tr>
      </thead>
    </table>
  </div>
  <!-- /.card-body -->
</div>
<!-- /.card -->

<div class="card" id="dvForm" style="display:none;">
  <!-- /.card-header -->
  <button type="button" class="btn btn-warning" id="btShowGrid">Voltar ao Grid</button>
  <div class="card-body">
    @include('forms.convite')
  </div>
  <!-- /.card-body -->
</div>

@endsection

@push('js')
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="https://cdn.datatables.net/fixedheader/3.1.8/js/dataTables.fixedHeader.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
<script>

  /*function exportUsers(_this) {
      let _url = $(_this).data('href');
      window.location.href = _url;
   }*/

  $(function () {
    
    var table = $('#tuble').DataTable({
        
      processing: true,
      serverSide: true,
      
      ajax: { url: '{{URL::to('/')}}/api/clientes{{isset($total)?'/'.$total:''}}' },
      "oLanguage": {
        "sUrl": "/pt_BR.txt"
      },
      pageLength: 100,
      columns: [
          
          {data:'id', name:'id'},
          
          @foreach($columns as $column)
            { data: '{{$column['name']}}', 
              name: '{{$column['name']}}'{!!isset($column['render'])?', 
              render: '.$column['render']:''!!} },
          @endforeach

          /* SOLICITAÇÃO CRÉDITO */
          {data:'fiducia', name:'fiducia', render:function(data,type,row) {

            st = '<span style="color:#888;">Sem Solicitação</span>'
            
            if (data == 1) {
              st= '<span style="font-weight:bold;color:red;">Envio Pendente</span>'
            }

            return st
          }},
          
          /* SITUAÇÃO FATURA */
          {data:'status_fatura', name:'status_fatura', render:function(data,type,row) {
            
            st = '<span style="color:#888;">Sem Fatura</span>'

            if (data == 1) {
              st = '<span style="font-weight:bold;color:orange;">Fatura Fechada</span>'
            } else if (data == 2) {
              st = '<span style="font-weight:bold;color:purple;">Fatura Emitida</span>'
            } else if (data == 3) {
              st = '<span style="font-weight:bold;color:red;">Fatura Atrasada</span>'
            } else if (data == 4) {
              st = '<span style="font-weight:bold;color:green;">Fatura Paga</span>'
            } else if (data == 5) {
              st = '<span style="color:#000;">Fatura Aberta</span>'
            }

            return st
          }},

          /* SITUAÇÃO CADASTRO */
          {data:'status', name:'status', render:function(data,type,row) {

            var st = '<span style="font-weight:bold;color:gray;">Cadastro Incompleto</span>';

            switch(data) {
              case 1 : st = '<span style="font-weight:bold;color:orange;">Pendente Documentação</span>'; break;
              case 2 : st = '<span style="font-weight:bold;color:purple;">Análise Documentação</span>'; break;
              case 3 : st = '<span style="font-weight:bold;color:green;">Cadastro Completo</span>'; break;
              case 4 : st = '<span style="font-weight:bold;color:red;">Cadastro Bloqueado</span>'; break;
              case 5 : st = '<span style="font-weight:bold;color:red;">Recusa de Crédito</span>'; break;
              case 6 : st = '<span style="font-weight:bold;color:red;">Bloqueio Falta Pgto</span>'; break;
            }

            return st
          }},

          { 
            data: null,
            orderable: false,
            searchable:false,
            width: '20%',
            className: "center",
            render:function(data,type,row) {
              
              var buttons = `<button type="button" class="btn btn-sm btedit btn-primary">Info &nbsp;<i class="far fa-play-circle"></i></button>`;
              
              return buttons
            } 
          }
          
          // { 
          // data: null,
          // orderable: false,
          // width: '20%',
          // className: "center",
          // render:function(data,type,row){
          //     var buttons = `<button type="button" class="btn btn-sm btedit btn-primary">Info &nbsp;<i class="far fa-play-circle"></i></button>`;
          //     if (row.status == 4) {
          //       buttons += `&nbsp;<button type="button" class="btn btn-sm btdelete btn-success" data-val="unblock">Desblock &nbsp;<i class="fas fa-unlock"></i></button>`;
          //     } else {
          //       buttons += `&nbsp;<button type="button" class="btn btn-sm btdelete btn-danger">Bloq &nbsp;<i class="fas fa-lock"></i></button>`;
          //     }
          //     return buttons
          //   } 
          // }
          
          // defaultContent: 
          // `<button type="button" class="btn btn-sm btedit btn-primary">Info &nbsp;<i class="far fa-play-circle"></i></button>
          // <button type="button" class="btn btn-sm btdelete btn-danger">Bloq &nbsp;<i class="fas fa-lock"></i></button>`
          // }
      ]}).on('processing.dt', function (e, settings, processing) {

        $('#processingIndicator').css('display', 'none');

        if (processing) {
          
          $(e.currentTarget).LoadingOverlay("show");
        } else {

          $(e.currentTarget).LoadingOverlay("hide", true);
        }
      });

    function inArray(needle, haystack) {
        
      var length = haystack.length;
      
      for(var i = 0; i < length; i++) {
          if(haystack[i] == needle) return true;
      }
      return false;
    }

    // Setup - add a text input to each footer cell
    $('#tuble thead tr').clone(true).appendTo( '#tuble thead' ).addClass('trbusca');
    $('#tuble thead tr:eq(1) th').each( function (i) {
      
      var title = $(this).text();
      
      if (inArray(i,[7,8,9])) {
        
        $(this).html( '<select id="sel_'+i+'" class="fbusca fselbusca form-control" placeholder="'+title+'"><option value=""></option></select>' );
      } else if (i < 10) {
        
        $(this).html( '<input class="fbusca ftxtbusca form-control" type="text" placeholder="'+title+'" />' );
      }

      $( 'input', this ).on( 'keyup', function (e) {

        var code = e.key
        if (code == 'Enter') {
          if (i >= 3 && i <= 6) {
              if ( table.column(i).search() !== this.value ) {
                table
                .column(i)
                .search( (this.value) ? '^'+this.value+'$' : '', true, false)
                .draw();
                console.log(this.value)
              }
          } else {
              if ( table.column(i).search() !== this.value ) {
                table
                .column(i)
                .search( this.value )
                .draw();
              }
          }
        }
      });

      $( 'select', this ).on( 'change', function () {
          if ( table.column(i).search() !== this.value ) {
              table
                  .column(i)
                  .search( this.value )
                  .draw();
          }
      } );

    } );

    ar7 = ['Sem Solicitação','Envio Pendente'];
    ar8 = ['Sem Fatura','Fatura Fechada','Fatura Emitida','Fatura Atrasada','Fatura Paga','Fatura Aberta'];
    ar9 = ['Cadastro Incompleto','Pendente Documentação','Análise Documentação','Cadastro Completo','Cadastro Bloqueado','Recusa de Crédito','Bloqueio Falta Pgto'];

    $.each(ar7,function(k,v){
      $('#sel_7').append('<option value="'+k+'">'+v+'</option>')
    })

    $.each(ar8,function(k,v){
      $('#sel_8').append('<option value="'+k+'">'+v+'</option>')
    })

    $.each(ar9,function(k,v){
      $('#sel_9').append('<option value="'+k+'">'+v+'</option>')
    })

    table.on('click', '.btedit', function () {
        
        selid = table.row($(this).closest('tr')).data().id;
        location.href='{{URL::to('/')}}/admin/cliente/'+selid
    });

    @if(!Auth()->user()->temPermissao('clientes','leitura'))
      
      table.on('click', '.btdelete', function () {
          if ($(this).attr('data-val') == 'unblock') {
            var q = confirm('Deseja desbloquear esse cliente?')
          } else {
            var q = confirm('Deseja bloquear esse cliente?')
          }
          if (q) {
            selid = table.row($(this).closest('tr')).data().id;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post('{{URL::to('/')}}/admin/cliente/bloqueio',{
              id_usuario: selid
            }, function(data){
              if (data.success) {
                table.ajax.reload();
              }
            });
          }
      });

      $('#btNewRegister').click(function() {

          $('#dvGrid').hide();
          if (typeof $.posEdit !== 'undefined' && $.isFunction($.posEdit)) {
              $.posEdit(null);
          }
          $('form').each(function(vv){
              $(this)[0].reset();
          })
          $('input[name="id"]').val('');
          $('#dvForm').fadeIn();
      });

      $('#btExportCsvClientes').click(function() {

        alert("Exportando clientes para CSV....");

        $('#processingIndicator').css('display', 'none');
        //$('#tuble').LoadingOverlay("show");  
        $.LoadingOverlay("show");  

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $.post('{{URL::to('/')}}/api/clientes/export', {

        }, function(data) {

          //$('#tuble').LoadingOverlay("hide", true);
          $.LoadingOverlay("hide", true);

          if (data.success) {
            alert("CSV Exportado com sucesso! Arquivo se encontra no diretório /public/CLIENTES.csv");
          } else {
            alert("FALHA NA EXPORTAÇÃO! Contate a equipe técnica.");
          }
        });
      });

      $('#btShowGrid').click(function(){
        $('#dvForm').hide();
        $('#dvGrid').fadeIn();
      })

    @endif
  });
</script>
@endpush