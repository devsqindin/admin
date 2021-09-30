<form id="theForm" method="post" action="/admin/operador">
<div class="row">
{!! Form::text("nome","Nome")->wrapperAttrs(['class'=>'col-md-12'])->required() !!}
{!! Form::text("email","E-mail")->type('email')->wrapperAttrs(['class'=>'col-md-6'])->required() !!}
{!! Form::text("celular","Celular")->attrs(['class'=>'cel'])->wrapperAttrs(['class'=>'col-md-6'])!!}
<hr/>
{!! Form::text("cpf","CPF")->attrs(['class'=>'cpf'])->wrapperAttrs(['class'=>'col-md-4']) !!}
{!! Form::text("rg","RG")->wrapperAttrs(['class'=>'col-md-4']) !!}
{!! Form::text("data_nascimento","Data de Nascimento")->attrs(['class'=>'dt'])->wrapperAttrs(['class'=>'col-md-4']) !!}
<hr/>
<div class="col-md-12">
<h4>Alterar a Senha</h4>
</div>
{{-- {!! Form::text("atual_password","Senha Atual")->type('password')->wrapperAttrs(['class'=>'col-md-4']) !!} --}}
{!! Form::text("password","Nova Senha")->type('password')->wrapperAttrs(['class'=>'col-md-4']) !!}
{!! Form::text("rep_password","Repetir Senha")->type('password')->wrapperAttrs(['class'=>'col-md-4']) !!}
</div>
<div class="col-md-12">
	<hr/><h5>Permissões do Operador</h5>
	<p>Na tabela abaixo marque a caixa de <b>Módulo</b> para quais módulos o Operador poderá acessar; o módulo permite que o Operador faça <u>qualquer alteração</u>, para limitar para apenas consultar marque também a caixa <b>Apenas Consultar</b>;</p>
</div>
<div class="col-md-4">
<table class="table">
	<thead>
		<tr>
			<th>Tela</th>
			<th>Módulo</th>
			<th>Apenas Consultar</th>
		</tr>
	</thead>
	<tbody>
		@foreach($telas as $slug=>$nome)
		<tr>
			<td>{!!$nome!!}</td>
			<td style="width:30px;"><input type="checkbox" name="acesso[{{$slug}}]" id="acesso_{{$slug}}" class="form-control chkacesso" value="1"></td>
			<td style="width:30px;"><input type="checkbox" name="leitura[{{$slug}}]" id="leitura_{{$slug}}" class="form-control chkleitura" value="1"></td>
		</tr>
		@endforeach
	</tbody>
</table>
</div>
<div class="col-md-12"><hr/></div>
@if(!Auth()->user()->temPermissao('operadores','leitura'))
{!! Form::submit("Enviar")->attrs(['class'=>'btenvio'])->color('success') !!}
@endif
{!! Form::hidden("id") !!}
@csrf
</div>
</form>