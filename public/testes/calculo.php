<?php

function consultaApiFiducia($valor,$valor_tac,$vencimento,$meses,$juros) {
	$curlHandler = curl_init();
	$userName = 'HOMOLOG';
	$password = '1234567890';
	curl_setopt_array($curlHandler, [
	    CURLOPT_URL => 'https://api.bancarizacao.fiducia.digital/api/v1/bancarizacao/simular',
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
	    CURLOPT_USERPWD => $userName . ':' . $password,
	    /**
	     * Specify POST method
	     */
	    CURLOPT_POST => true,
	    /**
	     * Specify request headers
	     */
	    CURLOPT_HTTPHEADER => [
	        'user: 90000000001'
	    ],
	    /**
	     * Specify request content
	     */
	    CURLOPT_POSTFIELDS => '{
	    "tipo_pessoa": "PF",
	    "valor_liberado": '.number_format($valor,2,'.','').',
	    "primeiro_vencimento": "'.$vencimento.'",
	    "parcelas": '.$meses.',
	    "taxa_juros": '.($juros*100).',
	    "TAC": '.$valor_tac.',
	    "periodicidade": "M"
		}',
	]);
	
	echo '<br>{
    "tipo_pessoa": "PF",
    "valor_liberado": '.$valor.',
    "primeiro_vencimento": "'.number_format($valor,2,'.','').'",
    "parcelas": '.$meses.',
    "taxa_juros": '.($juros*100).',
    "TAC": '.$valor_tac.',
    "periodicidade": "M"
	}<br>';

	$response = curl_exec($curlHandler);
	curl_close($curlHandler);
	$aresponse = json_decode($response,1);
   	return ($aresponse['status'] == 'sucesso') ? $aresponse : false;
}

$valor = 1000;
$meses = 3;
$vencimento = '2021-04-10';
$juros = 0.0499;

$taxas['taxa_intermedia'] = 0.0499;
$taxas['tarifa_core'] = 5;
$taxas['taxa_core'] = 0.012;
$taxas['tarifa_serasa'] = 15;
$taxas['taxa_cardholder'] = 0.0617;
$taxas['custos_fixo_bdi'] = 0.03;
$taxas['taxa_simples_nac'] = 0.01;
$taxas['taxa_imp_tac'] = 0.0975;

$txint['valor'][3] = 0.0604;
$txint['valor'][4] = 0.0759;
$txint['valor'][12] = 0.1980;

$taxa_variavel_intermediador = $txint['valor'][$meses];
$valor_base_cardholder = $valor / (1 - $taxas['taxa_intermedia']);
$valor_base_remuneracao = $valor_base_cardholder * (1+$taxa_variavel_intermediador);

echo 'taxa_variavel_intermediador '.$taxa_variavel_intermediador."<br>";
echo 'valor_base_cardholder '.$valor_base_cardholder."<br>";
echo 'valor_base_remuneracao '.$valor_base_remuneracao."<br>";

// elementos componentes da tac

$valor_tar_core = $taxas['tarifa_core'];
$valor_cons_tomador = $taxas['tarifa_serasa'];
$valor_cred_intermediador = $valor_base_cardholder - $valor;
$valor_intermediador_cardholder = $valor_base_cardholder * $taxa_variavel_intermediador;
$valor_remuneracao_cardholder = $valor_base_remuneracao * $taxas['taxa_cardholder'];
$valores_custos_bdi = $valor * $taxas['custos_fixo_bdi'];

$valor_tx_core = ($valor_base_cardholder + $valor_tar_core + $valor_cons_tomador + $valor_cred_intermediador + $valor_intermediador_cardholder + $valor_remuneracao_cardholder) * $taxas['taxa_core'];

echo '<br>';
echo 'valor_tar_core '.$valor_tar_core.'<br>';
echo 'valor_tx_core '.$valor_tx_core.'<br>';
echo 'valor_cons_tomador '.$valor_cons_tomador.'<br>';
echo 'valor_cred_intermediador '.$valor_cred_intermediador.'<br>';
echo 'valor_intermediador_cardholder '.$valor_intermediador_cardholder.'<br>';
echo 'valor_remuneracao_cardholder '.$valor_remuneracao_cardholder.'<br>';
echo 'valores_custos_bdi '.$valores_custos_bdi.'<br>';


$subtotal_base_tac = $valor_tar_core + $valor_tx_core + $valor_cons_tomador + $valor_cred_intermediador + $valor_intermediador_cardholder + $valor_remuneracao_cardholder + $valores_custos_bdi;

$valor_tac = $subtotal_base_tac / (1 - ($taxas['taxa_simples_nac']+$taxas['taxa_imp_tac']));

echo '<br>subtotal tac '.$subtotal_base_tac;
echo '<br>valor tac '.$valor_tac;

$valor_tac = number_format($valor_tac,2,'.','');

$calculo = consultaApiFiducia($valor,$valor_tac,$vencimento,$meses,$juros);

echo '<pre>';
print_r($calculo);