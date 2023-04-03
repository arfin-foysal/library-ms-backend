<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'status',
        
    ];

    public function group_member()
    {
        return $this->hasMany(Group_member::class, );
    }

    public function user()
    {
        return $this->belongsToMany(User::class, 'group_members', 'group_id', 'user_id');
    }

    public function group_file()
    {
        return $this->hasMany(Group_file::class);
    }

    public function groupCreator()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }



    

}
