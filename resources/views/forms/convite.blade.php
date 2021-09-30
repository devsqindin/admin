<form id="theForm" method="post" action="/admin/cliente/convite">
<div class="row">
{!! Form::text("nome_completo","Nome Completo")->wrapperAttrs(['class'=>'col-md-12'])->required() !!}
{!! Form::text("cpf","CPF")->attrs(['class'=>'cpf'])->wrapperAttrs(['class'=>'col-md-4'])->required() !!}
{!! Form::text("email","E-mail")->type('email')->wrapperAttrs(['class'=>'col-md-6'])->required() !!}
{!! Form::text("whatsapp","WhatsApp")->attrs(['class'=>'cel'])->wrapperAttrs(['class'=>'col-md-6'])->required() !!}
<div class="col-md-12"><hr/></div>
@if(!Auth()->user()->temPermissao('cadastro','leitura'))
{!! Form::submit("Enviar")->attrs(['class'=>'btenvio'])->color('success') !!}
@endif
@csrf
</div>
</form>
</row>

@push('js')
<script src="{{asset('plugins/crud.js')}}"></script>
<script type="text/javascript">
$(function(){
	$('#theForm').ajaxForm({
        beforeSubmit: function(arr,$form,options) {
            

            // formata valor
            $.each(arr, function(kk,vv){
                if (vv['name'].indexOf('valor') > -1 || vv['name'].indexOf('preco') > -1) {
                    arr[kk]['value'] = parseFloat(arr[kk]['value'].replace('.','').replace(',','.'));
                }
            })
            console.log(arr);
            if (!$('#theForm').valid()) {
              return false; 
            }

        },
        success: function() {
            bootbox.alert('Cliente salvo com sucesso', function(){
                location.href=''
            });
        }
    });
})	
</script>
@endpush