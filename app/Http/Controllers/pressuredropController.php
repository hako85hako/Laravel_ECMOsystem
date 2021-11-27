<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\pressure_drop;
use App\Models\material_detail;
use App\Models\material;
use phpDocumentor\Reflection\PseudoTypes\False_;


class pressuredropController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    public function show($id){

        $materials = material::where('id',$id)->get();

        //******************************************************************
        //  ここでCentrifugal-pumpのみ条件分岐させる
        //******************************************************************
            if($materials[0]['MATERIAL_KIND'] == 'Centrifugal-pump'){
                $material_details = material_detail::
                where('MATERIAL_ID',$id)
                ->where('DELETE_FLG',True)
                ->get();
                //var_dump($material_details[0]->id);
                //print($material_details[0]->id);
                //選択した物品の回転数を取得
                 $pre_speed_list = pressure_drop::
                 where('MATERIAL_DETAIL_ID',$material_details[0]->id)
                 ->where('DELETE_FLG',True)
                 ->groupBy('SPEED')
                 ->get('SPEED');
                 //↑ここで[0]指定しているので、規格追加はできないようにする
                for($i = 0; $i<count($pre_speed_list);$i++){
                    $skip_flg = false;
                    //public_flgが立っていない場合
                    //非公開にしたいのでログインユーザー==作成者ではない場合、配列から削除する
                    if($pre_speed_list[$i]->PUBLIC_FLG == 0){
                        if($pre_speed_list[$i]->CREATE_USER_ID != Auth::user()->id){
                            //unset($pre_speed_list[$i]);
                            //continue;
                            $skip_flg = true;
                        }
                    }
                    if($skip_flg){
                        //回転数のリスト作成
                        $speed_list[] = $pre_speed_list[$i]->SPEED;
                        //print($speed_list[$i]."<br>");
                        //回転数ごとの流量と揚程を取得
                        $headflows[$speed_list[$i]] = pressure_drop::where('MATERIAL_DETAIL_ID',$material_details[0]->id)
                        ->where('DELETE_FLG',True)
                        ->where('SPEED',$speed_list[$i])
                        ->orderBy('FLOW', 'asc')
                        ->get();

                        //print($headflows[$speed_list[$i]]);
                        //回転数をkey、揚程をvalueにした連想配列を作成(グラフ用)
                        $head_list = array();
                        for($iii = 0; $iii<count($headflows[$speed_list[$i]]);$iii++){
                            $skip_flg = false;
                            if($headflows[$speed_list[$i]][$iii]->PUBLIC_FLG == 0){
                                if($headflows[$speed_list[$i]][$iii]->CREATE_USER_ID != Auth::user()->id){
                                    $skip_flg = true;
                                }
                            }
                            if(!$skip_flg){
                                $head_list[] = $headflows[$speed_list[$i]][$iii]->HEAD;
                            }

                        }
                        $head_speed_list[$speed_list[$i]]=  $head_list;


                        //グラフ作成*****************************************
                        //選択した物品の流量を取得(ラベル用)
                        $pre_flow_list = pressure_drop::
                        where('MATERIAL_DETAIL_ID',$material_details[0]->id)
                        ->where('DELETE_FLG',True)
                        ->where('SPEED',$speed_list[$i])
                        ->groupBy('FLOW')
                        ->get('FLOW');
                        $pre_flow_list2 = array();
                        for($ii = 0; $ii<count($pre_flow_list);$ii++){
                            $pre_flow_list2[] = $pre_flow_list[$ii]->FLOW;
                            //print($flow_list[$i]."<br>");
                        }
                        $flow_list[$speed_list[$i]] = $pre_flow_list2;
                        //**************************************************
                    }
                }
                if(empty($speed_list)){
                    $speed_list = array();
                }
                return view(
                    'pressuredrop/showHead',
                    compact(
                        'material_details',
                        'speed_list',
                        'flow_list',//json変換
                        'head_speed_list',//json変換
                        'headflows',
                        'materials'
                        )
                    );
            }else{
                //選択した物品の規格を取得
                $material_details = material_detail::
                where('MATERIAL_ID',$id)
                ->where('DELETE_FLG',True)
                ->get();
                for($i = 0; $i<count($material_details);$i++){
                    $size_list[] = $material_details[$i]->MATERIAL_SIZE;
                    //print($size_list[$i]);//各種規格が出力されるテスト
                    $pre_pressuredrops = pressure_drop::where('MATERIAL_DETAIL_ID',$material_details[$i]->id)
                    ->where('DELETE_FLG',True)
                    //->where('PUBLIC_FLG',True)
                    ->orderBy('FLOW', 'asc')
                    ->get();

                    for($ii = 0; $ii<count($pre_pressuredrops);$ii++){
                        //public_flgが立っていない場合
                        //非公開にしたいのでログインユーザー==作成者ではない場合、配列から削除する
                        if($pre_pressuredrops[$ii]->PUBLIC_FLG == 0){
                            if($pre_pressuredrops[$ii]->CREATE_USER_ID != Auth::user()->id){
                                unset($pre_pressuredrops[$ii]);
                                continue;
                            }
                        }
                        $pre_pressuredrop_list[] = $pre_pressuredrops[$ii]->PRESSURE_DROP;

                        //**************************************************************************************
                        //  いらんかも
                        //**************************************************************************************
                            //その物品で流量が唯一の流量は削除不可にする
                            $flow_count = pressure_drop::where('MATERIAL_DETAIL_ID',$material_details[$i]->id)
                            ->where('DELETE_FLG',True)
                            ->where('PUBLIC_FLG',True)
                            ->where('FLOW',$pre_pressuredrops[$ii]->FLOW)
                            ->get();
                            if(count($flow_count)>1){
                                //この流量がそのmaterila_detailに対して複数ある場合の処理
                                $pre_pressuredrops[$ii]->ONLY_FLOW_FLG = 0;
                                $pre_pressuredrops[$ii]->save();
                            }else{
                                //この流量がそのmaterial_detailで唯一の場合
                                $pre_pressuredrops[$ii]->ONLY_FLOW_FLG = 1;
                                $pre_pressuredrops[$ii]->save();
                            }
                        //**************************************************************************************
                    }


                    $pressuredrop_list[$size_list[$i]] = $pre_pressuredrop_list;
                    $pre_pressuredrop_list = array();
                    //グラフ作成*****************************************
                    //選択した物品の流量を取得(ラベル用)
                    $material_flows = pressure_drop::where('MATERIAL_DETAIL_ID',$material_details[$i]->id)
                    ->where('DELETE_FLG',True)
                    ->orderBy('FLOW', 'asc')
                    ->get();
                    for($iii = 0; $iii<count($material_flows);$iii++){
                        $pre_flow_list[] = $material_flows[$iii]->FLOW;
                    }
                    $flow_list[$size_list[$i]] = $pre_flow_list;
                    $pre_flow_list = array();
                    $pressuredrops[] = $pre_pressuredrops;
                }
                //var_dump($pressuredrop_list);
                return view(
                    'pressuredrop/showPressuredrop',
                    compact(
                        'material_details',
                        'size_list',
                        'pressuredrops',
                        'materials',
                        'pressuredrop_list',
                        'flow_list'
                        )
                    );
            }
    }

    public function create(Request $request)
    {
        $pressuredrop = new pressure_drop();
        if($request->material_kind == "Centrifugal-pump"){
            $pressuredrop->MATERIAL_DETAIL_ID = $request->material_detail_id;
            $pressuredrop->FLOW = ($request->last_flow)+($request->slice_flow);
            $pressuredrop->SPEED = $request->last_speed;
            $pressuredrop->HEAD = 0;
        }else{
            $pressuredrop->MATERIAL_DETAIL_ID = $request->material_detail_id;
            $pressuredrop->FLOW = ($request->last_flow)+($request->slice_flow);
        }
        $pressuredrop->PUBLIC_FLG = 0;
        $pressuredrop->CREATE_USER = Auth::user()->name;
        $pressuredrop->UPDATE_USER = Auth::user()->name;
        $pressuredrop->CREATE_USER_ID = Auth::user()->id;
        $pressuredrop->UPDATE_USER_ID = Auth::user()->id;
        $pressuredrop->save();
        return redirect("/pressuredrop/".$request->material_id)->with($request->material_id);
    }

    public function store(Request $request)
    {
        $pressuredrop = new pressure_drop();
        $pressuredrop->FLOW = $request->FLOW;
        $pressuredrop->PRESSURE_DROP = $request->PRESSURE_DROP;
        //$pressuredrop->SPEED = $request->SPEED;
        //$pressuredrop->HEAD = $request->HEAD;
        $pressuredrop->CREATE_USER = Auth::user()->name;
        $pressuredrop->UPDATE_USER = Auth::user()->name;
        $pressuredrop->CREATE_USER_ID = Auth::user()->id;
        $pressuredrop->UPDATE_USER_ID = Auth::user()->id;
        $pressuredrop->save();
        return redirect("/pressuredrop");
    }

    public function edit($id){
        // DBよりURIパラメータと同じIDを持つBookの情報を取得
        $pressuredrop = pressure_drop::findOrFail($id);
        return view('pressuredrop/edit', compact('pressuredrop'));
    }

    public function update(Request $request, $id){
        $pressuredrop = pressure_drop::findOrFail($id);
        if($request->flg=="update"){
            if($request->material_kind == "Centrifugal-pump"){
                $pressuredrop->FLOW = $request->FLOW;
                $pressuredrop->HEAD = $request->HEAD;
            }else{
                $pressuredrop->FLOW = $request->FLOW;
                $pressuredrop->PRESSURE_DROP = $request->PRESSURE_DROP;
            }
        }elseif($request->flg=="lock"){
            $pressuredrop->LOCK_FLG = 1;
        }elseif($request->flg=="unlock"){
            $pressuredrop->LOCK_FLG = 0;
        }elseif($request->flg=="be_public"){
            $pressuredrop->PUBLIC_FLG = 1;
        }elseif($request->flg=="be_private"){
            $pressuredrop->PUBLIC_FLG = 0;
        }
        $pressuredrop->UPDATE_USER = Auth::user()->name;
        $pressuredrop->UPDATE_USER_ID = Auth::user()->id;
        $pressuredrop->save();
        return redirect("/pressuredrop/".$request->material_id)->with($request->material_id);
    }

    public function destroy(Request $request,$id){
        //$material = material::findOrFail($id);
        //$material->delete();
        $pressuredrop = pressure_drop::findOrFail($id);
        $pressuredrop->DELETE_FLG = 0;
        $pressuredrop->UPDATE_USER = Auth::user()->name;
        $pressuredrop->UPDATE_USER_ID = Auth::user()->id;
        $pressuredrop->save();
        return redirect("/pressuredrop/".$request->material_id)->with($request->material_id);
    }
}
