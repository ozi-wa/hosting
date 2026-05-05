<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        return view('client.orders.index', ['orders' => $request->user()->orders()->with('product')->latest()->paginate(15)]);
    }

    public function create(): View
    {
        return view('client.orders.create', ['products' => Product::where('is_active', true)->orderBy('sort_order')->get()]);
    }

    public function store(StoreOrderRequest $request, OrderService $orders): RedirectResponse
    {
        $product = Product::where('is_active', true)->findOrFail($request->integer('product_id'));
        $order = $orders->place($request->user(), $product, $request->string('billing_cycle')->toString(), $request->only(['domain', 'notes']));

        return redirect()->route('client.invoices.show', $order->invoice)->with('status', 'Sipariş oluşturuldu. Fatura hazır.');
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        return view('client.orders.show', ['order' => $order->load(['product', 'invoice', 'service'])]);
    }
}
