<?php

namespace App\Http\Controllers;

use App\Helpers\MessageResponse;
use App\Helpers\ValidationRules;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $supplierQuery = Supplier::query();
        $perPage = $request->per_page ?? 5;
        if ($request->filled('supplier_name')) {
            $supplierQuery->where('supplier_name', 'like', '%' . $request->supplier_name . '%');
        }

        if ($request->filled('address')) {
            $supplierQuery->where('address', 'like', '%' . $request->address . '%');
        }

        if ($request->filled('contact')) {
            $supplierQuery->where('contact', 'like', '%' . $request->contact . '%');
        }

        if ($request->has('is_active') && $request->is_active !== '') {
            $supplierQuery->where('is_active', $request->is_active === 'yes' ? 1 : 0);
        }

        $sortField = $request->get('sort', 'product_id');
        $sortOrder = $request->get('order', 'desc');

        $allowedSorts = ['supplier_name', 'address', 'contact', 'is_active'];
        if (in_array($sortField, $allowedSorts)) {
            $supplierQuery->orderBy($sortField, $sortOrder);
        }

        $data = [
            'title' => 'Supplier',
            'supplier' => $supplierQuery->orderByDesc('created_at')->paginate($perPage)->appends($request->except('page')),
        ];

        return view('pages.supplier.index', $data);
    }

    public function show($id)
    {
        try {
            $d = Supplier::find($id);
            return jsonResponse(ucfirst(MessageResponse::SUCCESS), $d);
        } catch (\Throwable $th) {
            return jsonResponse(ucfirst(MessageResponse::FAILED_PROCESS), $d, $th->getMessage(), 500);
        }
    }


    public function store(Request $request)
    {
        try {

            $rules = ValidationRules::get('suppliers');
            $messages = ValidationRules::messages('suppliers');

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return jsonResponse(ucfirst($validator->errors()->first()), $request->all(), '', 400);
            }

            $supplier = Supplier::create($request->only(array_keys($rules)));

            return jsonResponse(ucfirst(MessageResponse::ADD_SUCCESS), $supplier);
        } catch (\Throwable $th) {
            return jsonResponse(ucfirst(MessageResponse::FAILED_PROCESS), $request->all(), $th->getMessage(), 500);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $rules = ValidationRules::get('suppliers', 'update', $id);
            $messages = ValidationRules::messages('suppliers');

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return jsonResponse(ucfirst($validator->errors()->first()), $request->all(), '', 400);
            }

            $supplier = Supplier::findOrFail($id);
            $supplier->update($request->only(array_keys($rules)));

            return jsonResponse(ucfirst(MessageResponse::UPDATE_SUCCESS), $request->all());
        } catch (\Throwable $th) {
            return jsonResponse(ucfirst(MessageResponse::FAILED_PROCESS), $request->all(), $th->getMessage(), 500);
        }
    }

    public function delete($id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json([
                'code' => 404,
                'message' => MessageResponse::NOT_FOUND
            ], 404);
        }

        try {
            $supplier->delete();

            return response()->json([
                'code' => 200,
                'message' => 'Supplier ' . MessageResponse::DELETE_SUCCESS,
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
