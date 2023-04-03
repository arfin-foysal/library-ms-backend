<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemRental extends Model
{
    use HasFactory,SoftDeletes;
   
    protected $table='item_rentals';
    protected $fillable=['rental_no','rental_date','return_date','qty','user_id','amount_of_penalty',
        'penalty_status','note','status','created_by','updated_by'];

   
}
