@component('mail::tema')

@slot('nome')
Olá, {{$user->nome_completo}}!
@endslot

@slot('corpo')
<tr>
  <td align="center" style="padding:0px 0px 16px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:24px;color:#666666;text-align:center">
    <p><b>Seu cadastro foi bloqueado por irregularidades.</p>
    <p>Por favor entre em <b>contato com o nosso time respondendo este e-mail, pelo <a href="mailto:contato@qindin.com.br">contato@qindin.com.br</a></b>. Estamos aqui por você! 😉</p>
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
