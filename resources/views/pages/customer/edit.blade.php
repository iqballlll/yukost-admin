@extends('layout.index')
@section('content')
<div class="row">
    <div class="col">
        <button onclick="location.href=`{{ route('customers.index') }}`" class="btn btn-primary mb-3">Kembali</button>
    </div>
</div>
<div class="card">
    <div class="card-body pt-4 pb-1">
        <div class="alert alert-danger">Untuk mengisi perusahaan dan outlet, silakan simpan GRUP terlebih
            dahulu</div>
        <div class="card border border-secondary p-3">
            <h5>Grup</h5>
            <form action="{{ route('customers.group.storeOrUpdate') }}" method="POST">
                @csrf
                <input type="hidden" name="customer_group_id" id="customer_group_id" value="{{ $customerGroup->id }}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">ID Grup</label>
                                <div class="col-sm-8">
                                    <input maxlength="10" name="group_id" id="group_id" type="text" class="form-control"
                                        value="{{ $customerGroup->group_id }}">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">Nama Grup</label>
                                <div class="col-sm-8">
                                    <input maxlength="255" name="group_name" id="group_name" type="text"
                                        class="form-control" value="{{ $customerGroup->group_name }}">
                                </div>
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">Kontak</label>
                                <div class="col-sm-8">
                                    <input maxlength="15" name="contact" id="contact" maxlength="14" type="tel"
                                        class="form-control" value="{{ $customerGroup->contact }}">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">Alamat</label>
                                <div class="col-sm-8">
                                    <textarea name="address" id="address"
                                        class="form-control">{{ $customerGroup->address }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="d-grid">
                    <button id="btnSaveGroup" type="submit" class="btn btn-primary ">Update</button>
                </div>
            </form>
        </div>
        {{-- COMPANY --}}
        <div class="card border border-secondary p-3">
            <div class="d-flex justify-content-between">
                <h5>Perusahaan</h5>
                <button class="btn btn-sm btn-primary btnAddCompany">Tambah</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="tableCompany table table-lg table-stripped">
                        <thead>
                            <tr class="border-0">
                                <th class="border-0" style="position: sticky; left: 0; background: white; z-index: 10;">
                                    <div class="col">
                                        <label class="form-label">ID Perusahaan</label>
                                    </div>
                                </th>
                                <th class="border-0">
                                    <div class="col">
                                        <label class="form-label">Nama Perusahaan</label>
                                    </div>
                                </th>
                                <th class="border-0">
                                    <div class="col">
                                        <label class="form-label">Alamat</label>

                                    </div>
                                </th>
                                <th class="border-0">
                                    <div class="col">
                                        <label class="form-label">Kontak</label>

                                    </div>
                                </th>
                                <th class="border-0">
                                    <div class="col">
                                        <label class="form-label">Tukar Faktur</label>

                                    </div>
                                </th>
                                <th class="border-0">
                                    <div class="col">
                                        <label class="form-label">Status</label>

                                    </div>
                                </th>
                                <th class="border-0"
                                    style="position: sticky; right: 0; background: white; z-index: 10;">
                                    <div class="d-flex flex-column justify-content-center align-items-center"
                                        style="height: 100%;">
                                        <label class="form-label mb-1">Aksi</label>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($customerCompanies as $cc)
                            <tr>
                                <td style="position: sticky; left: 0; background: white; z-index: 10;">
                                    {{ $cc->company_id }}
                                </td>
                                <td>{{ $cc->company_name }}</td>
                                <td>{{ $cc->address }}</td>
                                <td>{{ $cc->contact }}</td>
                                <td><span class="badge bg-{{ $cc->invoice_exchange ? 'success' : 'danger' }}">
                                        {{$cc->invoice_exchange ? 'Ya' : 'Tidak'}}</span></td>
                                <td>{!! \App\Helpers\AppHelpers::badgeIsActive($cc->is_active, $cc->is_active ?
                                    'Aktif'
                                    :
                                    'Tidak Aktif') !!}</td>
                                <td style="position: sticky; right: 0; background: white; z-index: 10;">
                                    <button class="btn btn-sm btn-primary"><i class="bi bi-pencil-square"></i></button>
                                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr class="tr-company-empty">
                                <td colspan="6" class="text-center">Tidak ada data ditemukan</td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                    <x-pagination-info :paginator="$customerCompanies" />
                </div>
            </div>
        </div>

        {{-- OUTLET --}}
        <div class="card border border-secondary p-3">
            <div class="d-flex justify-content-between">
                <h5>Outlet</h5>
                <button class="btn btn-sm btn-primary btnAddOutlet">Tambah</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-lg table-stripped">
                        <thead>
                            <tr class="border-0">
                                <th class="border-0" style="position: sticky; left: 0; background: white; z-index: 10;">
                                    <div class="col">
                                        <label class="form-label">Nama Perusahaan</label>

                                    </div>
                                </th>
                                <th class="border-0">
                                    <div class="col">
                                        <label class="form-label">Nama Outlet</label>

                                    </div>
                                </th>
                                <th class="border-0">
                                    <div class="col">
                                        <label class="form-label">Alamat</label>

                                    </div>
                                </th>
                                <th class="border-0">
                                    <div class="col">
                                        <label class="form-label">Kontak</label>

                                    </div>
                                </th>
                                <th class="border-0">
                                    <div class="col">
                                        <label class="form-label">Status</label>
                                    </div>
                                </th>
                                <th class="border-0"
                                    style="position: sticky; right: 0; background: white; z-index: 10;">
                                    <div class="d-flex flex-column justify-content-center align-items-center"
                                        style="height: 100%;">
                                        <label class="form-label mb-1">Aksi</label>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="position: sticky; left: 0; background: white; z-index: 10;">
                                    <input type="text" id="filterDateOrder" name="filterOutlet" value=""
                                        class="form-control" style="width: 220px;" />
                                </td>
                                <td>
                                    <input type="text" id="filterDateJatuhTempo" name="filterAddress" value=""
                                        class="form-control" width="100" style="width: 220px;" />
                                </td>
                                <td><textarea rows="1" id="filterDateOrder" name="filterOutlet" value=""
                                        class="form-control" style="width: 220px;"></textarea></td>
                                <td><input type="text" class="form-control" style="width: 180px;"></td>
                                <td><select name="filterSendInvoice" style="width: 180px;" id="filterSendInvoice"
                                        class="form-control">
                                        <option value="">-Pilih-</option>
                                        <option value="1">Aktif</option>
                                        <option value="0">Tidak Aktif</option>
                                    </select></td>
                                <td style="position: sticky; right: 0; background: white; z-index: 10;">
                                    <button class="btn btn-sm btn-success"><i class="bi bi-check2"></i></button>
                                    <button class="btn btn-sm btn-danger"><i class="bi bi-x-lg"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td style="position: sticky; left: 0; background: white; z-index: 10;">
                                    a
                                </td>
                                <td>b</td>
                                <td>c</td>
                                <td>d</td>
                                <td>e</td>
                                <td style="position: sticky; right: 0; background: white; z-index: 10;">

                                    <button data-bs-toggle="modal" data-bs-target="#modalCustomPriceGroup"
                                        class="btn btn-sm btn-secondary">
                                        <i class="bi bi-file-earmark-plus"></i>
                                    </button>
                                    <button class="btn btn-sm btn-primary"><i class="bi bi-pencil-square"></i></button>
                                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            <tr class="tr-empty">
                                <td colspan="6" class="text-center">Tidak ada data ditemukan</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection