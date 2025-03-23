<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

class PushNotificationController extends Controller
{

   

    public function storeDeviceToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'platform' => 'required|string|in:ios,android',
        ]);

        $user = auth("sanctum")->user(); // Assuming the user is authenticated

        // Store or update the device token

        $user->device_token= $request->token;
        $user->platform= $request->platform;

        $user->save();

        return response()->json(['message' => 'Token stored successfully'], 200);
    }
}
