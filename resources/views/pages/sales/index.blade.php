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
<style>
    .rotate-icon {

        display: inline-block;
        /* penting agar transform bisa bekerja */
        transition: transform 0.3s ease;
    }

    .rotate {
        transform: rotate(90deg);
    }
</style>
<div class="card">
    <div class="card-body py-4">
        <div class="mb-5 row d-flex justify-content-between">
            <form method="GET" action="{{ route('sales.index') }}" class="col-sm-8 row">
                <div class="col-sm-11 d-flex align-items-center">
                    <label for="filter_perusahaan" class="me-3">Customer</label>
                    <div class="input-group">
                        <select style="width:110px;text-align:center" name="filter_perusahaan" id="filter_perusahaan"
                            class="form-control">
                            <option value="">-Perusahaan-</option>
                            {{-- Tambahkan data perusahaan --}}
                            @foreach ($companies as $c)
                            <option value="{{ $c->id }}" {{ request('filter_perusahaan')==$c->id ? 'selected' : '' }}>
                                {{$c->company_name}}
                            </option>
                            @endforeach
                        </select>

                        <select name="filter_operator" id="filter_operator" class="form-control text-center">
                            <option value="and" {{ request('filter_operator')=='and' ? 'selected' : '' }}>dan</option>
                            <option value="or" {{ request('filter_operator')=='or' ? 'selected' : '' }}>atau</option>
                        </select>

                        <select style="width:110px;text-align:center" name="filter_outlet" id="filter_outlet"
                            class="form-control">
                            <option value="">-Outlet-</option>
                            {{-- Tambahkan data outlet --}}
                            @foreach ($outlets as $o)
                            <option value="{{ $o->id }}" {{ request('filter_outlet')==$o->id ? 'selected' : '' }}>
                                {{$o->outlet_name}}
                            </option>
                            @endforeach
                        </select>

                        <button type="button" onclick="location.href=`{{ route('sales.index') }}`"
                            class="btn btn-outline-secondary">Reset</button>
                        <button type="submit" class="btn btn-outline-primary">Terapkan</button>
                    </div>
                </div>
            </form>

            <div class="col-sm-4 d-flex align-items-center justify-content-end">
                <a href="{{ route('sales.create') }}" class="btn btn-primary">Tambah</a>
            </div>
        </div>
        <div style="overflow-x: auto">
            <table class="table table-lg table-stripped">
                <thead>
                    <tr class="border-0">
                        <th class="border-0" style="position: sticky; left: 0; background: white; z-index: 10;">
                            <div class="col">
                                <label class="form-label">Tanggal Order</label>
                                <input type="text" id="filterDateOrder" name="filterDateOrder"
                                    value="{{ str_replace('|', ' - ', $filters['order_date'] ?? '') }}"
                                    class="form-control" style="width: 220px;" />
                            </div>
                        </th>
                        <th class="border-0">
                            <div class="col">
                                <label class="form-label">Jatuh Tempo</label>
                                <input type="text" id="filterDateJatuhTempo" name="filterDateJatuhTempo"
                                    value="{{ str_replace('|', ' - ', $filters['due_date'] ?? '') }}"
                                    class="form-control" width="100" style="width: 220px;" />
                            </div>
                        </th>

                        <th class="border-0">
                            <div class="col">
                                <label class="form-label">Tanggal Pembayaran</label>
                                <input type="text" id="filterDateTanggalPembayaran" name="filterDateTanggalPembayaran"
                                    value="{{ str_replace('|', ' - ', $filters['payment_date'] ?? '') }}"
                                    class="form-control" style="width: 220px;" />
                            </div>
                        </th>

                        <th class="border-0">
                            <form method="GET" id="filterForm">
                                <div class="col">
                                    <label class="form-label">Total Harga</label>
                                    <div class="input-group" style="width:220px">
                                        @php
                                        $totalOperator = $filters['total_operator'] ?? '';
                                        $totalValue = $filters['total_value'] ?? '';
                                        @endphp

                                        <select name="total_operator" id="filterTotalOperator" class="form-control"
                                            onchange="document.getElementById('filterForm').submit();">
                                            <option value="" {{ $totalOperator=='' ? 'selected' : '' }}>-Pilih-</option>
                                            <option value="=" {{ $totalOperator=='=' ? 'selected' : '' }}>=</option>
                                            <option value="<" {{ $totalOperator=='<' ? 'selected' : '' }}>&lt;</option>
                                            <option value=">" {{ $totalOperator=='>' ? 'selected' : '' }}>&gt;</option>
                                            <option value="<=" {{ $totalOperator=='<=' ? 'selected' : '' }}>&lt;=
                                            </option>
                                            <option value=">=" {{ $totalOperator=='>=' ? 'selected' : '' }}>&gt;=
                                            </option>
                                            <option value="!=" {{ $totalOperator=='!=' ? 'selected' : '' }}>!=</option>
                                        </select>

                                        <input type="number" min="0" max="9999999999" class="form-control"
                                            name="total_value" value="{{ $totalValue }}" style="width: 110px"
                                            oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);document.getElementById('filterForm').submit();"
                                            onkeydown="return event.key.length === 1 && !event.key.match(/[0-9]/) ? false : true" />
                                    </div>
                                </div>
                            </form>
                        </th>
                        <th class="border-0">
                            <form method="GET" id="filterDiscountForm">
                                <div class="col">
                                    <label class="form-label">Diskon</label>
                                    <div class="input-group" style="width:220px">
                                        {{-- <select name="discount_operator" id="filterDiscountOperator"
                                            class="form-control"
                                            onchange="document.getElementById('filterDiscountForm').submit();">
                                            @php
                                            $discountOperator = $filters['discount_operator'] ?? '';
                                            @endphp
                                            <option value="" {{ $discountOperator=='' ? 'selected' : '' }}>-Pilih-
                                            </option>
                                            <option value="=" {{ $discountOperator=='number' ? 'selected' : '' }}>
                                                Rp
                                            </option>
                                            <option value="=" {{ $discountOperator=='percentage' ? 'selected' : '' }}>
                                                %
                                            </option>
                                        </select> --}}
                                        <input type="text" class="form-control" name="discount_value"
                                            id="filterDiscountValue" value="{{ $filters['discount_value'] ?? '' }}"
                                            style="width: 110px"
                                            oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);document.getElementById('filterDiscountForm').submit();"
                                            onkeydown="return event.key.length === 1 && !event.key.match(/[0-9]/) ? false : true" />
                                    </div>
                                </div>
                            </form>
                        </th>

                        <th class="border-0">
                            <form method="GET" id="filterTotalPriceAfterDiscountForm">
                                <div class="col">
                                    <label class="form-label">Total Harga Setelah Diskon</label>
                                    <div class="input-group" style="width:220px">
                                        {{-- <select name="discount_operator" id="filterDiscountOperator"
                                            class="form-control"
                                            onchange="document.getElementById('filterTotalPriceAfterDiscountForm').submit();">
                                            @php
                                            $discountOperator = $filters['discount_operator'] ?? '';
                                            @endphp
                                            <option value="" {{ $discountOperator=='' ? 'selected' : '' }}>-Pilih-
                                            </option>
                                            <option value="=" {{ $discountOperator=='number' ? 'selected' : '' }}>
                                                Rp
                                            </option>
                                            <option value="=" {{ $discountOperator=='percentage' ? 'selected' : '' }}>
                                                %
                                            </option>
                                        </select> --}}
                                        <input type="text" class="form-control" name="total_price_after_discount"
                                            id="filterTotalPriceAfterDiscount"
                                            value="{{ $filters['total_price_after_discount'] ?? '' }}"
                                            style="width: 110px"
                                            oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);document.getElementById('filterTotalPriceAfterDiscountForm').submit();"
                                            onkeydown="return event.key.length === 1 && !event.key.match(/[0-9]/) ? false : true" />
                                    </div>
                                </div>
                            </form>
                        </th>
                        <th class="border-0">
                            <div class="col">
                                <label class="form-label">Kirim Invoice/Faktur</label>
                                <select name="filterSendInvoice" id="filterSendInvoice" class="form-control">
                                    <option value="">-Pilih-</option>
                                    <option value="1" {{ ($filters['send_invoice'] ?? '' )==='1' ? 'selected' : '' }}>Ya
                                    </option>
                                    <option value="0" {{ ($filters['send_invoice'] ?? '' )==='0' ? 'selected' : '' }}>
                                        Tidak</option>
                                </select>
                            </div>
                        </th>

                        <th class="border-0">
                            <div class="col">
                                <label class="form-label">Sudah Bayar</label>
                                <select name="filterIsPay" id="filterIsPay" class="form-control">
                                    <option value="">-Pilih-</option>
                                    <option value="1" {{ ($filters['is_paid'] ?? '' )==='1' ? 'selected' : '' }}>Ya
                                    </option>
                                    <option value="0" {{ ($filters['is_paid'] ?? '' )==='0' ? 'selected' : '' }}>
                                        Belum</option>
                                </select>
                            </div>
                        </th>
                        <th class="border-0" style="position: sticky; right: 0; background: white; z-index: 10;">
                            <div class="d-flex flex-column justify-content-center align-items-center"
                                style="height: 100%;">
                                <label class="form-label mb-1">Aksi</label>
                                <a style="cursor: pointer" onclick="location.href=`{{ route('sales.index') }}`"><i
                                        class="bi bi-arrow-clockwise fs-4"></i></a>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sales as $companyIndex => $company)
                    @php $companyCollapseId = 'collapse-company-' . $companyIndex; @endphp

                    {{-- Baris utama perusahaan --}}
                    <tr class="bg-secondary fw-bold" style="cursor:pointer;"
                        onclick="toggleCollapse('{{ $companyCollapseId }}', this)">
                        <td class="text-white" style="position: sticky; left: 0; z-index: 10;">
                            <i class="bi bi-chevron-right me-2 rotate-icon"></i> {{ $company['company'] }}
                        </td>
                        <td colspan="8"></td>
                    </tr>

                    {{-- Container row (bukan tr collapse langsung) --}}
                    <tr class="collapse-row">
                        <td colspan="9" class="p-0">
                            <div class="collapse" id="{{ $companyCollapseId }}">
                                <table class="table mb-0">
                                    <tbody>
                                        @foreach ($company['outlets'] as $outletIndex => $outlet)
                                        @php $outletCollapseId = 'collapse-outlet-' . $companyIndex . '-' .
                                        $outletIndex; @endphp

                                        {{-- Outlet --}}
                                        <tr style="cursor:pointer;"
                                            onclick="toggleCollapse('{{ $outletCollapseId }}', this)">
                                            <td style="position: sticky; left: 0; background: #f8f9fa; z-index: 10;">
                                                <i class="bi bi-chevron-right me-2 rotate-icon"></i> {{ $outlet['name']
                                                }}
                                            </td>
                                            <td colspan="8" style="background: #f8f9fa;"></td>
                                        </tr>

                                        {{-- Transaksi container --}}
                                        <tr>
                                            <td colspan="9" class="p-0">
                                                <div class="collapse" id="{{ $outletCollapseId }}">
                                                    <table class="table mb-0">
                                                        <tbody>
                                                            @foreach ($outlet['sales'] as $trans)
                                                            <tr>
                                                                <td
                                                                    style="position: sticky; left: 0; background: white; z-index: 5;">
                                                                    {{
                                                                    \Carbon\Carbon::parse($trans['order_date'])->format('d
                                                                    M Y') ??
                                                                    '-' }}
                                                                </td>
                                                                <td>{{
                                                                    \Carbon\Carbon::parse($trans['due_date'])->format('d
                                                                    M Y') ?? '-'
                                                                    }}</td>
                                                                <td>{{
                                                                    \Carbon\Carbon::parse($trans['payment_date'])->format('d
                                                                    M Y') ??
                                                                    '-' }}</td>
                                                                <td>Rp {{ number_format($trans['total_price'], 0, ',',
                                                                    '.') }}</td>
                                                                <td>
                                                                    @if ($trans['discount_amount'])
                                                                    Rp {{ number_format($trans['discount_amount'], 0,
                                                                    ',', '.') }}
                                                                    @else
                                                                    Tidak ada diskon
                                                                    @endif
                                                                </td>
                                                                <td>Rp {{
                                                                    number_format($trans['total_price_after_discount'],
                                                                    0, ',',
                                                                    '.') }}</td>
                                                                <td>{!! $helpers::badgeIsActive($trans['tukar_faktur'],
                                                                    $trans['tukar_faktur'] ? 'Ya' : 'Tidak') !!}</td>
                                                                <td>{!! $helpers::badgeIsActive($trans['is_active'],
                                                                    $trans['is_active']
                                                                    ? 'Ya' : 'Tidak') !!}</td>
                                                                <td
                                                                    style="position: sticky; right: 0; background: white; z-index: 5;">
                                                                    <a href="{{ route('sales.edit', $trans['id']) }}"><i
                                                                            class="text-secondary fs-5 bi bi-pencil-square"></i></a>
                                                                    <a style="cursor: pointer"
                                                                        onclick="deleteTransaction(`{{ $trans['id'] }}`)"><i
                                                                            class="text-secondary fs-5 bi bi-trash"></i></a>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
        <x-pagination-info :paginator="$sales" />
    </div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Penjualan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">Nama Customer</label>
                                        <div class="col-sm-8">
                                            <select name="" class="form-control">
                                                <option value="">-Pilih Customer-</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">Tanggal Pemesanan</label>
                                        <div class="col-sm-8">
                                            <input type="date" name="order_date" class="form-control">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">Total Harga</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="total_price" class="form-control" disabled>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">Tanggal Jatuh Tempo</label>
                                        <div class="col-sm-8">
                                            <input type="date" name="due_date" class="form-control">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">Tanggal Bayar</label>
                                        <div class="col-sm-8">
                                            <input type="date" name="payment_date" class="form-control">
                                        </div>
                                    </div>
                                    <div class="mb-3 row d-flex align-items-center">
                                        <label class="col-sm-4 col-form-label">Sudah Tukar Faktur</label>
                                        <div class="col-sm-8">
                                            <input type="checkbox" id="paid_status">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary">Tambah</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-lg table-stripped">
                                    <thead>
                                        <tr>
                                            <th>Nama Barang</th>
                                            <th>Harga Barang</th>
                                            <th>Quantity</th>
                                            <th>Harga Total</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>


                                        </tr>
                                        <td>a</td>
                                        <td>b</td>
                                        <td>c</td>
                                        <td>d</td>
                                        <td>e</td>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
                const filterInputs = document.querySelectorAll(
                    '#filterDateOrder, #filterDateJatuhTempo, #filterDateTanggalPembayaran, #filterSendInvoice, #filterIsPay, #filterCustomerPerusahaan, #filterCustomerOperator, #filterCustomerOutlet'
                );

                filterInputs.forEach(input => {
                    input.addEventListener('change', applyFilters);
                });


            });


            $(function () {
                $('#filterDateOrder').daterangepicker({
                    opens: 'left',
                    autoUpdateInput: false,
                    locale: {
                        format: 'DD/MM/YYYY',
                        separator: ' - ',
                        applyLabel: 'Terapkan',
                        cancelLabel: 'Batal',
                        customRangeLabel: 'Rentang Khusus'
                    },
                    showDropdowns: true,

                });


                $('#filterDateJatuhTempo').daterangepicker({
                    autoUpdateInput: false,
                    locale: {
                        format: 'DD/MM/YYYY',
                        separator: ' - ',
                        applyLabel: 'Terapkan',
                        cancelLabel: 'Batal',
                        customRangeLabel: 'Rentang Khusus'
                    },
                });
                $('#filterDateTanggalPembayaran').daterangepicker({
                    autoUpdateInput: false,
                    locale: {
                        format: 'DD/MM/YYYY',
                        separator: ' - ',
                        applyLabel: 'Terapkan',
                        cancelLabel: 'Batal',
                        customRangeLabel: 'Rentang Khusus'
                    },
                });

                $('#filterDateOrder').on('cancel.daterangepicker', function (ev, picker) {
                    $(this).val('');
                });
                $('#filterDafilterDateJatuhTempoteOrder').on('cancel.daterangepicker', function (ev, picker) {
                    $(this).val('');
                });
                $('#filterDateTanggalPembayaran').on('cancel.daterangepicker', function (ev, picker) {
                    $(this).val('');
                });
            });

            $('#filterDateOrder, #filterDateJatuhTempo, #filterDateTanggalPembayaran').on('apply.daterangepicker', function (ev,
                picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                applyFilters();
            });

            $('#filterDateOrder, #filterDateJatuhTempo, #filterDateTanggalPembayaran').on('cancel.daterangepicker', function (ev,
                picker) {
                $(this).val('');
                applyFilters();
            });

            async function deleteTransaction(id) {
                const url = `{{ route('sales.destroy', ':id') }}`.replace(':id', id);
                const x = await confirmDelete(url);

                if (x) {
                    location.reload();
                }
            }

            function parseDateRange(dateStr) {
                if (!dateStr || !dateStr.includes(' - ')) return null;
                const [start, end] = dateStr.split(' - ').map(d => {
                    const [day, month, year] = d.trim().split('/');
                    return `${year}-${month}-${day}`;
                });
                return `${start}|${end}`;
            }

            function applyFilters() {

                const params = new URLSearchParams();

                const getVal = id => document.getElementById(id)?.value;

                // Tanggal-tanggal
                const orderRange = parseDateRange(getVal('filterDateOrder'));
                if (orderRange) params.append('order_date', orderRange);

                const dueRange = parseDateRange(getVal('filterDateJatuhTempo'));
                if (dueRange) params.append('due_date', dueRange);

                const paymentRange = parseDateRange(getVal('filterDateTanggalPembayaran'));
                if (paymentRange) params.append('payment_date', paymentRange);

                // Total harga
                if (getVal('filterTotalOperator') && getVal('filterTotalValue')) {
                    params.append('total_op', getVal('filterTotalOperator'));
                    params.append('total_val', getVal('filterTotalValue'));
                }

                // Total diskon
                if (getVal('filterDiscountOperator') && getVal('filterDiscountValue')) {
                    params.append('discount_op', getVal('filterDiscountOperator'));
                    params.append('discount_val', getVal('filterDiscountValue'));
                }

                // Status invoice & pembayaran
                if (getVal('filterSendInvoice')) params.append('send_invoice', getVal('filterSendInvoice'));
                if (getVal('filterIsPay')) params.append('is_paid', getVal('filterIsPay'));

                // Customer (perusahaan, outlet, operator)
                if (getVal('filterCustomerPerusahaan')) params.append('company_id', getVal('filterCustomerPerusahaan'));
                if (getVal('filterCustomerOutlet')) params.append('outlet_id', getVal('filterCustomerOutlet'));
                if (getVal('filterCustomerOperator')) params.append('customer_logic', getVal('filterCustomerOperator'));



                // Redirect with new query string
                window.location.href = `${window.location.pathname}?${params.toString()}`;
            }

            function toggleCollapse(id, row) {
            const target = document.getElementById(id);
            const icon = row.querySelector('.rotate-icon');
                
           
            if (!target) return;
            
            const bsCollapse = new bootstrap.Collapse(target, {
            toggle: false
            });
            
            if (target.classList.contains('show')) {
            bsCollapse.hide();
            icon?.classList.remove('rotate');
            } else {
            bsCollapse.show();
            icon?.classList.add('rotate');
            }
            }
    </script>


    @endsection