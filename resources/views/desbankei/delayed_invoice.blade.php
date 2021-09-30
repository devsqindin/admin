@component('mail::tema')

@slot('nome')
Olá, {{$user->nome_completo}}!
@endslot

@slot('corpo')
<tr>
  <td align="center" style="padding:0px 0px 16px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:24px;color:#666666;text-align:center">
    <p>Ainda não reconhecemos o pagamento da sua última fatura, com vencimento no dia <b>{{date("d/m/Y",strtotime($fatura->vencimento))}}</b> Mas não se preocupe, nós do Qindin estamos aqui para te ajudar com a organização da sua <b>vida financeira</b>.</p>
    <p>Para pagar a sua fatura é só utilizar o boleto que enviamos neste e-mail ou no app do Qindin.</p>
    <p><b>O atraso nas faturas pode ser ruim devido ao:</b></p>
    <p>• Bloqueio de crédito no Qindin;</p>
    <p>• Diminuição do seu score personalizado no Qindin;</p>
    <p>• Diminuição de score nos birôs de crédito;</p>
    <p>• Pagamento de juros;</p>
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
