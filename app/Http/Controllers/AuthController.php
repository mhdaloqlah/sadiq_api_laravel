<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Notifications\ForgetPassword;
use App\Notifications\EmailVarificationNotification;
use App\Notifications\ResetPasswordNotification;
use Ichtrojan\Otp\Otp;
use Illuminate\Auth\Notifications\ResetPassword;

class AuthController extends Controller
{

    private $otp;

    public function __construct()
    {
        $this->otp = new Otp;
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',

        ]);

        if (!Auth::attempt($validated)) {
            return response()->json([
                'message' => 'login information invalid',

            ], 401);
        }

        $user = User::where('email', $validated['email'])->first();
        $user->notify(new ForgetPassword());
        return response()->json([
            'access_token' => $user->createToken('api_token')->plainTextToken,
            'token_type' => 'Bearer',
            'data' => $user,
            'message' => 'login successfully'
        ], 200);
    }

    public function register(Request $request)
    {


        $validateUser = Validator::make(
            $request->all(),
            [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'required|max:255|email|unique:users,email',
                'password' => 'required|confirmed|min:6',
                'birth_date'=>'required|date'
            ]
        );

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateUser->errors()
            ], 401);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'birth_date'=>$request->birth_date

        ]);


        $success['token'] = $user->createToken('api_token')->plainTextToken;
        $success['token_type'] = 'Bearer';
        $success['userdata'] = $user;
        $success['success'] = true;

       // $user->notify(new EmailVarificationNotification);
        return response()->json($success, 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'successfully logged out'
        ]);
    }

    public function emailverification(Request $request)
    {


        $validatedData = Validator::make($request->all(), [
           'email'=>['required','email','exists:users'],
            'otp'=>['required','max:4']          

        ]);

        if ($validatedData->fails()) {
            return response()->json(['success' => false, 'message' => $validatedData->errors()], 400);
        }
        $otp2 = $this->otp->validate($request->email, $request->otp);

        if (!$otp2->status) {
            return response()->json([
                'error' => $otp2
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

        $user->email_verified_at=now();
        $user->update();


        $success['userdata'] = $user;
        $success['success'] = true;
        return response()->json($success, 200);
    }

    public function sendemailverifiction(Request $request)  {

        $validatedData = Validator::make($request->all(), [
            'email'=>['required','email','exists:users'],         
 
         ]);
 
         if ($validatedData->fails()) {
             return response()->json(['success' => false, 'message' => $validatedData->errors()], 400);
         }
        $user = User::where('email', $request->email)->first();

        $user->notify(new EmailVarificationNotification);
        $success['success'] = $user;
        return response()->json($success, 200);
    }

    public function forgetpassword(Request $request)  {

        $validatedData = Validator::make($request->all(), [
            'email'=>['required','email','exists:users'],         
         ]);
 
         if ($validatedData->fails()) {
             return response()->json(['success' => false, 'message' => $validatedData->errors()], 400);
         }
        $user = User::where('email', $request->email)->first();

        $user->notify(new ResetPasswordNotification());
        $success['success'] = $user;
        return response()->json($success, 200);
    }

    public function passwordreset(Request $request)  {
        $validatedData = Validator::make($request->all(), [
            'email'=>['required','email','exists:users'],
             'otp'=>['required','max:4'],
             
          
 
         ]);  
         
         if ($validatedData->fails()) {
            return response()->json(['success' => false, 'message' => $validatedData->errors()], 400);
        }
        $otp2 = $this->otp->validate($request->email, $request->otp);

        if (!$otp2->status) {
            return response()->json([
                'error' => $otp2
            ], 401);
        }else{
            return response()->json([
                'success' => true,
                'message'=>'otp check correct'
            ], 200);
        }

        
    }

    
    public function changepassword(Request $request)  {
        $validatedData = Validator::make($request->all(), [
            'email'=>['required','email','exists:users'],
            'password'=>['required']        
         ]);
 
         if ($validatedData->fails()) {
             return response()->json(['success' => false, 'message' => $validatedData->errors()], 400);
         }
        $user = User::where('email', $request->email)->first();

        $user->password=Hash::make($request->password);
        $user->update();
        $user->tokens()->delete();


        $success['userdata'] = $user;
        $success['success'] = true;
        return response()->json($success, 200);
    }
}
