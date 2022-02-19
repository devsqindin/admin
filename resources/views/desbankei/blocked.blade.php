@component('mail::tema')

@slot('nome')
Olá, {{$user->nome_completo}}!
@endslot

@slot('corpo')
<tr>
  <td align="center" style="padding:0px 0px 30px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:23px;color:#000;text-align:left; padding: 0 5%;">
    <p><b>O seu crédito no Qindin e seu smartphone foram bloqueados por falta de pagamento.</b></p>
    <p>Para ter o seu crédito e as funções do seu smartphone de volta, pague o boleto que enviamos neste e-mail ou no app do Qindin. Assim que o pagamento for reconhecido, o seu crédito e seu smartphone serão desbloqueados.</p>
    <p><b>Evite maiores perdas por atraso como:</b></p>
    <p>• Diminuição do seu score personalizado no Qindin;</p>
    <p>• Diminuição de score nos birôs de crédito;</p>
    <p>• Negativação de CPF.</p>
  </td>
</tr>
@endslot

@slot('email')
{{$user->email}}
@endslot

@endcomponent
