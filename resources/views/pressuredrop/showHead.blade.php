<html>
@extends('material/header_layout')
@section('content')
<script>
	//サイズ
	const size_list = @json($size_list);
    //ラベル
    const flow_list = @json($flow_list);
    //取得した圧力損失
    const pressuredrop_list = @json($pressuredrop_list);
</script>
    <div class="container">
        <div class="row">
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                	<li class="breadcrumb-item active" aria-current="page"><a href="/">メインメニュー</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="/material">物品一覧</a></li>
                    <li class="breadcrumb-item"><a href="#">{{ $materials[0]->MATERIAL_NAME }}:圧力損失詳細</a></li>
                </ol>
            </nav>
        </div>
        <div class="row">
    		<canvas id="pressuredropGraph"></canvas>
    	</div>

        <div class="row">
            <div class="col">
    <!--         ============================================== -->
    <!--         ============================================== -->

            <?php //print($pressuredrops[0][0]->MATERIAL_DETAIL_ID);?>
            <?php //var_dump($material_details[0]->MATERIAL_ID);?>
         	<?php $i = 0 ;?>
    	@foreach($size_list as $size)
                <details>
                <summary>物品規格 ： {{$size}} </summary>
                	<table class="table text-center">
                		<tr>
                            <th class="text-center">流量<br>[L/min]</th>
                            <th class="text-center">圧力損失<br>[mmHg]</th>
                            <th class="text-center">回転数<br>[rpm]</th>
                            <th class="text-center">揚程<br>[mmHg]</th>
                            <th class="text-center">更新</th>
                            <th class="text-center">削除</th>
                        </tr>
        			@foreach($pressuredrops[$i] as $pressuredrop)
                        <tr>
                            <td>
                            	<label>{{ $pressuredrop->FLOW }}</label>
                            	<input type="hidden" class="form-control" name="FLOW" form="update{{$pressuredrop->id}}" value="{{ $pressuredrop->FLOW }}" />
                            </td>
                            <td>
                            	<input type="text" class="form-control" name="PRESSURE_DROP" form="update{{$pressuredrop->id}}" value="{{ $pressuredrop->PRESSURE_DROP }}" />
                            </td>
                            <td>
                            	<input type="text" class="form-control" name="SPEED" form="update{{$pressuredrop->id}}" value="{{ $pressuredrop->SPEED }}" />
                            </td>
                            <td>
                            	<input type="text" class="form-control" name="HEAD" form="update{{$pressuredrop->id}}" value="{{ $pressuredrop->HEAD }}" />
                            </td>
                            <td>
                            	<form action="/pressuredrop/{{ $pressuredrop->id }}" method="post" id="update{{$pressuredrop->id}}">
               						<input type="hidden" name="material_id" value="{{ $material_details[0]->MATERIAL_ID}}">
                                    <input type="hidden" name="_method" value="PUT">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                           			<button type="submit" class="btn btn-xs" aria-label="Left Align"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
                           		</form>
                       	 	</td>
                            <td>
                            @if($pressuredrop->ONLY_FLOW_FLG === 0 )
                            	<form action="/pressuredrop/{{ $pressuredrop->id }}" method="post">
                            		<input type="hidden" name="material_id" value="{{ $material_details[0]->MATERIAL_ID}}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                           			<button type="submit" class="btn btn-xs btn-danger" aria-label="Left Align"><span class="glyphicon glyphicon-trash"></span></button>
                           		</form>
                           	@endif
                       	 	</td>
                        </tr>
        			@endforeach
    			</table>
                	<form action="/pressuredrop/create" method="get">
                        <input type="hidden" name="material_detail_id" value="{{ $pressuredrops[$i][0]->MATERIAL_DETAIL_ID}}">
               			<input type="hidden" name="material_id" value="{{ $material_details[0]->MATERIAL_ID}}">
               			<input type="hidden" name="slice_flow" value="{{ $material_details[0]->SLICE_FLOW}}">
               			<input type="hidden" name="last_flow" value="{{ $pressuredrop->FLOW }}">
               			<button type="submit" class="btn btn-xs btn-danger" aria-label="Left Align">新規作成</button>
               		</form>
                </details>
                <?php $i = $i + 1;?>
             @endforeach
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script src="{{ asset('/js/createGraph_pressuredrop.js') }}"></script>
<script src="{{ asset('/js/accodion.js') }}"></script>
@endsection
</html>