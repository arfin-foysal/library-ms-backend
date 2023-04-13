<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model
{
    use HasFactory;
    
    protected $table='sub_categories';
    
    protected $fillable=['category_id','name','description','icon_photo','sequence','is_active',];

    protected $casts = [
        'is_active' => 'boolean'
    ];



}