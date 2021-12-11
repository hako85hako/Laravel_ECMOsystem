<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSimulationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('SIMULATION_DETAILs', function (Blueprint $table) {
            $table->id();
            $table->integer('SERIAL_NUMBER')->default(0)->comment('表示、計算順序');
            $table->integer('SIMULATION_ID')->comment('シミュレーションID');
            $table->integer('MATERIAL_ID')->nullable()->default(null)->comment('物品ID');
            $table->integer('MATERIAL_DETAIL_ID')->nullable()->default(null)->comment('物品詳細ID');
            $table->double('REVOLUTION_INF')->default(0.0)->comment('回転数情報');
            $table->boolean('PUMP_FLG')->default(false)->comment('ポンプフラグ');
            $table->integer('ERROR_FLG')->default(0)->comment('エラーフラグ');

            $table->boolean('DELETE_FLG')->default(true)->comment('削除フラグ');//削除フラグ
            $table->string('CREATE_USER', 200)->comment('作成者');//作成者
            $table->string('UPDATE_USER', 200)->comment('更新者');//更新者
            $table->string('CREATE_USER_ID', 200)->comment('作成者ID');//作成者ID
            $table->string('UPDATE_USER_ID', 200)->comment('更新者ID');//更新者ID
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
        #Schema::dropIfExists('SIMULATION_DETAILs');
    }
}
