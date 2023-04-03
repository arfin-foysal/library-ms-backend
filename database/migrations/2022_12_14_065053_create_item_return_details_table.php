<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemReturnDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_return_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_return_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('company_id')->nullable();
            $table->tinyInteger('item_qty',false,3)->default(0);
            $table->timestamp('return_date')->nullable();
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
        Schema::table('item_return_details',function (Blueprint $table){
            $table->dropForeign(['item_return_id']);
            $table->dropForeign(['item_id']);
        });
        Schema::dropIfExists('item_return_details');
    }
}
