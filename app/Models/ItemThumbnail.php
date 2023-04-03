<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemThumbnail extends Model
{
    use HasFactory,SoftDeletes;

    protected $table='item_thumbnails';
    protected $fillable=['item_id','big','medium','small','is_thumbnail',];
}
