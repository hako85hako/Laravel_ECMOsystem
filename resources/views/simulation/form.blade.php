<html>
<div class="container ops-main">
    <div class="row">
        <nav aria-label="breadcrumb" role="navigation">
            <ol class="breadcrumb">
            	<li class="breadcrumb-item active" aria-current="page"><a href="/">メインメニュー</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="/simulation">シミュレーション一覧</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">
                @if($target == 'store')
                	新規シミュレーション追加
                @elseif($target == 'update')
                	シミュレーション編集
                @endif
                </a></li>
            </ol>
        </nav>
    </div>
    <div class="row">
        <div class="col-md-8 col-md-offset-1">
        	@if($target == 'store')
            <form action="/simulation" method="post">
            @elseif($target == 'update')
             <form action="/simulation/{{ $simulation->id }}" method="post">
                <!-- updateメソッドにはPUTメソッドがルーティングされているのでPUTにする -->
                <input type="hidden" name="_method" value="PUT">
            @endif
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <label for="SIMULATION_NAME">シミュレーション名</label>
                	<input class="form-control" name="SIMULATION_NAME" value="{{$simulation -> SIMULATION_NAME}}">
                </div>
                <button type="submit" class="btn btn-default">登録</button>
                <button type="submit" form="nonPush" class="btn btn-default">登録せずに戻る</button>
            </form>
            <form action="/simulation" id="nonPush" method="get">
            </form>
        </div>
    </div>
</div>
</html>