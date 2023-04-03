<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorPayment extends Model
{
    use HasFactory,SoftDeletes;

    protected $table='vendor_payments';
    protected $fillable=['vendor_payment_no','item_receive_id','vendor_id','paid_amount','total_last_due_amount','payment_date','payment_photo','payment_through','comments','created_by','updated_by'];


}
