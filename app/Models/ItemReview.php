<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemReview extends Model
{
    use HasFactory,SoftDeletes;

    protected $table='item_reviews';
    protected $fillable=['item_id','user_id','content','rating','created_by','updated_by'];
}
