<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class userHasPermission extends Model
{
    use HasFactory;

    
    public function permission ()
    {
        return $this->belongsTo(permission::class);
    }


}
