<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('title',2500);
            $table->string('isbn',100)->nullable();
            $table->string('edition',100)->nullable();
            $table->string('number_of_page',50)->nullable();
            $table->text('summary')->nullable();
            $table->string('video_url')->nullable( );
            $table->string('brochure')->nullable( )->comment('Pdf file');
            $table->enum('status',['active','inactive'])->default('active');
            $table->tinyInteger('sequence',false,4)->default(0);
            $table->enum('is_show',['yes','no'])->default('no');
            $table->enum('publish_status',['published','unpublished'])->default('unpublished');
            $table->foreignId('publisher_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('language_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('country_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->unsignedBigInteger('third_category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
            $table->foreign('sub_category_id')->references('id')->on('sub_categories')->cascadeOnDelete();
            $table->foreign('third_category_id')->references('id')->on('third_sub_categories')->cascadeOnDelete();
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
        Schema::table('items',function (Blueprint $table){

            $table->dropForeign(['publisher_id']);
            $table->dropForeign(['language_id']);
            $table->dropForeign(['country_id']);

            $table->dropForeign(['category_id']);
            $table->dropForeign(['sub_category_id']);
            $table->dropForeign(['third_category_id']);

            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });

        Schema::dropIfExists('items');
    }
}
