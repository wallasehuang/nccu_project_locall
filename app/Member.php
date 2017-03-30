<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;

class Member extends Model implements AuthenticatableContract
{
    use Authenticatable;
    protected $table = 'members';

    protected $fillable = ['id', 'account', 'password', 'email', 'api_token', 'device_token', 'last_time'];

    protected $hidden = ['password', 'remember_token'];

    // friend of Inviter
    public function friendsOfInviter()
    {
        return $this->belongsToMany('App\Member', 'friendship', 'inviter', 'invitee')->withPivot('status', 'created_at', 'updated_at');
        // ->wherePivot('status', 2);
    }

    // friend of Invitee
    public function friendsOfInvitee()
    {
        return $this->belongsToMany('App\Member', 'friendship', 'invitee', 'inviter')
            ->withPivot('status', 'created_at', 'updated_at');
        // ->wherePivot('status', 2);
    }

    // merge friend
    protected function mergeFriends()
    {
        return $this->friendsOfInviter->merge($this->friendsOfInvitee);
    }

    // accessor allowing you call $user->friends
    public function getFriendsAttribute()
    {
        if (!array_key_exists('friends', $this->relations)) {
            $this->loadFriends();
        }

        return $this->getRelation('friends');
    }

    protected function loadFriends()
    {
        if (!array_key_exists('friends', $this->relations)) {
            $friends = $this->mergeFriends();

            $this->setRelation('friends', $friends);
        }
    }

    // relation of message
    public function messages()
    {
        return $this->hasMany('App\Message', 'sender', 'id');
    }

    // relation of reciver
    public function reciver()
    {
        return $this->hasMany('App\MessageReciver', 'reciver', 'id');
    }

}
