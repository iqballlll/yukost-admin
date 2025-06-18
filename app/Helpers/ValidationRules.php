<?php

namespace App\Helpers;

use Illuminate\Validation\Rule;

class ValidationRules
{
    public static function get(string $table, string $mode = 'create', $id = null): array
    {
        $isUpdate = $mode === 'update';

        switch ($table) {
            case 'users':
                return [
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
                    'password' => $isUpdate ? ['nullable'] : ['required', 'string', 'min:6'],
                ];

            case 'customer_groups':
                return [
                    'group_id' => [
                        'required',
                        'string',
                        'max:10',
                        Rule::unique('customer_groups', 'group_id')->ignore(request()->customer_group_id),
                    ],
                    'group_name' => ['required', 'string'],
                    'address' => ['required', 'string'],
                    'contact' => ['required', 'string', 'max:15'],
                ];

            case 'customer_companies':
                return [
                    'group_id' => ['required', 'exists:customer_groups,id'],
                    'company_id' => ['required', 'string', 'max:10'],
                    'company_name' => ['required', 'string'],
                    'address' => ['required'],
                    'contact' => ['required', 'string', 'regex:/^08[0-9]{6,13}$/'],
                    'invoice_exchange' => ['required'],
                    'is_active' => ['required', 'boolean'],
                ];

            case 'customer_outlets':
                return [
                    'company_id' => ['exists:customer_companies,id'],
                    'outlet_id' => ['required', 'string', 'max:10', Rule::unique('customer_outlets', 'outlet_id')->ignore(request()->customer_outlet_id),],
                    'outlet_name' => ['required', 'string'],
                    'address' => ['required'],
                    'contact' => ['required', 'string', 'regex:/^08[0-9]{6,13}$/'],
                    'type' => ['required', Rule::in(['company', 'individual'])],
                    'custom_price' => ['boolean'],
                    'is_active' => ['required', 'boolean'],
                ];

            case 'products':
                return [
                    'product_id' => array_merge(
                        ['required', 'string', 'max:20'],
                        $isUpdate
                        ? [Rule::unique('products', 'product_id')->ignore($id)]
                        : ['unique:products,product_id']
                    ),
                    'product_name' => ['required'],
                    'stock' => ['required', 'integer'],
                    'base_price' => ['required', 'numeric'],
                    'selling_price' => ['required', 'numeric'],
                    'min_stock' => ['required', 'integer'],
                ];

            case 'customer_prices':
                return [
                    'outlet_id' => ['required', 'exists:customer_outlets,id'],
                    'product_id' => ['required', 'exists:products,id'],
                    'base_price' => ['required', 'numeric'],
                    'selling_price' => ['required', 'numeric'],
                ];

            case 'transactions':
                return [
                    'outlet_id' => ['required', 'exists:customer_outlets,id'],
                    'order_date' => ['required', 'date'],
                    'due_date' => ['nullable', 'date'],
                    'payment_date' => ['nullable', 'date'],
                    'tukar_faktur' => ['nullable'],
                    'total_price' => ['required', 'numeric'],
                    'is_paid' => ['boolean'],
                ];

            case 'transaction_details':
                return [
                    'product_id' => ['required', 'exists:products,id'],
                    'transaction_id' => ['required', 'exists:transactions,id'],
                    'selling_price' => ['required', 'numeric'],
                    'quantity' => ['required', 'integer'],
                    'total_price' => ['required', 'numeric'],
                ];

            case 'suppliers':
                return [
                    'supplier_name' => array_merge(
                        ['required', 'string'],
                        $isUpdate
                        ? [Rule::unique('suppliers', 'supplier_name')->ignore($id)]
                        : ['unique:suppliers,supplier_name']
                    ),
                    'address' => ['required', 'string'],
                    'contact' => ['required', 'string', 'regex:/^08[0-9]{6,13}$/'],
                    'is_active' => ['required', 'boolean'],
                ];


            case 'purchases':
                return [
                    'supplier_id' => ['required', 'exists:suppliers,id'],
                    'order_date' => ['required', 'date'],
                    'total_price' => ['required', 'numeric'],
                ];

            case 'purchase_details':
                return [
                    'purchase_id' => ['required', 'exists:purchases,id'],
                    'product_id' => ['required', 'exists:products,id'],
                    'quantity' => ['required', 'integer'],
                    'base_price' => ['required', 'numeric'],
                    'selling_price' => ['required', 'numeric'],
                    'total_price' => ['required', 'numeric'],
                ];

            case 'stock_opnames':
                return [
                    'stock_opname_id' => ['required', 'string', 'max:20'],
                    'processed_date' => ['required', 'date'],
                    'system_stock' => ['required', 'integer'],
                    'warehouse_stock' => ['required', 'integer'],
                    'stock_difference' => ['required', 'integer'],
                    'status' => ['required', 'boolean'],
                ];

            case 'stock_opname_details':
                return [
                    'stock_opname_id' => ['required', 'exists:stock_opnames,id'],
                    'product_id' => ['required', 'exists:products,id'],
                    'system_stock' => ['required', 'integer'],
                    'warehouse_stock' => ['required', 'integer'],
                    'stock_difference' => ['required', 'integer'],
                ];

            default:
                return [];
        }
    }

    public static function messages(string $table): array
    {
        switch ($table) {
            case 'users':
                return [
                    'name.required' => 'Nama wajib diisi.',
                    'email.required' => 'Email wajib diisi.',
                    'email.email' => 'Format email tidak valid.',
                    'email.unique' => 'Email sudah digunakan.',
                    'password.required' => 'Password wajib diisi.',
                    'password.min' => 'Password minimal 6 karakter.',
                ];

            case 'customer_groups':
                return [
                    'group_id.required' => 'ID grup wajib diisi.',
                    'group_id.unique' => 'ID grup sudah digunakan',
                    'group_id.max' => 'ID grup maksimal 10 karakter.',
                    'group_name.required' => 'Nama grup wajib diisi.',
                    'address.required' => 'Alamat wajib diisi.',
                    'contact.required' => 'Kontak wajib diisi.',
                    'contact.max' => 'Kontak maksimal 15 karakter.',
                ];

            case 'customer_companies':
                return [
                    'contact.regex' => 'Format kontak tidak valid. Harus diawali dengan 08 dan 8-14 digit.',
                    'group_id.required' => 'Grup wajib dipilih.',
                    'group_id.exists' => 'Grup tidak ditemukan.',
                    'company_id.required' => 'ID perusahaan wajib diisi.',
                    'company_id.max' => 'ID perusahaan maksimal 10 karakter.',
                    'company_name.required' => 'Nama perusahaan wajib diisi.',
                    'address.required' => 'Alamat wajib diisi.',
                    'contact.required' => 'Kontak wajib diisi.',
                    'contact.max' => 'Kontak maksimal 15 karakter.',
                    'is_active.required' => 'Status aktif wajib diisi.',
                ];

            case 'customer_outlets':
                return [
                    'company_id.required' => 'Perusahaan wajib dipilih.',
                    'company_id.exists' => 'Perusahaan tidak ditemukan.',
                    'outlet_id.required' => 'ID outlet wajib diisi.',
                    'outlet_id.max' => 'ID outlet maksimal 10 karakter.',
                    'outlet_id.unique' => 'ID outlet sudah digunakan',
                    'outlet_name.required' => 'Nama outlet wajib diisi.',
                    'address.required' => 'Alamat wajib diisi.',
                    'contact.required' => 'Kontak wajib diisi.',
                    'contact.max' => 'Kontak maksimal 15 karakter.',
                    'contact.regex' => 'Format kontak tidak valid. Harus diawali dengan 08 dan 8-14 digit.',
                    'type.required' => 'Tipe outlet wajib dipilih.',
                    'type.in' => 'Tipe outlet tidak valid.',
                    'kontra_faktur.required' => 'Status kontra faktur wajib diisi.',
                    'is_active.required' => 'Status aktif wajib diisi.',
                ];

            case 'products':
                return [
                    'product_id.required' => 'ID produk wajib diisi.',
                    'product_id.unique' => 'ID produk sudah digunakan',
                    'product_id.max' => 'ID produk maksimal 20 karakter.',
                    'product_name.required' => 'Nama produk wajib diisi.',
                    'stock.required' => 'Stok wajib diisi.',
                    'stock.integer' => 'Stok harus berupa angka.',
                    'base_price.required' => 'Harga dasar wajib diisi.',
                    'base_price.numeric' => 'Harga dasar harus berupa angka.',
                    'selling_price.required' => 'Harga jual wajib diisi.',
                    'selling_price.numeric' => 'Harga jual harus berupa angka.',
                    'min_stock.required' => 'Stok minimum wajib diisi.',
                    'min_stock.integer' => 'Stok minimum harus berupa angka.',
                ];

            case 'customer_prices':
                return [
                    'outlet_id.required' => 'Outlet wajib dipilih.',
                    'outlet_id.exists' => 'Outlet tidak ditemukan.',
                    'product_id.required' => 'Produk wajib dipilih.',
                    'product_id.exists' => 'Produk tidak ditemukan.',
                    'base_price.required' => 'Harga dasar wajib diisi.',
                    'base_price.numeric' => 'Harga dasar harus berupa angka.',
                    'selling_price.required' => 'Harga jual wajib diisi.',
                    'selling_price.numeric' => 'Harga jual harus berupa angka.',
                ];

            case 'transactions':
                return [
                    'outlet_id.required' => 'Outlet wajib dipilih.',
                    'transaction_id.required' => 'ID transaksi wajib diisi.',
                    'transaction_id.max' => 'ID transaksi maksimal 25 karakter.',
                    'order_date.required' => 'Tanggal pemesanan wajib diisi.',
                    'payment_date.date' => 'Format tanggal pembayaran tidak valid.',
                    'total_price.required' => 'Total harga wajib diisi.',
                    'total_price.numeric' => 'Total harga harus berupa angka.',
                    'is_paid.required' => 'Status pembayaran wajib diisi.',
                ];

            case 'transaction_details':
                return [
                    'product_id.required' => 'Produk wajib dipilih.',
                    'transaction_id.required' => 'Transaksi wajib dipilih.',
                    'selling_price.required' => 'Harga jual wajib diisi.',
                    'selling_price.numeric' => 'Harga jual harus berupa angka.',
                    'quantity.required' => 'Kuantitas wajib diisi.',
                    'quantity.integer' => 'Kuantitas harus berupa angka.',
                    'total_price.required' => 'Total harga wajib diisi.',
                    'total_price.numeric' => 'Total harga harus berupa angka.',
                ];

            case 'suppliers':
                return [
                    'supplier_name.required' => 'Nama supplier wajib diisi.',
                    'supplier_name.string' => 'Nama supplier harus berupa teks.',
                    'supplier_name.unique' => 'Nama supplier sudah digunakan.',
                    'address.required' => 'Alamat wajib diisi.',
                    'address.string' => 'Alamat harus berupa teks.',
                    'contact.required' => 'Kontak wajib diisi.',
                    'contact.string' => 'Kontak harus berupa teks.',
                    'contact.regex' => 'Format kontak tidak valid. Harus diawali dengan 08 dan 8-14 digit.',
                    'contact.unique' => 'Kontak sudah digunakan.',
                    'is_active.required' => 'Status aktif wajib diisi.',
                    'is_active.boolean' => 'Status aktif harus berupa boolean.',
                ];

            case 'purchases':
                return [
                    'purchase_id.required' => 'ID pembelian wajib diisi.',
                    'purchase_id.max' => 'ID pembelian maksimal 20 karakter.',
                    'supplier_id.required' => 'Supplier wajib dipilih.',
                    'supplier_id.exists' => 'Supplier tidak ditemukan.',
                    'order_date.required' => 'Tanggal order wajib diisi.',
                    'due_date.required' => 'Tanggal jatuh tempo wajib diisi.',
                    'payment_date.date' => 'Format tanggal pembayaran tidak valid.',
                    'total_price.required' => 'Total harga wajib diisi.',
                    'total_price.numeric' => 'Total harga harus berupa angka.',
                    'is_paid.required' => 'Status pembayaran wajib diisi.',
                ];

            case 'purchase_details':
                return [
                    'purchase_id.required' => 'Pembelian wajib dipilih.',
                    'purchase_id.exists' => 'Pembelian tidak ditemukan.',
                    'product_id.required' => 'Produk wajib dipilih.',
                    'product_id.exists' => 'Produk tidak ditemukan.',
                    'quantity.required' => 'Kuantitas wajib diisi.',
                    'quantity.integer' => 'Kuantitas harus berupa angka.',
                    'base_price.required' => 'Harga dasar wajib diisi.',
                    'base_price.numeric' => 'Harga dasar harus berupa angka.',
                    'selling_price.required' => 'Harga jual wajib diisi.',
                    'selling_price.numeric' => 'Harga jual harus berupa angka.',
                    'total_price.required' => 'Total harga wajib diisi.',
                    'total_price.numeric' => 'Total harga harus berupa angka.',
                ];

            case 'stock_opnames':
                return [
                    'stock_opname_id.required' => 'ID stok opname wajib diisi.',
                    'stock_opname_id.max' => 'ID stok opname maksimal 20 karakter.',
                    'processed_date.required' => 'Tanggal proses wajib diisi.',
                    'system_stock.required' => 'Stok sistem wajib diisi.',
                    'warehouse_stock.required' => 'Stok gudang wajib diisi.',
                    'stock_difference.required' => 'Selisih stok wajib diisi.',
                    'status.required' => 'Status wajib diisi.',
                ];

            case 'stock_opname_details':
                return [
                    'stock_opname_id.required' => 'Stok opname wajib dipilih.',
                    'stock_opname_id.exists' => 'Stok opname tidak ditemukan.',
                    'product_id.required' => 'Produk wajib dipilih.',
                    'product_id.exists' => 'Produk tidak ditemukan.',
                    'system_stock.required' => 'Stok sistem wajib diisi.',
                    'warehouse_stock.required' => 'Stok gudang wajib diisi.',
                    'stock_difference.required' => 'Selisih stok wajib diisi.',
                ];

            default:
                return [];
        }
    }
}
