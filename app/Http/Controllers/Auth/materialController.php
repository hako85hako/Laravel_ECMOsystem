<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\material;
use App\Models\material_detail;
use App\Models\material_kind;
use App\Models\pressure_drop;
use phpDocumentor\Reflection\PseudoTypes\False_;
use App\Models\maker;


class materialController extends Controller{

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        // DBよりmaterialテーブルの値を全て取得
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
        $material->CREATE_USER = Auth::user()->name;
        $material->UPDATE_USER = Auth::user()->name;
        $material->CREATE_USER_ID = Auth::user()->id;
        $material->UPDATE_USER_ID = Auth::user()->id;
        $material->save();
        $material_detail = new material_detail();
        $material_detail->MATERIAL_ID = $material->id;
        $material_detail->CREATE_USER = Auth::user()->name;
        $material_detail->UPDATE_USER = Auth::user()->name;
        $material_detail->CREATE_USER_ID = Auth::user()->id;
        $material_detail->UPDATE_USER_ID = Auth::user()->id;
        $material_detail->save();
        $pressuredrop = new pressure_drop();
        $pressuredrop->MATERIAL_DETAIL_ID = $material_detail->id;
        $pressuredrop->CREATE_USER = Auth::user()->name;
        $pressuredrop->UPDATE_USER = Auth::user()->name;
        $pressuredrop->CREATE_USER_ID = Auth::user()->id;
        $pressuredrop->UPDATE_USER_ID = Auth::user()->id;
        $pressuredrop->save();
        return redirect("/material");
    }

    public function edit($id){
        // DBよりURIパラメータと同じIDを持つBookの情報を取得
        $material = material::findOrFail($id);
        $makers = maker::where('DELETE_FLG',True)->get();
        $material_kinds = material_kind::where('DELETE_FLG',True)->get();
        return view('material/edit', compact('material','makers','material_kinds'));
    }

    public function update(Request $request, $id){
        $material = material::findOrFail($id);
        $material->COMPANY_NAME = $request->COMPANY_NAME;
        $material->MATERIAL_KIND = $request->MATERIAL_KIND;
        $material->MATERIAL_NAME = $request->MATERIAL_NAME;
        $material->UPDATE_USER = Auth::user()->name;
        $material->UPDATE_USER_ID = Auth::user()->id;
        $material->save();
        return redirect("/material");
    }

    public function destroy($id){
        //$material = material::findOrFail($id);
        //$material->delete();
        $material = material::findOrFail($id);
        $material->DELETE_FLG = 0;
        $material->UPDATE_USER = Auth::user()->name;
        $material->UPDATE_USER_ID = Auth::user()->id;
        $material->save();
        return redirect("/material");
    }
}
