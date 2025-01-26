<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActiveGames extends Model
{
    protected $fillable = [
        'user_id_1',
        'user_id_2',
        'user_name_1',
        'user_name_2',
        'user_turn',
        'user_points_1',
        'user_points_2',
        'rounds',
        'user_1_has_answered_question',
        'user_2_has_answered_question'
    ];}
