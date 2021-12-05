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
    public function create(Request $request){
        $simulation = new simulation();
        $simulation->CREATE_USER = Auth::user()->name;
        $simulation->UPDATE_USER = Auth::user()->name;
        $simulation->CREATE_USER_ID = Auth::user()->id;
        $simulation->UPDATE_USER_ID = Auth::user()->id;
        return view('simulation/create', compact('simulation'));
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
        if(isset($request->type)){
            $simulation->FLOW = $request->FLOW;
            $simulation->ABP = $request->ABP;
            $simulation->CVP = $request->CVP;
            if($request->ABP_flg=="on"){
                $simulation->ABP_FLG = 1;
            }else{
                $simulation->ABP_FLG = 0;
            }
            if($request->CVP_flg=="on"){
                $simulation->CVP_FLG = 1;
            }else{
                $simulation->CVP_FLG = 0;
            }
            $simulation->UPDATE_USER = Auth::user()->name;
            $simulation->UPDATE_USER_ID = Auth::user()->id;
            $simulation->save();
            return redirect("/simulation/".$id)->with($id);

        }else{
            $simulation->SIMULATION_NAME = $request->SIMULATION_NAME;
            $simulation->UPDATE_USER = Auth::user()->name;
            $simulation->UPDATE_USER_ID = Auth::user()->id;
            $simulation->save();
            $simulations = simulation::where('DELETE_FLG',True)
            ->where('CREATE_USER_ID',Auth::user()->id)
            ->get();
            return view('simulation/index', compact('simulations'));
        }
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

    //シミュレーションの詳細表示
    public function show($id){
        $errorSetting = new errorSetting();
        $simulation = simulation::findOrFail($id);
        $simulation_details = simulation_detail::where('DELETE_FLG',True)
        ->where('SIMULATION_ID',$id)
        ->orderBy('SERIAL_NUMBER', 'asc')
        ->get();
        //ID確認処理&振り直し処理
        for($i = 0; $i<count($simulation_details);$i++){
            $simulation_details[$i]->SERIAL_NUMBER = $i+1;
            $simulation_details[$i]->save();
        }
        //回転数格納用配列を設置
        $speeds = array();
        $graphData = array();
        $graphLabel = array();
        //流量格納用配列を設置　
        //流量情報をセット
        $flow = $simulation->FLOW;
        //中心静脈圧フラグの確認
        if($simulation->CVP_FLG){
            //中心静脈圧をセット
            $graphData[] = $simulation->CVP;
            $graphLabel[] = 'CVP';
        }
        for($i = 0; $i<count($simulation_details);$i++){
            //物品情報を抽出
            $material_kinds = material::where('DELETE_FLG',True)
            ->where('id',$simulation_details[$i]->MATERIAL_ID)
            ->first();
            //物品詳細情報を取得
            $material_detail_kinds = material_detail::where('DELETE_FLG',True)
            ->where('id',$simulation_details[$i]->MATERIAL_DETAIL_ID)
            ->first();
            //流量情報を確認して格納
            //$material_detail_kinds->SLICE_FLOW;

            //存在確認
            if(isset($material_kinds->MATERIAL_KIND)){
                if($material_kinds->MATERIAL_KIND == "Centrifugal-pump"){
                    //回転数の種類を抽出
                    $pre_speeds = pressure_drop::
                    where('MATERIAL_DETAIL_ID',$simulation_details[$i]->MATERIAL_DETAIL_ID)
                    ->where('DELETE_FLG',True)
                    ->groupBy('SPEED')
                    ->get('SPEED');
                    //キーをつけて格納する用
                    $set_speeds = array();
                    for($ii = 0; $ii<count($pre_speeds);$ii++){
                        $set_speeds[] = $pre_speeds[$ii]->SPEED;
                    }
                    //speedのリストを格納
                    $speeds[$simulation_details[$i]->MATERIAL_DETAIL_ID] = $set_speeds;
                    //Simulation_detailsのポンプフラグをセット
                    $simulation_details[$i]->PUMP_FLG = 1;
                    $simulation_details[$i]->save();
                    //圧力構築物品の場合
                    //回転数情報をセット
                    $speed = $simulation_details[$i]->REVOLUTION_INF;
                    //物品の揚程を算出
                    $head = pressure_drop::where('DELETE_FLG',True)
                    ->where('MATERIAL_DETAIL_ID',$simulation_details[$i]->MATERIAL_DETAIL_ID)
                    ->where('FLOW',$flow)
                    ->where('SPEED',$speed)
                    ->first();
                    if(isset($head)){
                        if(isset($graphData[$i])){
                            $graphData[] = $graphData[$i] + $head->HEAD;
                            $graphLabel[] = $material_kinds->MATERIAL_NAME.' '.$speed."rpm";
                            $errorSetting->errorSet($simulation_details[$i],0);
                        }else{
                            $graphData[] = $head->HEAD;
                            $graphLabel[] = $material_kinds->MATERIAL_NAME.' '.$speed."rpm";
                            $errorSetting->errorSet($simulation_details[$i],2);
                        }
                    }else{
                        $graphData[] = $graphData[$i] + 0;
                        $graphLabel[] = $material_kinds->MATERIAL_NAME.' '.$speed."rpm";
                        $errorSetting->errorSet($simulation_details[$i],4);
                    }
                }else{
                    //Simulation_detailsのポンプフラグを回収
                    $simulation_details[$i]->PUMP_FLG = 0;
                    $simulation_details[$i]->save();
                    //圧力損失物品の場合
                    //物品の圧力損失を算出
                    $pressuredrop = pressure_drop::where('DELETE_FLG',True)
                    ->where('MATERIAL_DETAIL_ID',$simulation_details[$i]->MATERIAL_DETAIL_ID)
                    ->where('FLOW',$flow)
                    ->first();
                    if(isset($pressuredrop)){
                        if(isset($graphData[$i])){
                            $graphData[] = $graphData[$i] - $pressuredrop->PRESSURE_DROP;
                            $graphLabel[] = $material_kinds->MATERIAL_NAME.' '.$material_detail_kinds->MATERIAL_SIZE;
                            $errorSetting->errorSet($simulation_details[$i],0);
                        }else{
                            $graphData[] = $pressuredrop->PRESSURE_DROP;
                            $graphLabel[] = $material_kinds->MATERIAL_NAME.' '.$material_detail_kinds->MATERIAL_SIZE;
                            $errorSetting->errorSet($simulation_details[$i],8);
                        }

                    }else{
                        $graphData[] = $graphData[$i] - 0;
                        $graphLabel[] = $material_kinds->MATERIAL_NAME.' '.$material_detail_kinds->MATERIAL_SIZE;
                        $errorSetting->errorSet($simulation_details[$i],16);
                    }
                }
            }else{
                if(isset($graphData[$i])){
                    //物品情報取得できない場合の回避策
                    $graphData[] = $graphData[$i];
                    $graphLabel[] = '未登録';
                    $errorSetting->errorSet($simulation_details[$i],32);
                }else{
                    $errorSetting->errorSet($simulation_details[$i],1);
                }
            }
        }
        //動脈圧フラグの確認
        if($simulation->ABP_FLG){
            //動脈圧をセット
            $graphData[] = $simulation->ABP;
            $graphLabel[] = 'ABP';
        }

        $material_details = material_detail::where('DELETE_FLG',True)
        ->get();
        $materials = material::where('DELETE_FLG',True)
        ->get();

        //TODO 仮のflow情報格納
        $flow_slice = 0.1;
        $flow_max = 10;
        $flow_items = array();
        for($i=0; $i<($flow_max/$flow_slice);$i++){
            $flow_items[] = $flow_slice*$i;
        }
        //TODO 仮のcvp情報格納
        $cvp_slice = 1;
        $cvp_min = -30;
        $cvp_max = 100;
        $cvp_items = array();
        for($i=0; $i<(($cvp_max-$cvp_min)/$cvp_slice);$i++){
            $cvp_items[] = ($cvp_slice*$i)+$cvp_min;
        }
        //TODO 仮のabp情報格納
        $abp_slice = 5;
        $abp_min = 10;
        $abp_max = 150;
        $abp_items = array();
        for($i=0; $i<(($abp_max-$abp_min)/$abp_slice);$i++){
            $abp_items[] = ($abp_slice*$i)+$abp_min;
        }

        return view('simulation/showSimulation',
            compact('simulation',
                'simulation_details',
                'material_details',
                'speeds',
                'materials',
                'graphData',
                'graphLabel',
                'flow_items',
                'cvp_items',
                'abp_items'
                ));
    }
}

class errorSetting{
    //ErrorFlgの設定
    public function errorSet($simulation_detail,$errorCode){
        $simulation_detail->ERROR_FLG = $errorCode;
        $simulation_detail->save();
    }
}
