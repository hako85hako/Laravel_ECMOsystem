<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('MATERIALs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('COMPANY_NAME', 30)->comment('メーカー');//メーカー
            $table->string('MATERIAL_KIND', 30)->comment('物品種別');//物品種別
            $table->string('MATERIAL_NAME', 30)->comment('物品名');//物品名
            //$table->string('MATERIAL_VOLUME', 30)->comment('物品容量');//物品容量
            //$table->string('SLICE_FLOW', 10)->comment('検証データの流量間隔');//検証データの流量間隔
            //$table->string('SLICE_REVOLUTIONS', 10)->comment('検証データの回転数間隔(遠心ポンプ)');//物品名
            $table->boolean('DELETE_FLG')->default(True)->comment('削除フラグ');//削除フラグ
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('MATERIALSs');
    }
}
