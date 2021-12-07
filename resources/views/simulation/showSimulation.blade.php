<html>
@extends('material/header_layout')
@section('content')
<script>
	//データ
	const graphData = @json($graphData);
    //ラベル
    const graphLabel = @json($graphLabel);
</script>
<div class="container">
	<div class="row">
        <nav aria-label="breadcrumb" role="navigation">
            <ol class="breadcrumb">
            	<li class="breadcrumb-item active" aria-current="page"><a href="/">メインメニュー</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="/simulation">シミュレーション一覧</a></li>
                <li class="breadcrumb-item"><a href="#">シミュレーション詳細::{{ $simulation->SIMULATION_NAME }}</a></li>
            </ol>
        </nav>
    </div>
    <!-- グラフ表示 -->
    <div class="row">
    	<div class="chart-container" style="position: relative; height:70vh; width:auto">
			<canvas id="simulation1Graph"></canvas>
		</div>
	</div>
	<div class="row">　</div>
    <!--simulation設定 -->
    <div class="p-6 border-t border-gray-200 dark:border-gray-700 md:border-t-0 md:border-l">
    		<table class="table text-center">
        		<tr>
        			<th class="text-center">流量設定</th>
                    <th class="text-center">CVP設定</th>
                    <th class="text-center">CVP有効</th>
                    <th class="text-center">ABP設定</th>
                    <th class="text-center">ABP有効</th>
                </tr>
                <tr>
            		<td>
                    	<select class="form-control" form="simulation_inf" name="FLOW" onchange="simulationChange()">
                          @foreach($flow_items as $flow_item)
                          	@if($simulation->FLOW == $flow_item)
                          	 <option selected>{{$flow_item}}</option>
                          	@else
                          	 <option>{{$flow_item}}</option>
                          	@endif
                          @endforeach
                       	</select>
            		</td>
            		<td>
            			<select class="form-control" form="simulation_inf" name="CVP" onchange="simulationChange()">
                          @foreach($cvp_items as $cvp_item)
                          	@if($simulation->CVP == $cvp_item)
                          	 <option selected>{{$cvp_item}}</option>
                          	@else
                          	 <option>{{$cvp_item}}</option>
                          	@endif
                          @endforeach
                       	</select>
            		</td>
            		<td>
            			@if($simulation->CVP_FLG == 1)
                       		<input type="checkbox" class="form-check-input"
                       		form="simulation_inf" name="CVP_flg" checked onchange="simulationChange()">
                       	@else
                       		<input type="checkbox" class="form-check-input"
                       		form="simulation_inf" name="CVP_flg" onchange="simulationChange()">
                       	@endif
            		</td>
            		<td>
            			<select class="form-control" name="ABP" onchange="simulationChange()" form="simulation_inf">
                          @foreach($abp_items as $abp_item)
                          	@if($simulation->ABP == $abp_item)
                          	 <option selected>{{$abp_item}}</option>
                          	@else
                          	 <option>{{$abp_item}}</option>
                          	@endif
                          @endforeach
                       	</select>
            		</td>
            		<td>
            			@if($simulation->ABP_FLG == 1)
                           	<input type="checkbox" class="form-check-input" name="ABP_flg" form="simulation_inf"
                           	 checked onchange="simulationChange()">
                        @else
                        	<input type="checkbox" class="form-check-input" name="ABP_flg" form="simulation_inf" onchange="simulationChange()">
                        @endif
                	</td>
            	</tr>
        	</table>
    </div>
	<div class="row">
    		<form action="/simulation/{{$simulation->id}}" method="post" id="simulation_inf">
    		 	<input type="hidden" name="_method" value="PUT">
    			<input type="hidden" name="_token" value="{{ csrf_token() }}">
    			<input type="hidden" name="type" value="option">
   			</form>
   		</div>
    </div>
<!-- 		<div class="col-md-11 col-md-offset-1"> -->
		<div class="p-6 border-t border-gray-200 dark:border-gray-700 md:border-t-0 md:border-l">
    		<table class="table text-center">
        		<tr>
        			<th class="text-center">No.</th>
                    <th class="text-center">機器名</th>
                    <th class="text-center">機器種別</th>
                    <th class="text-center">削除</th>
                </tr>
                @foreach($simulation_details as $simulation_detail)
                @if($simulation_detail->ERROR_FLG!=0)
                <tr class="bg-danger">
                	<td>
                	</td>
                	<td colspan="2" class="table-active" style="color:red;">
						検証データが存在しない流量範囲です。
						グラフにデータが反映されていません。
                	</td>
                	<td>
                	</td>
                </tr>
                <tr class="bg-danger">
                @else
                <tr>
                @endif
                	@if($simulation_detail->ERROR_FLG!=0)
                	<td style="border:none;">
                	@else
                	<td>
                	@endif
                		{{ $simulation_detail -> SERIAL_NUMBER }}
                	</td>
                    @if($simulation_detail->ERROR_FLG!=0)
                	<td style="border:none;">
                	@else
                	<td>
                	@endif
						<form action="/simulation-detail/{{$simulation_detail->id}}"
							method="post" id="material_form{{ $simulation_detail -> SERIAL_NUMBER }}">
						 	<input type="hidden" name="_method" value="PUT">
                			<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<input type="hidden" name="type" value="0">
							<input type="hidden" name="simulation_id" value="{{$simulation_detail->id}}">
                   			<div class="form-group">
                            	<select class="form-control" name="MATERIAL_ID"
                            		onchange="materialChange({{ $simulation_detail -> SERIAL_NUMBER }})" id="material_select{{ $simulation_detail -> SERIAL_NUMBER }}">
                                  @foreach($materials as $material)
                                  	@if($material->id == $simulation_detail -> MATERIAL_ID)
                                  	 <option selected value="{{$material->id}}">{{$material->MATERIAL_NAME}}</option>
                                  	@else
                                  	 <option value="{{$material->id}}">{{$material->MATERIAL_NAME}}</option>
                                  	@endif
                                  @endforeach
                               	</select>
                            </div>
                   		</form>
                    </td>
                    @if($simulation_detail->ERROR_FLG!=0)
                	<td style="border:none;">
                	@else
                	<td>
                	@endif
						<form action="/simulation-detail/{{$simulation_detail->id}}"
							method="post" id="material_detail_form{{ $simulation_detail -> SERIAL_NUMBER }}">
						 	<input type="hidden" name="_method" value="PUT">
                			<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<input type="hidden" name="simulation_id" value="{{$simulation_detail->id}}">
							@if($simulation_detail->PUMP_FLG)
							<input type="hidden" name="type" value="3">
							<div class="form-group">
                            	<select class="form-control" name="SPEED"
                            		onchange="materialDetailChange({{ $simulation_detail -> SERIAL_NUMBER }})" id="material_detail_select{{ $simulation_detail -> SERIAL_NUMBER }}">
                                  @foreach($speeds[$simulation_detail->MATERIAL_DETAIL_ID] as $speed)
                                  	@if($simulation_detail->REVOLUTION_INF == $speed)
                                  		<option selected>{{$speed}}</option>
                                  	@else
										<option>{{$speed}}</option>
                                  	@endif
                                  @endforeach
                               	</select>
                            </div>
                   			@else
                   			<input type="hidden" name="type" value="1">
                   			<div class="form-group">
                            	<select class="form-control" name="MATERIAL_DETAIL_ID"
                            		onchange="materialDetailChange({{ $simulation_detail -> SERIAL_NUMBER }})" id="material_detail_select{{ $simulation_detail -> SERIAL_NUMBER }}">
                                  @foreach($material_details as $material_detail)
                                  	@if($material_detail->id == $simulation_detail -> MATERIAL_DETAIL_ID)
                                  	<option selected value="{{$material_detail->id}}">{{$material_detail->MATERIAL_SIZE}}</option>
                                  	@elseif($simulation_detail -> MATERIAL_ID == $material_detail->MATERIAL_ID)
                                  	<option value="{{$material_detail->id}}">{{$material_detail->MATERIAL_SIZE}}</option>
                                  	@else

                                  	@endif
                                  @endforeach
                               	</select>
                            </div>
                            @endif
                   		</form>
					</td>
    				@if($simulation_detail->ERROR_FLG!=0)
                	<td style="border:none;">
                	@else
                	<td>
                	@endif
						<form action="/simulation-detail/{{$simulation_detail->id}}" method="post">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                   			<button type="submit" class="btn btn-xs btn-danger" aria-label="Left Align"><span class="glyphicon glyphicon-trash"></span></button>
                       	</form>
					</td>
                </tr>
                @if($simulation_detail -> SERIAL_NUMBER != $simulation_details->count())
				<tr>
					<td style="border:none;"></td>
                	<td style="border:none;">
                		<form action="/simulation-detail/{{$simulation_detail->id}}"
							method="post" id="material_form{{ $simulation_detail -> SERIAL_NUMBER }}">
						 	<input type="hidden" name="_method" value="PUT">
                			<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<input type="hidden" name="type" value="2">
							<input type="hidden" name="simulation_id" value="{{$simulation_detail->SIMULATION_ID}}">
							<button type="submit" class="btn btn-xs btn-success" aria-label="Left Align">↑ ↓</button>
						</form>
                	</td>
                	<td style="border:none"></td>
				</tr>
				@endif
                @endforeach
            </table>

        		<div class="text-center">
        			<form action="/simulation-detail/create" method="get">
                		<input type="hidden" name="simulation_id" value="{{ $simulation -> id }}">
<!--                			<button type="submit" class="btn btn-xs btn-primary" aria-label="Left Align">追加</button> -->
               			<button type="submit" class="btn btn-default" aria-label="Right Align">シミュレーションに物品追加</button>

               		</form>
        		</div>

		</div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script src="{{ asset('/js/createGraph_simulation1.js') }}"></script>
<script src="{{ asset('/js/autoForm.js') }}"></script>
@endsection
</html>
