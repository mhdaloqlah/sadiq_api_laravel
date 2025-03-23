<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class MessageController extends Controller
{

    public function LoadThePreviousMessages(Request $request)
    {



        $success['data'] = Message::where(function ($query) use ($request) {
            $query->where('from_user', auth("sanctum")->user()->id)->where('to_user', $request->other);
        })->orWhere(function ($query) use ($request) {
            $query->where('from_user', $request->other)->where('to_user', auth("sanctum")->user()->id);
        })->orderBy('created_at', 'DESC')->limit(20)->get();


      
        return response()->json($success, 200);
    }


    public function LoadThePreviousMessagesWithChatId(Request $request)
    {

        $success['data'] = Chat::where("id", $request->chat_id)->with(["messages" => function ($q) use ($request) {
            $q->where("messages.chat_id", $request->chat_id)->orderBy("id", "DESC");
        }])->get();

        $success['success'] = true;
        return response()->json($success, 200);
    }

    public function LoadThePreviousMessagesWhenScroll(Request $request)
    {

        if (!$request->old_message_id || !$request->to_user)
            return;
        $message = Message::find($request->old_message_id);

        $lastMessages = Message::where(function ($query) use ($request, $message) {
            $query->where('from_user', Auth::user()->id)
                ->where('to_user', $request->to_user)
                ->where('created_at', '<', $message->created_at);
        })
            ->orWhere(function ($query) use ($request, $message) {
                $query->where('from_user', $request->to_user)
                    ->where('to_user', Auth::user()->id)
                    ->where('created_at', '<', $message->created_at);
            })
            ->orderBy('created_at', 'DESC')->limit(20)->get();

        $success['success'] = true;
        $success['data'] = $lastMessages;
        return response()->json($success, 200);
    }
}
