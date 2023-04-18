<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MembershipPlan extends Model
{
    use HasFactory,SoftDeletes;
    
    protected $table='membership_plans';
    protected $fillable=['name','image','valid_duration','fee_amount','description','term_policy','sequence','status','created_by','updated_by'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

   
}
