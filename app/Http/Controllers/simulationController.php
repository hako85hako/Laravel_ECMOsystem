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
        $simulation_detail_counts = array();
        //simulationsの存在判定
        if(isset($simulations)){
            //simulation_detailの個数をカウント
            for($i = 0; $i<count($simulations);$i++){
                $simulation_details = simulation_detail::where('DELETE_FLG',True)
                ->where('SIMULATION_ID',$simulations[$i]->id)
                ->get();
                $simulation_detail_counts[] =
                ['id'=> $simulations[$i]->id,
                'count'=>count($simulation_details)];
            }
        }
        return view('simulation/index',
            compact('simulations',
                    'simulation_detail_counts'));
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
            DB::beginTransaction();
            try{
                DB::commit();
                $simulation->UPDATE_USER = Auth::user()->name;
                $simulation->UPDATE_USER_ID = Auth::user()->id;
                $simulation->save();
            }catch (\Exception $e) {
                DB::rollback();
            }
            return redirect("/simulation/".$id)->with($id);
        }else{
            DB::beginTransaction();
            try{
                $simulation->SIMULATION_NAME = $request->SIMULATION_NAME;
                $simulation->UPDATE_USER = Auth::user()->name;
                $simulation->UPDATE_USER_ID = Auth::user()->id;
                $simulation->save();
                DB::commit();
            }catch (\Exception $e) {
                DB::rollback();
            }
            $simulations = simulation::where('DELETE_FLG',True)
            ->where('CREATE_USER_ID',Auth::user()->id)
            ->get();
            return view('simulation/index', compact('simulations'));
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try{
            $simulation = simulation::findOrFail($id);
            $simulation->DELETE_FLG = 0;
            $simulation->UPDATE_USER = Auth::user()->name;
            $simulation->UPDATE_USER_ID = Auth::user()->id;
            $simulation->save();
            DB::commit();
        }catch (\Exception $e) {
            DB::rollback();
        }
        return redirect("/simulation");
    }

    //シミュレーションの詳細表示
    public function show(Request $request,$id){
        $calc = new calcs();
        $tools = new myTools();
        $errorSetting = new errorSetting();
        $simulation = simulation::findOrFail($id);
        $simulation_details = simulation_detail::where('DELETE_FLG',True)
        ->where('SIMULATION_ID',$id)
        ->orderBy('SERIAL_NUMBER', 'asc')
        ->get();
        //ID確認処理&振り直し処理
        DB::beginTransaction();
        try {
            for($i = 0; $i<count($simulation_details);$i++){
                $simulation_details[$i]->SERIAL_NUMBER = $i+1;
                $simulation_details[$i]->save();
            }
            //遷移先画面振り分け初期処理
            if(!isset($simulation->MONITOR)){
                $simulation->MONITOR = "graphs";
            }elseif(!isset($request->monitor)){
                $simulation->MONITOR = "graphs";
            }else{
                $simulation->MONITOR = $request->monitor;
            }
            $simulation->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }
        //回転数格納用配列を設置
        $speeds = array();
        $printData=array();
        $graphData = array();
        $graphLabel = array();
        $unknown_pressure = 0;
        //流量格納用配列を設置　
        //流量情報をセット
        $flow = $simulation->FLOW;
        //中心静脈圧フラグの確認
        if($simulation->CVP_FLG){
            //中心静脈圧をセット
            $graphData[] = $simulation->CVP;
            $graphLabel[] = 'CVP';
            $unknown_pressure += $simulation->CVP;
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
            $check = 0;
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
                    DB::beginTransaction();
                    try{
                        //Simulation_detailsのポンプフラグをセット
                        $simulation_details[$i]->PUMP_FLG = 1;
                        $simulation_details[$i]->save();
                        DB::commit();
                    }catch (\Exception $e) {
                        DB::rollback();
                    }
                    //圧力構築物品の場合
                    //回転数情報をセット
                    $speed = $simulation_details[$i]->REVOLUTION_INF;
                    //物品の揚程を算出
                    $head = $calc->headCalc($material_detail_kinds,$flow,$speed);
                    $var = array();
                    $var = $tools->substrVal($material_kinds->MATERIAL_NAME,20);
                    if(is_array($var)){
                        $lavelData = [$var[0],$var[1],$speed."rpm"];
                    }else{
                        $lavelData = [$var,$speed."rpm"];
                    }


                    //CVPが無い場合のみインクリメント調整
                    if($i!=0){
                        if(!$simulation->CVP_FLG){
                            $i -= 1;
                            $check = 1;
                        }
                    }
                    //今回取得の揚程データがあるかの判定
                    if(isset($head)){
                        //前回データの存在判定
                        if(isset($graphData[$i])){
                            $graphData[] = round($graphData[$i] + $head,2);
                            $graphLabel[] = $lavelData;
                            $unknown_pressure += round($head,2);
                            if($check == 1){
                                $errorSetting->errorSet($simulation_details[$i+1],0);
                            }else{
                                $errorSetting->errorSet($simulation_details[$i],0);
                            }
                        }else{
                            $graphData[] = $head;
                            $graphLabel[] = $lavelData;
                            $unknown_pressure += round($head,2);
                            if($check == 1){
                                $errorSetting->errorSet($simulation_details[$i+1],0);
                            }else{
                                $errorSetting->errorSet($simulation_details[$i],0);
                            }
                        }
                        $printData[] = $head;
                    }else{
                        $printData[] = '--';
                        $graphData[] = $graphData[$i] + 0;
                        //$graphLabel[] = $material_kinds->MATERIAL_NAME.' '.$speed."rpm";
                        $graphLabel[] = $lavelData;
                        if($check == 1){
                            $errorSetting->errorSet($simulation_details[$i+1],4);
                        }else{
                            $errorSetting->errorSet($simulation_details[$i],4);
                        }
                    }
                    //CVPが無い場合のみインクリメント調整
                    if($check==1){
                        if(!$simulation->CVP_FLG){
                            $i += 1;
                        }
                        $check = 0;
                    }

                }else{
                //圧力を損失する物品のロジック
                    //Simulation_detailsのポンプフラグを回収
                    $simulation_details[$i]->PUMP_FLG = 0;
                    $simulation_details[$i]->save();
                    //圧力損失物品の場合
                    //物品の圧力損失を算出
                    $pressuredrop = $calc->pressureCalc($material_detail_kinds,$flow);
                    //********************************************************************
                    //20文字以上の物品名は折り返す
                    //********************************************************************
                        $var = array();
                        $var = $tools->substrVal($material_kinds->MATERIAL_NAME,20);
                        if(is_array($var)){
                            $lavelData = [$var[0],$var[1],$material_detail_kinds->MATERIAL_SIZE];
                        }else{
                            $lavelData = [$var,$material_detail_kinds->MATERIAL_SIZE];
                        }
                    //********************************************************************

                    //CVPが無い場合のみインクリメント調整
                    if($i!=0){
                        if(!$simulation->CVP_FLG){
                            $i -= 1;
                            $check = 1;
                        }
                    }
                    //pressuredropが取得できたかの判定
                    if(isset($pressuredrop)){
                        //前回データがあるかの判定
                        if(isset($graphData[$i])){
                            $graphData[] = round($graphData[$i] - $pressuredrop,2);
                            $graphLabel[] = $lavelData;
                            $unknown_pressure -= round($pressuredrop,2);
                            if($check == 1){
                                $errorSetting->errorSet($simulation_details[$i+1],0);
                            }else{
                                $errorSetting->errorSet($simulation_details[$i],0);
                            }
                        }else{
                            $graphData[] = -$pressuredrop;
                            $graphLabel[] = $lavelData;
                            $unknown_pressure -= round($pressuredrop,2);
                            if($check == 1){
                                $errorSetting->errorSet($simulation_details[$i+1],0);
                            }else{
                                $errorSetting->errorSet($simulation_details[$i],0);
                            }
                        }
                        $printData[] = $pressuredrop;
                    }else{
                        if(isset($graphData[$i])){
                            $printData[] = '--';
                            $graphData[] = $graphData[$i] - 0;
                            $graphLabel[] = $lavelData;
                            if($check == 1){
                                $errorSetting->errorSet($simulation_details[$i+1],16);
                            }else{
                                $errorSetting->errorSet($simulation_details[$i],16);
                            }
                        }else{
                            $printData[] = '--';
                            $graphData[] = 0;
                            $graphLabel[] = $lavelData;
                            if($check == 1){
                                $errorSetting->errorSet($simulation_details[$i+1],64);
                            }else{
                                $errorSetting->errorSet($simulation_details[$i],64);
                            }

                        }
                    }
                    //CVPが無い場合のみインクリメント調整
                    if($check == 1){
                        if(!$simulation->CVP_FLG){
                            $i += 1;
                        }
                        $check = 0;
                    }
                }
            }else{
                //CVPが無い場合のみインクリメント調整
                if($i!=0){
                    if(!$simulation->CVP_FLG){
                        $i -= 1;
                        $check = 1;
                    }
                }
                if(isset($graphData[$i])){
                    //物品情報取得できない場合の回避策
                    $graphData[] = $graphData[$i];
                    $printData[] = '--';
                    $graphLabel[] = '未登録';
                    if($check == 1){
                      $errorSetting->errorSet($simulation_details[$i+1],32);
                    }else{
                      $errorSetting->errorSet($simulation_details[$i],32);
                    }
                }else{
                    $graphData[] = 0;
                    $printData[] = '--';
                    $graphLabel[] = '未登録';
                    if($check == 1){
                        $errorSetting->errorSet($simulation_details[$i+1],1);
                    }else{
                        $errorSetting->errorSet($simulation_details[$i],1);
                    }
                }
                //CVPが無い場合のみインクリメント調整
                if($check == 1){
                    if(!$simulation->CVP_FLG){
                        $i += 1;
                    }
                    $check = 0;
                }
            }
        }
        //動脈圧フラグの確認
        if($simulation->ABP_FLG){
            //動脈圧をセット
            $graphData[] = $simulation->ABP;
            $graphLabel[] = 'ABP';
            $unknown_pressure -= $simulation->ABP;
        }

        $material_details = material_detail::where('DELETE_FLG',True)
        ->get();
        $materials = material::where('DELETE_FLG',True)
        ->get();

        //TODO 仮のflow情報格納
        $flow_slice = 1;
        $flow_max = 100;
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
            compact(
                'simulation',
                'simulation_details',
                'material_details',
                'speeds',
                'materials',
                'printData',
                'graphData',
                'graphLabel',
                'flow_items',
                'cvp_items',
                'abp_items',
                'unknown_pressure'
                ));
    }
}

class errorSetting{
    //ErrorFlgの設定
    public function errorSet($simulation_detail,$errorCode){
        DB::beginTransaction();
        try{
            $simulation_detail->ERROR_FLG = $errorCode;
            $simulation_detail->save();
        }catch (\Exception $e) {
            DB::rollback();
        }
    }
}


class calcs{
    public function pressureCalc($material_detail,$flow){
        $roundInt = new roundInt();
        $flows = $roundInt->branchRound($material_detail->SLICE_FLOW, $flow);
        $pressuredrop1 = pressure_drop::where('DELETE_FLG',True)
        ->where('MATERIAL_DETAIL_ID',$material_detail->id)
        ->where('FLOW',$flows[0])
        ->first();
        $pressuredrop2 = pressure_drop::where('DELETE_FLG',True)
        ->where('MATERIAL_DETAIL_ID',$material_detail->id)
        ->where('FLOW',$flows[1])
        ->first();
        if(isset($pressuredrop1)&&isset($pressuredrop2)){
            $pressuredrop1 = $pressuredrop1->PRESSURE_DROP;
            $pressuredrop2 = $pressuredrop2->PRESSURE_DROP;
            //流量の割合を算出
            $flow_rate = $flow/($flows[0]+$flows[1]);
            if($pressuredrop1>$pressuredrop2){
                $pre_result = $pressuredrop2;
            }else{
                $pre_result = $pressuredrop1;
            }
            $result = round($pre_result+(abs($pressuredrop1-$pressuredrop2)*$flow_rate),2);
        }else{
            $result = null;
        }
        return $result;
    }

    public function headCalc($material_detail,$flow,$speed){
        $roundInt = new roundInt;
        $flows = $roundInt->branchRound($material_detail->SLICE_FLOW, $flow);
        $head1 = pressure_drop::where('DELETE_FLG',True)
        ->where('MATERIAL_DETAIL_ID',$material_detail->id)
        ->where('FLOW',$flows[0])
        ->where('SPEED',$speed)
        ->first();
        $head2 = pressure_drop::where('DELETE_FLG',True)
        ->where('MATERIAL_DETAIL_ID',$material_detail->id)
        ->where('FLOW',$flows[1])
        ->where('SPEED',$speed)
        ->first();
        if(isset($head1)&&isset($head2)){
            $head1 = $head1->HEAD;
            $head2 = $head2->HEAD;
            //流量の割合を算出
            $flow_rate = $flow/($flows[0]+$flows[1]);
            if($head1>$head2){
                $pre_result = $head2;
            }else{
                $pre_result = $head1;
            }
            $result = round($pre_result+(abs($head1-$head2)*$flow_rate),2);
        }else{
            $result = null;
        }
        return $result;
    }
}

/**
 * どのメソッドを使用するかの分岐をする<br>
 *
 */
class roundInt{
    public function branchRound($slice,$i){
        //var_dump($i);
        //スライスの逆数をとる
        $reverse = 1/$slice;
        $i = $i*$reverse;
        $result = array();
        $pre_result1=floor($i);
        if($i>=0){
            //$floor=$i-$pre_result1;
            $add_num = 1;
        }else{
            //$floor=$i+$pre_result1;
            $add_num = 1;
        }
        //var_dump($floor);
        $result1 = $pre_result1/$reverse;
        $result2 = ($pre_result1+$add_num)/$reverse;
        $result[] = $result1;
        $result[] = $result2;
        return $result;
    }
}

class myTools{
    public function substrVal($val,$num){
        if(mb_strlen($val, 'UTF-8') > $num){
            return [mb_substr($val,0,$num), mb_substr($val,$num)];
        }else{
            return $val;
        }
    }
}
