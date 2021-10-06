@component('mail::tema')

@slot('nome')
Olá, {{$user->nome_completo}}!
@endslot

@slot('corpo')
<tr>
  <td align="center" style="padding:0px 0px 16px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:24px;color:#666666;text-align:center">
    <p>Clique no link abaixo para alterar a sua senha:</p>
  </td>
</tr>

<!-- BOTÃO -->
<tr>
  <td align="center" style="padding:0px 0px 16px 0px">
    <a href="https://andromeda-prod10397524v3.qindin.com.br/reset-password?resettoken={{$resetToken}}" style="font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:14px;line-height:24px;color:#1a33ec;text-decoration:none;background-color:#ffffff;border-top:1px solid #1a33ec;border-bottom:1px solid #1a33ec;border-right:1px solid #1a33ec;border-left:1px solid #1a33ec;border-radius:4px;display:inline-block;padding:20px;" target="_blank">ALTERAR SENHA</a>
  </td>
</tr>
<!-- END BOTÃO -->

<tr>
  <td align="center" style="padding:16px 0px 0px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:24px;color:#666666;text-align:center">
    <p>Esse link irá expirar em 12 horas.</p>
    
    <p>Caso você não tenha pedido essa alteração, entre em contato com a gente o mais rápido possível.</p>
    
    <p>Conte com a gente,<br>
    <b>Equipe Qindin</b></p>
 </td>
</tr>
@endslot

@slot('email')
{{$user->email}}
@endslot

@endcomponent
