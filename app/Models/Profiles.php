<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profiles extends Model
{
    //
    protected $table = 'profiles';
    protected $fillable = [
        'display_name',
        'user_id'
    ];

}
