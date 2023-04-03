<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_payments', function (Blueprint $table) {
            $table->id();
            $table->string('vendor_payment_no',30);
            $table->foreignId('item_receive_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('vendor_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->float('paid_amount',false,8,1)->default(0);
            $table->float('due_amount',false,8,1)->default(0);
            $table->enum('payment_through',['Cash','Bank','Check'])->default('Cash');
            $table->string('payment_photo',255)->nullable();
            $table->timestamp('payment_date')->useCurrent()->comment('Vendor payment date');
            $table->string('comments',150)->nullable();
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
        Schema::table('vendor_payments',function (Blueprint $table){
            $table->dropForeign(['item_receive_id']);
            $table->dropForeign(['vendor_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });
        Schema::dropIfExists('vendor_payments');
    }
}
