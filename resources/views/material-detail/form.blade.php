<html>
<div class="container ops-main">
    <div class="row">
        <div class="col-md-6">
            <h2>詳細物品登録</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 col-md-offset-1">
        	@if($target == 'store')
            <form action="/material-detail" method="post">
            @elseif($target == 'update')
             <form action="/material-detail/{{ $material_detail->id }}" method="post">
                <!-- updateメソッドにはPUTメソッドがルーティングされているのでPUTにする -->
                <input type="hidden" name="_method" value="PUT">
            @endif

                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <label for="MATERIAL_SIZE">物品規格(Frサイズなど)</label>
                    <input type="text" class="form-control" name="MATERIAL_SIZE" value="{{ $material_detail->MATERIAL_SIZE }}">
                </div>
                <div class="form-group">
                    <label for="MATERIAL_VOLUME">物品容量(ml)</label>
                    <input type="text" class="form-control" name="MATERIAL_VOLUME" value="{{ $material_detail->MATERIAL_VOLUME }}">
                </div>
                <div class="form-group">
                    <label for="SLICE_FLOW"></label>
                    <input type="text" class="form-control" name="SLICE_FLOW" value="{{ $material_detail->SLICE_FLOW }}">
                </div>
                <div class="form-group">
                    <label for="SLICE_REVOLUTIONS"></label>
                    <input type="text" class="form-control" name="SLICE_REVOLUTIONS" value="{{ $material_detail->SLICE_REVOLUTIONS }}">
                </div>
                <input type="hidden" name="material_id" value="{{ $material_detail->MATERIAL_ID}}">
                <button type="submit" class="btn btn-default">登録</button>
                <a href="/material">戻る</a>
            </form>
        </div>
    </div>
</div>
</html>
