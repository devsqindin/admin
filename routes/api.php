<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function () {
//     dd(Auth::user());
// });

Route::post('/login','UsuarioController@logInProtected');

Route::post('/parcelamento/calcular2','ParcelamentoController@calcular2');

Route::get('/belvo/token','UsuarioController@belvoAccessToken');

Route::middleware('auth:api')->group(function(){
	Route::get('/bancos','UsuarioController@bancos');
	Route::get('/user','UsuarioController@userData');
	Route::get('/user/home','UsuarioController@userHome');
	Route::get('/user/documentos','UsuarioController@userDocumentos');

	Route::post('/user/antecipa','UsuarioController@userAntecipa');
	Route::post('/user/password','UsuarioController@userPassword');
	Route::post('/user/edit/{etapa}','UsuarioController@userEdit');
	Route::post('/user/complete/{etapa}','UsuarioController@userComplete');
	//Route::post('/documento/store','DocumentoController@store');
	Route::post('/user/tipo_documento','UsuarioController@pDocumentType');
	Route::post('/mati/confirm','UsuarioController@confirmMati');
	Route::post('/belvo/link','UsuarioController@belvoLink');
	Route::post('/documento/storeapp','DocumentoController@storeApp');

	Route::post('/parcelamento/calcular','ParcelamentoController@calcular');
	Route::post('/parcelamento/confirmar','ParcelamentoController@confirmar');
	Route::post('/parcelamento/registrar','ParcelamentoController@registrar');
});

//Route::get('/check_bill','ParcelamentoController@cCheckBill');
Route::get('/faq/perguntas/{parent}','AdminController@pegaPerguntas');
Route::get('/perguntas','AdminController@perguntasApp');
Route::get('/perguntasjson','AdminController@perguntasAppJson');

Route::post('/user/startconvite','UsuarioController@userStartConvite');
Route::post('/user/start','UsuarioController@userStart');
Route::post('/senha/redefinir', 'UsuarioController@forgotMyPass');

// Route::get('/cliente/{clienteId}','AdminController@pegaCliente');
// Route::get('/cliente/{clienteId}/faturas','AdminController@pegaFaturas');
// Route::get('/cliente/{clienteId}/creditos','AdminController@pegaCreditos');
// Route::get('/cliente/{clienteId}/documentos','AdminController@pegaDocumentos');
// Route::get('/cliente/{clienteId}/disponivel','AdminController@pegaDispoCreditos');
// Route::get('/cliente/{clienteId}/historico','AdminController@pegaHistorico');
// Route::get('/operadores','AdminController@pegaOperadores');
// Route::get('/operador/{id}','AdminController@pegaOperador');

//Route::post('/financiar', 'ParcelamentoController@financiar'); //homologação