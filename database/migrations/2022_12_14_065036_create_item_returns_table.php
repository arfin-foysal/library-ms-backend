<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_no',30);
            $table->foreignId('item_rental_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->tinyInteger('qty',false,3)->default(0);
            $table->timestamp('return_date')->nullable();
            $table->string('comments',150)->nullable();

            $table->unsignedBigInteger('created_by', false);
            $table->unsignedBigInteger('updated_by', false)->nullable();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->cascadeOnDelete();
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
        Schema::table('item_returns',function (Blueprint $table){
            $table->dropForeign(['item_rental_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });
        Schema::dropIfExists('item_returns');
    }
}
