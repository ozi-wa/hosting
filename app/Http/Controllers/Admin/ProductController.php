<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('admin.products.index', ['products' => Product::with('category')->orderBy('sort_order')->paginate(20)]);
    }

    public function create(): View
    {
        return view('admin.products.form', ['product' => new Product, 'categories' => Category::orderBy('name')->get()]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        Product::create($this->payload($request->validated()));

        return redirect()->route('admin.products.index')->with('status', 'Ürün oluşturuldu.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.form', ['product' => $product, 'categories' => Category::orderBy('name')->get()]);
    }

    public function update(StoreProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($this->payload($request->validated()));

        return redirect()->route('admin.products.index')->with('status', 'Ürün güncellendi.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->update(['is_active' => false]);

        return back()->with('status', 'Ürün pasife alındı.');
    }

    private function payload(array $data): array
    {
        $data['features'] = array_values(array_filter(array_map('trim', explode("\n", $data['features'] ?? ''))));
        $data['specifications'] = array_filter(array_map('trim', explode("\n", $data['specifications'] ?? '')));
        $data['is_featured'] = (bool) ($data['is_featured'] ?? false);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['whmcs_product_id'] = $data['whmcs_product_id'] ?? null;
        $data['whmcs_gid'] = $data['whmcs_gid'] ?? null;

        return $data;
    }
}
