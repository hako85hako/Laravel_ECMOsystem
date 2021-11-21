<html>
@extends('material/header_layout')
@section('content')
<script>
	//回転数
	const speed_list = @json($speed_list);
    //流量(ラベル)
    const flow_list = @json($flow_list);
    //keyが回転数、valueが揚程
    const head_speed_list = @json($head_speed_list);
</script>
    <div class="container">
        <div class="row">
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                	<li class="breadcrumb-item active" aria-current="page"><a href="/">メインメニュー</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="/material">物品一覧</a></li>
                    <li class="breadcrumb-item"><a href="#">{{ $materials[0]->MATERIAL_NAME }}:揚程詳細</a></li>
                </ol>
            </nav>
        </div>
        <div class="row">
        	<div class="chart-container" style="position: relative; height:80vh; width:auto">
        		<canvas id="headGraph"></canvas>
   	 		</div>
    	</div>

        <div class="row">
            <div class="col">
    <!--         =============================== -->
    <!--         =============================== -->

         	<?php $i = 0 ;?>
    	@foreach($speed_list as $speed)
                <details>
                <summary>回転数 ： {{$speed}} rpm </summary>
                	<table class="table text-center">
                		<tr>
                            <th class="text-center">流量<br>[L/min]</th>
                            <th class="text-center">揚程<br>[mmHg]</th>
                            <th class="text-center">作成者</th>
                            @if(Auth::user()->role == "manager" or Auth::user()->role == "admin")
                                  	 <th class="text-center">更新者</th>
                                  	<th class="text-center">更新日時</th>
                                    <th class="text-center">更新</th>
                                    <th class="text-center">削除</th>
                                    <th class="text-center">編集ロック</th>
                                    <th class="text-center">公開</th>
                           @endif
                        </tr>
        			@foreach($headflows[$speed] as $headflow)
         			@if($headflow->PUBLIC_FLG==1 or Auth::user()->id == $headflow->CREATE_USER_ID)

                        <tr>
                            <td>
                            	<label>{{ $headflow->FLOW }}</label>
                            	<input type="hidden" class="form-control" name="FLOW" form="update{{$headflow->id}}" value="{{ $headflow->FLOW }}" />
                            </td>
                            <td>
                            	@if(Auth::user()->role == "manager" or Auth::user()->role == "admin")
                                	@if($headflow->LOCK_FLG == 1)
                                		{{ $headflow->HEAD }}
                                	@else
                            			<input type="text" class="form-control" name="HEAD" form="update{{$headflow->id}}" value="{{ $headflow->HEAD }}" />
                            		@endif
    							@else
    								{{ $headflow->HEAD }}
    							@endif

                            </td>
                            <td>
                            	<!--作成者-->
                            	{{ $headflow->CREATE_USER }}
                            </td>
                            @if(Auth::user()->role == "manager" or Auth::user()->role == "admin")
                            	<td>
                                	<!--更新者-->
                                	{{ $headflow->UPDATE_USER }}
                                </td>
                            	<td>
                                	<!--更新日時-->
                                	{{ $headflow->updated_at }}
                                </td>
                                <td>
                                 <!--更新ボタン-->
                             	 	@if($headflow->LOCK_FLG == 1)
                                    	<span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
                               		@else
                                    	<form action="/pressuredrop/{{ $headflow->id }}" method="post" id="update{{$headflow->id}}">
                                       		<input type="hidden" name="material_kind" value="{{ $materials[0] -> MATERIAL_KIND }}">
                       						<input type="hidden" name="material_id" value="{{ $material_details[0]->MATERIAL_ID}}">
                       						<input type="hidden" name="flg" value="update">
     										<!--↓↓↓↓↓↓↓なんでクラスから取れんかは不明↓↓↓↓↓↓↓-->
    <!--                                         <input type="hidden" name="SPEED" value="{{$speed}}"> -->
                                            <input type="hidden" name="_method" value="PUT">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                   			<button type="submit" class="btn btn-xs" aria-label="Left Align"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
                                   		</form>
                               		@endif
                           	 	</td>
                                <td>
    							<!--削除ボタン-->
                                @if($headflow->LOCK_FLG == 1)
                                	<span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
                                @else
                                	<form action="/pressuredrop/{{ $headflow->id }}" method="post">
                                		<input type="hidden" name="material_id" value="{{ $material_details[0]->MATERIAL_ID }}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                               			<button type="submit" class="btn btn-xs btn-danger" aria-label="Left Align"><span class="glyphicon glyphicon-trash"></span></button>
                               		</form>
    							@endif
                           	 	</td>

                            　　<td>
                       	     <!--編集ロックボタン-->
								@if($headflow->LOCK_FLG == 1)
                                    <!--編集ロックをオン -->
                                    <form action="/pressuredrop/{{ $headflow->id }}" method="post" id="update{{$headflow->id}}">
                   						<input type="hidden" name="flg" value="unlock">
                   						<input type="hidden" name="material_id" value="{{ $material_details[0]->MATERIAL_ID}}">
                                        <input type="hidden" name="_method" value="PUT">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                               			<button type="submit" class="btn btn-xs" aria-label="Left Align"><span class="glyphicon glyphicon-check" aria-hidden="true"></span></button>
                               		</form>
								@else
                                    <!--編集ロックをオフ-->
                                    <form action="/pressuredrop/{{ $headflow->id }}" method="post" id="update{{$headflow->id}}">
                   						<input type="hidden" name="flg" value="lock">
                   						<input type="hidden" name="material_id" value="{{ $material_details[0]->MATERIAL_ID}}">
                                        <input type="hidden" name="_method" value="PUT">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                               			<button type="submit" class="btn btn-xs" aria-label="Left Align"><span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span></button>
                               		</form>
                                @endif
                       	 	</td>
                       	 	<td>
                               	 	<!--公開ボタン-->
                               	 	@if(Auth::user()->id == $headflow->CREATE_USER_ID)
                                        @if($headflow->PUBLIC_FLG == 1)
                                             <!--公開中 -->
                                        	 <form action="/pressuredrop/{{ $headflow->id }}" method="post" id="update{{$headflow->id}}">
                           						<input type="hidden" name="flg" value="be_private">
                           						<input type="hidden" name="material_id" value="{{ $material_details[0]->MATERIAL_ID}}">
                                                <input type="hidden" name="_method" value="PUT">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                       			<button type="submit" class="btn btn-xs" aria-label="Left Align"><span class="glyphicon glyphicon-check" aria-hidden="true"></span></button>
                                   			</form>
                                        @else
                                         	<!--非公開中 -->
                                        	<form action="/pressuredrop/{{ $headflow->id }}" method="post" id="update{{$headflow->id}}">
                           						<input type="hidden" name="flg" value="be_public">
                           						<input type="hidden" name="material_id" value="{{ $material_details[0]->MATERIAL_ID}}">
                                                <input type="hidden" name="_method" value="PUT">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                       			<button type="submit" class="btn btn-xs" aria-label="Left Align"><span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span></button>
                                       		</form>
                                        @endif
        							@else
        								-
        							@endif
                         </td>
                    	@endif
						</tr>
        			@endif
        			@endforeach
    			</table>
    			@if(Auth::user()->role == "manager" or Auth::user()->role == "admin")
                	<form action="/pressuredrop/create" method="get">
                		<input type="hidden" name="material_kind" value="{{ $materials[0] -> MATERIAL_KIND }}">
                	    <input type="hidden" name="material_detail_id" value="{{ $headflow -> MATERIAL_DETAIL_ID }}">
               			<input type="hidden" name="material_id" value="{{ $material_details[0] -> MATERIAL_ID }}">
               			<input type="hidden" name="slice_flow" value="{{ $material_details[0] -> SLICE_FLOW }}">
               			<input type="hidden" name="slice_revolution" value="{{ $material_details[0] -> SLICE_REVOLUTIONS }}">
               			<input type="hidden" name="last_flow" value="{{ $headflow -> FLOW }}">
               			<input type="hidden" name="last_speed" value="{{ $headflow -> SPEED }}">
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
<script src="{{ asset('/js/createGraph_head.js') }}"></script>
<script src="{{ asset('/js/accodion.js') }}"></script>
@endsection
</html>