<form id="formCredito" method="post" action="/admin/cliente/credito">
<div class="row">
@if(!Auth()->user()->temPermissao('creditos','leitura'))
{!! Form::text("credito_aprovado","Crédito Aprovado (R$)")->attrs(['class'=>'din'])->wrapperAttrs(['class'=>'col-md-6'])->required() !!}
{!! Form::text("taxa_juros","Taxa de Juros (%) <small>(exclusiva do cliente)</small>")->attrs(['class'=>'din'])->wrapperAttrs(['class'=>'col-md-6']) !!}
@else
{!! Form::text("credito_aprovado","Crédito Aprovado (R$)")->attrs(['class'=>'din'])->wrapperAttrs(['class'=>'col-md-6'])->disabled()->required() !!}
{!! Form::text("taxa_juros","Taxa de Juros <small>(exclusiva do cliente)</small>")->wrapperAttrs(['class'=>'col-md-6'])->disabled()->required() !!}
@endif
<p><small>Valor total bruto do crédito aprovado para esse cliente</small></p>
<div class="col-md-12">
@if(!Auth()->user()->temPermissao('creditos','leitura'))
@endif
</div>
<div class="col-md-12"><hr/></div>
{!! Form::text("limite_total","Limite Total (R$)")->attrs(['class'=>'din'])->wrapperAttrs(['class'=>'col-md-12']) !!}
{!! Form::text("limite_utilizado","Limite Utilizado (R$)")->attrs(['class'=>'din'])->wrapperAttrs(['class'=>'col-md-12']) !!}
{!! Form::hidden("limite_utilizado_orig")->id('limite_utilizado_orig') !!}
{!! Form::text("limite_disponivel","Limite Disponível (R$)")->attrs(['class'=>'din'])->wrapperAttrs(['class'=>'col-md-12']) !!}
{!! Form::hidden("limite_disponivel_orig")->id('limite_disponivel_orig') !!}
</div>
@csrf
<input type="hidden" name="id_usuario" value="{{$clienteId}}">
{!! Form::submit("Atualizar Valores")->attrs(['class'=>'btenvio'])->color('success') !!}&nbsp; <button type="button" class="btn btn-default" onclick="$.historicoCredito()">Histórico de Créditos</button>
</form>