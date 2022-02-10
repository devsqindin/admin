@component('mail::tema')

@slot('nome')
{{$user->nome_completo}},<br>a sua fatura já está fechada!
@endslot

@slot('corpo')
<tr>
  <td align="center" style="padding:0px 0px 30px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:23px;color:#000;text-align:left; padding: 0 5%;">
    <p>A sua fatura já está fechada e vence no dia <b>{{date("d/m/Y",strtotime($fatura->vencimento))}}</b>.</p>
    <p>Para pagar a sua fatura é só utilizar o boleto que enviamos neste email ou no app do Qindin.</p>
    <p>O seu pagamento será reconhecido em até <b>3 dias úteis.</b></p>
    <p><b>Mantenha uma vida financeira saudável:</b> Evite atrasos e o bloqueio do seu smartphone! 😉</p>
  </td>
</tr>
@endslot

@slot('email')
{{$user->email}}
@endslot

@endcomponent
