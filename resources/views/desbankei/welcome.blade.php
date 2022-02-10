@component('mail::tema')

@slot('nome')
Olá, {{$user->nome_completo}}!
@endslot

@slot('corpo')
<tr>
  <td align="center" style="padding:0px 0px 30px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:23px;color:#000;text-align:left; padding: 0 5%;">
    <p><b>Você é muito mais que o seu score de crédito!</b></p>

    <p>Em breve, você poderá <b>transformar o seu cartão de débito em crédito</b> ou <b>receber juros mensais e milhas aéreas grátis</b> apenas investindo o limite sobrando no seu cartão de crédito.</p>

    <p>O Qindin nasceu para resolver o problema de mais de <b>86 milhões de pessoas no Brasil</b> sem acesso ao crédito justo.</p>

    <p>Nós acreditamos que <b>as pessoas devem ser dignas de confiança</b> não apenas com base em seu score de crédito. Por isso, é possível transformar o seu cartão de débito em crédito <b>mesmo com baixo score ou CPF negativado</b>.</p>

    <p>Em contrapartida, permitimos que pessoas comuns tornem-se investidores mesmo sem aportar dinheiro, utilizando apenas o seu cartão de crédito. <b>Ah, e totalmente sem risco!</b> Todo investimento com cartão de crédito realizado no Qindin possui garantia de recebimento. Com isso, acreditamos que conseguiremos entregar maior qualidade de vida aos nossos investidores e seus familiares, criando um ambiente <b>justo para todos</b>.</p>

    <p>Seja qual for a sua escolha, transformar o seu cartão de débito em crédito ou investir sem dinheiro, <b>nós sempre estaremos aqui por você</b>.</p>
  </td>
</tr>
<tr>
  <td align="center" style="padding:0px 0px 30px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:23px;color:#000;text-align:left; padding: 0 5%;">
    <p><b>Vamos vencer o sistema, juntos! 😉💪🏻</b></p>
 </td>
</tr>

@endslot

@slot('email')
{{$user->email}}
@endslot

@endcomponent
