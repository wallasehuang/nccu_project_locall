<?php

namespace App\Http\Middleware;

use App\Member;
use Closure;

class AccessToken
{
    public $member;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $api_token = $request->header('Authorization');
        if (!$api_token) {
            return response()->json(['error' => 'No Authorization!']);
        }
        $member = Member::where('api_token', $api_token)->first();
        if (!$member) {
            return response()->json(['error' => 'Not find member!']);
        }
        // $request->attributes('member_id', $member->id);
        $this->member = $member;
        return $next($request);
    }
}
