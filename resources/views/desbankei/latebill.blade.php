@component('mail::tema')

@slot('nome')
{{$user->id}} {{$user->nome_completo}},<br>deseja antecipar fatura
@endslot

@slot('corpo')
<tr>
  <td align="center" style="padding:0px 0px 16px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:24px;color:#666666;text-align:center">
     <p>
       <b>Data de Vencimento:</b> {{date("d/m/Y",strtotime($dados['fatura']->vencimento))}}<br/>
       <b>Valor Total:</b> R$ {{number_format($dados['fatura']->valor_total,2,',','.')}}<br/>
       <b>Data de Antecipação:</b> {{date("d/m/Y",strtotime(date('y-m-d')))}}<br/>
        <br />
       <b>Taxa:</b> {{$user->taxa_juros*100}} %<br/>
       <b>Dias:</b> {{ (( (strtotime($dados['fatura']->vencimento) - strtotime(date('y-m-d'))) / (60 * 60 * 24) ) -1) }}<br/>
       <b>Fator de Antecipação: </b> {{ number_format(pow( ( 1 + $user->taxa_juros ) , ( (( (strtotime($dados['fatura']->vencimento) - strtotime(date('y-m-d'))) / (60 * 60 * 24) ) ) / 30 ) ) ,6,',','.') }}<br/>
       <b>Valor Antecipado a constar no Boleto:</b> R$ {{number_format(
       $dados['fatura']->valor_total 
                    / 
       (
        pow( ( 1 + $user->taxa_juros ) , 
                (
                    (( (strtotime($dados['fatura']->vencimento) - strtotime(date('y-m-d'))) / (60 * 60 * 24) ) ) / 30
                )
           ) 
       ) ,2,',','.')
       }}<br/>
        <br />
        <i>*A taxa de desconto utilizada no cálculo do valor presente do financiamento foi definida em consonância com o disposto no art. 2º da Resolução CMN n. 3.516/2007.</i>
     </p>
  </td>
</tr>

<tr>
  <td align="center" style="padding:16px 0px 0px 0px;font-family:'Source Sans Pro','Helvetica Neue',Calibre,Helvetica,Arial,sans-serif;font-weight:400;font-size:16px;line-height:24px;color:#666666;text-align:center">
    <p><b>Vamos vencer o sistema juntos! 😉💪🏻</b></p>

    <p>Conte com a gente,<br>
    <b>Equipe Qindin</b></p>
 </td>
</tr>
@endslot

@slot('email')
{{$user->email}}
@endslot

@endcomponent
