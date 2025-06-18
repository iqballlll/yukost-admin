<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelpers;
use App\Helpers\MessageResponse;
use App\Helpers\ValidationRules;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Str;

class PurchaseController extends Controller
{

    public function index(Request $request)
    {
        $filterSupplier = $request->supplier;
        $filterIsPay = $request->is_paid;
        $filterTotalOperator = $request->filterTotalOperator;
        $filterTotalValue = $request->filterTotalValue;
        $perPage = $request->per_page ?? 5;
        $filters = [
            'supplier' => $filterSupplier,
            'order_date' => $request->order_date,
            'due_date' => $request->due_date,
            'payment_date' => $request->payment_date,
            'is_paid' => $filterIsPay,
            'total_operator' => $filterTotalOperator,
            'total_value' => $filterTotalValue
        ];


        // Konversi dari "Y-m-d|Y-m-d" menjadi "d/m/Y - d/m/Y" agar bisa ditampilkan kembali
        foreach (['order_date', 'due_date', 'payment_date'] as $field) {
            if (!empty($filters[$field]) && str_contains($filters[$field], '|')) {
                [$start, $end] = explode('|', $filters[$field]);

                // Pastikan format awal adalah Y-m-d sebelum diubah
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $start) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $end)) {
                    $filters[$field] = Carbon::createFromFormat('Y-m-d', trim($start))->format('d/m/Y') . ' - ' .
                        Carbon::createFromFormat('Y-m-d', trim($end))->format('d/m/Y');
                }
            }
        }

        // Ambil semua supplier untuk dropdown
        $allSupplier = Supplier::select('id', 'supplier_name')->get();

        // Ambil hanya supplier yang punya purchases
        $suppliers = Supplier::whereHas('purchases', function ($q) use ($filterIsPay, $filters, $filterTotalOperator, $filterTotalValue) {
            if ($filterIsPay !== null) {
                $q->where('is_paid', $filterIsPay);
            }

            foreach (['order_date', 'due_date', 'payment_date'] as $field) {
                if (!empty($filters[$field]) && str_contains($filters[$field], ' - ')) {
                    [$start, $end] = explode(' - ', $filters[$field]);
                    $q->whereBetween($field, [
                        Carbon::createFromFormat('d/m/Y', trim($start))->format('Y-m-d'),
                        Carbon::createFromFormat('d/m/Y', trim($end))->format('Y-m-d')
                    ]);
                }
            }

            if ($filterTotalOperator && $filterTotalValue !== null && is_numeric($filterTotalValue)) {
                $q->where('total_price', $filterTotalOperator, $filterTotalValue);
            }
        })
            ->with([
                'purchases' => function ($q) use ($filterIsPay, $filters) {
                    if ($filterIsPay !== null) {
                        $q->where('is_paid', $filterIsPay);
                    }

                    foreach (['order_date', 'due_date', 'payment_date'] as $field) {
                        if (!empty($filters[$field]) && str_contains($filters[$field], ' - ')) {
                            [$start, $end] = explode(' - ', $filters[$field]);
                            $q->whereBetween($field, [
                                Carbon::createFromFormat('d/m/Y', trim($start))->format('Y-m-d'),
                                Carbon::createFromFormat('d/m/Y', trim($end))->format('Y-m-d')
                            ]);
                        }
                    }

                    $q->with('purchaseDetails');
                }
            ])
            ->when($filterSupplier, function ($query) use ($filterSupplier) {
                $query->where('id', $filterSupplier);
            })
            ->paginate($perPage);

        return view('pages.purchase.index', [
            'title' => 'Pembelian',
            'suppliers' => $suppliers,
            'allSupplier' => $allSupplier,
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $data = ['title' => 'Tambah Pembelian', 'suppliers' => $suppliers];
        return view("pages.purchase.create", $data);
    }

    public function edit($id)
    {
        $purchase = Purchase::with([
            'purchaseDetails.product'
        ])->where('id', $id)->first();
        // AppHelpers::debugData($purchase);
        $suppliers = Supplier::all();
        $data = ['title' => 'Ubah Pembelian', 'purchase' => $purchase, 'suppliers' => $suppliers];
        return view("pages.purchase.edit", $data);
    }

    public function getProduct(Request $request)
    {
        $query = $request->get('q');

        $products = Product::when($query, function ($q) use ($query) {
            $q->where('product_name', 'like', "%{$query}%");
        })
            ->paginate(10);

        return view('components.modal-products-sales-pagination', compact('products'))->render();
    }
    public function store(Request $request)
    {
        try {

            $rules = ValidationRules::get('purchases');
            $messages = ValidationRules::messages('purchases');

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->with(['error' => $validator->errors()->first(), 'request' => $request->products])->withInput();
            }

            DB::beginTransaction();

            do {
                $purchaseId = 'PRC' . time() . strtoupper(Str::random(3));
            } while (Purchase::where('purchase_id', $purchaseId)->exists());

            $purchase = Purchase::create([
                'purchase_id' => $purchaseId,
                'supplier_id' => $request->supplier_id,
                'order_date' => $request->order_date,
                'due_date' => $request->due_date,
                'payment_date' => $request->payment_date,
                'total_price' => $request->total_price,
                'is_paid' => $request->is_paid ? 1 : 0,
            ]);


            foreach ($request->products as $prod) {
                $productExist = Product::where('product_id', $prod['sku'])->exists();

                if ($productExist) {
                    DB::rollBack();
                    return redirect()->back()->with([
                        'error' => 'Produk dengan SKU ' . $prod['sku'] . ' sudah ada',
                        'request' => $request->products
                    ])->withInput();
                }

                $product = Product::create([
                    'product_id' => $prod['sku'],
                    'product_name' => $prod['name'],
                    'base_price' => $prod['buy_price'],
                    'selling_price' => $prod['sell_price'],
                    'stock' => $prod['quantity'],
                    'min_stock' => 0,
                ]);

                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $prod['quantity'],
                    'base_price' => $prod['buy_price'],
                    'selling_price' => $prod['sell_price'],
                    'total_price' => $prod['total_price'],
                ]);
            }


            DB::commit();
            return redirect()->route('purchases.index')->with(['success' => MessageResponse::ADD_SUCCESS]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with(['error' => MessageResponse::ERROR_OCCURRED, 'request' => $request->products])->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $rules = ValidationRules::get('purchases');
            $messages = ValidationRules::messages('purchases');

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->with(['error' => $validator->errors()->first()])->withInput();
            }

            DB::beginTransaction();

            $purchase = Purchase::with('purchaseDetails.product')->findOrFail($id);
            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'order_date' => $request->order_date,
                'due_date' => $request->due_date,
                'payment_date' => $request->payment_date,
                'total_price' => $request->total_price,
                'is_paid' => $request->is_paid ? 1 : 0,
            ]);

            $inputSkus = collect($request->products)->pluck('sku')->toArray();

            // Hapus detail dan produk lama yang tidak ada dalam SKU input
            foreach ($purchase->purchaseDetails as $detail) {
                $product = $detail->product;

                if (!in_array($product->product_id, $inputSkus)) {
                    $usedInOtherPurchases = $product->purchaseDetails()->where('purchase_id', '!=', $purchase->id)->exists();
                    $usedInTransactions = $product->transactionDetails()->exists();

                    $detail->delete();

                    if (!$usedInOtherPurchases && !$usedInTransactions) {
                        $product->delete();
                    }
                }
            }

            $products = collect($request->products)
                ->unique('sku')
                ->values();

            foreach ($products as $prod) {
                // Cari atau buat produk berdasarkan SKU
                $product = Product::where('product_id', $prod['sku'])->first();

                if ($product) {
                    $product->update([
                        'product_name' => $prod['name'],
                        'base_price' => $prod['buy_price'],
                        'selling_price' => $prod['sell_price'],
                        'stock' => $prod['quantity'], // opsional tergantung logika stok
                    ]);
                } else {
                    $product = Product::create([
                        'product_id' => $prod['sku'],
                        'product_name' => $prod['name'],
                        'base_price' => $prod['buy_price'],
                        'selling_price' => $prod['sell_price'],
                        'stock' => $prod['quantity'],
                        'min_stock' => 0,
                    ]);
                }

                // Cek apakah detail-nya sudah ada
                $existingDetail = $purchase->purchaseDetails
                    ->firstWhere('product.product_id', $prod['sku']);

                if ($existingDetail) {
                    $existingDetail->update([
                        'quantity' => $prod['quantity'],
                        'base_price' => $prod['buy_price'],
                        'selling_price' => $prod['sell_price'],
                        'total_price' => $prod['total_price'],
                    ]);
                } else {
                    PurchaseDetail::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $product->id,
                        'quantity' => $prod['quantity'],
                        'base_price' => $prod['buy_price'],
                        'selling_price' => $prod['sell_price'],
                        'total_price' => $prod['total_price'],
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('purchases.index')->with(['success' => MessageResponse::UPDATE_SUCCESS]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('purchases.index')->with(['error' => 'Data pembelian tidak ditemukan.']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with(['error' => MessageResponse::ERROR_OCCURRED]);
        }
    }




    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $purchase = Purchase::with('purchaseDetails')->findOrFail($id);

            foreach ($purchase->purchaseDetails as $detail) {

                $product = $detail->product;

                // Kembalikan stok
                // $product->increment('stock', $detail->quantity);

                // Hapus detail
                $detail->delete();

                $usedInOtherPurchases = $product->purchaseDetails()
                    ->where('purchase_id', '!=', $purchase->id)
                    ->exists();
                $usedInTransactions = $product->transactionDetails()->exists();

                if (!$usedInOtherPurchases && !$usedInTransactions) {
                    $product->delete();
                }
            }


            $purchase->delete();



            DB::commit();

            return jsonResponse(MessageResponse::DELETE_SUCCESS);
        } catch (\Throwable $e) {
            DB::rollBack();
            return jsonResponse(MessageResponse::ERROR_OCCURRED, null, $e->getMessage(), 500);
        }
    }
}
