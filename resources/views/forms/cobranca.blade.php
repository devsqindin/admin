<form id="formCobranca" method="post" action="/admin/cliente/cobranca" autocomplete="off">
<h3>Incluir Lançamentos</h3>
<div class="row">
{!! Form::text("descricao","Descrição")->wrapperAttrs(['class'=>'col-md-8'])->required() !!}
{!! Form::select("tipo","Tipo de Operação",[""=>"Selecione...","D"=>"Débito (Taxas/Tarifas)","C"=>"Crédito (Antecipação)"])->wrapperAttrs(['class'=>'col-md-4'])->required() !!}
{!! Form::text("reg_date","Data Solicitação",date("d/m/Y"))->attrs(['class'=>'dt'])->wrapperAttrs(['class'=>'col-md-6'])->required() !!}
{!! Form::text("valor","Valor (R$)")->attrs(['class'=>'din'])->wrapperAttrs(['class'=>'col-md-6'])->required() !!}
<div class="col-md-12 float-right">
<div class="clearfix">
<button type="button" class="btn btn-primary" onclick="$.voltar()">&lt;&lt; Voltar</button>
{!! Form::submit("Enviar")->attrs(['class'=>'btenvio float-right'])->color('success') !!}
</div>
</div>
{!! Form::hidden('id') !!}
<input type="hidden" name="id_usuario" value="{{$clienteId}}">
<input type="hidden" name="id_fatura" value="{{$faturaId}}">
</div>
@csrf
</form>
<script src="{{asset('plugins/crud.js')}}"></script>
<script type="text/javascript">
$(function(){
	$.voltar = function() {
		$.get('/cliente/{{$clienteId}}/fatura/{{$faturaId}}', function(data){
	      $('#modalConteudo').html(data)
	    })
	}
	$('#formCobranca').ajaxForm({
		success: function(res) {
			if (res.success) {
				alert('Cobrança adicionada');
				$('#tbFaturas').DataTable().ajax.reload();
				$.voltar();
			}
		}
	});
})
</script>