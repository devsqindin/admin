<div class="modal-dialog modal-xl">
  <div class="modal-content">
    <div class="modal-header">
      <h4 class="modal-title">Histórico de Créditos</h4>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="modal-body">
        <h4>Solcitações de Crédito</h4>
        <table id="tbCreditos" class="table table-stripped" style="width:100%;">
          <thead>
            <tr>
              <th>Data Solicitação</th>
              <th>Valor Solicitado</th>
              <th>Valor Parcela</th>
              <th>Último Vencimento</th>
              <th>Parcelas Pagas/Total</th>
              <th>Juros</th>
              <th></th>
            </tr>
          </thead>
        </table>
        <h4>Disponibilidade Crédito</h4>
        <table id="tbDisponivel" class="table table-stripped" style="width:100%;">
          <thead>
            <tr>
              <th>Data</th>
              <th>Valor Disponibilizado</th>
              <th>Operador</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
    {{-- <div class="modal-footer justify-content-between">
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      <button type="button" class="btn btn-primary">Save changes</button>
    </div> --}}
</div>
<!-- /.modal-content -->
<!-- /.modal-dialog -->
<script type="text/javascript">
function ifNull(val) {
  if (!val) {
    val = 0
  }
  return val
}
$(function(){
    var table = $('#tbCreditos').DataTable({
      processing: true,
      serverSide: true,
      ajax: { url: '{{URL::to('/')}}/api/cliente/{{$clienteId}}/creditos' },
      "oLanguage": {
          "sUrl": "/pt_BR.txt"
      },
      columns: [
          {data:'reg_date',name:'reg_date',render:function(data,type,row){
            return moment(data,"YYYY-MM-DD").format('DD/MM/YYYY')
          }},
          {data:'valor_solicitado',name:'valor_solicitado',render:function(data,type,row){
            return 'R$ '+moeda.formatar(data);
          }},
          {data:'valor_parcela',name:'valor_parcela',render:function(data,type,row){
            return 'R$ '+moeda.formatar(data);
          }},
          {data:'vencimento',name:'vencimento',render:function(data,type,row){
            return moment(data,"YYYY-MM-DD").format('DD/MM/YYYY')
          }},
          {data:null,name:'parcelas',render:function(data,type,row){
            return ifNull(row.parcelas_pagas)+'/'+row.parcelas
          }},
          {data:'taxa_juros',name:'taxa_juros',render:function(data,type,row){
            return data;
          }},
          {data:'fiducia_geral',name:'fiducia_geral',render:function(data,type,row){
            if (data == 1) {
              return '<span style="color:green;">Envio OK</span>';
            } else {
              return '<button type="button" class="btn btn-sm btn-success btimpcredito">Enviar &agrave; Fidúcia</button>&nbsp;<button type="button" class="btn btn-sm btn-danger btcancela">Cancelar</button>';
            }
          }}
      ]
  });

  table.on('click', '.btimpcredito', function () {
      selid = table.row($(this).closest('tr')).data().id;
      var button = $(this);
      button.attr('disabled','disabled').text('Enviando...')
      console.log('impcredito '+selid)
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.post('{{URL::to('/')}}/admin/importa/credito',{
        id_credito: selid,
        id_cliente: {{$clienteId}},
      }, function (data) {
        if (data.success) {
          alert('Crédito importado com sucesso!')
          table.ajax.reload()
        } else {
          alert('ERRO: '+data.message)
          button.removeAttr('disabled').text('Enviar à Fidúcia')
        }
      })
  });

  table.on('click', '.btcancela', function () {
      var q = confirm('Deseja mesmo cancelar essa solicitação de Crédito?')
      if (q) {
        selid = table.row($(this).closest('tr')).data().id;
        console.log('impcredito '+selid)
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.post('{{URL::to('/')}}/admin/cancela/credito',{
          id_credito: selid,
          id_cliente: {{$clienteId}},
        }, function (data) {
          if (data.success) {
            alert('Crédito cancelado com sucesso!')
            table.ajax.reload()
          } else {
            alert('ERRO: '+data.message)
          }
        })
      }
  });

  var table2 = $('#tbDisponivel').DataTable({
      processing: true,
      serverSide: true,
      ajax: { url: '{{URL::to('/')}}/api/cliente/{{$clienteId}}/disponivel' },
      "oLanguage": {
          "sUrl": "/pt_BR.txt"
      },
      columns: [
          {data:'datahora',name:'datahora',render:function(data,type,row){
            return moment(data,"YYYY-MM-DD HH:mm:ss").format('DD/MM/YYYY HH:mm')
          }},
          {data:'valor',name:'valor',render:function(data,type,row){
            return 'R$ '+moeda.formatar(data);
          }},
          {data:'operador.nome',name:'operador.nome'}
      ]
  });
  
})
</script>