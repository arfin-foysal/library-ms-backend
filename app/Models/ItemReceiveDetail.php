<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemReceiveDetail extends Model
{
    use HasFactory,SoftDeletes;

    protected $table='item_receive_details';
    protected $fillable=['item_receive_id','item_id','item_qty','item_price'];


}
