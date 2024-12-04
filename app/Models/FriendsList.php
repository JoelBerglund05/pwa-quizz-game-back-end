<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FriendsList extends Model
{
    //
    protected $table = 'friends_list';
    protected $fillable = [
        'email_1',
        'email_2',
        'name_1',
        'name_2',
    ];
}
