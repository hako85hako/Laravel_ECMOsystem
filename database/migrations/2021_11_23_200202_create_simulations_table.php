<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSimulationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('SIMULATIONs', function (Blueprint $table) {
            $table->id()->comment('シミュレーションID');
            $table->string('SIMULATION_NAME', 200)->default('新規シミュレーション')->comment('シミュレーション名');//作成者
            $table->double('FLOW')->default(4.0)->comment('流量情報');
            $table->integer('ABP')->default(60)->comment('動脈圧情報');
            $table->integer('CVP')->default(5)->comment('中心静脈圧情報');
            $table->boolean('ABP_FLG')->default(true)->comment('動脈圧使用フラグ');
            $table->boolean('CVP_FLG')->default(true)->comment('中心静脈圧使用フラグ');
            $table->string('MONITOR', 200)->default('graphs')->comment('表示画面名');

            $table->boolean('PUBLIC_FLG')->default(false)->comment('公開フラグ');//公開フラグ
            $table->boolean('LOCK_FLG')->default(false)->comment('編集不可フラグ');//編集不可フラグ
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
        Schema::dropIfExists('SIMULATIONs');
    }
}
