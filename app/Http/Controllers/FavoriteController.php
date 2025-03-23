<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Http\Requests\StoreFavoriteRequest;
use App\Http\Requests\UpdateFavoriteRequest;
use App\Http\Resources\FavoriteCollection;
use App\Models\Service;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $userFavorites = QueryBuilder::for(Favorite::class)
                ->where('user_id', auth("sanctum")->user()->id)->get();
            $success['data'] = new FavoriteCollection($userFavorites);
            $success['success'] = true;
            return response()->json($success, 200);
        } catch (\Throwable $th) {
            $success['error'] = $th->getMessage();
            $success['success'] = false;
            return response()->json($success, 500);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFavoriteRequest $request)
    {
        try {

            $validated = $request->validated();

            $service = QueryBuilder::for(Service::class)
                ->where('user_id', auth("sanctum")->user()->id)
                ->where('id', $validated['service_id'])
                ->first();

            if ($service) {
                $success['message'] = 'This service is created by this user you can not favorite';
                $success['success'] = true;
                return response()->json($success, 200);
            }

            $userFavorite = QueryBuilder::for(Favorite::class)
                ->where('user_id', auth("sanctum")->user()->id)
                ->where('service_id', $validated['service_id'])
                ->first();

            if ($userFavorite) {
                $success['message'] = 'This service is preferred by the user';
                $success['success'] = true;
                return response()->json($success, 200);
            }

            $favorite = Favorite::create([
                'service_id' => $validated['service_id'],
                'user_id' =>  auth("sanctum")->user()->id
            ]);

            $success['data'] = $favorite;
            $success['success'] = true;
            return response()->json($success, 200);
        } catch (\Throwable $th) {
            $success['error'] = $th->getMessage();
            $success['success'] = false;
            return response()->json($success, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Favorite $favorite)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Favorite $favorite)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFavoriteRequest $request, Favorite $favorite)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Favorite $favorite)
    {
        try {


            // $userFavorite = QueryBuilder::for(Favorite::class)
            //     ->where('user_id', auth("sanctum")->user()->id)
            //     ->where('service_id', $favorite->service_id)
            //     ->first();

            // // if (!$userFavorite) {
            // //     $success['message'] = 'This service is is deleted from user favorites list';
            // //     $success['success'] = true;
            // //     return response()->json($success, 200);
            // // }

            $favorite->delete();

            $success['message'] = 'favorite deleted successfully';
            $success['success'] = true;
            return response()->json($success, 200);
        } catch (\Throwable $th) {
            $success['error'] = $th->getMessage();
            $success['success'] = false;
            return response()->json($success, 500);
        }
    }

    public function deletefavorite(Request $request)
    {
        $fav = QueryBuilder::for(Favorite::class)
            ->where('user_id', auth("sanctum")->user()->id)
            ->where('service_id', $request['service_id'])
            ->first();
        if (!$fav) {
            $success['message'] = 'This service is not preferred by the user';
            $success['success'] = true;
            return response()->json($success, 404);
        }

        $fav->delete();

        $success['message'] = 'Favorite Deleted Successfully';
        $success['success'] = true;
        return response()->json($success, 200);
    }
}
