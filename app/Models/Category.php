<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Category extends Model
{
    use HasFactory;
 
    protected $table='categories';
    protected $fillable=['name','description','icon_photo','sequence','status','show_home','is_active'];

    protected $casts = [
        'is_active' => 'boolean'
    ];
}
