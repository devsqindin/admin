@component('mail::tema')

@slot('nome')
{{$user->nome_completo}},<br>o seu crédito foi aprovado!
@endslot

@slot('corpo')
<tr>
  <td align="center" style="padding:0px 0px 0px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:24px;color:#666666;text-align:center">
    <p>
      <b>É maravilhoso ter você no Qindin! ❤️
      </b>
    </p>
    <p>Falta pouco para você utilizar o seu crédito<b> como e quando quiser</b>.
    </p>
    <p>Ao contrário do que acontece nos bancos e financeiras, no Qindin os bons pagadores 
      <b>não pagam
      </b> pelos maus pagadores.
    </p>
    <p>E para não penalizar quem paga suas contas em dia, o Qindin possui uma taxa única de adesão de 
      <b>R$ 64,99
      </b> em caso de aprovação do crédito.
    </p>
    <p>Como o <b>seu crédito foi aprovado</b>, é só realizar o pagamento através do nosso pix (chave CNPJ): <span style="color: #0030f1"><b>40076375000150</b></span> 😃
    </p>
    <p>Ah! E se você pagar os seus boletos em dia, ao final 
      <b>receberá 100% da taxa de adesão de volta em sua conta.
      </b> E nunca mais será cobrado por adesão ou anuidade no Qindin. 😊
    </p>
    <!-- <p>O vencimento da sua fatura está definido para o dia <b>10</b> de cada mês, conforme sua escolha. Mas fica tranquilo, você pode alterar a data sempre que achar necessário!</p> -->
    <p>Simples, né? 
      <b>Estamos aqui por você!
      </b>
    </p>
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

<tr>
  <td align="center" style="padding:0px 0px 0px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:24px;color:#666666;text-align:center">
    <p>Conte com a gente,
      <br>
      <b>Equipe Qindin
      </b>
    </p>
  </td>
</tr>
@endslot

@slot('email')
{{$user->email}}
@endslot

@endcomponent
