<?php

namespace App\Http\Controllers;

use App\Models\WhatsappDraft;
use Illuminate\Http\Request;
use App\Http\Requests\WhatsappDraftStoreRequest;
use App\Http\Requests\WhatsappDraftUpdateRequest;
use Illuminate\Support\Facades\DB;

class WhatsappDraftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'data' => WhatsappDraft::with('whatsappDraftItems.product')->get(),
            'message' => 'Whatsapp drafts retrieved successfully',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WhatsappDraftStoreRequest $request)
    {
        try {
            $draft = DB::transaction(function () use ($request) {
                return WhatsappDraft::create($request->validated());
            });

            return response()->json([
                'data' => $draft,
                'message' => 'Whatsapp draft created successfully',
            ], 201);

        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Failed to create whatsapp draft.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(WhatsappDraft $whatsappDraft)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WhatsappDraft $whatsappDraft)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WhatsappDraftUpdateRequest $request, WhatsappDraft $whatsappDraft)
    {
        try {
            $updatedDraft = DB::transaction(function () use ($request, $whatsappDraft) {
                $whatsappDraft->update($request->validated());
                return $whatsappDraft->refresh();
            });

        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Failed to update whatsapp draft.',
            ], 500);
        }

        return response()->json([
            'data' => $updatedDraft,
            'message' => 'Whatsapp draft updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WhatsappDraft $whatsappDraft)
    {
        try {
            DB::transaction(function () use ($whatsappDraft) {
                $whatsappDraft->delete();
            });

        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Failed to delete whatsapp draft.',
            ], 500);
        }

        return response()->json([
            'message' => 'Whatsapp draft deleted successfully',
        ], 200);
    }
}
