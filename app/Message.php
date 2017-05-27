<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';

    protected $fillable = ['id', 'sender', 'send_time', 'latitude', 'longitude', 'message', 'status'];

    // relation of sender
    public function senderModel()
    {
        return $this->belongsTo('App\Member', 'sender', 'id');
    }

    // relaction of message reciver
    public function reciver()
    {
        return $this->hasMany('App\MessageReciver', 'message_id', 'id');
    }

}
