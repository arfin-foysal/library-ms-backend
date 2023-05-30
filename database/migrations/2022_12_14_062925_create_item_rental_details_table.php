<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemRentalDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_rental_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_rental_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->tinyInteger('item_qty', false, 3)->default(0);
            $table->timestamp('return_date')->nullable();
            $table->enum('status', ['rental', 'return', 'overdue','damaged']);
            $table->bigInteger('company_id')->nullable();
            $table->float('item_amount_of_penalty')->default(0);
            $table->enum('item_payment_status', ['paid', 'due', 'nonamount'])->default('due');
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
        Schema::table('item_rental_details', function (Blueprint $table) {
            $table->dropForeign(['item_rental_id']);
            $table->dropForeign(['item_id']);
        });
        Schema::dropIfExists('item_rental_details');
    }
}
