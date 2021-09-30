<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use Storage;
use App\Documento;
use App\TipoDocumento;
use App\Usuario;

class DocumentoController extends Controller
{
    
	public function get_file_extension($file_name) {
	    return substr(strrchr($file_name,'.'),1);
	}

	public function storeApp(Request $request) {

		try {
			
			if ($request->extrato_bancario) {
				$ftime = time();
				$user = Auth::user();

				$fileraw = explode(",",$request->extrato_bancario);
				//$ext = $this->get_file_extension($request->filename);
				$ext='pdf';

				Storage::put(
				    "documentos/".$user->id."/extrato_bancario_".$ftime.$request->seq.".".$ext,
				    base64_decode($fileraw[1])
				);

				Documento::create([
					'nome'=>$request->nome,
			      	'id_usuario'=>$user->id,
			        'titulo' => "extrato_bancario_".$ftime.$request->seq.".".$ext,
			        'tipo' => $ext,
			        'id_tipo_documento'=>TipoDocumento::getBySlug('extrato_bancario')->id,
				]);

				$user->opcao_documento = 'x';
				$user->extrato_bancario = null;
				$user->save();


			}

		} catch (PDOException $e) { // caso retorne erro
		  return response()->json(['success'=>false,'msg'=>$e->getMessage()]);
		  die();
		}

		

		return response()->json(['success'=>true]);

	}

	/*public function store(Request $request) {

		$idUsuario = Auth::user()->id;

		DB::beginTransaction();

		try {

			if ($_POST['tipo'] == 'rg') {

				if ($request->file('rg_frente')) {
					$ext = substr(strrchr($request->file('rg_frente')->getClientOriginalName(), '.'), 1);

					$path = Storage::putFileAs(
					    'documentos', $request->file('rg_frente'), "rg_frente_".$idUsuario.".".$ext
					);

					Documento::create([
				      	'id_usuario'=>$idUsuario,
				      	'nome' => $request->file('rg_frente')->getClientOriginalName(),
				        'titulo' => "rg_frente_".$idUsuario.".".$ext,
				        'tipo' => $request->file('rg_frente')->getMimeType(),
				        'url' => $path,
					]);
				}

				if ($request->file('rg_verso')) {
					$ext = substr(strrchr($request->file('rg_verso')->getClientOriginalName(), '.'), 1);
					$path = Storage::putFileAs(
					    'documentos', $request->file('rg_verso'), "rg_verso_".$idUsuario.".".$ext
					);

					Documento::create([
				      	'id_usuario'=>$idUsuario,
				      	'nome' => $request->file('rg_verso')->getClientOriginalName(),
				        'titulo' => "rg_verso_".$idUsuario.".".$ext,
				        'tipo' => $request->file('rg_verso')->getMimeType(),
				        'url' => $path,
					]);
				}

				if ($request->file('cpf_frente')) {
					$ext = substr(strrchr($request->file('cpf_frente')->getClientOriginalName(), '.'), 1);
					$path = Storage::putFileAs(
					    'documentos', $request->file('cpf_frente'), "cpf_frente_".$idUsuario.".".$ext
					);

					Documento::create([
				      	'id_usuario'=>$idUsuario,
				      	'nome' => $request->file('cpf_frente')->getClientOriginalName(),
				        'titulo' => "cpf_frente_".$idUsuario.".".$ext,
				        'tipo' => $request->file('cpf_frente')->getMimeType(),
				        'url' => $path,
					]);
				}

			} else if ($_POST['tipo'] == 'cnh') {
				
				if ($request->file('cnh_frente')) {
					$ext = substr(strrchr($request->file('cnh_frente')->getClientOriginalName(), '.'), 1);
					$path = Storage::putFileAs(
					    'documentos', $request->file('cnh_frente'), "cnh_frente_".$idUsuario.".".$ext
					);

					Documento::create([
				      	'id_usuario'=>$idUsuario,
				      	'nome' => $request->file('cnh_frente')->getClientOriginalName(),
				        'titulo' => "cnh_frente_".$idUsuario.".".$ext,
				        'tipo' => $request->file('cnh_frente')->getMimeType(),
				        'url' => $path,
					]);
				}

			}

			if ($request->file('comprovante_residencia')) {
				$ext = substr(strrchr($request->file('comprovante_residencia')->getClientOriginalName(), '.'), 1);
				$path = Storage::putFileAs(
				    'documentos', $request->file('comprovante_residencia'), "comprovante_residencia_".$idUsuario.".".$ext
				);

				Documento::create([
			      	'id_usuario'=>$idUsuario,
			      	'nome' => $request->file('comprovante_residencia')->getClientOriginalName(),
			        'titulo' => "comprovante_residencia_".$idUsuario.".".$ext,
			        'tipo' => $request->file('comprovante_residencia')->getMimeType(),
			        'url' => $path,
				]);
			}

			if ($request->file('extrato_bancario')) {
				$ext = substr(strrchr($request->file('extrato_bancario')->getClientOriginalName(), '.'), 1);
				$path = Storage::putFileAs(
				    'documentos', $request->file('extrato_bancario'), "extrato_bancario_".$idUsuario.".".$ext
				);

				Documento::create([
			      	'id_usuario'=>$idUsuario,
			      	'nome' => $request->file('extrato_bancario')->getClientOriginalName(),
			        'titulo' => "extrato_bancario_".$idUsuario.".".$ext,
			        'tipo' => $request->file('extrato_bancario')->getMimeType(),
			        'url' => $path,
				]);
			}

		} catch (PDOException $e) { // caso retorne erro
		  return response()->json(['success'=>false,'msg'=>$e->getMessage()]);
		  die();
		}

		DB::commit();

		return response()->json(['success'=>true]);

	}*/

}
