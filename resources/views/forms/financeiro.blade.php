<form id="formFinanceiro" method="post" action="/admin/cliente/financeiro">
<div class="row">
{!! Form::text("renda_comprovada","Renda comprovada (R$)")->attrs(['class'=>'din'])->wrapperAttrs(['class'=>'col-md-12'])->required() !!}
{!! Form::select("profissao","Ramo de Atuação",[""=>"Selecione...","Agricultura"=>"Agricultura","Comércio"=>"Comércio","Comunicação e Design"=>"Comunicação e Design","Economia e Negócios"=>"Economia e Negócios","Educação"=>"Educação","Engenharia e Arquitetura"=>"Engenharia e Arquitetura","Governo"=>"Governo","Jurídico"=>"Jurídico","Militar"=>"Militar","Saúde"=>"Saúde","Tecnologia"=>"Tecnologia","Transportes"=>"Transportes","Vendas e Corretagem"=>"Vendas e Corretagem","Outros"=>"Outros"])->wrapperAttrs(['class'=>'col-md-6'])->required() !!}
{!! Form::select("ocupacao","Ocupação",[""=>"Selecione...","Empregado(a) (CLT)"=>"Empregado(a) (CLT)","Empregado(a) (PJ)"=>"Empregado(a) (PJ)","Servidor(a) Público(a)"=>"Servidor(a) Público(a)","Autônomo(a)"=>"Autônomo(a)","Profissional Liberal"=>"Profissional Liberal","Microempreendedor(a) (MEI)"=>"Microempreendedor(a) (MEI)","Empresário(a)"=>"Empresário(a)","Aposentado(a)"=>"Aposentado(a)","Outros"=>"Outros"])->wrapperAttrs(['class'=>'col-md-6'])->required() !!}
{!! Form::select("restritivo","CPF Negativado",[""=>"Selecione...","Sim"=>"Sim","Não"=>"Não"])->wrapperAttrs(['class'=>'col-md-6'])->required() !!}
{!! Form::select("vence_fatura","Vencimento da Fatura",[""=>"Selecione...",05=>05,10=>10,15=>15])->wrapperAttrs(['class'=>'col-md-6'])->required() !!}
<div class="col-md-12"><hr/></div>
{!! Form::select("banco","Banco",collect(array_merge([['nome'=>'Selecione...','codigo'=>'']],\DB::select("SELECT codigo, UPPER(nome) AS nome FROM bancos ORDER BY nome")))->pluck('nome','codigo'))->wrapperAttrs(['class'=>'col-md-12'])->required() !!}
{!! Form::text("agencia","N° da Agência")->wrapperAttrs(['class'=>'col-md-4'])->required() !!}
{!! Form::text("numero_conta","N° da Conta")->wrapperAttrs(['class'=>'col-md-4'])->required() !!}
{!! Form::text("dv_conta","Dígito da Conta")->wrapperAttrs(['class'=>'col-md-4'])->required() !!}
<div class="col-md-12"><hr/></div>
@if(!Auth()->user()->temPermissao('cadastro','leitura'))
{!! Form::submit("Enviar")->attrs(['class'=>'btenvio'])->color('success') !!}
@endif
@csrf
</div>
<input type="hidden" name="id_usuario" value="{{$clienteId}}">
</form>