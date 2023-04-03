<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sub_sub_catagory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sub_catagory_id',
        'description',
        'image',
        'status',
    ];

    public function subCatagory()
    {
        return $this->belongsTo(sub_catagory::class);
    }

    public function document()
    {
        return $this->hasMany(document::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function catagory()
    {
        return $this->belongsTo(catagory::class);
    }

}
