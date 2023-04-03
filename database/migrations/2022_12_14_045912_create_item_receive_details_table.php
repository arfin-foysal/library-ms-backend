<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemReceiveDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_receive_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_receive_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('item_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->tinyInteger('item_qty',false,3)->default(0);
            $table->integer('item_price',false,6)->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_receive_details',function (Blueprint $table){
            $table->dropForeign(['item_receive_id']);
            $table->dropForeign(['item_id']);
        });
        Schema::dropIfExists('item_receive_details');
    }
}
