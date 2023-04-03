<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory,SoftDeletes;

    protected $table='vendors';
    protected $fillable=['name','email','mobile','photo','total_due','balance','contact_person','contact_person_mobile','office_address',
        'warehouse_address','primary_supply_products','sequence','status','created_by','updated_by'];

    

}
