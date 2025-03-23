<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = QueryBuilder::for(Category::class)
            ->allowedIncludes(['services'])
            ->get();
            $success['data'] = CategoryResource::collection($categories);
            $success['success'] = true;
            return response()->json($success, 200);

        } catch (\Throwable $th) {
            $success['error'] = $th->getMessage();
            $success['success'] = false;
            return response()->json($success, 500);        }
    }

    

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            $validated = $request->validated();
            $image = null;
            if ($request->image != null) {
                $image = Str::random(32) . "." . $request->image->getClientOriginalExtension();
                Storage::disk('public')->put($image, file_get_contents($request->image));
                $validated['image']=$image;
            }
            $category = Category::create($validated);
            $success['data'] = $category ;
            $success['success'] = true;
            return response()->json($success, 200);
        } catch (\Throwable $th) {
            $success['error'] = $th->getMessage();
            $success['success'] = false;
            return response()->json($success, 500);           }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        try {
            $success['data'] = new CategoryResource($category);
            $success['success'] = true;
            return response()->json($success, 200);
        } catch (\Throwable $th) {
            $success['error'] = $th->getMessage();
            $success['success'] = false;
            return response()->json($success, 500);
        }    }

    

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $validated = $request->validated();
            $employee_image = $category->image;
            if ($request->image != null) {
                 // Public storage
                 $storage = Storage::disk('public');
                 // Old iamge delete
                 if (!$category->image == null) {
                     if ($storage->exists($category->image))
                         $storage->delete($category->image);
                 }
                $category_image = Str::random(32) . "." . $request->image->getClientOriginalExtension();
                Storage::disk('public')->put($category_image, file_get_contents($request->image));
                $validated['image']=$category_image;
            }
            $category->update($validated);
            $success['data'] = new CategoryResource($category);
            $success['message'] = 'Category Updated successfully';
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
    public function destroy(Category $category)
    {
        try {
            $storage = Storage::disk('public');
            // Old iamge delete
            if (!$category->image == null) {
                if ($storage->exists($category->image))
                    $storage->delete($category->image);
            }
            $category->delete();
            $success['message'] = 'Category Deleted successfully';
            $success['success'] = true;
            return response()->json($success, 200);
        } catch (\Throwable $th) {
            $success['error'] = $th->getMessage();
            $success['success'] = false;
            return response()->json($success, 500);
        }
    }
}
