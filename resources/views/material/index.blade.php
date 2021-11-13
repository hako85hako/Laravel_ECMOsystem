<html>
@extends('material/header_layout')
@section('content')
<div class="container ops-main">
	<div class="row">
        <nav aria-label="breadcrumb" role="navigation">
            <ol class="breadcrumb">
            	<li class="breadcrumb-item active" aria-current="page"><a href="/">メインメニュー</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">物品一覧</a></li>
            </ol>
        </nav>
    </div>
	<div class="row">
<!-- 		<div class="col-md-11 col-md-offset-1"> -->
		<div class="p-6 border-t border-gray-200 dark:border-gray-700 md:border-t-0 md:border-l">
    		<table class="table text-center">
        		<tr>
                    <th class="text-center">物品名</th>
                    <th class="text-center">メーカー</th>
                    <th class="text-center">種別</th>
                    <th class="text-center">圧力損失</th>
                    <th class="text-center">規格追加</th>
                    <th class="text-center">編集</th>
                    <th class="text-center">削除</th>
                </tr>
        	@foreach($materials as $material)
                <tr>


                    <td>{{ $material->MATERIAL_NAME }}</td>
                    <td>{{ $material->COMPANY_NAME }}</td>
                    <td>{{ $material->MATERIAL_KIND }}</td>
                    <td>
                    	@if($material->MATERIAL_KIND == 'Centrifugal-pump')
							<a href="/pressuredrop/{{ $material->id }}">
                        		<span class="glyphicon glyphicon-zoom-in" aria-hidden="true"></span>
                        	</a>
               			@else
                        	<a href="/pressuredrop/{{ $material->id }}">
                        		<span class="glyphicon glyphicon-zoom-in" aria-hidden="true"></span>
                        	</a>
                        @endif
                    </td>
                    <td>
                        <a href="/material-detail/{{ $material->id }}">
                   			<span class="glyphicon glyphicon-tags" aria-hidden="true"></span>
                   		</a>
                   	</td>
                    <td>
                    	<a href="/material/{{ $material->id }}/edit"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                    </td>
                    <td>
                    	<form action="/material/{{ $material->id }}" method="post">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                   			<button type="submit" class="btn btn-xs btn-danger" aria-label="Left Align"><span class="glyphicon glyphicon-trash"></span></button>
                   		</form>
               	 	</td>
                </tr>
            @endforeach
            </table>
        	<div><a href="/material/create" class="btn btn-default">新規作成</a></div>
		</div>
    </div>
</div>
@endsection
</html>





