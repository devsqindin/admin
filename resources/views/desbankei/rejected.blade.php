@component('mail::tema')

@slot('nome')
Olá, {{$user->nome_completo}}!
@endslot

@slot('corpo')
<tr>
  <td align="center" style="padding:0px 0px 0px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:24px;color:#666666;text-align:center">
    <p><b>Obrigado por seu interesse no Qindin!</b></p>
    <p>Fizemos uma análise interna sobre o seu pedido de crédito.</p>
    <p>Infelizmente, não conseguimos te conceder um limite de crédito nesse momento. Mas não se preocupe, você poderá tentar novamente daqui <b>3 meses</b>.</p>

    @if($user->motivo)
    <h5>Informações do Motivo</h5>
    <p>{{$user->motivo->mensagem}}</p>
    @endif

  </td>
</tr>
<tr>
  <td align="center" style="padding:16px 0px 0px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:24px;color:#666666;text-align:center">
    <p>Esperamos te ver em breve,<br>
    <b>Equipe Qindin</b></p>
 </td>
</tr>
@endslot

@slot('email')
{{$user->email}}
@endslot

@endcomponent
