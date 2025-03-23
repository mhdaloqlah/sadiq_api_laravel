<?php

namespace App\Http\Controllers;

use App\Events\SendMessageEvent;
use App\Http\Requests\SendMessageRequest;
use App\Models\Chat;
use App\Http\Requests\StoreChatRequest;
use App\Http\Requests\UpdateChatRequest;
use App\Http\Resources\ChatCollection;
use App\Http\Resources\ChatResource;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\QueryBuilder;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use LaravelFCM\Facades\FCM as FacadesFCM;
use Pushok\AuthProvider\Token;
use Pushok\Client;
use Pushok\Notification;
use Pushok\Payload;
use Pushok\Payload\Alert;
class ChatController extends Controller
{

    private Chat $chat;
    /**
     * Display a listing of the resource.
     */




    public function getChatsByLoginUser()
    {

        $userid = auth("sanctum")->user()->id;






        // $messages =  DB::table('messages')
        //     ->select(['from_user', 'to_user'])
        //     ->where('from_user', $userid)
        //     ->orwhere('to_user', $userid)
        //     ->get();

        $chats =  DB::table('chats')
            ->where('user_id', $userid)
            ->orwhere('user_to', $userid)
            ->get();


        // $messages = DB::table('users')
        //     ->select('*')
        //     ->join('messages', 'user.id', '=', 'messages.user_from')
        //     ->join('messages', 'user.id', '=', 'messages.user_to')
        //     ->whereIn('chats.user_id', (function ($query) {
        //         $query->from('messages')
        //             ->select('from_user')
        //             ->where(
        //                 'from_user',
        //                 '=',
        //                 DB::raw('chats.user_id')

        //             )->orwhere(
        //                 'to_user',
        //                 '=',
        //                 DB::raw('chats.user_id')
        //             );
        //     }))

        //     ->get();

        $users = collect([]);

        foreach ($chats as $chat) {



            $from = $chat->user_id;
            $to = $chat->user_to;


            $userall = db::table('users')
                ->where('id', '!=', $userid)
                ->where(function ($query) use ($from, $to) {

                    $query->where('id', '=', $from)
                        ->orWhere('id', '=', $to);
                })
                ->first();



            if (!$users->contains($userall)) {

                $users->push($userall);
            }
        }


        $success['data'] = $users;


        $success['success'] = true;
        return response()->json($success, 200);
    }



    public function sendMessage(SendMessageRequest $request)
    {
        
        if ($request->to == auth("sanctum")->user()->email) {
            return response()->json(['message' => "You cannot send message to yourself"]);
        }

        $OtherUserId = User::where("email", $request->to)->first()->id;
        $collection = $this->IsTherePreviousChat($OtherUserId, auth("sanctum")->user()->id);

        if ($collection == false) {
            $chat = Chat::create([
                'user_id' => auth("sanctum")->user()->id,
                'user_to' => $OtherUserId
            ]);
        }

        // $message = Message::class;
        // $message = Message::create([
        //     'from_user' => auth("sanctum")->user()->id,
        //     'to_user'   => $OtherUserId,
        //     'content'   => $request->message,
        //     'chat_id'   => $collection == false ? $chat->id : $collection[0]->chat_id,
        // ]);

        $message = [
            'from_user' => auth("sanctum")->user()->id,
            'to_user'   => $OtherUserId,
            'content'   => $request->message,
            'chat_id'   => $collection == false ? $chat->id : $collection[0]->chat_id,
        ];


        if ($request->attachment != null) {
            $video = Str::random(32) . "." . $request->attachment->getClientOriginalExtension();
            Storage::disk('public')->put($video, file_get_contents($request->attachment));
            $message['attachment'] = $video;
        }

        $message2 = Message::create($message);



        // broadcast(new SendMessageEvent($message2->toArray()))->toOthers();
        event(new SendMessageEvent($message2->toArray(), $message2));



        // $recipient = User::find($OtherUserId); // Assuming User B is the recipient

        // if ($recipient->platform === 'android') {
        //     $this->sendNotificationToAndroid($recipient->device_token, $request->message);
        // } elseif ($recipient->platform === 'ios') {
        //     $this->sendNotificationToIOS($recipient->device_token, $request->message);
        // }

        
        $success['data'] = $message2;
        $success['success'] = true;
        return response()->json($success, 200);
    }

    public function IsTherePreviousChat($OtherUserId, $user_id)
    {
        $collection = Message::whereHas('chat', function ($q) use ($OtherUserId, $user_id) {
            $q->where('from_user', $OtherUserId)
                ->where('to_user', $user_id);
        })->orWhere(function ($q) use ($OtherUserId, $user_id) {
            $q->where('from_user', $user_id)
                ->where('to_user', $OtherUserId);
        })->get();

        if (count($collection) > 0) {
            return $collection;
        }
        return false;
    }

    public function chatsdata($user_id)
    {
        $collection = Message::whereHas('chat', function ($q) use ($user_id) {
            $q->where('from_user', $user_id)
                ->orwhere('to_user', $user_id);
        })->get();


        return $collection;
    }

    public function sendNotificationToIOS($deviceToken, $message)
    {
        $authProvider = Token::create([
            'key_id' => 'your_key_id',   // Your APNs Key ID
            'team_id' => 'your_team_id', // Your Apple Developer Team ID
            'app_bundle_id' => 'your.bundle.id',
            'private_key_path' => storage_path('apns.p8'), // Path to your .p8 file
            'private_key_secret' => null // If there is a password for the .p8 file
        ]);

        $alert = Alert::create()
            ->setTitle('New Message')
            ->setBody($message);

        $payload = Payload::create()->setAlert($alert);
        $payload->setSound('default');

        $notifications = [new Notification($payload, $deviceToken)];

        $client = new Client($authProvider, $production = false);
        $client->addNotifications($notifications);

        $responses = $client->push(); // Returns an array of responses
        return $responses;
    }

    public function sendNotificationToAndroid($deviceToken, $message)
    {
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder('New Message');
        $notificationBuilder->setBody($message)
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['message' => $message]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $downstreamResponse = FacadesFCM::sendTo($deviceToken, $option, $notification, $data);

        return $downstreamResponse->numberSuccess(); // Return success count
    }
}
