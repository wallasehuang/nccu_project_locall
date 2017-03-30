<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Http\Middleware\AccessToken;
use App\Member;
use Illuminate\Http\Request;

class FriendController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth.api');
    }

    public function friends(Request $request, AccessToken $access)
    {
        $self = $access->member;
        return response()->json($self->friends->where('pivot.status', 2));
    }

    public function friendsOfInviter(Request $request, AccessToken $access)
    {
        $self = $access->member;
        return response()->json($self->friendsOfInviter->where('pivot.status', 1));
    }

    public function friendsOfInvitee(Request $request, AccessToken $access)
    {
        $self = $access->member;
        return response()->json($self->friendsOfInvitee->where('pivot.status', 1));
    }

    public function invite(Request $request, AccessToken $access)
    {
        $account = $request->input('account', null);
        $member  = Member::where('account', $account)->first();
        $self    = $access->member;
        if (!$account) {
            return response()->json(['error' => 'No member\'s account']);
        }
        if ($self->account == $account) {
            return response()->json(['message' => 'This account is yourself']);
        }
        $is_friend = $self->friends->where('account', $account)
            ->where('pivot.status', 2)->first();
        if ($is_friend) {
            return response()->json(['message' => 'You are alrady friend']);
        }

        $is_waiting = $self->friends->where('account', $account)->first();
        if ($is_waiting) {
            return response()->json(['message' => 'Waiting for accept']);
        }

        $self->friendsOfInviter()->attach($member->id, ['status' => 1]);

        return response()->json(['message' => 'Invitation sent...']);
    }

    public function accept(Request $request, AccessToken $access)
    {
        $account = $request->input('account', null);
        $member  = Member::where('account', $account)->first();
        $self    = $access->member;
        if (!$account) {
            return response()->json(['error' => 'No member\'s account']);
        }

        $is_waiting = $self->friends->where('account', $account)->first();
        if (!$is_waiting) {
            return response()->json(['message' => 'No invite this account']);
        }

        $self->friendsOfInvitee()->updateExistingPivot($member->id, ['status' => 2]);

        return response()->json(['message' => 'Accept done!']);

    }

}
