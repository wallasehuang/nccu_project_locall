<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Http\Middleware\AccessToken;
use App\Member;
use Illuminate\Http\Request;

class MemberController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth.api');
    }

    public function memberByAccount(Request $request, AccessToken $access)
    {
        $account = $request->input('account', null);
        $self    = $access->member;
        if (!$account) {
            return response()->json(['errors' => ['No member\'s account']]);
        }
        if ($self->account == $account) {
            return response()->json(['errors' => ['This account is yourself']]);
        }
        $member = Member::where('account', $account)->first();
        if (!$member) {
            return response()->json(['errors' => ['Can\'t find this account']]);
        }
        return response()->json($member);
    }
}
