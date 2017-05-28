<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Http\Middleware\AccessToken;
use App\Member;
use App\Message;
use DB;
use Illuminate\Http\Request;
use Validator;

class MessageController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth.api');
    }

    public function message(AccessToken $access)
    {
        $self   = $access->member;
        $result = collect();
        foreach ($self->reciver->where('status', 1) as $reciver) {
            $data = [
                'id'        => $reciver->message->id,
                'sender'    => $reciver->message->senderModel->account,
                'send_time' => $reciver->message->send_time,
                'latitude'  => $reciver->message->latitude,
                'longitude' => $reciver->message->longitude,
                'message'   => $reciver->message->message,
            ];
            $result->push($data);
        }
        return response()->json($result);
    }

    public function send(Request $request, AccessToken $access)
    {
        $self  = $access->member;
        $data  = $request->all();
        $rules = [
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'message'   => 'required|max:255',
            'reciver'   => 'required|array',
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        DB::beginTransaction();
        try {
            $message = Message::create([
                'sender'    => $self->id,
                'send_time' => date("Y-m-d H:i:s"),
                'latitude'  => $data['latitude'],
                'longitude' => $data['longitude'],
                'message'   => $data['message'],
            ]);
            foreach ($data['reciver'] as $reciver) {
                $message->reciver()->create(['reciver' => $reciver, 'status' => 1]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e]);
        }

        return response()->json(['message' => 'success send message!']);
        // return response()->json($message->with('reciver')->where('id', $message->id)->get());

    }

    public function watch(Request $request, AccessToken $access)
    {
        $self  = $access->member;
        $data  = $request->all();
        $rules = [
            'latitude'   => 'required|numeric',
            'longitude'  => 'required|numeric',
            'message_id' => 'required',
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $is_message = $self->reciver->where('status', 1)->where('message_id', $data['message_id'])->first();
        if (!$is_message) {
            return response()->json(['error' => 'Message not found!']);
        }

        $message = $is_message->message;

        // determine message and member's location distance
        $radLatMessage  = deg2rad($message->latitude);
        $radLongMessage = deg2rad($message->longitude);
        $radLatSelf     = deg2rad($data['latitude']);
        $radLongSelf    = deg2rad($data['longitude']);
        $a              = $radLatMessage - $radLatSelf;
        $b              = $radLongMessage - $radLongSelf;
        $distance       = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLatMessage) * cos($radLatSelf) * pow(sin($b / 2), 2))) * 6378137;

        if ($distance > 5) {
            return response()->json(['message' => 'You are too far away from that message!']);
        }

        $is_message->status = 2;
        $is_message->save();
        return response()->json($message);
    }
}
