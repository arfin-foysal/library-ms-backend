<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sub_catagory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'catagory_id',
        'description',
        'image',
        'status',
    ];

    public function catagory()
    {
        return $this->belongsTo(catagory::class);
    }

    public function subSubCatagory()
    {
        return $this->hasMany(sub_sub_catagory::class);
    }

    public function document()
    {
        return $this->hasMany(document::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }






}
