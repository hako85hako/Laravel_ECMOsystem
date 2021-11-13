<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\material;
use App\Models\material_detail;
use App\Models\material_kind;
use App\Models\pressure_drop;
use phpDocumentor\Reflection\PseudoTypes\False_;
use App\Models\maker;


class materialController extends Controller{

    public function index(){
        // DBよりmaterialテーブルの値を全て取得
        //$materials = material::all();
        $materials = material::where('DELETE_FLG',True)->get();

        return view('material/index', compact('materials'));
    }

    public function create(){
        $material = new material();
        $makers = maker::where('DELETE_FLG',True)->get();
        $material_kinds = material_kind::where('DELETE_FLG',True)->get();
        return view('material/create', compact('material','makers','material_kinds'));
    }

    public function store(Request $request){
        $material = new material();
        $material->COMPANY_NAME = $request->COMPANY_NAME;
        $material->MATERIAL_KIND = $request->MATERIAL_KIND;
        $material->MATERIAL_NAME = $request->MATERIAL_NAME;
        //$material->MATERIAL_SIZE = $request->MATERIAL_SIZE;
        //$material->MATERIAL_VOLUME = $request->MATERIAL_VOLUME;
        //$material->SLICE_FLOW = $request->SLICE_FLOW;
        //$material->SLICE_REVOLUTIONS = $request->SLICE_REVOLUTIONS;
        $material->save();
        $material_detail = new material_detail();
        $material_detail->MATERIAL_ID = $material->id;
        $material_detail->save();
        $pressuredrop = new pressure_drop();
        $pressuredrop->MATERIAL_DETAIL_ID = $material_detail->id;
        $pressuredrop->save();
        return redirect("/material");
    }

    public function edit($id){
        // DBよりURIパラメータと同じIDを持つBookの情報を取得
        $material = material::findOrFail($id);
        $makers = maker::where('DELETE_FLG',True)->get();
        $material_kinds = material_kind::where('DELETE_FLG',True)->get();
        return view('material/create', compact('material','makers','material_kinds'));
    }

    public function update(Request $request, $id){
        $material = material::findOrFail($id);
        $material->COMPANY_NAME = $request->COMPANY_NAME;
        $material->MATERIAL_KIND = $request->MATERIAL_KIND;
        $material->MATERIAL_NAME = $request->MATERIAL_NAME;
        //$material->MATERIAL_SIZE = $request->MATERIAL_SIZE;
        //$material->MATERIAL_VOLUME = $request->MATERIAL_VOLUME;
        //$material->SLICE_FLOW = $request->SLICE_FLOW;
        //$material->SLICE_REVOLUTIONS = $request->SLICE_REVOLUTIONS;
        $material->save();
        return redirect("/material");
    }

    public function destroy($id){
        //$material = material::findOrFail($id);
        //$material->delete();
        $material = material::findOrFail($id);
        $material->DELETE_FLG = 0;
        $material->save();
        return redirect("/material");
    }
}
