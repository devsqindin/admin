@component('mail::tema')

@slot('nome')
Olá, {{$user->nome_completo}}!
@endslot

@slot('corpo')
<tr>
  <td align="center" style="padding:0px 0px 30px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:23px;color:#000;text-align:left; padding: 0 5%;">
    <img src="https://media.giphy.com/media/st7RtYXtqAFMs/giphy.gif" width=100% style="margin-bottom: 64px;" />
    <p>
      <b>O seu convite do Qindin chegou!
      </b>
    </p>
    <p>Mas tenha calma, isso não significa que você já tem um limite de crédito liberado! Para prosseguir com sua análise de crédito, basta seguir os passos abaixo:
    </p>
    <p>
      <b>1.
      </b> Baixe ou atualize o app Qindin na 
      <a href="https://play.google.com/store/apps/details?id=com.qindin">Play Store
      </a>.
    </p>
    <p>
      <b>2.
      </b> Clique em "Login".
    </p>
    <p>
      <b>3.
      </b> Selecione a opção "Esqueceu sua senha?".
    </p>
    <p>
      <b>4.
      </b> Insira seu e-mail cadastrado no Qindin e clique em "Solicitar".
    </p>
    <p>
      <b>5.
      </b> Você receberá um e-mail de redefinição de senha. Após criar sua senha, basta voltar para o app Qindin, fazer o login com a senha cadastrada e seguir os passos na tela.
    </p>
    <p>😊
    </p>
    <!-- BOTÃO -->
    <tr>
      <td align="center" style="padding:16px 0px 16px 0px">
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
