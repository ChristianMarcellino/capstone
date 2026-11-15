<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderUpdateRequest;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'data' => Order::with('orderItems.product')->get(),
            'message' => 'Orders retrieved successfully',
        ]);
    }

    private function syncOrderItems(Order $order, array $items)
    {
        $order->orderItems()->delete();

        foreach ($items as $item) {
            $product = Product::findOrFail($item['product_id']);

            $order->orderItems()->create([
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
    public function store(OrderStoreRequest $request)
    {
        try {
            $order = DB::transaction(function () use ($request) {
                $data = $request->validated();
                $items = $data['order_items'];
                unset($data['order_items']);

                $order = Order::create($data);
                $this->syncOrderItems($order, $items);

                return $order->load('orderItems.product');
            });

            return response()->json([
                'data' => $order,
                'message' => 'Order created successfully',
            ], 201);

        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Failed to create order.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderUpdateRequest $request, Order $order)
    {
        try {
            $updatedOrder = DB::transaction(function () use ($request, $order) {
                $data = $request->validated();
                $items = $data['order_items'];
                unset($data['order_items']);

                $order->update($data);
                $this->syncOrderItems($order, $items);

                return $order->refresh()->load('orderItems.product');
            });

        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Failed to update order.',
            ], 500);
        }

        return response()->json([
            'data' => $updatedOrder,
            'message' => 'Order updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        try {
            DB::transaction(function () use ($order) {
                $order->delete();
            });

        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Failed to delete order.',
            ], 500);
        }

        return response()->json([
            'message' => 'Order deleted successfully',
        ], 200);
    }
}
