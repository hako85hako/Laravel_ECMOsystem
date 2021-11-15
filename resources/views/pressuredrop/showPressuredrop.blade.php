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
                            <th class="text-center">更新者<br></th>
                            <th class="text-center">作成者<br></th>
                            <th class="text-center">最終更新日時</th>

                          	@if(Auth::user()->role === "manager" or
								Auth::user()->role === "admin")
                                    <th class="text-center">更新</th>
                                    <th class="text-center">削除</th>
                                    <th class="text-center">公開</th>
                                    <th class="text-center">編集ロック</th>
                           @endif
                        </tr>
        			@foreach($pressuredrops[$i] as $pressuredrop)
                        <tr>
                            <td>
                                <!--流量-->
                            	<label>{{ $pressuredrop->FLOW }}</label>
                            	<input type="hidden" class="form-control" name="FLOW" form="update{{$pressuredrop->id}}" value="{{ $pressuredrop->FLOW }}" />
                            </td>
                            <td>
                            	<!--圧力損失-->
                            	@if($pressuredrop->LOCK_FLG == 1)
                            		{{ $pressuredrop->PRESSURE_DROP }}
                            	@else
                            		<input type="text" class="form-control" name="PRESSURE_DROP" form="update{{$pressuredrop->id}}" value="{{ $pressuredrop->PRESSURE_DROP }}" />
								@endif
                            </td>
                            <td>
                            	<!--作成者-->
                            	{{ $pressuredrop->CREATE_USER }}
                            </td>
                            <td>
                            	<!--更新者-->
                            	{{ $pressuredrop->UPDATE_USER }}
                            </td>
                            <td>
                            	<!--更新日時-->
                            	{{ $pressuredrop->updated_at }}
                            </td>



							@if(Auth::user()->role === "manager" or
								Auth::user()->role === "admin")
                                    <td>
                                    <!--更新ボタン-->
                                    @if($pressuredrop->LOCK_FLG === 1)
                                    	<span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
                                   	@else
                                   		<form action="/pressuredrop/{{ $pressuredrop->id }}" method="post" id="update{{$pressuredrop->id}}">
                       						<input type="hidden" name="material_id" value="{{ $material_details[0]->MATERIAL_ID}}">
                                            <input type="hidden" name="_method" value="PUT">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                   			<button type="submit" class="btn btn-xs" aria-label="Left Align"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
                                   		</form>
                                   	@endif
                               	 	</td>
                                    <td>
                                    <!--削除ボタン-->
                                    @if($pressuredrop->LOCK_FLG === 1)
                                   		<span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
                                   	@else
                                   		<form action="/pressuredrop/{{ $pressuredrop->id }}" method="post">
                                    		<input type="hidden" name="material_id" value="{{ $material_details[0]->MATERIAL_ID}}">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                   			<button type="submit" class="btn btn-xs btn-danger" aria-label="Left Align"><span class="glyphicon glyphicon-trash"></span></button>
                                   		</form>
                                   	@endif
                               	 	</td>
                               	 	<td>
                               	 	 <!--公開ボタン-->
                               	 	@if(Auth::user()->id === $pressuredrop->CREATE_USER_ID)
                                        @if($pressuredrop->PUBLIC_FLG == 1)
                                        	公開中
                                        @else
                                        	非公開中
                                        @endif
                                        <!--公開設定ボタンを設置 -->
        							@else
        								公開中
        							@endif
                               	 	</td>
                               	 	 <td>
                               	     <!--編集ロックボタン-->
                               	    @if(Auth::user()->role === "manager" or
                               	    	Auth::user()->role === "admin"
                               	    )
        								@if($pressuredrop->PUBLIC_FLG == 1)
                                            編集アンロック<!--編集ロックをオフにするボタンを設置 -->
        								@else
                                            編集ロック<!--編集ロックをオンにするボタンを設置 -->
                                        @endif
                               	 	@else
                               	 		権限なし
                               	 	@endif
                               	 	</td>
                            @endif
						</tr>
        			@endforeach
    			</table>
    			@if(Auth::user()->role === "manager" or
           	    	Auth::user()->role === "admin"
           	    )
                	<form action="/pressuredrop/create" method="get">
                        <input type="hidden" name="material_detail_id" value="{{ $pressuredrops[$i][0]->MATERIAL_DETAIL_ID}}">
               			<input type="hidden" name="material_id" value="{{ $material_details[0]->MATERIAL_ID}}">
               			<input type="hidden" name="slice_flow" value="{{ $material_details[0]->SLICE_FLOW}}">
               			<input type="hidden" name="last_flow" value="{{ $pressuredrop->FLOW }}">
               			<button type="submit" class="btn btn-xs btn-danger" aria-label="Left Align">新規作成</button>
               		</form>
               	@endif
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