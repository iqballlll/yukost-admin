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
            <div class="col-sm-4 d-flex align-items-center">
                <label for="filterSupplier" class="me-3">Supplier</label>
                <select onchange="filterBySupplier(this)" name="filterSupplier" id="filterSupplier"
                    class="form-control">
                    <option value="">-Pilih Supplier-</option>
                    @foreach ($allSupplier as $s)
                    <option {{ request('supplier')==$s->id ? 'selected' : '' }} value="{{ $s->id }}">
                        {{$s->supplier_name}}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-4 d-flex align-items-center justify-content-end">
                <button onclick="location.href=`{{ route('purchases.create') }}`"
                    class="btn btn-primary">Tambah</button>
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

                                        <select name="filterTotalOperator" id="filterTotalOperator" class="form-control"
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
                                            name="filterTotalValue" value="{{ $totalValue }}" style="width: 110px"
                                            oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);"
                                            onkeydown="return event.key.length === 1 && !event.key.match(/[0-9]/) ? false : true"
                                            onchange="document.getElementById('filterForm').submit();" />
                                    </div>
                                </div>
                            </form>
                        </th>

                        <th class="border-0">
                            <div class="col">
                                <label class="form-label">Sudah Bayar</label>
                                <select onchange="applyFilters()" name="filterIsPay" id="filterIsPay"
                                    class="form-control">
                                    <option value="">-Pilih-</option>
                                    <option value="1" {{ ($filters['is_pay'] ?? '' )==='1' ? 'selected' : '' }}>Ya
                                    </option>
                                    <option value="0" {{ ($filters['is_pay'] ?? '' )==='0' ? 'selected' : '' }}>
                                        Belum</option>
                                </select>
                            </div>
                        </th>

                        <th class="border-0" style="position: sticky; right: 0; background: white; z-index: 10;">
                            <div class="d-flex flex-column justify-content-center align-items-center"
                                style="height: 100%;">
                                <label class="form-label mb-1">Aksi</label>
                                <a style="cursor: pointer" onclick="location.href=`{{ route('purchases.index') }}`"><i
                                        class="bi bi-arrow-clockwise fs-4"></i></a>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $s)

                    <tr>
                        <td class="bg-secondary py-3"
                            style="position: sticky; left: 0; background: white; z-index: 10;">
                            <span class="text-white">{{$s->supplier_name}}</span>
                        </td>
                        <td class="bg-secondary"></td>
                        <td class="bg-secondary"></td>
                        <td class="bg-secondary"></td>
                        <td class="bg-secondary"></td>
                        <td class="bg-secondary"></td>

                    </tr>
                    @forelse ($s->purchases as $sp)


                    <tr>
                        <td style="position: sticky; left: 0; background: #f8f9fa; z-index: 5;">
                            {{$helpers::toDateID($sp->order_date)}}
                        </td>
                        <td>{{$helpers::toDateID($sp->due_date)}}</td>
                        <td>{{$helpers::toDateID($sp->payment_date)}}</td>
                        <td>{{$helpers::formatToRupiah($sp->total_price)}}</td>
                        <td>{!! $helpers::badgeIsActive($sp->is_paid, $sp->is_paid ? 'Sudah' : 'Belum')!!}</td>
                        <td style="position: sticky; right: 0; background: #f8f9fa; z-index: 5;">
                            <a href="{{ route('purchases.edit', $sp->id) }}"><i
                                    class="text-secondary fs-5 bi bi-pencil-square"></i></a>
                            <a onclick="deletePurchase(`{{ $sp->id }}`)"><i
                                    class="text-secondary fs-5 bi bi-trash"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td style="position: sticky; left: 0; background: white; z-index: 10;">Tidak ada
                            data ditemukan
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    @endforelse
                    @empty
                    <tr>
                        <td style="position: sticky; left: 0; background: white; z-index: 10;">Tidak ada
                            data ditemukan
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-info :paginator="$suppliers" />
    </div>

    <script>
        $(function () {
                $('#filterDateOrder').daterangepicker({
                    locale: {
                        format: 'DD/MM/YYYY',
                        separator: ' - ',
                        applyLabel: 'Terapkan',
                        cancelLabel: 'Batal',
                        customRangeLabel: 'Rentang Khusus'
                    },
                    autoUpdateInput: false,
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




            });

            function filterBySupplier(select) {
                const supplierId = select.value;
                const url = new URL(window.location.href);

                if (supplierId) {
                    url.searchParams.set('supplier', supplierId);
                } else {
                    url.searchParams.delete('supplier');
                }

                window.location.href = url.toString();
            }

            function applyFilters() {
                const params = new URLSearchParams(window.location.search);

                const getVal = id => document.getElementById(id)?.value;

                // Tanggal-tanggal
                const orderRange = parseDateRange(getVal('filterDateOrder'));
                if (orderRange) params.append('order_date', orderRange);

                const dueRange = parseDateRange(getVal('filterDateJatuhTempo'));
                if (dueRange) params.append('due_date', dueRange);

                const paymentRange = parseDateRange(getVal('filterDateTanggalPembayaran'));
                if (paymentRange) params.append('payment_date', paymentRange);

                // Total harga
              

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

            function parseDateRange(dateStr) {
                if (!dateStr || !dateStr.includes(' - ')) return null;
                const [start, end] = dateStr.split(' - ').map(d => {
                    const [day, month, year] = d.trim().split('/');
                    return `${year}-${month}-${day}`;
                });
                return `${start}|${end}`;
            }

            async function deletePurchase(id) {
            const url = `{{ route('purchases.destroy', ':id') }}`.replace(':id', id);
            const x = await confirmDelete(url);
            
            if (x) {
            location.reload();
            }
            }
    </script>
    @endsection