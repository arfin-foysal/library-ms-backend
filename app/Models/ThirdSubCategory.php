<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThirdSubCategory extends Model
{
    use HasFactory,SoftDeletes;


    protected $table='third_sub_categories';
    protected $fillable=['sub_category_id','name','description','icon_photo','sequence','status',];

    protected $casts = [
        'is_active' => 'boolean'
    ];

}
