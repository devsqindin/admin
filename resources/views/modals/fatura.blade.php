<style type="text/css">
.tbparcelas td, .tbparcelas th {
  font-size: 14px;
}
</style>
<div class="modal-dialog modal-xl">
  <div class="modal-content">
    <div class="modal-header">
      <h4 class="modal-title">Faturas</h4>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="modal-body">
      <form id="formFatura" method="post" action="{{URL::to('/')}}/admin/cliente/fatura">
      <div class="row">
        @if(Auth()->user()->temPermissao('faturas','leitura'))
        <div class="col-12" style="border-right: solid 1px #AAA;">
        @else
        <div class="col-6" style="border-right: solid 1px #AAA;">
        @endif
        <h4>Extrato da Fatura</h4>
        <p><b>Data Emissão:</b> {{date("d/m/Y",strtotime($fatura->reg_date))}} - <b>Data Vencimento:</b>  {{date("d/m/Y",strtotime($fatura->vencimento))}} <br/> <b>Valor Total:</b> R$  {{number_format($fatura->valor_total,2,',','.')}} - <b>Situação:</b> 
          <select name="status" @if(Auth()->user()->temPermissao('faturas','leitura') || $fatura->status == 2)disabled="disabled"@endif>
            <option value="0" @if($fatura->status == 0) selected="selected"@endif>Pendente</option>
            <option value="1" @if($fatura->status == 1) selected="selected"@endif>Emitido</option>
            <option value="2" @if($fatura->status == 2) selected="selected"@endif>Pago</option>
            <option value="3" @if($fatura->status == 3) selected="selected"@endif>Cancelado</option>
          </select></p>
        <h4>Parcelas da Fatura</h4>
        <div style="height:300px;overflow-y:scroll;">
        <table class="table table-stripped tbparcelas">
          <thead>
            <tr>
              <th></th>
              <th>Data Solicitação</th>
              <th>Descrição Cobrança</th>
              <th>Valor Solicitado</th>
              <th>Valor Parcela</th>
              <th>Juros</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($fatura->parcelas as $prc)
            
            @if($prc->parcela_type == 'App\SolicitacaoParcelamento' && isset($prc->parcela))
            <tr>
              <td><input type="checkbox" name="selparcela[]" class="selparcela" value="{{$prc->id}}" @if(Auth()->user()->temPermissao('faturas','leitura') || $fatura->status == 2)disabled="disabled"@endif></td>
              <td>{{(isset($prc->parcela->reg_date)) ? date("d/m/Y",strtotime($prc->parcela->reg_date)) : ''}}</td>
              <td>Parcela {{$prc->numparcela}}/{{$prc->parcela->parcelas}}</td>
              <td>R$ {{number_format($prc->parcela->valor_solicitado,2,',','.')}}</td>
              <td>R$ {{number_format($prc->parcela->valor_parcela,2,',','.')}}</td>
              <td>{{number_format($prc->parcela->taxa_juros,4,',','.')}} %</td>
              <td>{!!($prc->pago==1)?'<span style="font-weight:bold;color:green;">PAGO</span>':'<span style="font-weight:bold;color:orange;">ABERTO</span>'!!}</td>
            </tr>
            @elseif($prc->parcela_type == 'App\Cobranca')
            <tr>
              <td><a href="javascript:;"><i class="fas fa-times" style="color:red;" onclick="$.apagaCobranca({{$prc->id}})"></i></a></td>
              <td>{{date("d/m/Y",strtotime($prc->parcela->reg_date))}}</td>
              <td>{{$prc->parcela->descricao}}</td>
              <td>--</td>
              <td>R$ {{number_format($prc->parcela->valor,2,',','.')}}</td>
              <td></td>
              <td></td>
            </tr>
            @endif
            @endforeach
          </tbody>
        </table>
        </div>
        @if(!Auth()->user()->temPermissao('faturas','leitura'))
        <a href="javascript:;" onclick="$.incluirCobranca()">Incluir Lançamentos</a> - <button type="button" class="btn btn-primary" onclick="$.marcarPago()">Marcar Pago/Não Pago</button>
        @endif
        </div>
        @if(!Auth()->user()->temPermissao('faturas','leitura'))
        <div class="col-6">
          <h4>Informações da Fatura</h4>
          <p>
            <label>Arquivo PDF @if($fatura->url) - <small>Fatura: <a href="{{asset($fatura->url)}}" target="_blank">VISUALIZAR</a>  - <a href="javascript:;" style="color:red;" onclick="$.cancelarEmissao()"><i class="fas fa-times"></i> Cancelar Emissão</a></small>@endif</label>
            <input type="file" name="fatura_file" id="fatura_file" class="form-control">
          </p>
          <p>
            <label>Digitos Boleto</label>
            <input type="text" name="fatura_digitos" id="fatura_digitos" value="{{$fatura->digitos}}" class="form-control">
          </p>
          <p><button type="submit" class="btn btn-primary" name="">Enviar</button></p>
          <hr>
          <input type="hidden" name="id_usuario" value="{{$clienteId}}">
          <input type="hidden" name="id_fatura" value="{{$faturaId}}">
        </div>
        @endif
      </div>
      @csrf
      </form>
    </div>
    {{-- <div class="modal-footer justify-content-between">
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      <button type="button" class="btn btn-primary">Save changes</button>
    </div> --}}
  </div>
  <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
<script type="text/javascript">
$(function(){
  $('#formFatura').validate()
  $('#formFatura').ajaxForm({
    beforeSubmit: function(arr,$form,options) {
      if (!$('#formFatura').valid()) {
        return false; 
      }
    },
    success: function(data) {
      if (data.success) {
        alert('Dados financeiros atualizados');
        $('#tbFaturas').DataTable().ajax.reload()
        $('#modalConteudo').modal('hide')
      } else {
        alert(data.message)
      }
    }
  });

  $.incluirCobranca = function() {
    $.get('{{URL::to('/')}}/cliente/{{$clienteId}}/fatura/{{$faturaId}}/cobranca', function(data){
      $('.modal-body').html(data)
    })
  }

  $.marcarPago = function() {
    var arrparcela = [];
    $('.selparcela:checked').each(function(){
      arrparcela.push($(this).val())
    })
    console.log(arrparcela)
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.post('{{URL::to('/')}}/admin/cliente/fatura/marcar', {
      parcelas: arrparcela,
      id_usuario: {{$clienteId}},
      id_fatura: {{$faturaId}},
    }, function(data) {
      if (data.success) {
        $.get('/cliente/{{$clienteId}}/fatura/{{$faturaId}}', function(data){
          $('#modalConteudo').html(data)
        })
        alert('Status de parcela alterado')
      }
    })
  }

  $.cancelarEmissao = function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.post('{{URL::to('/')}}/admin/cliente/fatura/cancelar',{
      id_usuario: {{$clienteId}},
      id_fatura: {{$faturaId}}
    }, function(data){
      if (data.success) {
        alert('Emissão cancelada');
        $('#tbFaturas').DataTable().ajax.reload()
        $('#modalConteudo').modal('hide')
      }
    })
  }

  $.apagaCobranca = function(pid) {
    var q = confirm('Deseja mesmo apagar essa cobrança?');
    if (q) {
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.post('{{URL::to('/')}}/admin/cliente/cobranca/apagar',{
        id_parcela: pid
      }, function(data){
        if (data.success) {
          $.get('/cliente/{{$clienteId}}/fatura/{{$faturaId}}', function(data){
            $('#modalConteudo').html(data)
          })
        }
      })
    }
  }

})
</script>