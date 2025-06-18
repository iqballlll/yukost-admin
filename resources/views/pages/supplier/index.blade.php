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

        z-index: 3;
    }

    .right-col {
        right: 0;
        z-index: 3;
    }

    th,
    td {
        white-space: nowrap;
    }
</style>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
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
            <table id="supplierTable" class="table table-lg table-stripped">
                <thead>
                    <tr class="border-0">
                        <th class="border-0" style="position: sticky; left: 0; background: white; z-index: 10;">
                            <div class="col">
                                <label class="form-label d-flex align-items-center justify-content-between">
                                    <span onclick="sortBy('supplier_name')" style="cursor: pointer;">
                                        Nama Supplier <i class="{{ $helpers::sortIcon('supplier_name') }} ms-2"></i>
                                    </span>
                                </label>
                                <input placeholder="Contoh : PT Makmur Saya" type="text" id="filter_supplier_name"
                                    name="filter_supplier_name" class="form-control" style="width: 220px;"
                                    value="{{ request('supplier_name') }}" />
                            </div>
                        </th>
                        <th class="border-0">
                            <div class="col">
                                <label class="form-label d-flex align-items-center justify-content-between">
                                    <span onclick="sortBy('address')" style="cursor: pointer;">
                                        Alamat <i class="{{ $helpers::sortIcon('address') }} ms-2"></i>
                                    </span>
                                </label>
                                <input placeholder="Contoh : Bekasi" type="text" id="filter_address"
                                    name="filter_address" value="{{ request('address') }}" class="form-control"
                                    width="100" style="width: 220px;" />
                            </div>
                        </th>
                        <th class="border-0">
                            <div class="col">
                                <label class="form-label d-flex align-items-center justify-content-between">
                                    <span onclick="sortBy('contact')" style="cursor: pointer;">
                                        Kontak <i class="{{ $helpers::sortIcon('contact') }} ms-2"></i>
                                    </span>
                                </label>
                                <input value="{{ request('contact') }}" type="tel" id="filter_contact"
                                    name="filter_contact" maxlength="14" placeholder="Contoh : 08xxxxxxxxxx"
                                    class="form-control" style="width: 220px;">
                            </div>
                        </th>
                        <th class="border-0">
                            <div class="col">
                                <label class="form-label d-flex align-items-center justify-content-between">
                                    <span onclick="sortBy('is_active')" style="cursor: pointer;">
                                        Status <i class="{{ $helpers::sortIcon('is_active') }} ms-2"></i>
                                    </span>
                                </label>
                                <select name="filter_is_active" id="filter_is_active" class="form-control">
                                    <option value="">-Pilih Status-</option>
                                    <option value="yes" {{ request('is_active')=='yes' ? 'selected' : '' }}>Aktif
                                    </option>
                                    <option value="no" {{ request('is_active')=="no" ? 'selected' : '' }}>Tidak Aktif
                                    </option>
                                </select>
                            </div>
                        </th>
                        <th class="border-0" style="position: sticky; right: 0; background: white; z-index: 10;">
                            <div class="d-flex flex-column justify-content-center align-items-center"
                                style="height: 100%;">
                                <label class="form-label mb-1">Aksi</label>
                                <a style="cursor: pointer"
                                    onclick="window.location.href=`{{ route('suppliers.index') }}`"><i
                                        class="bi bi-arrow-clockwise fs-4"></i></a>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($supplier as $s)


                    <tr id="row-supplier-{{ $s->id }}">
                        <td class="name" style="position: sticky; left: 0; background: white; z-index: 10;">
                            <span>{{$s->supplier_name}}</span>
                        </td>
                        <td class="address">
                            <span>{{$s->address}}</span>
                        </td>
                        <td class="contact">
                            <span>{{$s->contact}}</span>
                        </td>
                        <td class="is_active">
                            <span>{!! App\Helpers\AppHelpers::badgeIsActive($s->is_active, $s->is_active ? 'Aktif'
                                :
                                'Tidak Aktif')!!}</span>
                        </td>
                        <td style="position: sticky; right: 0; background: white; z-index: 5;">
                            <a onclick="editData(`{{ $s->id }}`)" href="#"><i
                                    class="text-secondary fs-5 bi bi-pencil-square"></i></a>
                            <a onclick="handleDeleteSupplier(`{{ $s->id }}`)" href="#"><i
                                    class="text-secondary fs-5 bi bi-trash"></i></a>
                        </td>

                    </tr>
                    @empty
                    <tr class="tr-empty">
                        <td class="text-center" colspan="5">Tidak ada data ditemukan</td>
                    </tr>

                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-info :paginator="$supplier" />
    </div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Supplier</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="supplierForm">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3 row">
                                            <label class="col-sm-4 col-form-label">Nama Supplier</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="supplier_name" id="supplier_name"
                                                    class="form-control" placeholder="Masukkan nama" maxlength="32">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-sm-4 col-form-label">Alamat</label>
                                            <div class="col-sm-8">
                                                <textarea name="address" id="address" class="form-control"
                                                    placeholder="Masukkan alamat"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">

                                        <div class="mb-3 row">
                                            <label class="col-sm-4 col-form-label">Kontak</label>
                                            <div class="col-sm-8">
                                                <input value="{{ request('contact') }}" type="tel" id="contact"
                                                    name="contact" maxlength="14" placeholder="Contoh : 08xxxxxxxxxx"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-sm-4 col-form-label">Status</label>
                                            <div class="col-sm-8">
                                                <select name="is_active" id="is_active" class="form-control">
                                                    <option value="">-Pilih Status-</option>
                                                    <option value="1">Aktif</option>
                                                    <option value="0">Tidak Aktif</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>


                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="submitBtn" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        const submitBtn = document.getElementById('submitBtn');
            let supplier = null;
            const debouncedHandleSupplierFilters = debounce(handleSupplierFilters, 900);

            //SORT
            
            function sortBy(field) {
                const url = new URL(window.location.href);
                const currentSort = url.searchParams.get('sort');
                const currentOrder = url.searchParams.get('order') || 'desc';
                
                // Toggle order
                const newOrder = (currentSort === field && currentOrder === 'asc') ? 'desc' : 'asc';
                
                url.searchParams.set('sort', field);
                url.searchParams.set('order', newOrder);
                
                window.location.href = url.toString();
            }

            function handleSupplierFilters() {
                showLoading()
                const url = new URL(window.location.href);

                const supplierName = document.getElementById('filter_supplier_name')?.value.trim() || '';
                const address = document.getElementById('filter_address')?.value.trim() || '';
                const contact = document.getElementById('filter_contact')?.value.trim() || '';
                const isActive = document.getElementById('filter_is_active')?.value ?? '';


                if (supplierName !== '') url.searchParams.set('supplier_name', supplierName);
                else url.searchParams.delete('supplier_name');

                if (address !== '') url.searchParams.set('address', address);
                else url.searchParams.delete('address');

                if (contact !== '') url.searchParams.set('contact', contact);
                else url.searchParams.delete('contact');

                if (isActive !== '') {
                    url.searchParams.set('is_active', isActive);
                } else {
                    url.searchParams.delete('is_active');
                }


                url.searchParams.delete('page');


                window.location.href = url.toString();
            }

            document.getElementById('filter_supplier_name').addEventListener('input', debouncedHandleSupplierFilters);
            document.getElementById('filter_address').addEventListener('input', debouncedHandleSupplierFilters);
            document.getElementById('filter_contact').addEventListener('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '')
                debouncedHandleSupplierFilters()
            }
            );
            document.getElementById('filter_is_active').addEventListener('change', debouncedHandleSupplierFilters);

            document.getElementById('supplierForm').addEventListener('submit', async function (e) {
                e.preventDefault();
                const form = e.target;
                const supplierId = document.getElementById('supplierForm').dataset.supplierId;

                showLoading();

                const data = {
                    supplier_name: form.supplier_name.value,
                    address: form.address.value,
                    contact: form.contact.value,
                    is_active: form.is_active.value
                };

                try {

                    let url = '';
                    let method = 'POST';



                    if (supplierId) {

                        url = `{{ route('suppliers.update', ':id') }}`;

                        url = url.replace(':id', supplierId);

                        method = 'PUT';
                    } else {

                        url = `{{ route("suppliers.store") }}`;
                    }

                    const res = await apiRequest(url, method, data);
                    if (res.code == 200) {
        
                        location.reload()

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

            document.getElementById('filter_contact').addEventListener('keydown', function (e) {

                const allowedKeys = ['Backspace', 'ArrowLeft', 'ArrowRight', 'Delete', 'Tab'];
                if (
                    !/[0-9]/.test(e.key) &&
                    !allowedKeys.includes(e.key)
                ) {
                    e.preventDefault();
                }
            });

            document.getElementById('filter_contact').addEventListener('paste', function (e) {
                const pasted = (e.clipboardData || window.clipboardData).getData('text');
                if (!/^\d+$/.test(pasted)) {
                    e.preventDefault();
                }
            });

            document.getElementById('filter_contact').addEventListener('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
            document.getElementById('contact').addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
            });

            async function editData(id) {
                showLoading()
                url = `{{ route('suppliers.show', ':id') }}`;

                url = url.replace(':id', id);
                const res = await apiRequest(url);

                if (res.code == 200) {
                    document.getElementById('supplier_name').value = res.data.supplier_name || '';
                    document.getElementById('address').value = res.data.address || '';
                    document.getElementById('contact').value = res.data.contact || '';
                    document.getElementById('is_active').value = res.data.is_active != null ? res.data.is_active.toString() : '';

                    const form = document.getElementById('supplierForm');
                    form.dataset.supplierId = res.data.id;


                    document.getElementById('exampleModalLabel').textContent = 'Edit Supplier';
                    document.getElementById('submitBtn').textContent = 'Update';

                    hideLoading()
                    $('#exampleModal').modal('show')

                } else {
                    Toast.fire({ icon: 'error', title: err.message || 'Terjadi kesalahan!' });
                    hideLoading()
                    return;
                }


            }

           async function handleDeleteSupplier(id) {
                url = `{{ route('suppliers.delete', ':id') }}`;

                url = url.replace(':id', id);
                const del = await confirmDelete(url, `row-supplier-${id}`);
                if(del) location.reload()
            }

            $('#exampleModal').on('hidden.bs.modal', function () {
                const form = document.getElementById('supplierForm');
                form.reset();

                delete form.dataset.supplierId;

                document.getElementById('exampleModalLabel').textContent = 'Tambah Supplier';
                document.getElementById('submitBtn').textContent = 'Simpan';
            });


    </script>
    @endsection