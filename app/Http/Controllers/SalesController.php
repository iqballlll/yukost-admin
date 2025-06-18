<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelpers;
use App\Helpers\MessageResponse;
use App\Helpers\ValidationRules;
use App\Models\CustomerCompany;
use App\Models\CustomerGroup;
use App\Models\CustomerOutlet;
use App\Models\CustomerPrice;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $companies = CustomerCompany::all();
        $outlets = CustomerOutlet::all();

        $filterPerusahaan = $request->filter_perusahaan;
        $filterOutlet = $request->filter_outlet;
        $operator = $request->filter_operator;
        $totalOperator = $request->total_operator;
        $totalValue = $request->total_value;
        $discountOperator = $request->discount_operator;
        $discountValue = $request->discount_value;
        $totalPriceAfterDiscount = $request->total_price_after_discount;

        $filterSendInvoice = $request->send_invoice;
        $filterIsPay = $request->is_paid;

        $filters = [
            'filter_perusahaan' => $filterPerusahaan,
            'filter_outlet' => $filterOutlet,
            'filter_operator' => $operator,
            'total_operator' => $totalOperator,
            'total_value' => $totalValue,
            'discount_operator' => $discountOperator,
            'discount_value' => $discountValue,
            'order_date' => $request->order_date,
            'due_date' => $request->due_date,
            'payment_date' => $request->payment_date,
            'send_invoice' => $filterSendInvoice,
            'is_paid' => $filterIsPay,
            'total_price_after_discount' => $totalPriceAfterDiscount
        ];


        $filtersRaw = [
            'order_date' => $request->order_date,
            'due_date' => $request->due_date,
            'payment_date' => $request->payment_date,
        ];


        foreach (['order_date', 'due_date', 'payment_date'] as $field) {
            if (!empty($filtersRaw[$field]) && str_contains($filtersRaw[$field], '|')) {
                [$start, $end] = explode('|', $filtersRaw[$field]);

                // Pastikan valid format Y-m-d
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $start) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $end)) {
                    // Untuk display di view (filter tetap pakai format raw)
                    $filtersDisplay[$field] = Carbon::parse(trim($start))->format('d/m/Y') . ' - ' . Carbon::parse(trim($end))->format('d/m/Y');
                }
            }
        }





        if (!empty($filters['order_date']) && str_contains($filters['order_date'], '|')) {
            [$start, $end] = explode('|', $filters['order_date']);
            $filters['order_date'] = Carbon::parse($start)->format('d/m/Y') . ' - ' . Carbon::parse($end)->format('d/m/Y');
        }

        if (!empty($filters['due_date']) && str_contains($filters['due_date'], '|')) {
            [$start, $end] = explode('|', $filters['due_date']);
            $filters['due_date'] = Carbon::parse($start)->format('d/m/Y') . ' - ' . Carbon::parse($end)->format('d/m/Y');
        }

        if (!empty($filters['payment_date']) && str_contains($filters['payment_date'], '|')) {
            [$start, $end] = explode('|', $filters['payment_date']);
            $filters['payment_date'] = Carbon::parse($start)->format('d/m/Y') . ' - ' . Carbon::parse($end)->format('d/m/Y');
        }

        // $query = CustomerOutlet::with([
        //     'company',
        //     'sales' => function ($q) use ($filtersRaw, $filterSendInvoice, $filterIsPay, $totalOperator, $totalValue, $discountOperator, $discountValue, $totalPriceAfterDiscount) {
        //         foreach (['order_date', 'due_date', 'payment_date'] as $field) {
        //             if (!empty($filtersRaw[$field]) && str_contains($filtersRaw[$field], '|')) {
        //                 [$start, $end] = explode('|', $filtersRaw[$field]);
        //                 $q->whereBetween($field, [
        //                     Carbon::parse($start)->startOfDay(),
        //                     Carbon::parse($end)->endOfDay()
        //                 ]);
        //             }
        //         }

        //         if ($filterSendInvoice !== null && $filterSendInvoice !== '') {
        //             $q->where('tukar_faktur', $filterSendInvoice);
        //         }

        //         if ($filterIsPay !== null && $filterIsPay !== '') {
        //             $q->where('is_paid', $filterIsPay);
        //         }

        //         if ($totalOperator && $totalValue !== null && is_numeric($totalValue)) {
        //             $q->where('total_price', $totalOperator, $totalValue);
        //         }
        //         if ($totalPriceAfterDiscount !== null && is_numeric($totalPriceAfterDiscount)) {
        //             $q->where('total_price_after_discount', $totalPriceAfterDiscount);
        //         }

        //         if ($discountOperator && $discountValue !== null && is_numeric($discountValue)) {
        //             $q->where('discount_amount', $discountOperator, number_format($discountValue, 2, '.', ''));
        //         }
        //     }
        // ])
        //     ->whereHas('sales', function ($q) use ($filtersRaw, $filterSendInvoice, $filterIsPay, $totalOperator, $totalValue, $discountOperator, $discountValue, $totalPriceAfterDiscount) {
        //         foreach (['order_date', 'due_date', 'payment_date'] as $field) {
        //             if (!empty($filtersRaw[$field]) && str_contains($filtersRaw[$field], '|')) {
        //                 [$start, $end] = explode('|', $filtersRaw[$field]);
        //                 $q->whereBetween($field, [
        //                     Carbon::parse($start)->startOfDay(),
        //                     Carbon::parse($end)->endOfDay()
        //                 ]);
        //             }
        //         }

        //         if ($filterSendInvoice !== null && $filterSendInvoice !== '') {
        //             $q->where('tukar_faktur', $filterSendInvoice);
        //         }

        //         if ($filterIsPay !== null && $filterIsPay !== '') {
        //             $q->where('is_paid', $filterIsPay);
        //         }

        //         if ($totalOperator && $totalValue !== null && is_numeric($totalValue)) {
        //             $q->where('total_price', $totalOperator, $totalValue);
        //         }
        //         if ($totalPriceAfterDiscount !== null && is_numeric($totalPriceAfterDiscount)) {
        //             $q->where('total_price_after_discount', $totalPriceAfterDiscount);
        //         }

        //         if ($discountOperator && $discountValue !== null && is_numeric($discountValue)) {
        //             $q->where('discount_amount', $discountOperator, number_format($discountValue, 2, '.', ''));
        //         }
        //     })
        //     ->when($filterPerusahaan, fn($q) => $q->where('company_id', $filterPerusahaan))
        //     ->when($filterOutlet, function ($q) use ($filterOutlet, $operator) {
        //         if ($operator === 'or') {
        //             $q->orWhere('id', $filterOutlet);
        //         } else {
        //             $q->where('id', $filterOutlet);
        //         }
        //     });


        // $paginated = $query->paginate($perPage);

        // $currentPageItems = collect($paginated->items());




        // $grouped = $currentPageItems->groupBy(function ($outlet) {
        //     return $outlet->type === 'individual'
        //         ? 'PERSONAL'
        //         : ($outlet->company->company_name ?? 'TANPA NAMA');
        // });

        // $finalResults = $grouped->map(function ($outlets, $companyName) {
        //     return [
        //         'company' => $companyName,
        //         'outlets' => $outlets->map(function ($outlet) {
        //             return [
        //                 'name' => $outlet->outlet_name,
        //                 'sales' => $outlet->sales,
        //             ];
        //         })->values()
        //     ];
        // })->values();

        // $paginated->setCollection($finalResults);

        $query = CustomerOutlet::with([
            'company',
            'sales' => function ($q) use ($filtersRaw, $filterSendInvoice, $filterIsPay, $totalOperator, $totalValue, $discountOperator, $discountValue, $totalPriceAfterDiscount) {
                foreach (['order_date', 'due_date', 'payment_date'] as $field) {
                    if (!empty($filtersRaw[$field]) && str_contains($filtersRaw[$field], '|')) {
                        [$start, $end] = explode('|', $filtersRaw[$field]);
                        $q->whereBetween($field, [
                            Carbon::parse($start)->startOfDay(),
                            Carbon::parse($end)->endOfDay()
                        ]);
                    }
                }

                if ($filterSendInvoice !== null && $filterSendInvoice !== '') {
                    $q->where('tukar_faktur', $filterSendInvoice);
                }

                if ($filterIsPay !== null && $filterIsPay !== '') {
                    $q->where('is_paid', $filterIsPay);
                }

                if ($totalOperator && $totalValue !== null && is_numeric($totalValue)) {
                    $q->where('total_price', $totalOperator, $totalValue);
                }

                if ($totalPriceAfterDiscount !== null && is_numeric($totalPriceAfterDiscount)) {
                    $q->where('total_price_after_discount', $totalPriceAfterDiscount);
                }

                if ($discountOperator && $discountValue !== null && is_numeric($discountValue)) {
                    $q->where('discount_amount', $discountOperator, number_format($discountValue, 2, '.', ''));
                }
            }
        ])
            ->whereHas('sales', function ($q) use ($filtersRaw, $filterSendInvoice, $filterIsPay, $totalOperator, $totalValue, $discountOperator, $discountValue, $totalPriceAfterDiscount) {
                foreach (['order_date', 'due_date', 'payment_date'] as $field) {
                    if (!empty($filtersRaw[$field]) && str_contains($filtersRaw[$field], '|')) {
                        [$start, $end] = explode('|', $filtersRaw[$field]);
                        $q->whereBetween($field, [
                            Carbon::parse($start)->startOfDay(),
                            Carbon::parse($end)->endOfDay()
                        ]);
                    }
                }

                if ($filterSendInvoice !== null && $filterSendInvoice !== '') {
                    $q->where('tukar_faktur', $filterSendInvoice);
                }

                if ($filterIsPay !== null && $filterIsPay !== '') {
                    $q->where('is_paid', $filterIsPay);
                }

                if ($totalOperator && $totalValue !== null && is_numeric($totalValue)) {
                    $q->where('total_price', $totalOperator, $totalValue);
                }

                if ($totalPriceAfterDiscount !== null && is_numeric($totalPriceAfterDiscount)) {
                    $q->where('total_price_after_discount', $totalPriceAfterDiscount);
                }

                if ($discountOperator && $discountValue !== null && is_numeric($discountValue)) {
                    $q->where('discount_amount', $discountOperator, number_format($discountValue, 2, '.', ''));
                }
            })
            ->when($filterPerusahaan, fn($q) => $q->where('company_id', $filterPerusahaan))
            ->when($filterOutlet, function ($q) use ($filterOutlet, $operator) {
                if ($operator === 'or') {
                    $q->orWhere('id', $filterOutlet);
                } else {
                    $q->where('id', $filterOutlet);
                }
            });

        $outlets = $query->get();

        // Grouping outlet berdasarkan perusahaan
        $grouped = $outlets->groupBy(function ($outlet) {
            return $outlet->type === 'individual'
                ? 'PERSONAL'
                : ($outlet->company->company_name ?? 'TANPA NAMA');
        });

        // Format hasil akhir
        $finalResults = $grouped->map(function ($outlets, $companyName) {
            return [
                'company' => $companyName,
                'outlets' => $outlets->map(function ($outlet) {
                    return [
                        'name' => $outlet->outlet_name,
                        'sales' => $outlet->sales,
                    ];
                })->values()
            ];
        })->values();

        // Pagination per grup perusahaan
        $page = request('page', 1);
        $perPage = $request->per_page ?? 5;
        $total = $finalResults->count();
        $currentItems = $finalResults->slice(($page - 1) * $perPage, $perPage)->values();

        $paginated = new LengthAwarePaginator(
            $currentItems,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );



        return view('pages.sales.index', [
            'title' => 'Penjualan',
            'sales' => $paginated,
            'companies' => $companies,
            'outlets' => $outlets,
            'filters' => $filters,
        ]);
    }


    public function create(Request $request)
    {
        $outlets = CustomerOutlet::get();
        $products = Product::paginate();
        $data = ['title' => 'Tambah Penjualan', 'customers' => $outlets, 'products' => $products];
        return view('pages.sales.create', $data);
    }

    public function store(Request $request)
    {
        try {
            $rules = ValidationRules::get('transactions');
            $messages = ValidationRules::messages('transactions');

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withInput()->with(['error' => $validator->errors()->first()]);
            }

            DB::beginTransaction();

            $payloadTransaction = $request->only(array_keys($rules));

            do {
                $transactionId = 'TRX' . time() . strtoupper(Str::random(3));
            } while (Transaction::where('transaction_id', $transactionId)->exists());

            $payloadTransaction['transaction_id'] = $transactionId;
            $payloadTransaction['is_paid'] = false;
            $payloadTransaction['discount_type'] = $request->discount_type;
            $payloadTransaction['discount_amount'] = $request->discount_amount;
            $payloadTransaction['total_price_after_discount'] = $request->total_price_after_discount;

            $transaction = Transaction::create($payloadTransaction);

            $payloadTransactionDetails = [];
            foreach ($request->products as $prod) {
                $payloadTransactionDetails[] = [
                    'product_id' => $prod['product_id'],
                    'transaction_id' => $transaction->id,
                    'selling_price' => $prod['selling_price'],
                    'quantity' => $prod['quantity'],
                    'total_price' => $prod['total_price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            TransactionDetail::insert($payloadTransactionDetails);

            DB::commit();
            return redirect()->route('sales.index')->with(['success' => MessageResponse::ADD_SUCCESS]);
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
            return redirect()->back()->with(['error' => MessageResponse::ERROR_OCCURRED]);
        }
    }

    public function getProductCustomerPrices(Request $request)
    {
        $query = $request->get('q');
        $perPage = $request->per_page ?? 5;
        $outlet = CustomerOutlet::find($request->outlet_id);

        if ($outlet && $outlet->custom_price) {


            $rawProducts = CustomerPrice::with('product')->where('outlet_id', $outlet->id)
                ->when($query, function ($q) use ($query) {
                    $q->whereHas('product', fn($p) => $p->where('product_name', 'like', "%{$query}%"));
                })
                ->paginate($perPage);


            $products = $rawProducts->through(function ($item) {
                return (object) [
                    'id' => $item->product?->id,
                    'product_name' => $item->product?->product_name,
                    'selling_price' => $item->selling_price,
                    'stock' => $item->product?->stock,
                    'min_stock' => $item->product?->min_stock
                ];
            });
        } else {

            $rawProducts = Product::when($query, function ($q) use ($query) {
                $q->where('product_name', 'like', "%{$query}%");
            })
                ->paginate($perPage);

            $products = $rawProducts;
        }



        return view('components.modal-products-sales-pagination', compact('products'))->render();
    }

    public function edit($id)
    {
        $sale = Transaction::with('details.product')->find($id);

        if (!$sale)
            abort(404);

        $customers = CustomerOutlet::all();
        $products = Product::paginate();
        $data = [
            'title' => 'Edit Transaksi',
            'sale' => $sale,
            'customers' => $customers,
            'products' => $products
        ];

        // AppHelpers::debugData($sale);

        return view('pages.sales.edit', $data);
    }
    public function detail($id)
    {
        $sale = Transaction::with('details.product')->find($id);

        if (!$sale)
            abort(404);

        $customers = CustomerOutlet::all();
        $products = Product::paginate();
        $data = [
            'title' => 'Detail Transaksi',
            'sale' => $sale,
            'customers' => $customers,
            'products' => $products
        ];

        // AppHelpers::debugData($sale);

        return view('pages.sales.detail', $data);
    }

    public function update($id, Request $request)
    {
        DB::beginTransaction();
        try {

            $transaction = Transaction::find($id);
            if (!$transaction) {
                return redirect()->back()->withInput()->with(['error' => MessageResponse::NOT_FOUND]);
            }

            $rules = ValidationRules::get('transactions');
            $messages = ValidationRules::messages('transactions');

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withInput()->with(['error' => $validator->errors()->first()]);
            }

            $transaction->update([
                'outlet_id' => $request->outlet_id,
                'order_date' => $request->order_date,
                'due_date' => $request->due_date,
                'payment_date' => $request->payment_date,
                'tukar_faktur' => $request->tukar_faktur,
                'total_price' => $request->total_price,
                'discount_type' => $request->discount_type,
                'total_price_after_discount' => $request->total_price_after_discount,
                'discount_amount' => $request->discount_amount
            ]);

            $newProducts = $request->products ?? [];

            $existingDetails = $transaction->details()->get();

            $existingProductIds = $existingDetails->pluck('product_id')->toArray();

            $newProductIds = array_keys($newProducts);

            $deletedProductIds = array_diff($existingProductIds, $newProductIds);

            foreach ($existingDetails as $detail) {
                if (in_array($detail->product_id, $deletedProductIds)) {
                    // Kembalikan stok
                    $product = Product::find($detail->product_id);
                    if ($product) {
                        $product->stock += $detail->quantity;
                        $product->save();
                    }

                    // Hapus detail
                    $detail->delete();
                }
            }

            foreach ($newProducts as $productId => $data) {
                $product = Product::findOrFail($productId);
                $quantity = (int) $data['quantity'];
                $sellingPrice = (float) $data['selling_price'];
                $totalPrice = (float) $data['total_price'];

                $existingDetail = $existingDetails->firstWhere('product_id', $productId);

                if ($existingDetail) {
                    // Hitung selisih quantity
                    $diff = $quantity - $existingDetail->quantity;

                    // Update stok produk
                    $product->stock -= $diff;
                    $product->save();

                    // Update detail transaksi
                    $existingDetail->update([
                        'quantity' => $quantity,
                        'selling_price' => $sellingPrice,
                        'total_price' => $totalPrice,
                    ]);
                } else {
                    // Kurangi stok karena baru ditambahkan
                    $product->stock -= $quantity;
                    $product->save();

                    // Buat detail baru
                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'selling_price' => $sellingPrice,
                        'total_price' => $totalPrice,
                    ]);
                }
            }
            DB::commit();
            return redirect()->back()->with('success', MessageResponse::UPDATE_SUCCESS);
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
            return redirect()->back()->with('error', MessageResponse::ERROR_OCCURRED);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $transaction = Transaction::with('details')->findOrFail($id);

            foreach ($transaction->details as $detail) {

                Product::where('id', $detail->product_id)
                    ->increment('stock', $detail->quantity);
            }


            $transaction->details()->delete();
            $transaction->delete();

            DB::commit();

            return jsonResponse(MessageResponse::DELETE_SUCCESS);
        } catch (\Throwable $e) {
            DB::rollBack();
            return jsonResponse(MessageResponse::ERROR_OCCURRED, null, $e->getMessage(), 500);
        }
    }
}
