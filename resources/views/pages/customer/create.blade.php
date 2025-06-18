@extends('layout.index')
@section('content')
<div class="row">
    <div class="col">
        <button onclick="location.href=`{{ route('customers.index', ['tab' => 'group']) }}`"
            class="btn btn-primary mb-3">Kembali</button>
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
                <input type="hidden" name="customer_group_id" id="customer_group_id" value="{{ request('group_id') }}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">ID Grup</label>
                                <div class="col-sm-8">
                                    <input maxlength="10" name="group_id" id="group_id" type="text"
                                        value="{{ old('group_id', $customerGroup?->group_id ?? '') }}"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">Nama Grup</label>
                                <div class="col-sm-8">
                                    <input maxlength="32" name="group_name" id="group_name"
                                        value="{{ old('group_name', $customerGroup?->group_name ?? '') }}" type="text"
                                        class="form-control">
                                </div>
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">Kontak</label>
                                <div class="col-sm-8">
                                    <input name="contact" id=""
                                        value="{{ old('contact', $customerGroup?->contact ?? '') }}" maxlength="14"
                                        type="tel" class="form-control">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">Alamat</label>
                                <div class="col-sm-8">
                                    <textarea name="address" id="address"
                                        class="form-control">{{ old('address', $customerGroup?->address ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="d-grid">
                    <button id="btnSaveGroup" type="submit" class="btn btn-primary ">{{request('group_id') ? 'Perbarui'
                        : 'Simpan'}}</button>
                </div>
            </form>
        </div>
        {{-- COMPANY --}}
        <div class="card border border-secondary p-3">
            <div class="d-flex justify-content-between">
                <h5>Perusahaan</h5>
                <button class="btn btn-sm btn-primary btnAddCompany" {{ !request('group_id') ? 'disabled' : ''
                    }}>Tambah</button>
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
                                <th width="5%" class="border-0">
                                    <div class="col">
                                        <label class="form-label">Tukar Faktur</label>

                                    </div>
                                </th>
                                <th width="5%" class="border-0">
                                    <div class="col">
                                        <label class="form-label">Status</label>

                                    </div>
                                </th>
                                <th width="12%" class="border-0"
                                    style="position: sticky; right: 0; background: white; z-index: 10;">
                                    <div class="d-flex flex-column justify-content-center align-items-center"
                                        style="height: 100%;">
                                        <label class="form-label mb-1">Aksi</label>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($customerCompany as $c)

                            <tr>
                                <td style="position: sticky; left: 0; background: white; z-index: 10;">
                                    {{ $c->company_id }}
                                </td>
                                <td>{{ $c->company_name }}</td>
                                <td>{{ $c->address }}</td>
                                <td>{{ $c->contact }}</td>
                                <td>{!! App\Helpers\AppHelpers::badgeIsActive($c->invoice_exchange,
                                    $c->invoice_exchange
                                    ?
                                    'Ya'
                                    :
                                    'Tidak')!!}</td>
                                <td>{!! App\Helpers\AppHelpers::badgeIsActive($c->is_active, $c->is_active
                                    ?
                                    'Aktif'
                                    :
                                    'Tidak Aktif')!!}</td>
                                <td style="position: sticky; right: 0; background: white; z-index: 10;">

                                    <button data-company="{{ json_encode($c) }}"
                                        class="btn btn-sm btn-primary btnEditCompany"><i
                                            class="bi bi-pencil-square"></i></button>
                                    <button onclick="deleteCompany({{ $c->id }})" class="btn btn-sm btn-danger"><i
                                            class="bi bi-trash"></i></button>
                                </td>
                            </tr>

                            @empty
                            <tr class="tr-company-empty">
                                <td colspan="7" class="text-center">Tidak ada data ditemukan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- OUTLET --}}
        <div class="card border border-secondary p-3">
            <div class="d-flex justify-content-between">
                <h5>Outlet</h5>
                <button class="btn btn-sm btn-primary btnAddOutlet" {{ $customerCompany->isNotEmpty() ? '' : 'disabled'
                    }}>Tambah</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="tableOutlet table table-lg table-stripped ">
                        <thead>
                            <tr class="border-0">
                                <th class="border-0" style="position: sticky; left: 0; background: white; z-index: 10;">
                                    <label class="form-label">Nama Perusahaan</label>
                                </th>
                                <th class="border-0"><label class="form-label">ID Outlet</label></th>
                                <th class="border-0"><label class="form-label">Nama Outlet</label></th>
                                <th class="border-0"><label class="form-label">Alamat</label></th>
                                <th class="border-0"><label class="form-label">Kontak</label></th>
                                <th class="border-0"><label class="form-label">Kustom Harga</label></th>
                                <th class="border-0"><label class="form-label">Status</label></th>
                                <th class="border-0 text-center"
                                    style="white-space: nowrap; position: sticky; right: 0; background: white; z-index: 10;">
                                    <label class="form-label mb-1">Aksi</label>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($customerOutlet as $o)
                            <tr>
                                <td style="position: sticky; left: 0; background: white; z-index: 10;">
                                    {{ $o->company?->company_name }}
                                </td>
                                <td>{{$o->outlet_id}}</td>
                                <td>{{$o->outlet_name}}</td>
                                <td>{{$o->address}}</td>
                                <td>{{$o->contact}}</td>
                                <td>{!! App\Helpers\AppHelpers::badgeIsActive($o->custom_price, $o->custom_price
                                    ?
                                    'Ya'
                                    :
                                    'Tidak')!!}</td>
                                <td>{!! App\Helpers\AppHelpers::badgeIsActive($o->is_active, $o->is_active
                                    ?
                                    'Aktif'
                                    :
                                    'Tidak Aktif')!!}</td>

                                <td style="position: sticky; right: 0; background: white; z-index: 10;">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <button onclick="editProductPrice(`{{ json_encode($o) }}`)"
                                            class="btn btn-sm btn-secondary">
                                            <i class="bi bi-file-earmark-plus"></i>
                                        </button>
                                        <button data-outlet="{{ json_encode($o) }}"
                                            class="btn btn-sm btn-primary btnEditOutlet">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button onclick="deleteOutlet({{ $o->id }})" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr class="tr-outlet-empty">
                                <td colspan="6" class="text-center">Tidak ada data ditemukan</td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Company -->
<div class="modal fade" id="companyModal" tabindex="-1" aria-labelledby="companyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="companyModalLabel">Tambah Perusahaan</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('customers.company.storeOrUpdate') }}" method="POST">
                @csrf
                <input type="hidden" name="group_id" id="group_id" value="{{ request('group_id') }}">
                <input type="hidden" name="customer_company_id" id="customerCompanyId"
                    value="{{ request('customerCompanyId') }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="companyId">ID Perusahaan</label>
                                <input type="text" id="companyId" class="form-control" placeholder="ID Perusahaan"
                                    name="company_id" maxLength="10" value="{{ old('company_id') }}">
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="companyName">Nama Perusahaan</label>
                                <input maxlength="60" type="text" id="companyName" class="form-control"
                                    placeholder="Nama Perusahaan" name="company_name" value="{{ old('company_name') }}">
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="companyAddress">Alamat</label>
                                <textarea name="address" id="companyAddress"
                                    class="form-control">{{ old('address') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="companyContact">Kontak</label>
                                <input maxlength="15" type="text" id="companyContact" class="form-control"
                                    name="contact" placeholder="Kontak" value="{{ old('contact') }}">
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="tukarFaktur">Tukar Faktur</label>
                                <select name="invoice_exchange" id="invoiceExchange" class="form-control">
                                    <option value="">-Pilih Tukar Faktur-</option>
                                    <option value="1" {{ old('invoice_exchange')==='1' ? 'selected' : '' }}>Ya</option>
                                    <option value="0" {{ old('invoice_exchange')==='0' ? 'selected' : '' }}>Tidak
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="is_active" id="companyIsActive" class="form-control">
                                    <option value="">-Pilih Status-</option>
                                    <option value="1" {{ old('is_active')==='1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ old('is_active')==='0' ? 'selected' : '' }}>Tidak Aktif
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button id="btnSaveCompany" type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Add Outlet -->
<div class="modal fade" id="outletModal" tabindex="-1" aria-labelledby="outletModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="outletModalLabel">Tambah Outlet</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('customers.outlets.storeOrUpdate') }}" method="POST">
                @csrf
                <input type="hidden" name="customer_outlet_id" id="customerOutletId"
                    value="{{ request('customerOutletId') }}">
                <input type="hidden" name="type" id="type" value="company">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="outletCompanyId">Perusahaan</label>
                                <select name="company_id" id="outletCompanyId" class="form-control" required>
                                    <option value="">-Pilih Perusahaan-</option>
                                    @foreach ($companies as $c)
                                    <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="outletId">ID Outlet</label>
                                <input maxlength="10" type="text" id="outletId" class="form-control"
                                    placeholder="ID Outlet" name="outlet_id" value="{{ old('outlet_id') }}">
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="outletName">Nama Outlet</label>
                                <input maxlength="32" type="text" id="outletName" class="form-control"
                                    name="outlet_name" placeholder="Nama Outlet" value="{{ old('outlet_name') }}">

                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="outletAddress">Alamat</label>
                                <textarea name="address" id="outletAddress" placeholder="Alamat Lengkap"
                                    class="form-control">{{ old('address') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="outletContact">Kontak</label>
                                <input type="tel" id="outletContact" class="form-control" name="contact"
                                    placeholder="Wajib diawali dengan 08" value="{{ old('contact') }}" maxlength="15"
                                    pattern="^08[0-9]{7,13}$"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15);">
                            </div>
                        </div>
                        <div class=" col-md-6 col-12">
                            <div class="form-group">
                                <label for="customPrice">Kustom Harga</label>
                                <br>
                                <span>Tidak</span>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="is_active" id="outletIsActive" class="form-control">
                                    <option value="">-Pilih Status-</option>
                                    <option value="1" {{ old('is_active')==='1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ old('is_active')==='0' ? 'selected' : '' }}>Tidak Aktif
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button id="btnSaveOutlet" type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Kustom Harga Group -->
<div class="modal fade" id="modalCustomPriceGroup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Kustom Harga <span id="outletLabel"></span></h1>
                <button type="button" class="btn-close" onclick="closeCustomModal()" aria-label="Close"></button>
            </div>
            <div id="modalProductContent" class="modal-body">

            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>

<script>
    // modal
        let modalCustomPriceGroup = null;
        let companyModal = null;
        let outletModal = null;

        //label
        const companyModalLabel = document.querySelector("#companyModalLabel")
        const outletModalLabel = document.querySelector("#outletModalLabel")

        //button
        const btnAddCompany = document.querySelector('.btnAddCompany');
        const btnEditCompany = document.querySelector('.btnEditCompany');
        const btnAddOutlet = document.querySelector('.btnAddOutlet');
        const btnSaveCompany = document.querySelector('#btnSaveCompany');
        const btnSaveOutlet = document.querySelector('#btnSaveOutlet');

        //field input company
        const companyId = document.querySelector('#companyId');
        const companyName = document.querySelector('#companyName');
        const companyAddress = document.querySelector('#companyAddress');
        const companyIsActive = document.querySelector('#companyIsActive');
        const companyContact = document.querySelector('#companyContact');
        const invoiceExchange = document.querySelector('#invoiceExchange');
        
        //field input outlet
        const outletCompanyId = document.querySelector('#outletCompanyId');
        const outletId = document.querySelector('#outletId');
        const outletName = document.querySelector('#outletName');
        const outletAddress = document.querySelector('#outletAddress');
        const outletContact = document.querySelector('#outletContact');
      
        const customPrice = document.querySelector('#customPrice');
        const outletIsActive = document.querySelector('#outletIsActive');



        // element input type phone number
        const numericFields = [companyContact, outletContact];

        numericFields.forEach(restrictToNumericInput);

       

        const customPriceValues = {};

        function lockCustomPrice(button, customerPriceData) {
            const wrapper = button.closest('.custom-price-wrapper');
            const input = wrapper.querySelector('.custom-price-input');
            const productId = input.dataset.id;
            const price = input.value;
            const newCustPrice = JSON.parse(customerPriceData)
       
          
            if (price) {
                customPriceValues[productId] = price;
                Toast.fire({ icon: 'success', title: input.disabled == true ? 'Harga dibuka!' : 'Harga dikunci!' });
                input.disabled = !input.disabled;
                button.innerHTML = `<i class="bi bi-unlock-fill"></i>`;

                if(input.disabled == true){
                    location.href = `{{ route('customers.custom_prices.updateSellingPrice') }}?id=${newCustPrice.id}&selling_price=${unformatRupiah(price)}`
                }

            } else {
                Toast.fire({ icon: 'warning', title: 'Isi harga terlebih dahulu' });
            }
        }

        function restoreSellingPrice(id){
            location.href = `{{ route('customers.custom_prices.restoreSellingPrice')
            }}?id=${id}`
        }

        document.addEventListener('DOMContentLoaded', function () {
            companyModal = new bootstrap.Modal(document.getElementById('companyModal'))
            outletModal = new bootstrap.Modal(document.getElementById('outletModal'))
            modalCustomPriceGroup = new bootstrap.Modal(document.getElementById('modalCustomPriceGroup'))
        });

        btnAddCompany.addEventListener('click', () => showCompanyInputForm());

        btnAddOutlet.addEventListener('click', () => showOutletInputForm());

        document.querySelectorAll('.btnEditCompany').forEach(btn => {
            btn.addEventListener('click', function () {
                const dataStr = this.dataset.company;
                const companyData = JSON.parse(dataStr);

                showCompanyInputForm(companyData);
            });
        });

        document.querySelectorAll('.btnEditOutlet').forEach(btn => {
            btn.addEventListener('click', function () {
                const dataStr = this.dataset.outlet;
                const outletData = JSON.parse(dataStr);
                showOutletInputForm(outletData);
            });
        });

        function customerPriceInitState(outletId, dat){
         const isChecked = dat.checked ? 1 : 0;
            location.href = `{{ route('customers.custom_prices.insertInitCustomPrice')
            }}?outlet_id=${outletId}&custom_price=${isChecked}`;
        }

        function syncProduct(outletId){
            location.href = `{{ route('customers.custom_prices.syncProducts')
            }}?outlet_id=${outletId}`;
        }


        
        
        function editProductPrice(dat) {
            //outletData
            let newData = JSON.parse(dat)
            document.querySelector('#outletLabel').innerHTML = newData.outlet_name

            loadProducts(`{{ route('customers.ajaxGetCustomPrice') }}`, newData, true);

        }

    

        async function loadProducts(url, newData, isInitialLoad = false) {

            if (modalCustomPriceGroup == null) {
                modalCustomPriceGroup = new bootstrap.Modal(document.getElementById('modalCustomPriceGroup'))
            }

            showLoading()
            const urlWithParams = url.includes('?')
                ? `${url}&is_custom_price=${newData.custom_price}&id=${newData.id}`
                : `${url}?is_custom_price=${newData.custom_price}&id=${newData.id}`;
            fetch(urlWithParams, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    hideLoading()
                    if (!response.ok) {
                        throw new Error('Gagal fetch data')
                    };
                    return response.text();
                })
                .then(html => {
                    hideLoading()
                    document.getElementById('modalProductContent').innerHTML = html;

                    bindPaginationLinks(newData);

                  

                    initCustomPriceInputs();

                    if (isInitialLoad && modalCustomPriceGroup) {
                        modalCustomPriceGroup.show();
                    }


                })
                .catch(err => {
                    hideLoading()
                    Toast.fire({ icon: 'error', title: 'Terjadi kesalahan!' });
                    // FOR DEBUG
                    // Toast.fire({ icon: 'error', title: err.message });
                    console.log(err.message)
                    return;
                });

        }

        function closeCustomModal() {

            if (modalCustomPriceGroup) {
                modalCustomPriceGroup.hide();
            }
        }

        function bindPaginationLinks(newData) {
            let filterTimeout;
            const container = document.getElementById('modalProductContent');
            const filterInputs = container.querySelectorAll('#sku, #productName, #basePrice, #sellingPrice');

            const paginationLinks = container.querySelectorAll('.pagination a');
            paginationLinks.forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const urlObj = new URL(this.href);

                    loadProducts(urlObj.toString(), newData, false);

                });
            });

            filterInputs.forEach(input => {
                input.addEventListener('input', () => {
                    clearTimeout(filterTimeout);
                    filterTimeout = setTimeout(() => {
                    applyCustomPriceFilters(newData);
                    }, 500);
                });
            });
        }

        function applyCustomPriceFilters(newData) {
            const container = document.getElementById('modalProductContent');
            
            const sku = container.querySelector('#sku')?.value.trim();
            const productName = container.querySelector('#productName')?.value.trim();
            const basePrice = container.querySelector('#basePrice')?.value.trim();
            const sellingPrice = container.querySelector('#sellingPrice')?.value.trim();
            
            const params = new URLSearchParams();
            
            if (sku) params.append('sku', sku);
            if (productName) params.append('product_name', productName);
            if (basePrice) params.append('base_price', basePrice);
            if (sellingPrice) params.append('selling_price', sellingPrice);
            
            const baseUrl = `{{ route('customers.index') }}`;
            const finalUrl = `${baseUrl}?${params.toString()}`;
            
            loadProducts(finalUrl, newData, true);
        }

        function initCustomPriceInputs() {
            const container = document.getElementById('modalProductContent');
            const customPrices = container.querySelectorAll('#customPrice');
            customPrices.forEach(link => {
                handleRupiahFormat(link);
            });
        }

        function showOutletInputForm(data = null) {

            if(data){
                customerOutletId.value = data.id
                outletCompanyId.value = data.company_id
                outletId.value = data.outlet_id
                outletName.value = data.outlet_name
                outletAddress.value = data.address
                outletContact.value = data.contact
           
                outletIsActive.value = data.is_active
            }
            
            if(data){
            btnSaveOutlet.innerHTML = 'Update';
            outletModalLabel.innerHTML = 'Update Outlet';
            } else {
            btnSaveOutlet.innerHTML = 'Tambah';
            companyModalLabel.innerHTML = 'Tambah Outlet';
            
            }
            
            outletModal.show()

           
        }

        function showCompanyInputForm(data = null) {

            if(data){
                customerCompanyId.value = data.id || '';
                companyId.value = data.company_id;
                companyName.value = data.company_name;
                companyAddress.value = data.address;
                companyIsActive.value = data.is_active;
                companyContact.value = data.contact;
                invoiceExchange.value = data.invoice_exchange;
            }

            if(data){
                btnSaveCompany.innerHTML = 'Update';
                companyModalLabel.innerHTML = 'Update Perusahaan';
            } else {
                btnSaveCompany.innerHTML = 'Tambah';
                companyModalLabel.innerHTML = 'Tambah Perusahaan'; 

            } 
           
            companyModal.show()
           
        }

        async function deleteCompany(companyId) {
            const baseUrl = `{{ route("customers.company.delete", ":id") }}`;
            const finalUrl = baseUrl.replace(':id', companyId);
            const x = await confirmDelete(finalUrl, companyId)
            if (x) {
                location.reload()
            }
        }

        async function deleteOutlet(outletId) {
            const baseUrl = `{{ route("customers.outlets.delete", ":id") }}`;
            const finalUrl = baseUrl.replace(':id', outletId);
            const x = await confirmDelete(finalUrl, outletId)
            if (x) {
                location.reload()
            }
        }

</script>
@if(session('show_modal'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
                companyModal.show();
            });
</script>
@endif
@if(session('show_modal_outlet'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
                outletModal.show();
            });
</script>
@endif
@if(session('show_modal_custom_price'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const outletData = @json(session('outlet'));
       loadProducts(`{{ route('customers.ajaxGetCustomPrice') }}`, outletData, true);
            });
</script>
@endif
@endsection