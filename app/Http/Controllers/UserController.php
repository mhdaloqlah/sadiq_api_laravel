<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    public function index()
    {
        $users = QueryBuilder::for(User::class)
            ->get();
        return new UserCollection($users);
    }

    public function show(User $user)
    {

        return new UserResource($user);
    }

    public function getUserProfile()
    {
        $user = auth("sanctum")->user();

        return new UserResource($user);
    }

    public function updateprofile(Request $request)
    {

        $validatedData = Validator::make($request->all(), [
            'id'=>'required',
            // 'email' => 'required|max:255|email',
            'first_name' => 'required|max:100|string',
            'last_name' => 'required|max:100|string',
            'birth_date' => 'sometimes|date',
            'about' => 'sometimes',
            'longitude' => 'sometimes',
            'latitude' => 'sometimes',
            'image_profile' => 'sometimes|image|mimes:jpg,png,jpeg,gif,svg|max:2048',

            

        ]);

        if ($validatedData->fails()) {
            return response()->json(['success' => false, 'message' => $validatedData->errors()], 400);
        }

        $id = $request->id;
        $user = User::find($id);

        // $checkmail = QueryBuilder::for(User::class)
        // ->where('id','<>',$id)
        // ->where('email',$request->email)->get();
        
        // if(count($checkmail)>0){
        //     return response()->json(['success' => false, 'message' => 'Email is used before'], 400);

        // }

        if ($user) {
            // $user->email=$request->email;
            $user->first_name=$request->first_name;
            $user->last_name=$request->last_name;
            $user->phone=$request->phone;
            $user->birth_date=$request->birth_date;
            $user->about=$request->about;
            $user->longitude=$request->longitude;
            $user->latitude=$request->latitude;

            if($request->image_profile && $request->image_profile->isValid()){
                $storage = Storage::disk('public');
                if (!$user->image_profile == null) {
                    if ($storage->exists($user->image_profile)) {
                        $image_path = $user->image_profile;
                        if ($storage->exists($image_path)) {
                            $storage->delete($image_path);
                        }
                    }
                }
                $image_profile = $request->file('image_profile')->store('images/user', 'public');
                $user->image_profile= $image_profile;
    
    
            }


            $user->update();

            return response()->json([
                'data'=>$user,
                'message'=>'User profile update succssefully'
            ],200);



        }else{
            return response()->json([
                'message' => 'user not found',
            ]);
        }


    }

    public function uploadidimages(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'id'=>'required',
            'image_id_front' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'image_id_back' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);



        if ($validatedData->fails()) {
            return response()->json(['success' => false, 'message' => $validatedData->errors()], 400);
        }
        $id = $request->id;
        $user = User::find($id);
        if ($user) {

           

            if ($request->image_id_front && $request->image_id_front->isValid()) {

                $storage = Storage::disk('public');
                if (!$user->image_id_front == null) {
                    if ($storage->exists($user->image_id_front)) {
                        $image_path = $user->image_id_front;
                        if ($storage->exists($image_path)) {
                            $storage->delete($image_path);
                        }
                    }
                }
                $imageLink = $request->file('image_id_front')->store('images/IDs', 'public');
                $user->image_id_front = $imageLink;
            }

            if ($request->image_id_back && $request->image_id_back->isValid()) {

                $storage = Storage::disk('public');
                if (!$user->image_id_back == null) {
                    if ($storage->exists($user->image_id_back)) {
                        $image_path = $user->image_id_back;
                        if ($storage->exists($image_path)) {
                            $storage->delete($image_path);
                        }
                    }
                }
                $imageLink = $request->file('image_id_back')->store('images/IDs', 'public');
                $user->image_id_back = $imageLink;
            }

            $user->update();
            return response()->json([
                'data' => $user,
                'Message' => 'IDs Images upload sucsessfully'
            ],200);
        } else {
            return response()->json([
                'message' => 'not found',
            ]);
        }
    }

    public function destroy(User $user)
    {

        $user->delete();
        return response()->json([
            'message' => 'User Has been Deleted'
        ]);
    }

    public function changeStatus($id)
    {
        $user = QueryBuilder::for(User::class)
            ->where('id', $id)
            ->get();
        $user->status = true;
        $user->update();
        return response()->json([
            'message' => 'User Has been Updated succeffully'
        ]);
    }
}
