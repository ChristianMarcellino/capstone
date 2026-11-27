<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Support\Facades\DB;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'data' => Product::with('category')->get(),
            'message' => 'Products retrieved successfully',
        ]);
    }

    private function deleteCloudinaryImage(?string $publicId)
    {
        if (!$publicId) {
            return;
        }

        Cloudinary::destroy($publicId, [
            'invalidate' => true
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request)
    {
        $image = null;

        try {
            $data = $request->validated();
            unset($data['image']);
            if ($request->hasFile('image')) {
                $image = $request->file('image')
                    ->storeOnCloudinary('products');

                $data['image_url'] = $image->getSecurePath();
                $data['image_id'] = $image->getPublicId();
            }

            $product = DB::transaction(function () use ($data) {
                return Product::create($data);
            });

            return response()->json([
                'data' => $product,
                'message' => 'Product created successfully',
            ], 201);

        } catch (\Throwable $e) {
            report($e);

            if ($image) {
                try{
                    $this->deleteCloudinaryImage(
                        $image->getPublicId()
                    );
                } catch (\Throwable $cleanupError) {
                    report($cleanupError);
                }
            }

            return response()->json([
                'message' => 'Failed to create product.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        $image = null;
        $oldImageId = $product->image_id;
        try{
            $newProduct = DB::transaction(function() use ($request, &$image, $product){
                $data = $request->validated();
                unset($data['image']);
                if($request->hasFile('image')){
                    $image = $request->file('image')->storeOnCloudinary('products');
                    $url = $image->getSecurePath();
                    $imageId = $image->getPublicId();
                    $data['image_url'] = $url;
                    $data['image_id'] = $imageId;
                }
                $product->update($data);
                return $product->refresh();
            });
        }catch (\Throwable $e) {
            report($e);

            if ($image) {
                try {
                    $this->deleteCloudinaryImage($image->getPublicId());
                } catch (\Throwable $cleanupError) {
                    report($cleanupError);
                }
            }

            return response()->json([
                'message' => 'Failed to update product.',
            ], 500);
        }

        if($image && $oldImageId){
            $this->deleteCloudinaryImage($oldImageId);
        }
        
        return response()->json([
            'data' => $newProduct,
            'message' => 'Product updated successfully',
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $oldImageId = $product->image_id;

        try {
            DB::transaction(function () use ($product) {
                $product->delete();
            });

        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Failed to delete product.',
            ], 500);
        }

        try {
            $this->deleteCloudinaryImage($oldImageId);
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'message' => 'Product deleted successfully',
        ], 200);
    }
}
