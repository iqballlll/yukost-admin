<?php

namespace App\Http\Controllers;

use App\Helpers\MessageResponse;
use App\Helpers\ValidationRules;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $productQuery = Product::query();
        $perPage = $request->per_page ?? 5;

        if ($request->filled('product_id')) {
            $productQuery->where('product_id', 'like', '%' . $request->product_id . '%');
        }

        if ($request->filled('product_name')) {
            $productQuery->where('product_name', 'like', '%' . $request->product_name . '%');
        }

        if ($request->filled('quantity')) {
            $productQuery->where('stock', 'like', '%' . $request->quantity . '%');
        }

        if ($request->filled('base_price')) {
            $productQuery->where('base_price', 'like', '%' . $request->base_price . '%');
        }

        if ($request->filled('selling_price')) {
            $productQuery->where('selling_price', 'like', '%' . $request->selling_price . '%');
        }
        if ($request->filled('min_stock')) {
            $productQuery->where('min_stock', 'like', '%' . $request->min_stock . '%');
        }

        $sortField = $request->get('sort');
        $sortOrder = $request->get('order', 'desc');

        $allowedSorts = ['product_id', 'product_name', 'stock', 'base_price', 'selling_price', 'min_stock'];
        if (in_array($sortField, $allowedSorts)) {
            $productQuery->orderBy($sortField, $sortOrder);
        } else {
            $productQuery->orderBy('created_at', 'desc');
        }

        $data = [
            'title' => 'Produk',
            'products' => $productQuery->paginate($perPage)->appends($request->except('page')),
        ];
        return view('pages.product.index', $data);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $products = Product::when($query, fn($q) => $q->where('product_name', 'like', "%{$query}%"))
            ->paginate();

        return view('components.modal-products-sales-pagination', compact('products'))->render();
    }

    public function store(Request $request)
    {
        try {

            $rules = ValidationRules::get('products');
            $messages = ValidationRules::messages('products');

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return jsonResponse(ucfirst($validator->errors()->first()), $request->all(), '', 400);
            }
            $data = $request->only(array_keys($rules));

            //INI NANTI GANTI SAMA AUTH YAAAA

            // $data['created_by'] = 1;

            // /////////////////

            $product = Product::create($data);

            return jsonResponse(ucfirst(MessageResponse::ADD_SUCCESS), $product);
        } catch (\Throwable $th) {
            return jsonResponse(ucfirst(MessageResponse::FAILED_PROCESS), $request->all(), $th->getMessage(), 500);
        }
    }
    public function update($id, Request $request)
    {
        try {
            $rules = ValidationRules::get('products', 'update', $id);
            $messages = ValidationRules::messages('products');

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return jsonResponse(ucfirst($validator->errors()->first()), $request->all(), '', 400);
            }

            $product = Product::findOrFail($id);
            $product->update($request->only(array_keys($rules)));

            return jsonResponse(ucfirst(MessageResponse::UPDATE_SUCCESS), $request->all());
        } catch (\Throwable $th) {
            return jsonResponse(ucfirst(MessageResponse::FAILED_PROCESS), $request->all(), $th->getMessage(), 500);
        }
    }
    public function show($id)
    {
        try {
            $d = Product::find($id);
            return jsonResponse(ucfirst(MessageResponse::SUCCESS), $d);
        } catch (\Throwable $th) {
            return jsonResponse(ucfirst(MessageResponse::FAILED_PROCESS), $d, $th->getMessage(), 500);
        }
    }
    public function delete($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'code' => 404,
                'message' => MessageResponse::NOT_FOUND
            ], 404);
        }

        try {

            if ($product->transactionDetails()->exists() || $product->purchaseDetails()->exists()) {
                return back()->with('error', 'Produk tidak bisa dihapus karena sudah digunakan dalam proses lain');
            }

            $product->delete();

            return response()->json([
                'code' => 200,
                'message' => 'Produk ' . MessageResponse::DELETE_SUCCESS
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => MessageResponse::FAILED_DELETE,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
