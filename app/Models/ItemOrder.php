<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemOrder extends Model
{
    use HasFactory,SoftDeletes;
    
    protected $table='item_orders';
    protected $fillable=['order_no','qty','amount','discount','total','tentative_date','vendor_id','note','status','order_status','created_by','updated_by'];

}
