@extends('layout.index')
@section('content')
<div class="row">
    <div class="col">
        <button onclick="location.href=`{{ route('customers.index', ['tab' => 'group']) }}`"
            class="btn btn-primary mb-3">Kembali</button>
    </div>
</div>
<div class="card">
    <div class="card-body py-4">

        <form action="{{ route('customers.outlets.storeOrUpdate') }}" method="POST">
            @csrf
            <input type="hidden" name="customer_outlet_id" id="customer_outlet_id" value="{{  $personal?->id }}">
            <input type="hidden" name="type" id="type" value="individual">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label">ID Customer</label>
                            <div class="col-sm-8">
                                <input maxlength="10" name="outlet_id" id="outlet_id"
                                    value="{{ old('outlet_id', $personal?->outlet_id ?? '') }}" type="text"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label">Nama Customer</label>
                            <div class="col-sm-8">
                                <input maxlength="32" name="outlet_name" id="customer_name"
                                    value="{{ old('outlet_name', $personal?->outlet_name ?? '') }}" type="text"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label">Alamat</label>
                            <div class="col-sm-8">
                                <textarea name="address" id="address"
                                    class="form-control">{{old('address', $personal?->address ?? '')}}</textarea>
                            </div>
                        </div>

                    </div>

                    <div class="col-md-6">
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label">Kontak</label>
                            <div class="col-sm-8">
                                <input name="contact" id="contact"
                                    value="{{ old('contact', $personal?->contact ?? '') }}" maxlength="14" type="tel"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label">Status</label>
                            <div class="col-sm-8">
                                <select name="is_active" id="status" class="form-control">
                                    <option value="">-Pilih-</option>
                                    <option value="1" {{ old('is_active', $personal?->is_active)===1 ? 'selected' : ''
                                        }}>Aktif</option>
                                    <option value="0" {{ old('is_active', $personal?->is_active)===0 ? 'selected' : ''
                                        }}>Tidak Aktif
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="d-grid">
                <button id="btnSaveGroup" type="submit" class="btn btn-primary ">{{request('customer_id') ? 'Perbarui'
                    : 'Simpan'}}</button>
            </div>
        </form>
        <div id="sectionCustomPrice" class="mt-3">
            <div class="form-check form-switch mb-3">

                <input onchange="customerPriceInitState(``, this)" class="form-check-input" type="checkbox" value=""
                    id="switchCustomPrice" switch>
                <label class="form-check-label" for="switchCustomPrice">Kustom Harga</label>
            </div>
            <div id="productContent">

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
        const kontraFaktur = document.querySelector('#kontraFaktur');
        const customPrice = document.querySelector('#customPrice');
        const outletIsActive = document.querySelector('#outletIsActive');

        window.addEventListener("DOMContentLoaded", function(){
         
        })

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

        document.getElementById('contact').addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '')
        });

</script>

@endsection