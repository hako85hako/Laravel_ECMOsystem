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
        Schema::create('simulation_details', function (Blueprint $table) {
            $table->id();
            $table->integer('SIMULATION_ID')->comment('シミュレーションID');
            $table->integer('MATERIAL_ID')->default(null)->comment('物品ID');
            $table->integer('MATERIAL_DETAIL_ID')->default(null)->comment('物品詳細ID');
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
        Schema::dropIfExists('simulation_details');
    }
}
