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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@php
$helpers = \App\Helpers\AppHelpers::class;
@endphp
<div class="card">
    <div class="card-body py-4">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ request('tab') !== 'personal' ? 'active' : '' }}"
                    href="{{ route('customers.index', ['tab' => 'group']) }}">Grup</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ request('tab') === 'personal' ? 'active' : '' }}"
                    href="{{ route('customers.index', ['tab' => 'personal']) }}">Personal</a>
            </li>
        </ul>
        <div class="tab-content mt-5" id="myTabContent">
            @if(request('tab') == 'group')
            <div class="tab-pane fade show active" id="group" role="tabpanel" aria-labelledby="group-tab">
                <div class="d-flex justify-content-end mb-3">
                    <button onclick="location.href=`{{ route('customers.group.create') }}`"
                        class="btnAddGroup btn btn-primary">Tambah Customer Grup</button>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Grup</label>
                            <div class="col-sm-8">
                                <select name="filterGroup" id="filterGroup" class="form-control">
                                    <option value="">-Pilih Grup-</option>
                                    @foreach ($groupIds as $g)

                                    <option value="{{ $g->id }}">{{$g->group_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-2" style="overflow-x: auto">
                    <table class="table table-lg table-stripped">
                        <thead>
                            <tr class="border-0">
                                <th class="border-0" style="position: sticky; left: 0; background: white; z-index: 10;">
                                    <div class="col">
                                        <label class="form-label">Nama Outlet</label>
                                        <input type="text" id="filterOutlet" name="filterOutlet"
                                            value="{{ request('outlet') }}" class="form-control"
                                            style="width: 220px;" />
                                    </div>
                                </th>
                                <th class="border-0">
                                    <div class="col">
                                        <label class="form-label">Alamat</label>
                                        <input type="text" id="filterAddress" name="filterAddress"
                                            value="{{ request('address') }}" class="form-control" width="100"
                                            style="width: 220px;" />
                                    </div>
                                </th>
                                <th class="border-0">
                                    <div class="col">
                                        <label class="form-label">Kontak</label>
                                        <input type="tel" id="filterContact" name="filterContact"
                                            value="{{ request('contact') }}" class="form-control"
                                            style="width: 220px;" />
                                    </div>
                                </th>
                                <th class="border-0">
                                    <div class="col">
                                        <label class="form-label">Kustom Harga</label>
                                        <select name="filterCustomPrice" id="filterCustomPrice" class="form-control">
                                            <option value="">-Pilih-</option>
                                            <option value="1" {{ request('custom_price')=='1' ? 'selected' : '' }}>Ya
                                            </option>
                                            <option value="0" {{ request('custom_price')=='0' ? 'selected' : '' }}>Tidak
                                            </option>
                                        </select>
                                    </div>
                                </th>
                                <th class="border-0">
                                    <div class="col">
                                        <label class="form-label">Status</label>
                                        <select name="filterStatus" id="filterStatus" class="form-control"
                                            style="width: 100px;">
                                            <option value="">-Pilih-</option>
                                            <option value="1" {{ request('status')=='1' ? 'selected' : '' }}>Aktif
                                            </option>
                                            <option value="0" {{ request('status')=='0' ? 'selected' : '' }}>Tidak Aktif
                                            </option>
                                        </select>
                                    </div>
                                </th>
                                <th class="border-0"
                                    style="position: sticky; right: 0; background: white; z-index: 10;">
                                    <div class="d-flex flex-column justify-content-center align-items-center"
                                        style="height: 100%;">
                                        <label class="form-label mb-1">Aksi</label>
                                        <a style="cursor: pointer"
                                            onclick="location.href=`{{ route('customers.index') }}`"><i
                                                class="bi bi-arrow-clockwise fs-4"></i></a>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($groups as $g)
                            <tr>
                                <td
                                    style="background-color: #000040; position: sticky; left: 0; z-index: 10; max-width: 220px; width: 220px;">
                                    <span class="text-white"
                                        style="display: block; word-break: break-word; white-space: normal;">
                                        {{ $g->group_name }}
                                    </span>
                                </td>
                                <td style="background-color: #000040;" colspan="4"></td>
                                <td style="background-color: #000040; position: sticky; right: 0; z-index: 10;"
                                    class="text-end">
                                    <a class="btn btn-sm bg-white"
                                        onclick="location.href=`{{ route('customers.group.edit', ['group_id' => $g->id]) }}`">
                                        <i class="text-secondary bi bi-pencil-square"></i>
                                    </a>

                                    @if ($g->companies->isEmpty())
                                    <a class="btn btn-sm bg-white ms-2" onclick="deleteGroup(`{{ $g->id }}`)">
                                        <i class="text-danger bi bi-trash"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @forelse ($g->companies as $c)

                            <tr>
                                <td
                                    style="background: #f8f9fa;position: sticky; left: 0; z-index: 10; max-width: 220px; width: 220px;">
                                    <span style="display: block; word-break: break-word; white-space: normal;">
                                        {{ $c->company_name }}
                                    </span>
                                </td>
                                <td colspan="4" style="background: #f8f9fa;"></td>
                                <td style="background: #f8f9fa; position: sticky; right: 0; z-index: 10;">

                                </td>
                            </tr>
                            @forelse ($c->outlets as $o)
                            <tr>
                                <td style="background:white; position: sticky; left: 0; z-index: 5;">{{
                                    $o->outlet_name }}</td>
                                <td>{{ $o->address }}</td>
                                <td>{{ $o->contact }}</td>
                                <td>{!! App\Helpers\AppHelpers::badgeIsActive($o->custom_price, $o->custom_price ? 'Ya'
                                    : 'Tidak') !!}</td>
                                <td>{!! App\Helpers\AppHelpers::badgeIsActive($o->is_active, $o->is_active ? 'Aktif' :
                                    'Tidak Aktif') !!}</td>
                                <td style="position: sticky; right: 0; background: white; z-index: 10;"></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-3 text-start"
                                    style="position: sticky; left: 0; background: white; z-index: 10;">Tidak ada outlet
                                    ditemukan</td>
                            </tr>
                            @endforelse
                            @empty
                            <tr>
                                <th colspan="4" class="border-0 text-start"
                                    style="position: sticky; left: 0; background: white; z-index: 10;">
                                    <span>Tidak ada perusahaan ditemukan</span>
                                </th>
                            </tr>
                            @endforelse
                            @empty
                            <tr>
                                <td colspan="4" class="py-3 text-start"
                                    style="position: sticky; left: 0; background: white; z-index: 10;">Tidak ada grup
                                    ditemukan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
                <div class="mb-3">
                    <x-pagination-info :paginator="$groups" />
                </div>
            </div>
            @endif
            @if(request('tab') == 'personal')
            <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                <div class="d-flex justify-content-end mb-3">
                    <button type="button" onclick="location.href=`{{ route('customers.personal.create') }}`"
                        class="btnAddGroup btn btn-primary">Tambah
                        Customer Personal</button>
                </div>
                <div class="mt-2" style="overflow-x: auto">
                    <table class="table table-lg table-stripped">
                        <thead>
                            <tr class="border-0">
                                <th class="border-0" style="position: sticky; left: 0; background: white; z-index: 10;">
                                    <div class="col">
                                        <span onclick="sortBy('customer_name')" style="cursor: pointer;">
                                            Nama Customer <i class="{{ $helpers::sortIcon('customer_name') }} ms-2"></i>
                                        </span>
                                        <input type="text" id="customerNameFilter" name="customer_name"
                                            value="{{ request('customer_name') }}" class="form-control"
                                            style="width: 220px;" />
                                    </div>
                                </th>
                                <th class="border-0">
                                    <div class="col">
                                        <span onclick="sortBy('address')" style="cursor: pointer;">
                                            Alamat <i class="{{ $helpers::sortIcon('address') }} ms-2"></i>
                                        </span>
                                        <input type="text" id="customerPersonalAddressFilter" name="address" value=""
                                            class="form-control" width="100" style="width: 220px;" />
                                    </div>
                                </th>
                                <th class="border-0">
                                    <div class="col">
                                        <span onclick="sortBy('contact')" style="cursor: pointer;">
                                            Kontak <i class="{{ $helpers::sortIcon('contact') }} ms-2"></i>
                                        </span>
                                        <input type="text" id="customerPersonalContactFilter" name="contact" value=""
                                            class="form-control" style="width: 220px;" />
                                    </div>
                                </th>
                                <th class="border-0">
                                    <div class="col">
                                        <span onclick="sortBy('custom_price')" style="cursor: pointer;">
                                            Kustom Harga <i class="{{ $helpers::sortIcon('custom_price') }} ms-2"></i>
                                        </span>
                                        <select name="custom_price" id="customerPersonalCustomPriceFilter"
                                            class="form-control">
                                            <option value="">-Pilih-</option>
                                            <option value="1">Ya</option>
                                            <option value="0">Tidak</option>
                                        </select>
                                    </div>
                                </th>
                                <th class="border-0">
                                    <div class="col">
                                        <span onclick="sortBy('status')" style="cursor: pointer;">
                                            Status <i class="{{ $helpers::sortIcon('status') }} ms-2"></i>
                                        </span>
                                        <select name="status" id="customerPersonalStatusFilter" class="form-control"
                                            style="width: 100px;">
                                            <option value="">-Pilih-</option>
                                            <option value="1">Aktif</option>
                                            <option value="0">Tidak Aktif</option>
                                        </select>
                                    </div>
                                </th>
                                <th class="border-0"
                                    style="position: sticky; right: 0; background: white; z-index: 10;">
                                    <div class="d-flex flex-column justify-content-center align-items-center"
                                        style="height: 100%;">
                                        <label class="form-label mb-1">Aksi</label>
                                        <a style="cursor: pointer"
                                            onclick="window.location.href=`{{ route('customers.index', ['tab' => 'personal']) }}`"><i
                                                class="bi bi-arrow-clockwise fs-4"></i></a>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($personal as $p)

                            <tr>
                                <td style="position: sticky; left: 0; background: white; z-index: 10;">
                                    {{$p->outlet_name}}</td>
                                <td>{{$p->address}}</td>
                                <td>{{$p->contact}}</td>
                                <td>{!! $helpers::badgeIsActive($p->custom_price, $p->custom_price ? 'Ya' : 'Tidak')!!}
                                </td>
                                <td>{!! $helpers::badgeIsActive($p->is_active, $p->is_active ? 'Aktif' : 'Tidak
                                    Aktif')!!}
                                </td>

                                <td style="position: sticky; right: 0; background: white; z-index: 10;">
                                    <a style="cursor: pointer"
                                        onclick="location.href=`{{ route('customers.personal.edit', $p->id) }}`"><i
                                            class="text-secondary fs-5 bi bi-pencil-square"></i></a>
                                    <a style="cursor: pointer" onclick="deletePersonal(`{{ $p->id }}`)"><i
                                            class="text-secondary fs-5 bi bi-trash"></i></a>
                                </td>
                            </tr>
                            @empty
                            <td colspan="5">Tidak ada data ditemukan</td>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <x-pagination-info :paginator="$personal" />
            </div>
            @endif
        </div>

    </div>
</div>

<script>
    const btnAddCompany = document.querySelector('.btnAddCompany');
        const groupForm = document.querySelector('#groupForm');
        const btnSaveGroup = document.querySelector('#btnSaveGroup');
        const tbodyCompany = document.querySelector('.tableCompany tbody');
     
        const contact = document.getElementById('contact');
        const customerGroupId = document.getElementById('customer_group_id');
        const groupName = document.getElementById('group_name');
        const groupAddress = document.getElementById('address');
        const groupContact = document.getElementById('contact');
        
        @if(request('tab') == 'group')
        const filterContact = document.getElementById('filterContact');
        @endif

        //filter personal
        const customerNameFilter = document.getElementById('customerNameFilter');
        const customerPersonalAddress = document.getElementById('customerPersonalAddressFilter');
        const customerPersonalContact = document.getElementById('customerPersonalContactFilter');
        const customerPersonalCustomPrice = document.getElementById('customerPersonalCustomPriceFilter');
        const customerPersonalStatus = document.getElementById('customerPersonalStatusFilter');
     
        const debouncedHandleCustomerGroupFilters = debounce(handleCustomerGroupFilters, 900);
        const debouncedHandleCustomerPersonalFilters = debounce(handleCustomerPersonalFilters, 900);
        let groupId = null;

        document.addEventListener('DOMContentLoaded', function () {

            @if(request('tab') == 'group')
            filterContact.addEventListener('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '')
            });
            @endif
            
            customerPersonalContact?.addEventListener('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '')
            });
 
            $('#filterGroup').select2();
          
           
        });

        groupId = getQueryParam('group_id');
        
        
       
        
        if (groupId) {
        $('#filterGroup').val(groupId).trigger('change');
        }

        //DELETE
       async function deletePersonal(id){
        const baseUrl = `{{ route("customers.personal.delete", ":id") }}`;
        const finalUrl = baseUrl.replace(':id', id);
        const x = await confirmDelete(finalUrl, id)
        if(x){
        location.reload()
        }
        }

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

        async function deleteGroup(id){
            const baseUrl = `{{ route("customers.group.delete", ":id") }}`; 
            const finalUrl = baseUrl.replace(':id', id);
            const x = await confirmDelete(finalUrl, id)
            if(x){
                location.reload()
            }
        }

       function handleCustomerGroupFilters() {
            showLoading()
            const url = new URL(window.location.href);
            
            const filterGroupId = document.getElementById('filterGroup')?.value.trim() || '';
            const filterOutlet = document.getElementById('filterOutlet')?.value.trim() || '';
            const filterAddress = document.getElementById('filterAddress')?.value.trim() || '';
            const filterContact = document.getElementById('filterContact')?.value.trim() || '';
            const filterCustomPrice = document.getElementById('filterCustomPrice')?.value.trim() || '';
            const filterStatus = document.getElementById('filterStatus')?.value.trim() || '';
            
            if (filterGroupId !== '') url.searchParams.set('group_id', filterGroupId);
            else url.searchParams.delete('group_id');
            if (filterOutlet !== '') url.searchParams.set('outlet', filterOutlet);
            else url.searchParams.delete('outlet');
            if (filterAddress !== '') url.searchParams.set('address', filterAddress);
            else url.searchParams.delete('address');
            if (filterContact !== '') url.searchParams.set('contact', filterContact);
            else url.searchParams.delete('contact');
            if (filterCustomPrice !== '') url.searchParams.set('custom_price', filterCustomPrice);
            else url.searchParams.delete('custom_price');
            if (filterStatus !== '') url.searchParams.set('status', filterStatus);
            else url.searchParams.delete('status');
            
         
            url.searchParams.delete('page');
            
            window.location.href = url.toString();

        }

       function handleCustomerPersonalFilters() {
            showLoading()
            const url = new URL(window.location.href);
            
            const customerNameFilter = document.getElementById('customerNameFilter')?.value.trim() || '';
            const customerPersonalAddressFilter = document.getElementById('customerPersonalAddress')?.value.trim() || '';
            const customerPersonalContactFilter = document.getElementById('customerPersonalContact')?.value.trim() || '';
            const customerPersonalCustomPriceFilter = document.getElementById('customerPersonalCustomPrice')?.value.trim() || '';
            const customerPersonalStatusFilter = document.getElementById('customerPersonalStatus')?.value.trim() || '';
            
            if (customerNameFilter !== '') url.searchParams.set('customer_name', customerNameFilter);
            else url.searchParams.delete('customer_name');
            if (customerPersonalAddressFilter !== '') url.searchParams.set('address', customerPersonalAddressFilter);
            else url.searchParams.delete('address');
            if (customerPersonalContactFilter !== '') url.searchParams.set('contact', customerPersonalContactFilter);
            else url.searchParams.delete('contact');
            if (customerPersonalCustomPriceFilter !== '') url.searchParams.set('custom_price', customerPersonalCustomPriceFilter);
            else url.searchParams.delete('custom_price');
            if (customerPersonalStatusFilter !== '') url.searchParams.set('status', customerPersonalStatusFilter);
            else url.searchParams.delete('status');
            
         
            url.searchParams.delete('page');
            
            window.location.href = url.toString();

        }

        $('#filterGroup').on('change', debouncedHandleCustomerGroupFilters);
        
        @if(request('tab') == 'group')
        //filter group
        document.getElementById('filterOutlet').addEventListener('input', debouncedHandleCustomerGroupFilters);
        document.getElementById('filterAddress').addEventListener('input', debouncedHandleCustomerGroupFilters);
        document.getElementById('filterContact').addEventListener('input', debouncedHandleCustomerGroupFilters);
        document.getElementById('filterCustomPrice').addEventListener('change', debouncedHandleCustomerGroupFilters);
        document.getElementById('filterStatus').addEventListener('change', debouncedHandleCustomerGroupFilters);

        @endif

        @if(request('tab') == 'personal')
        //filter personal
        document.getElementById('customerNameFilter').addEventListener('input', debouncedHandleCustomerPersonalFilters);
        document.getElementById('customerPersonalAddressFilter').addEventListener('input', debouncedHandleCustomerPersonalFilters);
        document.getElementById('customerPersonalContactFilter').addEventListener('input', debouncedHandleCustomerPersonalFilters);
        document.getElementById('customerPersonalCustomPriceFilter').addEventListener('input', debouncedHandleCustomerPersonalFilters);
        document.getElementById('customerPersonalStatusFilter').addEventListener('input', debouncedHandleCustomerPersonalFilters);
       @endif
</script>
@endsection