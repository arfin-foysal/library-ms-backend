<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Author extends Model
{
    use HasFactory;




    protected $casts = [
        'is_show' => 'boolean',
        'is_active' => 'boolean'
    ];
}
