{!! Form::open()->id('formPergunta')->route('admin.post_pergunta')->fill($pergunta) !!}
<div class="row">
{!! Form::text("pergunta","Pergunta")->wrapperAttrs(['class'=>'col-md-12'])->required() !!}
{!! Form::textarea("resposta","Resposta")->attrs(['rows'=>5])->wrapperAttrs(['class'=>'col-md-12']) !!}
{!! Form::select("type","Tipo",[""=>"Pergunta/Resposta","C"=>"Categoria","S"=>"Subcategoria"])->wrapperAttrs(['class'=>'col-md-12'])->disabled()->required() !!}

@if($pergunta->type=='C')
{!! Form::text("order","Ordem")->wrapperAttrs(['class'=>'col-md-12'])->required() !!}
@elseif($pergunta->type=='S')
{!! Form::select("parent","Primeira pergunta",App\Pergunta::whereNull('type')->get()->pluck('pergunta','id'),$pergunta->parent)->wrapperAttrs(['class'=>'col-md-12'])->required() !!}
@endif

<div class="col-md-12 float-right">
{!! Form::submit("Enviar")->attrs(['class'=>'btenvio'])->color('success') !!}
</div>
{!! Form::hidden('id') !!}
{!! Form::close() !!}