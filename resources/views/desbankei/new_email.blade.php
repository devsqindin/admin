@component('mail::tema')

@slot('nome')
Olá, {{$user->nome_completo}}!
@endslot

@slot('corpo')
<tr>
  <td align="center" style="padding:0px 0px 16px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:24px;color:#666666;text-align:center">
    <p>Clique no link abaixo para autorizar a mudança do e-mail da conta de <b>{{$user->email}}</b> para <b>{{$user->new_email}}</b>:</p>
  </td>
</tr>

<!-- BOTÃO -->
<tr>
  <td align="center" style="padding:0px 0px 16px 0px">
    <a href="https://api.desbankei.com.br/reset-email?token={{$user->reset_email}}" style="font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:14px;line-height:24px;color:#1a33ec;text-decoration:none;background-color:#ffffff;border-top:1px solid #1a33ec;border-bottom:1px solid #1a33ec;border-right:1px solid #1a33ec;border-left:1px solid #1a33ec;border-radius:4px;display:inline-block;padding:20px;" target="_blank">ALTERAR E-MAIL</a>
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
{{$user->new_email}}
@endslot

@endcomponent
