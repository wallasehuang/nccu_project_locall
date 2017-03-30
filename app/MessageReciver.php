<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessageReciver extends Model
{
    protected $table = 'messages_recivers';

    protected $fillable = ['id', 'message_id', 'reciver', 'status'];

    // relation of member
    public function member()
    {
        return $this->belongsTo('App\Member', 'reciver', 'id');
    }

    // relation of message
    public function message()
    {
        return $this->belongsTo('App\Message', 'message_id', 'id');
    }
}
