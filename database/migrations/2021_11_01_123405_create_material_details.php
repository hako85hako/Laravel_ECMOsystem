<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('MATERIAL_DETAILs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('MATERIAL_ID', 30)->comment('物品ID');//物品ID
            $table->string('MATERIAL_SIZE', 30)->default('-')->comment('物品規格');//物品規格
            $table->string('MATERIAL_VOLUME', 30)->default('0')->comment('物品容量');//物品容量
            $table->string('SLICE_FLOW', 10)->default('0')->comment('検証データの流量間隔');//検証データの流量間隔
            $table->string('SLICE_REVOLUTIONS', 10)->default('0')->comment('検証データの回転数間隔(遠心ポンプ)');//検証データの回転数間隔(遠心ポンプ)
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
        Schema::dropIfExists('MATERIAL_DETAILs');
    }
}
