<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Member;
use Auth;
use Illuminate\Http\Request;
use Validator;

class AuthController extends ApiController
{

    public function register(Request $request)
    {
        $data  = $request->all();
        $rules = [
            'account'  => 'required|max:255|unique:members,account',
            'password' => 'required|min:6',
            'email'    => 'required|email|unique:members,email',
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $member = Member::create([
            'account'  => $data['account'],
            'password' => bcrypt($data['password']),
            'email'    => $data['email'],
        ]);
        return response()->json($member);
    }

    public function login(Request $request)
    {
        $data  = $request->all();
        $rules = [
            'account'      => 'required',
            'password'     => 'required',
            'device_token' => 'required',
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $attempt = Auth::attempt([
            'account'  => $data['account'],
            'password' => $data['password'],
        ]);
        if (!$attempt) {
            return response()->json(['error' => 'Account or password is wrong!']);
        }
        $member = Auth::guard('api')->user();

        $member->device_token = $data['device_token'];
        $member->api_token    = str_random(60);
        $member->last_time    = date("Y-m-d H:i:s");
        $member->save();

        return response()->json($member);
    }

    public function logout(Request $request)
    {
        $api_token = $request->header('Authorization');
        if (!$api_token) {
            return response()->json(['error' => 'No Authorization']);
        }
        $member = Member::where('api_token', $api_token)->first();
        if (!$member) {
            return response()->json(['message' => 'Member has logout']);
        }
        $member->api_token = '';
        $member->save();
        return response()->json(['message' => 'Member has logout']);
    }

    public function test(Request $request)
    {
        return response()->json(Member::all());
    }
}
