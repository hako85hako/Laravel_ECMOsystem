<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePressuredropsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('PRESSURE_DROPs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('MATERIAL_DETAIL_ID', 30)->comment('物品ID');//物品ID
            $table->double('FLOW',8,4)->default(0)->comment('流量');//流量
            $table->double('PRESSURE_DROP',8,4)->default(0)->comment('揚程');//揚程
            $table->double('SPEED',8,4)->default(0)->comment('回転数');//圧力損失
            $table->double('HEAD',8,4)->default(0)->comment('揚程');//揚程
            $table->boolean('DELETE_FLG')->default(true)->comment('削除フラグ');//削除フラグ→falseで論理削除
            $table->boolean('ONLY_FLOW_FLG')->default(false)->comment('一意フラグ');//一意フラグ→trueで削除不可
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
        Schema::dropIfExists('pressuredrops');
    }
}
