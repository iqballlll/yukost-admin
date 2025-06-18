@extends('layout.index')
@section('content')
<style>
    .sticky-col {
        position: sticky;
        z-index: 2;
    }

    .left-col {
        left: 0;
        z-index: 3;
    }

    .left-col-2 {
        left: 150px;
        /* Sesuaikan dengan lebar kolom pertama */
        z-index: 3;
    }

    .right-col {
        right: 0;
        z-index: 3;
    }

    /* Styling tambahan agar tidak bertabrakan saat scroll */
    th,
    td {
        white-space: nowrap;
    }
</style>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@php
$helpers = \App\Helpers\AppHelpers::class;
@endphp
<div class="card">
    <div class="card-body py-4">
        <div class="mb-5 row d-flex justify-content-between">
            <div class="col-sm-12 d-flex align-items-center justify-content-end">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Tambah</button>
            </div>
        </div>
        <div style="overflow-x: auto">
            <table id="productTable" class="table table-lg table-stripped">
                <thead>
                    <tr class="border-0">
                        <th class="border-0" style="position: sticky; left: 0; background: white; z-index: 10;">
                            <div class="col">
                                <label class="form-label d-flex align-items-center justify-content-between">
                                    <span onclick="sortBy('product_id')" style="cursor: pointer;">
                                        SKU <i class="{{ $helpers::sortIcon('product_id') }} ms-2"></i>
                                    </span>
                                </label>
                                <input value="{{ request('product_id') }}" type="text" id="filter_product_id"
                                    name="product_id" value="" class="form-control" style="width: 220px;" />
                            </div>
                        </th>
                        <th class="border-0">
                            <div class="col">
                                <span onclick="sortBy('product_name')" style="cursor: pointer;">
                                    Nama Produk <i class="{{ $helpers::sortIcon('product_name') }} ms-2"></i>
                                </span>

                                <input value="{{ request('product_name') }}" type="text" id="filter_product_name"
                                    name="product_name" value="" class="form-control" width="100"
                                    style="width: 220px;" />
                            </div>
                        </th>
                        <th class="border-0">
                            <div class="col">
                                <span onclick="sortBy('stock')" style="cursor: pointer;">
                                    Quantity <i class="{{ $helpers::sortIcon('stock') }} ms-2"></i>
                                </span>
                                <input value="{{ request('quantity')  }}" type="text" id="filter_quantity"
                                    name="quantity" value="" class="form-control" style="width: 220px;" />
                            </div>
                        </th>
                        <th class="border-0">
                            <div class="col">
                                <span onclick="sortBy('base_price')" style="cursor: pointer;">
                                    Harga Dasar <i class="{{ $helpers::sortIcon('base_price') }} ms-2"></i>
                                </span>

                                <input value="{{ request('base_price')  }}" type="text" id="filter_base_price"
                                    name="base_price" value="" class="form-control" style="width: 220px;"
                                    inputmode="numeric" autocomplete="off" />
                            </div>
                        </th>

                        <th class="border-0">
                            <div class="col">
                                <span onclick="sortBy('selling_price')" style="cursor: pointer;">
                                    Harga Jual <i class="{{ $helpers::sortIcon('selling_price') }} ms-2"></i>
                                </span>
                                <input value="{{ request('selling_price')  }}" type="text" id="filter_selling_price"
                                    name="selling_price" value="" class="form-control" style="width: 220px;" />
                            </div>
                        </th>
                        <th class="border-0">
                            <div class="col">
                                <span onclick="sortBy('min_stock')" style="cursor: pointer;">
                                    Minimum Stok <i class="{{ $helpers::sortIcon('min_stock') }} ms-2"></i>
                                </span>
                                <input value="{{ request('min_stock')  }}" type="text" id="filter_min_stock"
                                    name="filter_min_stock" value="" class="form-control" style="width: 220px;" />
                            </div>
                        </th>
                        <th class="border-0" style="position: sticky; right: 0; background: white; z-index: 10;">
                            <div class="d-flex flex-column justify-content-center align-items-center"
                                style="height: 100%;">
                                <label class="form-label mb-1">Aksi</label>
                                <a style="cursor: pointer"
                                    onclick="window.location.href=`{{ route('products.index') }}`"><i
                                        class="bi bi-arrow-clockwise fs-4"></i></a>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $p)

                    <tr id="row-product-{{ $p->id }}">
                        <td class="product_id" style="position: sticky; left: 0; background: white; z-index: 10;">
                            <span>{{$p->product_id}}</span>
                        </td>
                        <td class="product_name">
                            <span>{{ $p->product_name }}</span>
                        </td>
                        <td class="stock">
                            <span>{{$p->stock}}</span>
                        </td>
                        <td class="base_price">
                            <span>{{\App\Helpers\AppHelpers::formatToRupiah($p->base_price)}}</span>
                        </td>
                        <td class="selling_price">
                            <span>{{\App\Helpers\AppHelpers::formatToRupiah($p->selling_price)}}</span>
                        </td>
                        <td class="min_stock">
                            <span>{{$p->min_stock}}</span>
                        </td>
                        <td style="position: sticky; right: 0; background: white; z-index: 5;">
                            <a onclick="editData(`{{ $p->id }}`)" href="#"><i
                                    class="text-secondary fs-5 bi bi-pencil-square"></i></a>
                            <a onclick="handleDeleteProduct(`{{ $p->id }}`)" href="#"><i
                                    class="text-secondary fs-5 bi bi-trash"></i></a>
                        </td>

                    </tr>
                    @empty
                    <tr class="tr-empty">
                        <td colspan="7" class="text-center">Tidak ada data ditemukan</td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
        <x-pagination-info :paginator="$products" />
    </div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Produk</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="productForm">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3 row">
                                            <label class="col-sm-4 col-form-label">SKU</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="product_id" name="product_id"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-sm-4 col-form-label">Nama Produk</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="product_name" name="product_name"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-sm-4 col-form-label">Quantity</label>
                                            <div class="col-sm-8">
                                                <input type="text" maxlength="10" min="1" id="quantity" name="quantity"
                                                    class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 row">
                                            <label class="col-sm-4 col-form-label">Harga Dasar</label>
                                            <div class="col-sm-8">
                                                <input min="1000" inputmode="numeric" autocomplete="off" id="base_price"
                                                    name="base_price" class="form-control" maxlength="16">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-sm-4 col-form-label">Harga Jual</label>
                                            <div class="col-sm-8">
                                                <input min="1000" inputmode="numeric" autocomplete="off"
                                                    id="selling_price" name="selling_price" class="form-control"
                                                    maxlength="16">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-sm-4 col-form-label">Minimum Stok</label>
                                            <div class="col-sm-8">
                                                <input type="text" id="min_stock" maxlength="10" min="1"
                                                    name="min_stock" class="form-control">
                                            </div>
                                        </div>
                                    </div>


                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button id="submitBtn" type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        const filterBasePrice = document.getElementById('filter_base_price');
            const filterSellingPrice = document.getElementById('filter_selling_price');
            const basePrice = document.getElementById('base_price');
            const sellingPrice = document.getElementById('selling_price');
            const filterQuantity = document.getElementById('filter_quantity');
            const quantity = document.getElementById('quantity');
            const filterMinStock = document.getElementById('filter_min_stock');
            const minStock = document.getElementById('min_stock');
            const submitBtn = document.getElementById('submitBtn');
            let products = null;
            const debouncedHandleProductFilters = debounce(handleProductFilters, 900);

            document.addEventListener('DOMContentLoaded', function () {

                if (filterBasePrice) handleRupiahFormat(filterBasePrice);
                if (basePrice) handleRupiahFormat(basePrice);
                if (filterSellingPrice) handleRupiahFormat(filterSellingPrice);
                if (sellingPrice) handleRupiahFormat(sellingPrice);
                if (filterQuantity) allowOnlyNumbers('filter_quantity');
                if (quantity) allowOnlyNumbers('quantity');
                if (filterMinStock) allowOnlyNumbers('filter_min_stock');
                if (minStock) allowOnlyNumbers('min_stock');
            });

            //SORT

            function sortBy(field) {
                const url = new URL(window.location.href);
                const currentSort = url.searchParams.get('sort');
                const currentOrder = url.searchParams.get('order') || 'asc';
                
                // Toggle order
                const newOrder = (currentSort === field && currentOrder === 'asc') ? 'desc' : 'asc';
                
                url.searchParams.set('sort', field);
                url.searchParams.set('order', newOrder);
                
                window.location.href = url.toString();
            }

            //FILTER

            function handleProductFilters() {
                showLoading()
                const url = new URL(window.location.href);

                const sku = document.getElementById('filter_product_id')?.value.trim() || '';
                const productName = document.getElementById('filter_product_name')?.value.trim() || '';
                const quantity = document.getElementById('filter_quantity')?.value.trim() || '';
                const basePrice = document.getElementById('filter_base_price')?.value.trim() || '';
                const sellingPrice = document.getElementById('filter_selling_price')?.value.trim() || '';
                const minStock = document.getElementById('filter_min_stock')?.value.trim() || '';

                if (sku !== '') url.searchParams.set('product_id', sku);
                else url.searchParams.delete('product_id');

                if (productName !== '') url.searchParams.set('product_name', productName);
                else url.searchParams.delete('product_name');

                if (quantity !== '') url.searchParams.set('quantity', quantity);
                else url.searchParams.delete('quantity');

                if (basePrice !== '') url.searchParams.set('base_price', unformatRupiah(basePrice));
                else url.searchParams.delete('base_price');

                if (sellingPrice !== '') url.searchParams.set('selling_price', unformatRupiah(sellingPrice));
                else url.searchParams.delete('selling_price');

                if (minStock !== '') url.searchParams.set('min_stock', minStock);
                else url.searchParams.delete('min_stock');

                // Reset ke halaman 1 biar hasil filter akurat
                url.searchParams.delete('page');

                // Redirect
                window.location.href = url.toString();
            }

            document.getElementById('filter_product_id').addEventListener('input', debouncedHandleProductFilters);
            document.getElementById('filter_product_name').addEventListener('input', debouncedHandleProductFilters);
            document.getElementById('filter_quantity').addEventListener('input', debouncedHandleProductFilters);
            document.getElementById('filter_base_price').addEventListener('input', debouncedHandleProductFilters);
            document.getElementById('filter_selling_price').addEventListener('input', debouncedHandleProductFilters);
            document.getElementById('filter_min_stock').addEventListener('input', debouncedHandleProductFilters);

            async function handleDeleteProduct(id) {
                url = `{{ route('products.delete', ':id') }}`;

                url = url.replace(':id', id);
                const del = await confirmDelete(url, `row-product-${id}`);
                if(del)location.reload()
            }

            //SAVE or UPDATE
            document.getElementById('productForm').addEventListener('submit', async function (e) {
                e.preventDefault();
                const form = e.target;
                const id = document.getElementById('productForm').dataset.id;

                showLoading();

                const data = {
                    product_id: form.product_id.value,
                    product_name: form.product_name.value,
                    stock: form.quantity.value,
                    base_price: unformatRupiah(form.base_price.value),
                    selling_price: unformatRupiah(form.selling_price.value),
                    min_stock: form.min_stock.value,
                };

                try {

                    let url = '';
                    let method = 'POST';

                    if (id) {

                        url = `{{ route('products.update', ':id') }}`;

                        url = url.replace(':id', id);

                        method = 'PUT';
                    } else {

                        url = `{{ route("products.store") }}`;
                    }

                    const res = await apiRequest(url, method, data);
                    if (res.code == 200) {
                        Toast.fire({ icon: 'success', title: res.message });

                        document.getElementById('productForm').reset();

                        hideLoading();

                        $("#exampleModal").modal('hide');

                        if (id) {

                            const row = document.getElementById(`row-product-${id}`);
                           
                            if (row) {
                                row.querySelector('.product_id').textContent = data.product_id;
                                row.querySelector('.product_name').textContent = data.product_name;
                                row.querySelector('.stock').textContent = data.stock;
                                row.querySelector('.base_price').textContent = formatRupiah(data.base_price);
                                row.querySelector('.selling_price').textContent = formatRupiah(data.selling_price);
                                row.querySelector('.min_stock').textContent = data.min_stock;
                            }
                        } else {

                            const newId = res.data.id;

                            

                            const tableBody = document.querySelector('#productTable tbody');

                            const emptyRow = tableBody.querySelector('.tr-empty');
                            if (emptyRow) {
                            emptyRow.remove();
                            }

                            const newRow = document.createElement('tr');
                            newRow.id = `row-product-${newId}`;
                            newRow.innerHTML = `
                                                <td class="product_id" style="position: sticky; left: 0; background: white; z-index: 10;">${data.product_id}</td>
                                                <td class="product_name">${data.product_name}</td>
                                                <td class="quantity">${data.stock}</td>
                                                <td class="base_price">${data.base_price}</td>
                                                <td class="selling_price">${data.selling_price}</td>
                                                <td class="min_stock">${data.min_stock}</td>
                                                <td style="position: sticky; right: 0; background: white; z-index: 5;">
                                                   <a onclick="editData(${newId})" href="#"><i class="text-secondary fs-5 bi bi-pencil-square"></i></a>
                                                    <a onclick="handleDeleteProduct(${newId})" href="#"><i class="text-secondary fs-5 bi bi-trash"></i></a>
                                                </td>
                                                `;
                            tableBody.appendChild(newRow);

                           updatePaginationCounter({})
                        }

                    } else {
                        hideLoading();
                        Toast.fire({ icon: 'error', title: res.message });
                    }
                } catch (err) {
                    hideLoading();
                    Toast.fire({ icon: 'error', title: err.message || 'Terjadi kesalahan!' });
                    return;
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Simpan';
                }

            });

            //EDIT
            async function editData(id) {
                showLoading()
                url = `{{ route('products.show', ':id') }}`;

                url = url.replace(':id', id);
                const res = await apiRequest(url);

                if (res.code == 200) {
                    document.getElementById('product_id').value = res.data.product_id || '';
                    document.getElementById('product_name').value = res.data.product_name || '';
                    document.getElementById('quantity').value = res.data.stock || '';
                    document.getElementById('base_price').value = formatRupiah(res.data.base_price) || '';
                    document.getElementById('selling_price').value = formatRupiah(res.data.selling_price) || '';
                    document.getElementById('min_stock').value = res.data.min_stock || '';

                    const form = document.getElementById('productForm');
                    form.dataset.id = res.data.id;


                    document.getElementById('exampleModalLabel').textContent = 'Edit Produk';
                    document.getElementById('submitBtn').textContent = 'Update';

                    hideLoading()
                    $('#exampleModal').modal('show')

                } else {
                    Toast.fire({ icon: 'error', title: err.message || 'Terjadi kesalahan!' });
                    hideLoading()
                    return;
                }


            }

            $('#exampleModal').on('hidden.bs.modal', function () {
            const form = document.getElementById('productForm');
            form.reset();
            
            delete form.dataset.id;
            
            document.getElementById('exampleModalLabel').textContent = 'Tambah Produk';
            document.getElementById('submitBtn').textContent = 'Simpan';
            });
    </script>
    @endsection