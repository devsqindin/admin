@component('mail::tema')

@slot('nome')
{{$user->nome_completo}},<br>recebemos o seu pagamento!
@endslot

@slot('corpo')
<tr>
  <td align="center" style="padding:0px 0px 30px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:23px;color:#000;text-align:left; padding: 0 5%;">
    <p>Nós já recebemos o seu pagamento<br>com vencimento em <b>{{date("d/m/Y",strtotime($fatura->vencimento))}}</b>:</p>
    <p><b style="font-size:20px;color:#1a33ec;">R$ {{number_format($fatura->valor_total,2,',','.')}}</b><br><span style="color:#999999">{{date("d/m/Y",strtotime($fatura->dtpagamento))}}</span></p>
  </td>
</tr>
<tr>
  <td align="center" style="padding:0px 0px 30px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:23px;color:#000;text-align:left; padding: 0 5%;">
    <p>Muito obrigado.<br>
 </td>
</tr>
@endslot

@slot('email')
{{$user->email}}
@endslot

@endcomponent
