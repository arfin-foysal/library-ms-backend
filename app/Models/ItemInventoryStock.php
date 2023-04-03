<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemInventoryStock extends Model
{
    use HasFactory,SoftDeletes;

    protected $table='item_inventory_stocks';
    protected $fillable=['item_id','qty','created_by','updated_by'];

   
}
