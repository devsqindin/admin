@php
function getValue($taxa) {
	if ($taxa->type == 'perc') {
		$taxa->valor = $taxa->valor*100;
	} else {
		$taxa->valor = number_format($taxa->valor,2,',','');
	}
	$taxa->valor = str_replace(".", ",", $taxa->valor);
	return $taxa->valor;
}
function getAttrs($taxa) {
	if ($taxa->type == 'perc') {
		return [];
	} else {
		return ['class'=>'din'];
	}
}
@endphp
<form id="formTaxas" method="post" action="/admin/taxas">
<div class="row">
	<div class="col-md-6">
		@foreach($taxas_multi as $taxa)
		@if(Auth()->user()->temPermissao('cadastro','leitura'))
		{!! Form::text($taxa->slug,$arrtaxas[$taxa->slug],getValue($taxa))->attrs(getAttrs($taxa))->disabled()->required() !!}
		@else
		{!! Form::text($taxa->slug,$arrtaxas[$taxa->slug],getValue($taxa))->attrs(getAttrs($taxa))->required() !!}
		@endif
		@endforeach
	</div>
	<div class="col-md-6">
		<h5>Taxas Int</h5>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Parcelas</th>
					<th>Taxa (%)</th>
				</tr>
			</thead>
			@foreach($taxas_int as $taxa)
			<tr>
				<td><strong>{{$taxa->nparcela}}</strong></td>
				<td><input type="text" name="{{$taxa->slug}}[{{$taxa->nparcela}}]" value="{!!getValue($taxa)!!}" class="form-control @php echo ($taxa->type != 'perc') ? 'din' : ''; @endphp" required="required" @if(Auth()->user()->temPermissao('cadastro','leitura'))disabled="disabled"@endif></td>
			</tr>
			@endforeach
		</table>
	</div>
	@if(!Auth()->user()->temPermissao('cadastro','leitura'))
	<div class="col-md-12">
		<button type="submit" class="btn btn-success">Salvar</button>
	</div>
	@endif
</div>
@csrf
</form>