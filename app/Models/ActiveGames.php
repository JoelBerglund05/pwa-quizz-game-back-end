<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActiveGames extends Model
{
    //
    protected $fillable = [
        'user_id_1',
        'user_turn',
        'user_points_1',
        'user_points_2',
    ];

}
