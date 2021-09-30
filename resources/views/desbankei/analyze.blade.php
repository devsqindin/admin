@component('mail::tema')

@slot('nome')
Olá, {{$user->nome_completo}}!
@endslot

@slot('corpo')
<tr>
  <td align="center" style="padding:0px 0px 16px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:24px;color:#666666;text-align:center">
    <p>Recebemos os seus dados e estamos analisando o seu pedido de crédito.</p>
    <p>Você receberá uma resposta por e-mail em até <b>7 dias</b>!</p>
  </td>
</tr>
<tr>
  <td align="center" style="padding:0px 0px 0px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:24px;color:#666666;text-align:center">
    <p><b>Vamos vencer o sistema, juntos! 😉💪🏻</b></p>

    <p>Conte com a gente,<br>
    <b>Equipe Qindin</b></p>
 </td>
</tr>
@endslot

@slot('email')
{{$user->email}}
@endslot

@endcomponent
