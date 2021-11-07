<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\pressure_drop;
use App\Models\material_detail;
use App\Models\material;
use phpDocumentor\Reflection\PseudoTypes\False_;


class pressuredropController extends Controller
{
    public function show($id){

        $materials = material::where('id',$id)->get();
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
            ->orderBy('FLOW', 'asc')
            ->get();
            for($ii = 0; $ii<count($pre_pressuredrops);$ii++){
                $pre_pressuredrop_list[] = $pre_pressuredrops[$ii]->PRESSURE_DROP;
                //その物品で流量が唯一の流量は削除不可にする
                $flow_count = pressure_drop::where('MATERIAL_DETAIL_ID',$material_details[$i]->id)
                ->where('DELETE_FLG',True)
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
        // 取得した値をビュー「book/index」に渡す

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

    public function create(Request $request)
    {
        $pressuredrop = new pressure_drop();
        $pressuredrop->MATERIAL_DETAIL_ID = $request->material_detail_id;
        $pressuredrop->FLOW = ($request->last_flow)+($request->slice_flow);
        $pressuredrop->save();
        return redirect("/pressuredrop/".$request->material_id)->with($request->material_id);
    }

    public function store(Request $request)
    {
        $pressuredrop = new pressure_drop();
        $pressuredrop->FLOW = $request->FLOW;
        $pressuredrop->PRESSURE_DROP = $request->PRESSURE_DROP;
        $pressuredrop->SPEED = $request->SPEED;
        $pressuredrop->HEAD = $request->HEAD;
        $pressuredrop->save();
        var_dump($request->material_id);
        return redirect("/pressuredrop");
    }

    public function edit($id){
        // DBよりURIパラメータと同じIDを持つBookの情報を取得
        $pressuredrop = pressure_drop::findOrFail($id);

        // 取得した値をビュー「book/edit」に渡す
        return view('pressuredrop/edit', compact('pressuredrop'));
    }

    public function update(Request $request, $id){
        $pressuredrop = pressure_drop::findOrFail($id);
        $pressuredrop->FLOW = $request->FLOW;
        $pressuredrop->PRESSURE_DROP = $request->PRESSURE_DROP;
        $pressuredrop->SPEED = $request->SPEED;
        $pressuredrop->HEAD = $request->HEAD;
        $pressuredrop->save();
        return redirect("/pressuredrop/".$request->material_id)->with($request->material_id);
    }

    public function destroy(Request $request,$id){
        //$material = material::findOrFail($id);
        //$material->delete();
        $pressuredrop = pressure_drop::findOrFail($id);
        $pressuredrop->DELETE_FLG = 0;
        $pressuredrop->save();
        return redirect("/pressuredrop/".$request->material_id)->with($request->material_id);
    }
}
