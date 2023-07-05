<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemReceivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_receives', function (Blueprint $table) {
            $table->id();
            $table->string('receive_no', 30);
            $table->foreignId('item_order_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->integer('qty', false, 5);
            $table->string('invoice_no', 50)->nullable()->comment('Invoice is provided by Vendor');
            $table->string('invoice_photo', 255)->nullable();
            $table->enum('payment_status', ['paid', 'unpaid', 'due'])->default('unpaid');
            $table->float('sub_total_amount', 8, 1)->default(0)->comment('before discount amount');
            $table->float('payable_amount', 8, 1)->default(0);
            $table->float('discount', 8, 1)->default(0);
            $table->float('paid_amount', 8, 1)->default(0);
            $table->float('due_amount', 8, 1)->default(0);
            $table->timestamp('received_date')->useCurrent()->comment('Item Order Received date');
            $table->string('comments', 200)->nullable();
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
        Schema::table('item_receives', function (Blueprint $table) {
            $table->dropForeign(['item_order_id']);
            $table->dropForeign(['vendor_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });
        Schema::dropIfExists('item_receives');
    }
}
