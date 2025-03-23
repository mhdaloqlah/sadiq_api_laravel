<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Favorite;
use App\Models\Search;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\AllowedFilter;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        try {
            $services = QueryBuilder::for(Service::class)
                ->allowedFilters(['name', 'category_id', AllowedFilter::exact('service_date'), AllowedFilter::exact('location'), AllowedFilter::scope('price_between'), AllowedFilter::scope('time_between')])
                ->allowedIncludes(['category'])
                ->orderBy('created_at', 'DESC')
                ->get();



            $success['data'] = ServiceResource::collection($services);



            $success['success'] = true;
            return response()->json($success, 200);
        } catch (\Throwable $th) {
            $success['error'] = $th->getMessage();
            $success['success'] = false;
            return response()->json($success, 500);
        }
    }
    public function ServicesForLoginUser(Request $request)
    {
        try {
            $services = QueryBuilder::for(Service::class)
                ->allowedFilters(['name', 'category_id', AllowedFilter::exact('service_date'), AllowedFilter::exact('location'), AllowedFilter::scope('price_between'), AllowedFilter::scope('time_between')])
                ->allowedIncludes(['category'])
                ->orderBy('created_at', 'DESC')
                ->get();

            foreach ($services as $service) {
                $favorite = QueryBuilder::for(Favorite::class)->where('service_id', $service->id)
                    ->where('user_id', auth("sanctum")->user()->id)->first();
                $service->favorite = false;
                if ($favorite) {
                    $service->favorite = true;
                }
            }
            if ($request->has('filter.name')) {
                Search::create(
                    [
                        'search_text' => $request->filter['name'],
                        'user_id' => auth("sanctum")->user()->id
                    ]
                );
            }



            $success['data'] = ServiceResource::collection($services);
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
    public function store(StoreServiceRequest $request)
    {
        try {
            $validated = $request->validated();
            $video = null;
            if ($request->video != null) {
                $video = Str::random(32) . "." . $request->video->getClientOriginalExtension();
                Storage::disk('public')->put($video, file_get_contents($request->video));
                $validated['video'] = $video;
            }
            $service = Service::create($validated);
            $success['data'] = $service;
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
    public function show(Service $service)
    {
        try {
            $success['data'] = new ServiceResource($service);
            $success['success'] = true;
            return response()->json($success, 200);
        } catch (\Throwable $th) {
            $success['error'] = $th->getMessage();
            $success['success'] = false;
            return response()->json($success, 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        try {
            $validated = $request->validated();
            $service_video = $service->video;
            if ($request->video != null) {
                // Public storage
                $storage = Storage::disk('public');
                // Old iamge delete
                if (!$service->video == null) {
                    if ($storage->exists($service->video))
                        $storage->delete($service->video);
                }
                $service_video = Str::random(32) . "." . $request->video->getClientOriginalExtension();
                Storage::disk('public')->put($service_video, file_get_contents($request->video));
                $validated['video'] = $service_video;
            }
            $service->update($validated);
            $success['data'] = new ServiceResource($service);
            $success['message'] = 'Service Updated successfully';
            $success['success'] = true;
            return response()->json($success, 200);
        } catch (\Throwable $th) {
            $success['error'] = $th->getMessage();
            $success['success'] = false;
            return response()->json($success, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        try {
            $storage = Storage::disk('public');
            // Old iamge delete
            if (!$service->video == null) {
                if ($storage->exists($service->video))
                    $storage->delete($service->video);
            }
            $service->delete();
            $success['message'] = 'service Deleted successfully';
            $success['success'] = true;
            return response()->json($success, 200);
        } catch (\Throwable $th) {
            $success['error'] = $th->getMessage();
            $success['success'] = false;
            return response()->json($success, 500);
        }
    }
}
