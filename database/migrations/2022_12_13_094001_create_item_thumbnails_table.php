<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemThumbnailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_thumbnails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('big');
            $table->string('medium')->nullable();
            $table->string('small')->nullable();

            $table->enum('status',['active','inactive'])->default('active');
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
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
        Schema::table('item_thumbnails',function (Blueprint $table){
            $table->dropForeign(['item_id']);
        });
        Schema::dropIfExists('item_thumbnails');
    }
}
