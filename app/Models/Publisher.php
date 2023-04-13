<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Publisher extends Model
{
    use HasFactory,SoftDeletes;

    protected $table='publishers';
    protected $fillable=['name','email','phone','contact','photo','address1','address2','bio','establish','sequence','status','created_by','updated_by'];


    protected $casts = [
        'is_active' => 'boolean'
    ];


}
