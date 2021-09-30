@component('mail::tema')

@slot('nome')
Olá, {{$user->nome_completo}}!
@endslot

@slot('corpo')
<tr>
  <td align="center" style="padding:0px 0px 16px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:24px;color:#666666;text-align:center">
    <p><b>O seu crédito no Qindin foi bloqueado por falta de pagamento.</b></p>
    <p>Para ter o seu crédito de volta, pague o boleto que enviamos neste e-mail ou no app do Qindin. Assim que o pagamento for reconhecido, o seu crédito será desbloqueado.</p>
    <p><b>Evite maiores perdas por atraso como:</b></p>
    <p>• Diminuição do seu score personalizado no Qindin;</p>
    <p>• Diminuição de score nos birôs de crédito;</p>
    <p>• Pagamento de mais juros;</p>
    <p>• Negativação de CPF.</p>
    <p>Se você precisa de ajuda, fique à vontade para entrar em <b>contato com o nosso time respondendo este e-mail, pelo <a href="mailto:contato@qindin.com.br">contato@qindin.com.br</a></b>. Estamos aqui por você! 😉</p>
  </td>
</tr>
<tr>
  <td align="center" style="padding:0px 0px 0px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:24px;color:#666666;text-align:center">
    <p>Conte com a gente,<br>
    <b>Equipe Qindin</b></p>
 </td>
</tr>
@endslot

@slot('email')
{{$user->email}}
@endslot

@endcomponent
