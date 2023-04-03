<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemReceive extends Model
{
    use HasFactory,SoftDeletes;
   

    protected $table='item_receives';
    protected $fillable=['receive_no','item_order_id','vendor_id','qty','invoice_no','invoice_photo','payment_status','payable_amount','paid_amount',
        'due_amount','received_date','comments','created_by','updated_by'];

    
}
