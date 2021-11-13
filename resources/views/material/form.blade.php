<html>
<div class="container ops-main">
    <div class="row">
        <nav aria-label="breadcrumb" role="navigation">
            <ol class="breadcrumb">
            	<li class="breadcrumb-item active" aria-current="page"><a href="/">メインメニュー</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="/material">物品一覧</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">
                @if($target == 'store')
                	物品追加
                @elseif($target == 'update')
                	物品編集
                @endif
                </a></li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-md-8 col-md-offset-1">
        	@if($target == 'store')
            <form action="/material" method="post">
            @elseif($target == 'update')
             <form action="/material/{{ $material->id }}" method="post">
                <!-- updateメソッドにはPUTメソッドがルーティングされているのでPUTにする -->
                <input type="hidden" name="_method" value="PUT">
            @endif
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <label for="COMPANY_NAME">メーカー名</label>
                	<select class="form-control" name="COMPANY_NAME">
                          @foreach($makers as $maker)
                          	@if($maker->COMPANY_NAME===$material->COMPANY_NAME)
                          	<option selected>{{$maker->COMPANY_NAME}}</option>
                          	@else
                          	 <option>{{$maker->COMPANY_NAME}}</option>
                          	@endif
                          @endforeach
                   	</select>
                </div>

                <div class="form-group">
                	 <div class="form-group">
                       	<label for="MATERIAL_KIND">物品種別</label>
                        <select class="form-control" name="MATERIAL_KIND">
                          @foreach($material_kinds as $material_kind)
                          	@if($material_kind->MATERIAL_KIND===$material->MATERIAL_KIND)
                          	<option selected>{{$material_kind->MATERIAL_KIND}}</option>
                          	@else
                          	 <option>{{$material_kind->MATERIAL_KIND}}</option>
                          	@endif
                          @endforeach
                        </select>
                	</div>
                </div>

<!--                     <input type="text" class="form-control" name="MATERIAL_KIND" value="{{ $material->MATERIAL_KIND }}"> -->


                <div class="form-group">
                    <label for="MATERIAL_NAME">物品名</label>
                    <input type="text" class="form-control" name="MATERIAL_NAME" value="{{ $material->MATERIAL_NAME }}">
                </div>
                <button type="submit" class="btn btn-default">登録</button>
                <button type="submit" form="nonPush" class="btn btn-default">登録せずに戻る</button>
            </form>
            <form action="/material" id="nonPush" method="get">
            </form>
        </div>
    </div>
</div>
</html>