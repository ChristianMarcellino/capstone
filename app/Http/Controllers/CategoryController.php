<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'data' => Category::all(),
            'message' => 'Categories retrieved successfully',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryStoreRequest $request)
    {
        try {
            $category = DB::transaction(function () use ($request) {
                return Category::create($request->validated());
            });

            return response()->json([
                'data' => $category,
                'message' => 'Category created successfully',
            ], 201);

        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Failed to create category.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryUpdateRequest $request, Category $category)
    {
        try {
            $updatedCategory = DB::transaction(function () use ($request, $category) {
                $category->update($request->validated());
                return $category->refresh();
            });

        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Failed to update category.',
            ], 500);
        }

        return response()->json([
            'data' => $updatedCategory,
            'message' => 'Category updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            DB::transaction(function () use ($category) {
                $category->delete();
            });

        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Failed to delete category.',
            ], 500);
        }

        return response()->json([
            'message' => 'Category deleted successfully',
        ], 200);
    }
}
