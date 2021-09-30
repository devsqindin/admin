<form id="theForm" method="post" action="/admin/motivo">
<div class="row">
{!! Form::text("nome","Nome")->wrapperAttrs(['class'=>'col-md-12'])->required() !!}
{!! Form::textarea("mensagem","Mensagem")->wrapperAttrs(['class'=>'col-md-12'])->required() !!}
<hr/>

<div class="col-md-12"><hr/></div>
@if(!Auth()->user()->temPermissao('operadores','leitura'))
{!! Form::submit("Enviar")->attrs(['class'=>'btenvio'])->color('success') !!}
@endif
{!! Form::hidden("id") !!}
@csrf
</div>
</form>