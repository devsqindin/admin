<?php

namespace App\BReAD;

use DataTables;
use App\BReAD\ControllerHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

abstract class BReADController extends Controller
{
	public static abstract function getClass($request);
	public static abstract function getFolder($type);
	public static abstract function getLink($type,$request);

    public function index(Request $request)
    {
        $model = $this->getClass($request);
        if(isset($model->info['paginate'])){
            $items = $model->paginate($model->info['paginate'])->items();
        }else{
            $items = $model->get();
        }
        
        $link = $this->getLink('index',$request);

        return view($this->getFolder('index').'.index',compact(['items','model','link']));
    }

    public function apiIndex(Request $request)
    {
        $model = $this->getClass($request);
        if(isset($model->info['paginate'])){
            $items = $model->paginate($model->info['paginate'])->items();
        }else{
            $items = $model->get();
        }
        
        $items = ControllerHelper::dig($items,$model);

        return response()->json(['success'=>true,'items'=>$items]);
    }

    public function create(Request $request)
    {
        $item = $this->getClass($request);
        $connect = $item->getRelations();
        $link = $this->getLink('create',$request);

        return view($this->getFolder('create').'.create',compact(['item','connect','link']));
    }

    public function apiCreate(Request $request)
    {
        $model = $this->getClass($request);

        return response()->json(['success'=>true,'model'=>['attributes'=>$model->atr,'foreign'=>$model->foreign]]);
    }

    public function apiCreateOrEdit(Request $request)
    {
        $model = $this->getClass($request);

        if ($request->id != null) {
            $model = $this->getClass($request);
            $item = $model->find($request->id);
            $response = ControllerHelper::update($model,$item,$request);
        } else {
            $response = ControllerHelper::store($model,$request);
        }
        if(method_exists($this,'afterCreateOrEdit') && $response['success']){
            $this->afterCreateOrEdit($request,$response['item']);
        }
        return response()->json($response);
    }

    public function show(Request $request)
    {
        $id = $request->route()->parameters[end($request->route()->parameterNames)];
        $model = $this->getClass($request);
        $item = $model->find($id);
        $connect = $item->getRelations();
        $link = $this->getLink('show',$request);

        return view($this->getFolder('show').'.show',compact(['item','link','connect','model']));
    }

    public function apiShow(Request $request)
    {
        $id = $request->route()->parameters[end($request->route()->parameterNames)];
        $model = $this->getClass($request);
        $item = $model->find($id);
        if(!is_null($item)){
            $connections = $item->getRelations();
        }else{
            $connections = [];
        }
        $item = ControllerHelper::dig($item,$model,true);

        return response()->json(['success'=>true,'item'=>$item,'connections'=>$connections]);
    }

    public function edit(Request $request)
    {
        $id = $request->route()->parameters[end($request->route()->parameterNames)];
        $model = $this->getClass($request);
        $item = $model->find($id);
        $connect = $item->getRelations();
        $link = $this->getLink('edit',$request);

        return view($this->getFolder('edit').'.edit',compact(['link','item','connect','model']));
    }

    public function apiEdit(Request $request)
    {
        $id = $request->route()->parameters[end($request->route()->parameterNames)];
        $model = $this->getClass($request);
        $item = $model->find($id);
        if(is_null($item))
        {
            return response()->json(['error'=>true,'success'=>false,'message'=>"Item $id doesn't exits"]);
        }

        return response()->json(['success'=>true,'item'=>$item,'model'=>['attributes'=>$model->atr,'foreign'=>$model->foreign]]);
    }

    public function destroy(Request $request)
    {
        $id = $request->route()->parameters[end($request->route()->parameterNames)];
        $this->getClass($request)->find($id)->delete();

        return redirect($this->getLink('destroy',$request));
    }

    public function apiDestroy(Request $request)
    {
        $id = $request->route()->parameters[end($request->route()->parameterNames)];
        $model = $this->getClass($request);

        try{
            $ret = $model->find($id)->delete();
        }catch(Exception $err){
            return response()->json(['error'=>true,'success'=>false,'message'=>$err]);
        }
        return response()->json(['success'=>true]);
    }

    public function store(Request $request)
    {
        $model = $this->getClass($request);

        ControllerHelper::store($model,$request);

        return redirect($this->getLink('store',$request));
    }

    public function apiStore(Request $request)
    {
        $model = $this->getClass($request);

        $response = ControllerHelper::store($model,$request);
        
        return response()->json($response);
    }

    public function update(Request $request)
    {
        $id = $request->route()->parameters[end($request->route()->parameterNames)];
        $model = $this->getClass($request);
        $item = $model->find($id);

        ControllerHelper::update($model,$item,$request);

        return redirect($this->getLink('update',$request).'/'.$id);
    }

    public function apiUpdate(Request $request)
    {
        $id = $request->route()->parameters[end($request->route()->parameterNames)];
        $model = $this->getClass($request);
        $item = $model->find($id);

        $response = ControllerHelper::update($model,$item,$request);

        return response()->json($response);
    }

    public function dtIndex(Request $request)
    {
        $x = $this->getClass($request);
        $test = $x::query();
        $quick = DataTables::of($test);
        return $quick->make(true);
    }

    public function apiSearch(Request $request)
    {
        $column = $request->query('column');
        $value = $request->query('value');
        $mode = $request->query('mode') ?? '';

        $model = $this->getClass($request);
        if(!is_null($value)){
            $items = $model->where($column,$mode,$value)->get();
        }else{
            $items = $model->get(); 
        }

        $items = ControllerHelper::dig($items,$model);

        return response()->json(['success'=>true,'items'=>$items]);
    }
}
