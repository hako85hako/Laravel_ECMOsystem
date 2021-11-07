<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\material;
use App\Models\material_detail;
use App\Models\pressure_drop;
use phpDocumentor\Reflection\PseudoTypes\False_;


class materialDetailController extends Controller
{
    public function index(){
        //indexは使用しない
        //showをindexのように使用する
    }

    public function show($id){
        // DBよりmaterialテーブルの値を全て取得
        //$materials = material::all();
        $material_details = material_detail::where('DELETE_FLG',True)
        ->where('MATERIAL_ID',$id)
        ->get();
        // 取得した値をビュー「book/index」に渡す
        return view('material-detail/index', compact('material_details'));
    }



    public function create(Request $request)
    {
        // 空の$bookを渡す
        $material_detail = new material_detail();
        $material_detail->MATERIAL_ID = $request->material_id;
        $material_detail->save();
        $pressuredrop = new pressure_drop();
        $pressuredrop->MATERIAL_DETAIL_ID = $material_detail->id;
        $pressuredrop->FLOW = 0;
        $pressuredrop->save();
        return redirect("/material-detail/".$request->material_id)->with($request->material_id);
    }

    public function store(Request $request)
    {
        $material_detail = new material_detail();
        //$material_detail->MATERIAL_ID = $request->MATERIAL_ID;
        $material_detail->MATERIAL_SIZE = $request->MATERIAL_SIZE;
        $material_detail->MATERIAL_VOLUME = $request->MATERIAL_VOLUME;
        $material_detail->SLICE_FLOW = $request->SLICE_FLOW;
        $material_detail->SLICE_REVOLUTIONS = $request->SLICE_REVOLUTIONS;
        $material_detail->save();
        $material_details = material_detail::where('DELETE_FLG',True)
        ->where('MATERIAL_ID',$request->MATERIAL_ID)
        ->get();
        return view('material-detail/index', compact('material_details'));
    }

    public function edit($id){
        // DBよりURIパラメータと同じIDを持つBookの情報を取得
        $material_detail = material_detail::findOrFail($id);
        // 取得した値をビュー「book/edit」に渡す
        return view('material-detail/edit', compact('material_detail'));
    }

    public function update(Request $request, $id){
        $material_detail = material_detail::findOrFail($id);
        //$material_detail->MATERIAL_ID = $request->MATERIAL_ID;
        $material_detail->MATERIAL_SIZE = $request->MATERIAL_SIZE;
        $material_detail->MATERIAL_VOLUME = $request->MATERIAL_VOLUME;
        $material_detail->SLICE_FLOW = $request->SLICE_FLOW;
        $material_detail->SLICE_REVOLUTIONS = $request->SLICE_REVOLUTIONS;
        $material_detail->save();
        return redirect("/material-detail/".$request->material_id)->with($request->material_id);
    }

    public function destroy(Request $request, $id){
        //$material = material::findOrFail($id);
        //$material->delete();
        $material_detail = material_detail::findOrFail($id);
        $material_detail->DELETE_FLG = 0;
        $material_detail->save();
        return redirect("/material-detail/".$request->material_id)->with($request->material_id);

    }
}
