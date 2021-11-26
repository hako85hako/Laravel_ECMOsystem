<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\material;
use App\Models\material_detail;
use App\Models\material_kind;
use App\Models\pressure_drop;
use App\Models\simulation;
use phpDocumentor\Reflection\PseudoTypes\False_;
use App\Models\maker;
use App\Models\simulation_detail;


class simulationController extends Controller{

    public function __construct(){
        $this->middleware('auth');
    }

    //今までのシミュレーションを表示
    //無ければ新規シミュレーションに移行
    public function index(){
        $simulations = simulation::where('DELETE_FLG',True)
        ->where('CREATE_USER_ID',Auth::user()->id)
        ->get();
        return view('simulation/index', compact('simulations'));
    }

    //新規シミュレーションの登録
    public function create(){
        $simulation = new simulation();
        $simulation->CREATE_USER = Auth::user()->name;
        $simulation->UPDATE_USER = Auth::user()->name;
        $simulation->CREATE_USER_ID = Auth::user()->id;
        $simulation->UPDATE_USER_ID = Auth::user()->id;
        return view('simulation/create', compact('simulation'));
    }

    //シミュレーションの詳細表示
    public function show(){
        $simulations = simulation::where('DELETE_FLG',True)
        ->where('CREATE_USER_ID',Auth::user()->id)
        ->get();
        return view('simulation/index', compact('simulations'));
    }

    public function store(Request $request){
        $simulation = new simulation();
        $simulation->SIMULATION_NAME = $request->SIMULATION_NAME;
        $simulation->CREATE_USER = Auth::user()->name;
        $simulation->UPDATE_USER = Auth::user()->name;
        $simulation->CREATE_USER_ID = Auth::user()->id;
        $simulation->UPDATE_USER_ID = Auth::user()->id;
        $simulation->save();
        return redirect("/simulation");
    }

    public function edit($id){
        // DBよりURIパラメータと同じIDを持つBookの情報を取得
        $simulation = simulation::findOrFail($id);

        return view('simulation/edit', compact('simulation'));
    }

    public function update(Request $request, $id){
        $simulation = simulation::findOrFail($id);
        $simulation->SIMULATION_NAME = $request->SIMULATION_NAME;
        //$simulation->UPDATE_USER = Auth::user()->name;
        $simulation->UPDATE_USER_ID = Auth::user()->id;
        $simulation->save();
        $simulations = simulation::where('DELETE_FLG',True)
        ->where('CREATE_USER_ID',Auth::user()->id)
        ->get();
        return view('simulation/index', compact('simulations'));
    }

    public function destroy($id){
        //$material = material::findOrFail($id);
        //$material->delete();
        $simulation = simulation::findOrFail($id);
        $simulation->DELETE_FLG = 0;
        $simulation->UPDATE_USER = Auth::user()->name;
        $simulation->UPDATE_USER_ID = Auth::user()->id;
        $simulation->save();
        return redirect("/simulation");
    }
}
