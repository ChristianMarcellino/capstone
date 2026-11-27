<?php

namespace App\Http\Controllers;

use App\Models\WhatsappDraft;
use App\Models\Product;
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

    private function syncWhatsappDraftItems(WhatsappDraft $draft, array $items)
    {
        $draft->whatsappDraftItems()->delete();

        foreach ($items as $item) {
            $product = Product::findOrFail($item['product_id']);

            $draft->whatsappDraftItems()->create([
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'item_price' => $product->price,
                'item_name' => $product->name,
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WhatsappDraftStoreRequest $request)
    {
        try {
            $draft = DB::transaction(function () use ($request) {
                $data = $request->validated();
                $items = $data['whatsapp_draft_items'];
                unset($data['whatsapp_draft_items']);

                $draft = WhatsappDraft::create($data);
                $this->syncWhatsappDraftItems($draft, $items);

                return $draft->refresh()->load('whatsappDraftItems.product');
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
                $data = $request->validated();
                $items = $data['whatsapp_draft_items'];
                unset($data['whatsapp_draft_items']);

                $whatsappDraft->update($data);
                $this->syncWhatsappDraftItems($whatsappDraft, $items);

                return $whatsappDraft->refresh()->load('whatsappDraftItems.product');
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
