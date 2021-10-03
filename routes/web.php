<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return redirect('/admin');
// });

/*Route::get('/datetime', function () {
	echo date("d/m/Y H:i:s");
});*/

// Route::prefix('admin')->name('admin.')->group(function () {
	Route::get('/','AdminController@login')->name('login');
	Route::get('/logout','AdminController@logout');
	Route::post('/login','AdminController@fazerLogin');
	Route::middleware('auth:admin')->group(function(){

		Route::get('/check_contas','AdminController@checkContas');
		Route::get('/ler','AdminController@ler');
		Route::get('/dashboard','AdminController@index')->name('dashboard');
		Route::get('/clientes/{total?}','AdminController@clientes')->name('clientes');
		Route::get('/cliente/{clienteId}','AdminController@cliente');
		Route::get('/perguntas','AdminController@perguntas')->name('perguntas');
		Route::get('/pergunta/{perguntaId}','AdminController@pergunta');
		Route::get('/taxas','AdminController@taxas')->name('taxas');
		Route::get('/operadores','AdminController@operadores')->name('operadores');
		Route::get('/motivos','AdminController@motivos')->name('motivos');

		Route::get('/belvo/test','AdminController@belvoTest');
		Route::get('/belvo/{type}/{userId}','AdminController@belvoConsulta');
		Route::get('/belvo/{type}/{userId}/cache','AdminController@belvoCache');
		Route::post('/importa/credito','AdminController@importaCredito');
		Route::post('/cancela/credito','AdminController@cancelaCredito');
		Route::post('/pergunta','AdminController@postPergunta')->name('post_pergunta');
		Route::post('/taxas','AdminController@postTaxas')->name('post_taxas');
		Route::post('/operador','AdminController@formOperador');
		Route::post('/motivo','AdminController@formMotivo');
		Route::post('/enviodoc','AdminController@enviodoc');

		Route::post('/cliente/convite','AdminController@formConvite');
		Route::post('/cliente/pessoais','AdminController@formPessoais');
		Route::post('/cliente/endereco','AdminController@formEndereco');
		Route::post('/cliente/financeiro','AdminController@formFinanceiro');
		Route::post('/cliente/credito','AdminController@formCredito');
		Route::post('/cliente/fatura','AdminController@formFatura');
		Route::post('/cliente/cobranca','AdminController@formCobranca');
		Route::post('/cliente/cobranca/apagar','AdminController@apagarCobranca');
		Route::post('/cliente/fatura/cancelar','AdminController@cancelarEmissaoFatura');
		Route::post('/cliente/fatura/marcar','AdminController@marcarFatura');
		Route::post('/cliente/bloqueio','AdminController@bloqueioCliente');
		Route::post('/cliente/{clienteId}/cadastro/{step?}','AdminController@pAuthorizeUser');
		//Route::post('/cliente/{clienteId}/acao','AdminController@pActions');
		Route::post('/cliente/{clienteId}/aceite','AdminController@aceiteDocumento');
		Route::post('/cliente/{clienteId}/acao','AdminController@postAcao');
	});
// });

Route::prefix('cron')->name('cron.')->group(function () {
	Route::get('/fatura','AdminController@cronFatura');
});

Route::middleware('auth:admin')->group(function(){ //mover
	Route::get('/cliente/{clienteId}/fatura/{faturaId}','AdminController@carregaFatura');
	Route::get('/cliente/{clienteId}/fatura/{faturaId}/cobranca','AdminController@carregaCobranca');
	Route::get('/cliente/{clienteId}/creditos','AdminController@carregaCreditos');
	Route::get('/cliente/{clienteId}/documento/{documentoId}','AdminController@dwnDocumento');
});

Route::prefix('api')->name('api.')->group(function () {
	Route::middleware('auth:admin')->group(function(){
		Route::get('/testt','AdminController@testt');
		Route::get('/clientes/{total?}','AdminController@pegaClientes');
		Route::get('/cliente/{clienteId}','AdminController@pegaCliente');
		Route::get('/cliente/{clienteId}/faturas','AdminController@pegaFaturas');
		Route::get('/cliente/{clienteId}/creditos','AdminController@pegaCreditos');
		Route::get('/cliente/{clienteId}/documentos','AdminController@pegaDocumentos');
		Route::get('/cliente/{clienteId}/disponivel','AdminController@pegaDispoCreditos');
		Route::get('/cliente/{clienteId}/historico','AdminController@pegaHistorico');

		Route::get('/operadores','AdminController@pegaOperadores');
		Route::get('/operador/{id}','AdminController@pegaOperador');
		Route::get('/operador/{id}/apagar','AdminController@apagarOperador');
		Route::get('/motivos','AdminController@pegaMotivos');
		Route::get('/motivo/{id}','AdminController@pegaMotivo');
	});
});

//Route::get('/bancos','UsuarioController@bancos');
Route::get('/send','UsuarioController@sendNotification');
Route::get('/reset-password','UsuarioController@recoverPass');
Route::get('/reset-email','UsuarioController@resetEmail');
Route::post('/reset-password','UsuarioController@changePass');
