<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemReturnDetail extends Model
{
    use HasFactory,SoftDeletes;

    protected $table='item_return_details';
    protected $fillable=['item_return_id','item_id','item_qty','return_date'];
}
