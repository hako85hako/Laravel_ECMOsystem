<html>
<div class="container ops-main">
    <div class="row">
        <div class="col-md-6">
            <h2>物品登録</h2>
        </div>
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
                    <input type="text" class="form-control" name="COMPANY_NAME" value="{{ $material->COMPANY_NAME }}">
                </div>
                <div class="form-group">
                    <label for="MATERIAL_KIND">物品種別</label>
                    <input type="text" class="form-control" name="MATERIAL_KIND" value="{{ $material->MATERIAL_KIND }}">
                </div>
                <div class="form-group">
                    <label for="MATERIAL_NAME">物品名</label>
                    <input type="text" class="form-control" name="MATERIAL_NAME" value="{{ $material->MATERIAL_NAME }}">
                </div>
                <button type="submit" class="btn btn-default">登録</button>
                <a href="/material">戻る</a>
            </form>
        </div>
    </div>
</div>
</html>