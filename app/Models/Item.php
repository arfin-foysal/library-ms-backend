<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable=['title','isbn','edition','number_of_page','summary','video_url','brochure','publisher_id',
        'language_id','country_id','category_id','sub_category_id','third_category_id','show_home','sequence','status','publish_status','created_by','updated_by'];

        protected $casts = [
            'is_active' => 'boolean',
            'is_show' => 'boolean',
            
        ];
   
}
