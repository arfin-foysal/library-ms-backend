<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class catagory extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'user_id',
        'description',
        'image',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subCatagory()
    {
        return $this->hasMany(sub_catagory::class);
    }

    public function document()
    {
        return $this->hasMany(document::class);
    }

    public function subSubCatagory()
    {
        return $this->hasMany(sub_sub_catagory::class);
    }



}
