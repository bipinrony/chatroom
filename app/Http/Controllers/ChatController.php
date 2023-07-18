<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $message = [];
        $message['username'] = $request->username;
        $message['message'] = $request->message;
        $message['datetime'] = date('Y-m-d H:i:s');

        Message::create($message);

        event(new NewMessage($message));

        return response()->json(['status' => true, 'message' => $message]);
    }

    public function messages()
    {
        return response()->json(['messages' => Message::all()]);
    }
}
