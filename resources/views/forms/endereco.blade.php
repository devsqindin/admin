<form id="formEndereco" method="post" action="/admin/cliente/endereco">
<div class="row">
{!! Form::text("cep","CEP")->attrs(['class'=>'cep'])->wrapperAttrs(['class'=>'col-md-3'])->required() !!}
{!! Form::text("endereco","Endereço")->wrapperAttrs(['class'=>'col-md-9'])->required() !!}
{!! Form::text("numero","Número")->wrapperAttrs(['class'=>'col-md-6'])->required() !!}
{!! Form::text("complemento","Complemento")->wrapperAttrs(['class'=>'col-md-6']) !!}
{!! Form::text("bairro","Bairro")->wrapperAttrs(['class'=>'col-md-4'])->required() !!}
{!! Form::text("cidade","Cidade")->wrapperAttrs(['class'=>'col-md-4'])->required() !!}
{!! Form::text("estado","Estado")->wrapperAttrs(['class'=>'col-md-4'])->required() !!}
{!! Form::select("morapais","Mora com os pais?",[""=>"Selecione...","Sim"=>"Sim","Não"=>"Não"])->wrapperAttrs(['class'=>'col-md-6'])->required() !!}
{!! Form::select("moradia","Tipo de Moradia",[""=>"Selecione...","Própria"=>"Própria","Alugada"=>"Alugada","Cedida"=>"Cedida","Financiada"=>"Financiada"])->wrapperAttrs(['class'=>'col-md-6'])->required() !!}
<div class="col-md-12"><hr/></div>
@if(!Auth()->user()->temPermissao('cadastro','leitura'))
{!! Form::submit("Enviar")->attrs(['class'=>'btenvio'])->color('success') !!}
@endif
@csrf
</div>
<input type="hidden" name="id_usuario" value="{{$clienteId}}">
</form>