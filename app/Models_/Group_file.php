<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group_file extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'group_id',
        'doc_id',
        'description',
        'file',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
