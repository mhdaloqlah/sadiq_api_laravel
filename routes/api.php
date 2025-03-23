<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FcmController;
use App\Http\Controllers\ImageServiceController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('emailverification', [AuthController::class, 'emailverification']);
Route::post('sendemailverifiction', [AuthController::class, 'sendemailverifiction']);
Route::post('passwordreset', [AuthController::class, 'passwordreset']);
Route::post('forgetpassword', [AuthController::class, 'forgetpassword']);
Route::post('changepassword', [AuthController::class, 'changepassword']);
// Route::post('category/update', [CategoryController::class, 'updatecategory']);

Route::post('/store-token', [PushNotificationController::class, 'storeDeviceToken']);


Route::put('update-device-token', [FcmController::class, 'updateDeviceToken']);
Route::post('send-fcm-notification', [FcmController::class, 'sendFcmNotification']);



Route::apiResource('category', CategoryController::class)->only(
    [
        'index',
        'show',
        'store',
        'update',
        'destroy'
    ]
);



Route::apiResource('service', ServiceController::class)->only(
    [
        'index',
        'show',
    ]
);

Route::apiResource('ImageService', ImageServiceController::class)->only(
    [
        'index',
        'store',
        'destroy'
    ]
);



Route::get('ImageService/{id}', [ImageServiceController::class, 'showimage']);
Route::get('imagesbyserviceid/{serviceid}', [ImageServiceController::class, 'imagesbyserviceid']);
Route::post('ImageServiceUpdate', [ImageServiceController::class, 'ImageServiceUpdate']);
Route::post('DeleteImageService', [ImageServiceController::class, 'DeleteImageService']);



Route::middleware('auth:sanctum')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('user/uploadidimages', [UserController::class, 'uploadidimages']);
    Route::post('user/updateprofile', [UserController::class, 'updateprofile']);
    Route::post("SendMessage", [ChatController::class, "SendMessage"]);
    Route::get("LoadThePreviousMessages", [MessageController::class, "LoadThePreviousMessages"]);
    Route::get("LoadThePreviousMessagesWithChatId", [MessageController::class, "LoadThePreviousMessagesWithChatId"]);
    Route::get("LoadThePreviousMessagesWhenScroll", [MessageController::class, "LoadThePreviousMessagesWhenScroll"]);
    Route::get("getChatsByLoginUser", [ChatController::class, "getChatsByLoginUser"]);
    Route::get("ServicesForLoginUser", [ServiceController::class, "ServicesForLoginUser"]);
    Route::post("deletefavorite", [FavoriteController::class, "deletefavorite"]);
    Route::get("user/getUserProfile", [UserController::class, 'getUserProfile']);
    Route::post("deleteAllSearch",[SearchController::class,'deleteAllSearch']);
    Route::post("deleteOneSearch",[SearchController::class,'deleteOneSearch']);

    Route::apiResource('service', ServiceController::class)->only(
        [

            'store',
            'update',
            'destroy'
        ]
    );


    Route::apiResource('search', SearchController::class)->only(
        [
            'index',
            'store',
            'update',
            'destroy'
        ]
    );






    Route::apiResource('comment', CommentController::class)->only(
        [
            'index',
            'show',
            'store',
            'update',
            'destroy'
        ]
    );
    Route::apiResource('favorite', FavoriteController::class)->only(
        [
            'index',
            'store',
            'destroy'
        ]
    );
});


Route::apiResource('user', UserController::class)->only(
    [
        'index',
        'show',
        'store',
        'update',
        'destroy'
    ]
);
