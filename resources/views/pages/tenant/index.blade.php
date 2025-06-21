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
<div class="col-lg-12">
    <div class="card">
        <div class="card-body py-4">
            <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a onclick="navigateTab('approved')"
                        class="nav-link {{ request('tab', 'approved') === 'approved' ? 'active' : '' }}"
                        id="approved-tab" role="tab">Penyewa</a>
                </li>

                <li class="nav-item" role="presentation">
                    <a onclick="navigateTab('registered')"
                        class="nav-link {{ request('tab') === 'registered' ? 'active' : '' }}" id="registered-tab"
                        role="tab">Registrasi</a>
                </li>

            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="approved" role="tabpanel" aria-labelledby="approved-tab">
                    <div class="d-flex justify-content-between">
                        <div class="col-md-3 d-flex align-items-center gap-2">
                            <select style="width:80px" name="" id="" class="form-control">
                                <option value="">10</option>
                                <option value="">25</option>
                                <option value="">50</option>
                                <option value="">100</option>
                                <option value="">500</option>
                            </select>
                            <span>data per halaman</span>
                        </div>
                        <div class="col-md-3">
                            <input class="form-control" type="text" placeholder="Cari sesuatu">
                        </div>
                    </div>
                    <div class="table-responsive mt-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama Pengguna</th>
                                    <th>Email</th>
                                    <th>Nomor Telepon</th>
                                    <th>Persetujuan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tenants as $tenant)


                                <tr>
                                    <td>{{$tenant->name}}</td>
                                    <td>{{$tenant->email}}</td>
                                    <td>-</td>
                                    <td>
                                        <form method="POST" action="{{ route('tenants.status.update', $tenant->id) }}">
                                            @csrf
                                            @method('PUT')

                                            <select name="status" class="form-control" onchange="toggleAlasan(this)">
                                                <option value="">-- Pilih Status --</option>
                                                <option {{ $tenant->status == 'approved' ? 'selected' : '' }}
                                                    value="approved">Disetujui</option>
                                                <option {{ $tenant->status == 'rejected' ? 'selected' : '' }}
                                                    value="rejected">Ditolak</option>
                                            </select>

                                            <input type="text" name="alasan" class="form-control mt-2 d-none"
                                                placeholder="Masukkan alasan penolakan">

                                            <button type="submit" class="btn btn-primary mt-3">Kirim</button>
                                        </form>
                                    </td>


                                    <td>
                                        <button onclick="alert('on develop')" class="btn btn-primary"><i
                                                class="bi bi-pencil"></i></button>
                                        <button onclick="alert('on develop')" class="btn btn-danger"><i
                                                class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data ditemukan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center">
                            {{-- <nav aria-label="Page navigation example">
                                <ul class="pagination pagination-primary">
                                    <li class="page-item"><a class="page-link" href="#">
                                            <span aria-hidden="true"><i class="bi bi-chevron-left"></i></span>
                                        </a></li>
                                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                                    <li class="page-item active"><a class="page-link" href="#">2</a></li>
                                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                                    <li class="page-item"><a class="page-link" href="#">
                                            <span aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
                                        </a></li>
                                </ul>
                            </nav> --}}
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="tenant-registered" role="tabpanel"
                    aria-labelledby="tenant-registered-tab">
                    <div class="table-responsive">
                        <div class="d-flex justify-content-between">
                            <div class="col-md-3 d-flex align-items-center gap-2">
                                <select style="width:80px" name="" id="" class="form-control">
                                    <option value="">10</option>
                                    <option value="">25</option>
                                    <option value="">50</option>
                                    <option value="">100</option>
                                    <option value="">500</option>
                                </select>
                                <span>data per halaman</span>
                            </div>
                            <div class="col-md-3">
                                <input class="form-control" type="text" placeholder="Cari sesuatu">
                            </div>
                        </div>
                        <div class="table-responsive mt-4">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nama Pengguna</th>
                                        <th>Email</th>
                                        <th>Nomor Telepon</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($tenants as $tenant)


                                    <tr>
                                        <td>{{$tenant->name}}</td>
                                        <td>{{$tenant->email}}</td>
                                        <td>-</td>
                                        <td>
                                            <form method="POST"
                                                action="{{ route('tenants.status.update', $tenant->id) }}">
                                                @csrf
                                                @method('PUT')

                                                <select name="status" class="form-control"
                                                    onchange="toggleAlasan(this)">
                                                    <option value="">-- Pilih Status --</option>
                                                    <option {{ $tenant->status == 'approved' ? 'selected' : '' }}
                                                        value="approved">Disetujui</option>
                                                    <option {{ $tenant->status == 'rejected' ? 'selected' : '' }}
                                                        value="rejected">Ditolak</option>
                                                </select>

                                                <input type="text" name="alasan" class="form-control mt-2 d-none"
                                                    placeholder="Masukkan alasan penolakan">

                                                <button type="submit" class="btn btn-primary mt-3">Kirim</button>
                                            </form>
                                        </td>


                                        <td>
                                            <button onclick="alert('on develop')" class="btn btn-primary"><i
                                                    class="bi bi-pencil"></i></button>
                                            <button onclick="alert('on develop')" class="btn btn-danger"><i
                                                    class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data ditemukan</td>
                                    </tr>
                                    @endforelse
                                </tbody>

                            </table>
                            {{-- <div class="d-flex justify-content-center">
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination pagination-primary">
                                        <li class="page-item"><a class="page-link" href="#">
                                                <span aria-hidden="true"><i class="bi bi-chevron-left"></i></span>
                                            </a></li>
                                        <li class="page-item"><a class="page-link" href="#">1</a></li>
                                        <li class="page-item active"><a class="page-link" href="#">2</a></li>
                                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                                        <li class="page-item"><a class="page-link" href="#">
                                                <span aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
                                            </a></li>
                                    </ul>
                                </nav>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleAlasan(select) {
            const alasanInput = select.parentElement.querySelector('input[name="alasan"]');
            if (select.value === 'rejected') {
                alasanInput.classList.remove('d-none');
            } else {
                alasanInput.classList.add('d-none');
                alasanInput.value = ''; // kosongkan jika bukan ditolak
            }
        }

        function navigateTab(tabName) {
        const baseUrl = "{{ route('tenants.index') }}";
        window.location.href = `${baseUrl}?tab=${tabName}`;
        }
</script>
@endsection