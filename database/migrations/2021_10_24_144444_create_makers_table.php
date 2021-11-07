<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMakersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('MAKERs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('COMPANY_NAME', 30)->comment('メーカー');//メーカー
            $table->string('MAKER_URL', 200)->default('')->comment('公式サイトURL');//公式サイトURL
            $table->boolean('DELETE_FLG')->default(true)->comment('削除フラグ');//削除フラグ
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
        Schema::dropIfExists('MAKERSs');
    }
}
