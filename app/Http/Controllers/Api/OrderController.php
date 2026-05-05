<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['data' => $request->user()->orders()->with(['product', 'invoice'])->latest()->get()]);
    }

    public function store(StoreOrderRequest $request, OrderService $orders): JsonResponse
    {
        $product = Product::where('is_active', true)->findOrFail($request->integer('product_id'));
        $order = $orders->place($request->user(), $product, $request->string('billing_cycle')->toString(), $request->only(['domain', 'notes']));

        return response()->json(['data' => $order], 201);
    }
}
