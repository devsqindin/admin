@component('mail::tema')

@slot('nome')
{{$user->nome_completo}},<br>o seu crédito foi aprovado!
@endslot

@slot('corpo')
<tr>
  <td align="center" style="padding:0px 0px 30px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:23px;color:#000;text-align:left; padding: 0 5%;">
    <p><b>É maravilhoso ter você no Qindin! ❤️</b></p>
    <p>Falta pouco para você utilizar o seu crédito<b> como e quando quiser</b>.
    </p>
    <p>No Qindin, você não precisa ter bens caros quitados para oferecer como garantia. Apenas use seu smartphone.</p>
    <p>Em breve, nosso time irá te enviar o link para instalação do app "Brelo", que permite você disponibilizar o seu smartphone como garantia de maneira 100% segura e transparente.</p>
    <p><b>Estamos aqui por você!</b></p>
    <!-- BOTÃO -->
    <tr>
      <td align="center" style="padding:30px 0px 30px 0px">
        <a href='https://play.google.com/store/apps/details?id=com.qindin'>
          <img alt='Disponível no Google Play' src='https://play.google.com/intl/pt-BR/badges/static/images/badges/pt_badge_web_generic.png' width="220" />
        </a>
      </td>
    </tr>
    <!-- END BOTÃO -->
  </td>
</tr>
@endslot

@slot('email')
{{$user->email}}
@endslot

@endcomponent
