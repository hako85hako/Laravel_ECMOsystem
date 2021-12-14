<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\material;
use App\Models\material_detail;
use App\Models\material_kind;
use App\Models\pressure_drop;
use App\Models\simulation;
use phpDocumentor\Reflection\PseudoTypes\False_;
use App\Models\maker;
use App\Models\simulation_detail;


class simulationDetailController extends Controller{

    public function __construct(){
        $this->middleware('auth');
    }
    //新規物品の登録
    public function create(Request $request){
        $simulation_detail = new simulation_detail();
        $simulation_detail->SIMULATION_ID = $request->simulation_id;
        $simulation_details = simulation_detail::where('DELETE_FLG',True)
        ->where('SIMULATION_ID',$request->simulation_id)
        ->get();
        DB::beginTransaction();
        try{
            $simulation_detail->SERIAL_NUMBER = $simulation_details->count()+1;
            $simulation_detail->CREATE_USER = Auth::user()->name;
            $simulation_detail->UPDATE_USER = Auth::user()->name;
            $simulation_detail->CREATE_USER_ID = Auth::user()->id;
            $simulation_detail->UPDATE_USER_ID = Auth::user()->id;
            $simulation_detail->save();
            DB::commit();
        }catch (\Exception $e) {
            DB::rollback();
        }
        return redirect("/simulation/".$request->simulation_id)->with($request->simulation_id);
    }

    public function store(Request $request){
        DB::beginTransaction();
        try{
            $simulation = new simulation();
            $simulation->SIMULATION_NAME = $request->SIMULATION_NAME;
            $simulation->CREATE_USER = Auth::user()->name;
            $simulation->UPDATE_USER = Auth::user()->name;
            $simulation->CREATE_USER_ID = Auth::user()->id;
            $simulation->UPDATE_USER_ID = Auth::user()->id;
            $simulation->save();
            DB::commit();
        }catch (\Exception $e) {
            DB::rollback();
        }
        return redirect("/simulation");
    }

    public function edit($id){
        // DBよりURIパラメータと同じIDを持つBookの情報を取得
        $simulation = simulation::findOrFail($id);

        return view('simulation/edit', compact('simulation'));
    }

    public function update(Request $request, $id){
        $simulation_detail = simulation_detail::findOrFail($id);
        if($request->type == 0){
            //materialの更新をした場合
            //こちらはmaterial_detail_idを一度リセットする必要あり
            $simulation_detail->MATERIAL_ID = $request->MATERIAL_ID;
            $material_detail_id = material_detail::where('DELETE_FLG',True)
            ->where('MATERIAL_ID',$request->MATERIAL_ID)
            ->first();
            $simulation_detail->MATERIAL_DETAIL_ID = $material_detail_id->id;
        }else if($request->type == 1){
            //material_detailの更新をした場合
            $simulation_detail->MATERIAL_DETAIL_ID = $request->MATERIAL_DETAIL_ID;
        }else if($request->type == 2){
            //material_detailのIDを前後入れ替える
            //simulationに対してのsimulation_detailの個数を算出
            $simulation_details = simulation_detail::where('DELETE_FLG',True)
            ->where('SIMULATION_ID',$request->simulation_id)
            ->get();
            $simulation_details_count = $simulation_details->count();
            //選択したsimulation_detailのシリアル番号取得
            $simulation_detail_num = $simulation_detail->SERIAL_NUMBER;
            DB::beginTransaction();
            try{
                //最大個数とシリアル番号が一致するならスルーする
                if($simulation_details_count!=$simulation_detail_num){
                    //一致しない（シリアル番号入れ替え可能）ならシリアル番号を入れ替える
                    //＋1のシリアル番号のモデル
                    $simulation_detail_puls1 = simulation_detail::where('DELETE_FLG',True)
                    ->where('SIMULATION_ID',$request->simulation_id)
                    ->where('SERIAL_NUMBER',$simulation_detail_num+1)
                    ->first();
                    //SERIAL_NUMBERの入れ替え
                    $simulation_detail_puls1->SERIAL_NUMBER -= 1;
                    $simulation_detail->SERIAL_NUMBER += 1;
                    //＋1のシリアル番号のモデルを保存
                    $simulation_detail_puls1->UPDATE_USER = Auth::user()->name;
                    $simulation_detail_puls1->UPDATE_USER_ID = Auth::user()->id;
                    $simulation_detail_puls1->save();
                }
                DB::commit();
            }catch (\Exception $e) {
                DB::rollback();
            }
        }elseif($request->type == 3){
            //遠心ポンプの回転数設定処理
            $simulation_detail->REVOLUTION_INF = $request->SPEED;
        }
        DB::beginTransaction();
        try{
            $simulation_detail->UPDATE_USER = Auth::user()->name;
            $simulation_detail->UPDATE_USER_ID = Auth::user()->id;
            $simulation_detail->save();
            DB::commit();
        }catch (\Exception $e) {
            DB::rollback();
        }
        //return view('simulation/index', compact('simulations'));
        return redirect("/simulation/".$simulation_detail->SIMULATION_ID)->with($simulation_detail->SIMULATION_ID);
    }

    public function destroy($id){
        DB::beginTransaction();
        try{
            $simulation_detail = simulation_detail::findOrFail($id);
            $simulation_detail->DELETE_FLG = 0;
            $simulation_detail->UPDATE_USER = Auth::user()->name;
            $simulation_detail->UPDATE_USER_ID = Auth::user()->id;
            $simulation_detail->save();
            DB::commit();
        }catch (\Exception $e) {
            DB::rollback();
        }
        return redirect("/simulation/".$simulation_detail->SIMULATION_ID)->with($simulation_detail->SIMULATION_ID);
    }
}
