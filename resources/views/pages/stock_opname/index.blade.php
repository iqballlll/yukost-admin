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
<div class="card">
    <div class="card-body pt-4 pb-1">
        <div class="mb-5 row d-flex justify-content-between">
            <div class="col-sm-12 d-flex align-items-center justify-content-end" data-bs-toggle="modal"
                data-bs-target="#exampleModal">
                <button class="btn btn-primary">Tambah</button>
            </div>
        </div>
        <div style="overflow-x: auto">
            <table class="table table-lg table-stripped">
                <thead>
                    <tr class="border-0">
                        <th class="border-0" style="position: sticky; left: 0; background: white; z-index: 10;">
                            <div class="col">
                                <label class="form-label">Tanggal Proses</label>
                                <input type="text" id="filterDateOrder" name="filterDateOrder" value=""
                                    class="form-control" style="width: 220px;" />
                            </div>
                        </th>
                        <th class="border-0">
                            <div class="col">
                                <label class="form-label">Stok Sistem</label>
                                <input type="text" id="filterDateJatuhTempo" name="filterDateJatuhTempo" value=""
                                    class="form-control" width="100" style="width: 220px;" />
                            </div>
                        </th>
                        <th class="border-0">
                            <div class="col">
                                <label class="form-label">Stok Gudang</label>
                                <input type="text" id="filterDateTanggalPembayaran" name="filterDateTanggalPembayaran"
                                    value="" class="form-control" style="width: 220px;" />
                            </div>
                        </th>
                        <th class="border-0">
                            <div class="col">
                                <label class="form-label">Selisih</label>
                                <div class="input-group" style="width:220px">
                                    <select name="" id="" class="form-control">
                                        <option value="">=</option>
                                        <option value="">
                                            < </option>
                                        <option value="">></option>
                                        <option value="">
                                            <= </option>
                                        <option value="">>=</option>
                                        <option value="">!=</option>
                                    </select>
                                    <input type="text" class="form-control" aria-label="Text input with dropdown button"
                                        style="width: 110px">
                                </div>
                            </div>
                        </th>

                        <th class="border-0">
                            <div class="col">
                                <label class="form-label">Status</label>
                                <select name="filterIsPay" id="filterIsPay" class="form-control">
                                    <option value="">-Pilih-</option>
                                    <option value="1">Ya</option>
                                    <option value="0">Belum</option>
                                </select>
                            </div>
                        </th>
                        <th class="border-0" style="position: sticky; right: 0; background: white; z-index: 10;">
                            <div class="d-flex flex-column justify-content-center align-items-center"
                                style="height: 100%;">
                                <label class="form-label mb-1">Aksi</label>
                                <a style="cursor: pointer" onclick="window.location.reload()"><i
                                        class="bi bi-arrow-clockwise fs-4"></i></a>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="position: sticky; left: 0; background: white; z-index: 10;">
                            <span>Sinar Mas Abadi</span>
                        </td>
                        <td>
                            <span>Sinar Mas Abadi</span>
                        </td>
                        <td>
                            <span>Sinar Mas Abadi</span>
                        </td>
                        <td>
                            <span>Sinar Mas Abadi</span>
                        </td>
                        <td>
                            <span>Sinar Mas Abadi</span>
                        </td>
                        <td style="position: sticky; right: 0; background: white; z-index: 5;">
                            <a href=""><i class="text-secondary fs-4 bi bi-printer"></i></a>
                            <a href=""><i class="text-secondary fs-4 bi bi-file-earmark-text"></i></a>
                            <a href=""><i class="text-secondary fs-4 bi bi-trash"></i></a>
                        </td>

                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Stock Opname</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">Tanggal Diproses</label>
                                        <div class="col-sm-8">
                                            <input type="date" name="processed_date" class="form-control" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3 row">
                                                <label class="col-sm-4 col-form-label">Diproses Oleh</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="processed_by" class="form-control"
                                                        disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3 row">
                                                <label class="col-sm-4 col-form-label">Petugas Gudang</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="warehouse_staff" class="form-control"
                                                        disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <hr>

                            <div class="table-responsive">
                                <table class="table table-lg table-stripped">
                                    <thead>
                                        <tr>
                                            <th>Nama Barang</th>
                                            <th>Stok Sistem</th>
                                            <th>Stok Gudang</th>
                                            <th>Selisih Stok</th>
                                            <th>Harga Dasar</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <td>a</td>
                                        <td>b</td>
                                        <td><input type="number" class="form-control" style="width:150px"></td>
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
        $(function() {
            $('#filterDateOrder').daterangepicker({
                opens: 'left'
                }, 
                function(start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });
            $('#filterDateJatuhTempo').daterangepicker({
                opens: 'left'
                }, 
                function(start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });
            $('#filterDateTanggalPembayaran').daterangepicker({
                opens: 'left'
                }, 
                function(start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });
        });
    </script>
    @endsection