@component('mail::tema')

@slot('nome')
{{$user->nome_completo}},
boas vindas ao futuro do crédito: sem julgamentos.
@endslot

@slot('corpo')
<tr>
  <td align="center" style="padding:0px 0px 30px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:23px;color:#000;text-align:left; padding: 0 5%;">
    <p>Você já sabe que a Qindin nasceu com a missão de inovar e facilitar o acesso ao crédito para <b>todos</b>, sem julgamentos.<br><br>
      
      <b>Por isso, aqui está o seu convite para fazer parte da Qindin.</b> Mas tenha calma, isso não significa que você já tem um limite de crédito liberado! Para prosseguir com sua análise de crédito, basta seguir os passos abaixo:<br><br>
      
      <b>1.</b> Baixe ou atualize o app da Brelo para usar o seu smartphone como garantia Qindin na <a href="https://play.google.com/store/apps/details?id=com.brelo.score">Play Store</a>.<br>
      <b>2.</b> Após a instalação, clique em seu <a href="https://breloscore.page.link/?link=https://play.google.com/store/apps/details?id%3Dcom.brelo.score%26token%3D{{$token}}&apn=com.brelo.score&utm_campaign=t3&utm_medium=t2&utm_source=t1&efr=1">link personalizado</a> e realize o procedimento conforme os passos demonstrados em sua tela.<br>
      <b>3.</b> Aguarde o contato de nosso time com o resultado da avaliação do seu smartphone para saber se você possui um limite pré-aprovado na Qindin.<br><br>
      <b>Fácil, não é? Além disso, só na Qindin você tem as vantagens:</b><br><br>
      <b>&#10003;</b> Sem multas e juros por atraso.<br>
      <b>&#10003;</b> Sem precisar ter bens caros quitados para oferecer como garantia.<br>
      <b>&#10003;</b> Ganhe descontos por antecipação.<br>
      <b>&#10003;</b> Mesmo negativado ou com baixo score.<br>
      <b>&#10003;</b> Totalmente empático.<br>
      <b>&#10003;</b> 100% transparente.<br><br>
      E isso é <b>apenas o começo</b>.<br><br></p>
  </td>
</tr>
@endslot

@slot('email')
{{$user->email}}
@endslot

@endcomponent
