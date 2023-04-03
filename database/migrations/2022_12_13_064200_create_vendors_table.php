<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->string('photo')->nullable();
            $table->float('total_due',13,2)->default(0);
            $table->float('balance',13,2)->default(0)->comment('All Payment (Advance, initial, and regular payment');
            $table->string('contact_person',10)->nullable();
            $table->string('contact_person_mobile',40)->nullable();
            $table->text('office_address')->nullable();
            $table->text('warehouse_address')->nullable();
            $table->text('primary_supply_products')->nullable();
            $table->enum('status',['active','inactive'])->default('active');
            $table->tinyInteger('sequence',false,4)->default(0);
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
    {Schema::table('vendors',function (Blueprint $table){
        $table->dropForeign(['created_by']);
        $table->dropForeign(['updated_by']);
    });
        Schema::dropIfExists('vendors');
    }
}
