<html>
@extends('material/header_layout')
@section('content')
<div class="container">
	<div class="row">
        <nav aria-label="breadcrumb" role="navigation">
            <ol class="breadcrumb">
            	<li class="breadcrumb-item active" aria-current="page"><a href="/">メインメニュー</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">シミュレーション一覧</a></li>
            </ol>
        </nav>
    </div>
	<div class="row">
<!-- 		<div class="col-md-11 col-md-offset-1"> -->
		<div class="p-6 border-t border-gray-200 dark:border-gray-700 md:border-t-0 md:border-l">
    		<table class="table text-center">
        		<tr>
                    <th class="text-center">シミュレーション名</th>
                    <th class="text-center">物品数</th>
                	<th class="text-center">更新日</th>
                    <th class="text-center">作成者</th>
                    <th class="text-center">名称変更</th>
                    <th class="text-center">削除</th>
                </tr>
                @foreach($simulations as $simulation)
                <tr>
                    <td>
                    	<a href="/simulation/{{ $simulation->id }}">{{ $simulation->SIMULATION_NAME }}</a>
                    </td>
                    <td>
                    	@foreach($simulation_detail_counts as $simulation_detail_count)
                    		@if($simulation->id == $simulation_detail_count['id'])
                    			{{$simulation_detail_count['count']}}
                    		@endif
                    	@endforeach
                    </td>
                    <td>{{ $simulation->updated_at }}</td>
					<td>{{ $simulation->CREATE_USER }}</td>
    				<td>
    					<a href="/simulation/{{ $simulation->id }}/edit">
    						<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
    					</a>
					</td>
					<td>
						<form action="/simulation/{{ $simulation->id }}" method="post">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                   			<button type="submit" class="btn btn-xs btn-danger" aria-label="Left Align"><span class="glyphicon glyphicon-trash"></span></button>
                   		</form>
                    </td>
                </tr>
                @endforeach
            </table>
        		<div><a href="/simulation/create" class="btn btn-default">新規作成</a></div>
		</div>
    </div>
</div>
@endsection
</html>





