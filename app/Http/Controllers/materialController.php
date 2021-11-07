<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\material;
use App\Models\pressure_drop;
use phpDocumentor\Reflection\PseudoTypes\False_;


class materialController extends Controller
{
    public function index(){
        // DBよりmaterialテーブルの値を全て取得
        //$materials = material::all();
        $materials = material::where('DELETE_FLG',True)->get();
        // 取得した値をビュー「book/index」に渡す
        return view('material/index', compact('materials'));
    }

    public function create()
    {
        // 空の$bookを渡す
        $material = new material();
        return view('material/create', compact('material'));
    }

    public function store(Request $request)
    {
        $material = new material();
        $material->COMPANY_NAME = $request->COMPANY_NAME;
        $material->MATERIAL_KIND = $request->MATERIAL_KIND;
        $material->MATERIAL_NAME = $request->MATERIAL_NAME;
        //$material->MATERIAL_SIZE = $request->MATERIAL_SIZE;
        //$material->MATERIAL_VOLUME = $request->MATERIAL_VOLUME;
        //$material->SLICE_FLOW = $request->SLICE_FLOW;
        //$material->SLICE_REVOLUTIONS = $request->SLICE_REVOLUTIONS;
        $material->save();
        $pressuredrop = new pressure_drop();
        $pressuredrop->MATERIAL_ID = $material->id;
        $pressuredrop->save();

        return redirect("/material");
    }

    public function edit($id){
        // DBよりURIパラメータと同じIDを持つBookの情報を取得
        $material = material::findOrFail($id);

        // 取得した値をビュー「book/edit」に渡す
        return view('material/edit', compact('material'));
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
