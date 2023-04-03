<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserMembership extends Model
{
    use HasFactory,SoftDeletes;

    protected $table='user_memberships';
    protected $fillable=['user_id','membership_plan_id','valid_till','status','created_by','updated_by'];



   
}
