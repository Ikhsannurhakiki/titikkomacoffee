<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_available' => 'boolean',
        ]);

        $product = Product::create($request->only('name', 'price', 'description', 'category_id', 'is_available'));

        if ($request->hasFile('thumbnail')) {
            $product->addMediaFromRequest('thumbnail')->toMediaCollection('thumbnail');
        }

        return response()->json(['message' => 'Produk berhasil ditambahkan', 'product' => $product], 201);
    }
}
