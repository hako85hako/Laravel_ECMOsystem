<html>
@extends('material/header_layout')
@section('content')
<div class="container ops-main">
   <div class="row">
        <nav aria-label="breadcrumb" role="navigation">
            <ol class="breadcrumb">
            	<li class="breadcrumb-item active" aria-current="page"><a href="/">メインメニュー</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="/material">物品一覧</a></li>
                <li class="breadcrumb-item"><a href="#">{{ $material[0]->MATERIAL_NAME }}:規格編集</a></li>
            </ol>
        </nav>
    </div>
	<div class="row">
		<div class="col-md-11 col-md-offset-1">
    		<table class="table text-center">
        		<tr>
                    <th class="text-center">物品規格(Frサイズなど)</th>
                    <th class="text-center">物品容量(ml)</th>
                    <th class="text-center">データの流量間隔</th>
                    <th class="text-center">データの回転数間隔(遠心ポンプ用)</th>
                    <th class="text-center">決定</th>
                    <th class="text-center">削除</th>
                </tr>
        	@foreach($material_details as $material_detail)
        		<tr>
                    <td>
                    	<input type="text" class="form-control" name="MATERIAL_SIZE" form="update{{$material_detail->id}}" value="{{ $material_detail->MATERIAL_SIZE }}" />
                    </td>
                    <td>
                    	<input type="text" class="form-control" name="MATERIAL_VOLUME" form="update{{$material_detail->id}}" value="{{ $material_detail->MATERIAL_VOLUME }}" />
                    </td>
                    <td>
                    	<input type="text" class="form-control" name="SLICE_FLOW" form="update{{$material_detail->id}}" value="{{ $material_detail->SLICE_FLOW }}" />
                    </td>
                    <td>
                    	<input type="text" class="form-control" name="SLICE_REVOLUTIONS" form="update{{$material_detail->id}}" value="{{ $material_detail->SLICE_REVOLUTIONS }}" />
                    </td>
                    <td>
                    	<form action="/material-detail/{{$material_detail->id }}" method="post" id="update{{$material_detail->id}}">
                            <input type="hidden" name="material_id" value="{{ $material_detail->MATERIAL_ID }}">
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                   			<button type="submit" class="btn btn-xs" aria-label="Left Align"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
                   		</form>
               	 	</td>
                    <td>
                    	<form action="/material-detail/{{$material_detail->id }}" method="post">
                    		<input type="hidden" name="material_id" value="{{ $material_detail->MATERIAL_ID }}">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                   			<button type="submit" class="btn btn-xs btn-danger" aria-label="Left Align"><span class="glyphicon glyphicon-trash"></span></button>
                   		</form>
               	 	</td>
                </tr>
            @endforeach
            </table>
        	<div>
        		<form action="/material-detail/create" method="get">
<!--         			<input type="hidden" name="material_detail_id" value="{{ $material_detail->id }}"> -->
        			<input type="hidden" name="material_id" value="{{ $material_detail->MATERIAL_ID }}">
        			<button type="submit" class="btn btn-default">追加</button>
               		<button type="submit" form="nonPush" class="btn btn-default">追加せずに戻る</button>
                </form>
                <form action="/material" id="nonPush" method="get">
                </form>
        	</div>
		</div>
    </div>
</div>
@endsection
</html>