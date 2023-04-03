<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class document extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sub_sub_catagory_id',
        'description',
        'image',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(user::class);
    }

    public function catagory()
    {
        return $this->belongsTo(catagory::class);
    }

    public function subCatagory()
    {
        return $this->belongsTo(sub_catagory::class);
    }
    
    public function subSubCatagory()
    {
        return $this->belongsTo(sub_sub_catagory::class);
    }
  



}
