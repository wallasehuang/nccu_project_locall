<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FriendShip extends Model
{
    protected $table = 'friendship';

    protected $fillable = ['id', 'inviter', 'invitee', 'status'];
}
