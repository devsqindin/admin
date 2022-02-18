@component('mail::tema')

@slot('nome')
Olá, {{$user->nome_completo}}!
@endslot

@slot('corpo')
<tr>
  <td align="center" style="padding:0px 0px 30px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:23px;color:#000;text-align:left; padding: 0 5%;">
    <p><b>Obrigado por seu interesse no Qindin!</b></p>
    <p>Fizemos uma análise interna sobre o seu pedido de crédito.</p>
    <p>Infelizmente, não conseguimos te conceder um limite de crédito nesse momento. Mas não se preocupe, você poderá tentar novamente daqui <b>3 meses</b> ou sacar o seu saldo FGTS no <a href="https://app.qindin.com.br">Qindin FGTS</a>.</p>

    @if($user->motivo)
    <h5 style="font-weight:bold;font-size:16px;line-height:23px;color:#000;">Informações do Motivo</h5>
    <p>{{$user->motivo->mensagem}}</p>
    @endif

  </td>
</tr>
<tr>
  <td align="center" style="padding:0px 0px 30px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:23px;color:#000;text-align:left; padding: 0 5%;">
    <p>Esperamos te ver em breve.<br>
 </td>
</tr>
@endslot

@slot('email')
{{$user->email}}
@endslot

@endcomponent
