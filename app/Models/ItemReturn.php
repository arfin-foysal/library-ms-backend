<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemReturn extends Model
{
    use HasFactory,SoftDeletes;


    protected $table='item_returns';
    protected $fillable=['return_no','item_rental_id','qty','return_date','comments','created_by','updated_by'];


    
}
