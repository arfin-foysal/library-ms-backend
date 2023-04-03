<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_order_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('item_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('company_id')->nullable();
            $table->tinyInteger('item_qty',false,4)->default(0);
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
        Schema::table('item_order_details',function (Blueprint $table){
            $table->dropForeign(['item_order_id']);
            $table->dropForeign(['item_id']);
        });
        Schema::dropIfExists('item_order_details');
    }
}
