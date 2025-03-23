<?php

namespace App\Http\Controllers;

use App\Models\ImageService;
use App\Http\Requests\StoreImageServiceRequest;
use App\Http\Requests\UpdateImageServiceRequest;
use App\Http\Resources\ImageServiceResource;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ImageServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $services = QueryBuilder::for(ImageService::class)
                ->get();
            $success['data'] = ImageServiceResource::collection($services);
            $success['success'] = true;
            return response()->json($success, 200);
        } catch (\Throwable $th) {
            $success['error'] = $th->getMessage();
            $success['success'] = false;
            return response()->json($success, 500);
        }
    }


    public function imagesbyserviceid(string $serviceid)
    {

      
        try {
            $services = QueryBuilder::for(ImageService::class)
                ->where('service_id', $serviceid)
                ->get();
            $success['data'] = ImageServiceResource::collection($services);
            $success['count of images'] = count($services);
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
    public function store(StoreImageServiceRequest $request)
    {
        try {
            $validated = $request->validated();
            $image = null;
            $imagesUrls=[];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    
                    $name = Str::random(32) . "." . $image->getClientOriginalExtension();
                Storage::disk('public')->put($name, file_get_contents($image));
                $validated['image'] = $name;
                $imageUrls[]= $name;
                $service = ImageService::create($validated);
                }
                
            }
            // $service = ImageService::create($validated);
            $success['data'] = 'images added succefully';
            $success['success'] = true;
            return response()->json($success, 200);
        } catch (\Throwable $th) {
            $success['error'] = $th->getMessage();
            $success['success'] = false;
            return response()->json($success, 500);
        }
    }


    public function createserviceimage(StoreImageServiceRequest $request)
    {
        try {
            $validated = $request->validated();
            $image = null;
           
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    
                    $name = Str::random(32) . "." . $image->getClientOriginalExtension();
                Storage::disk('public')->put($name, file_get_contents($image));
                $validated['image'] = $name;
                $imageUrls[]= $name;
                $service = ImageService::create($validated);
                }
                
            }
            // $service = ImageService::create($validated);
            $success['data'] = 'images added succefully';
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
    public function showimage(string $id)
    {
        try {
            $services = QueryBuilder::for(ImageService::class)
                ->where('id', $id)
                ->first();

            if ($services) {
                $success['data'] = new ImageServiceResource($services);

                $success['success'] = true;
                return response()->json($success, 200);
            } else {
                $success['success'] = true;
                $success['data'] = 'image not found';
                return response()->json($success, 404);
            }
        } catch (\Throwable $th) {
            $success['error'] = $th->getMessage();
            $success['success'] = false;
            return response()->json($success, 500);
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function ImageServiceUpdate(UpdateImageServiceRequest $request, ImageService $imageService)
    {
        try {
            $imageService = QueryBuilder::for(ImageService::class)
            ->where('id', $request->id)
            ->first();

            $validated = $request->validated();
            $service_image = $imageService->image;
            if ($request->image != null) {
                // Public storage
                $storage = Storage::disk('public');
                // Old iamge delete
                if (!$imageService->image == null) {
                    if ($storage->exists($imageService->image))
                        $storage->delete($imageService->image);
                }
                $service_image = Str::random(32) . "." . $request->image->getClientOriginalExtension();
                Storage::disk('public')->put($service_image, file_get_contents($request->image));
                $validated['image'] = $service_image;
            }
            $imageService->update($validated);
            $success['data'] = new ImageServiceResource($imageService);
            $success['message'] = 'Image Updated successfully';
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
    public function DeleteImageService(Request $request)
    {
        try {

            $services = QueryBuilder::for(ImageService::class)
                ->where('id', $request->id)
                ->first();

            if($services){
                $storage = Storage::disk('public');
                // Old iamge delete
                if (!$services->image == null) {
                    if ($storage->exists($services->image))
                        $storage->delete($services->image);
                }
                $services->delete();
                $success['message'] = 'image Deleted successfully';
                $success['success'] = true;
                return response()->json($success, 200);
            }else{
                $success['success'] = true;
                $success['data'] = 'image not found';
                return response()->json($success, 404);
            }
            
        } catch (\Throwable $th) {
            $success['error'] = $th->getMessage();
            $success['success'] = false;
            return response()->json($success, 500);
        }
    }
}
