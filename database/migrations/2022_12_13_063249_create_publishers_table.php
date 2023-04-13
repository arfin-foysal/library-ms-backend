<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublishersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publishers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('email', 100)->nullable();
            $table->string('mobile', 15)->nullable();
            $table->string('photo')->nullable();
            $table->string('contact', 50)->nullable();
            $table->text('address1')->nullable();
            $table->text('address2')->nullable();
            $table->text('bio')->nullable();
            $table->timestamp('establish')->nullable();
            $table->boolean('is_active')->default(true);
            $table->tinyInteger('sequence', false, 4)->default(0);
            $table->unsignedBigInteger('created_by', false);
            $table->unsignedBigInteger('updated_by', false)->nullable();
            $table->bigInteger('company_id')->nullable();
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
        Schema::table('publishers', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });
        Schema::dropIfExists('publishers');
    }
}
