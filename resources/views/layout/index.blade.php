<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Mazer Admin Dashboard</title>



    <link rel="shortcut icon" href="{{ asset('assets/compiled/svg/favicon.svg') }}" type="image/x-icon">
    <link rel="shortcut icon"
        href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACEAAAAiCAYAAADRcLDBAAAEs2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS41LjAiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgeG1sbnM6ZXhpZj0iaHR0cDovL25zLmFkb2JlLmNvbS9leGlmLzEuMC8iCiAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIKICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIgogICAgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIKICAgZXhpZjpQaXhlbFhEaW1lbnNpb249IjMzIgogICBleGlmOlBpeGVsWURpbWVuc2lvbj0iMzQiCiAgIGV4aWY6Q29sb3JTcGFjZT0iMSIKICAgdGlmZjpJbWFnZVdpZHRoPSIzMyIKICAgdGlmZjpJbWFnZUxlbmd0aD0iMzQiCiAgIHRpZmY6UmVzb2x1dGlvblVuaXQ9IjIiCiAgIHRpZmY6WFJlc29sdXRpb249Ijk2LjAiCiAgIHRpZmY6WVJlc29sdXRpb249Ijk2LjAiCiAgIHBob3Rvc2hvcDpDb2xvck1vZGU9IjMiCiAgIHBob3Rvc2hvcDpJQ0NQcm9maWxlPSJzUkdCIElFQzYxOTY2LTIuMSIKICAgeG1wOk1vZGlmeURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiCiAgIHhtcDpNZXRhZGF0YURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiPgogICA8eG1wTU06SGlzdG9yeT4KICAgIDxyZGY6U2VxPgogICAgIDxyZGY6bGkKICAgICAgc3RFdnQ6YWN0aW9uPSJwcm9kdWNlZCIKICAgICAgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWZmaW5pdHkgRGVzaWduZXIgMS4xMC4xIgogICAgICBzdEV2dDp3aGVuPSIyMDIyLTAzLTMxVDEwOjUwOjIzKzAyOjAwIi8+CiAgICA8L3JkZjpTZXE+CiAgIDwveG1wTU06SGlzdG9yeT4KICA8L3JkZjpEZXNjcmlwdGlvbj4KIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+Cjw/eHBhY2tldCBlbmQ9InIiPz5V57uAAAABgmlDQ1BzUkdCIElFQzYxOTY2LTIuMQAAKJF1kc8rRFEUxz9maORHo1hYKC9hISNGTWwsRn4VFmOUX5uZZ36oeTOv954kW2WrKLHxa8FfwFZZK0WkZClrYoOe87ypmWTO7dzzud97z+nec8ETzaiaWd4NWtYyIiNhZWZ2TvE946WZSjqoj6mmPjE1HKWkfdxR5sSbgFOr9Ll/rXoxYapQVik8oOqGJTwqPL5i6Q5vCzeo6dii8KlwpyEXFL519LjLLw6nXP5y2IhGBsFTJ6ykijhexGra0ITl5bRqmWU1fx/nJTWJ7PSUxBbxJkwijBBGYYwhBgnRQ7/MIQIE6ZIVJfK7f/MnyUmuKrPOKgZLpEhj0SnqslRPSEyKnpCRYdXp/9++msneoFu9JgwVT7b91ga+LfjetO3PQ9v+PgLvI1xkC/m5A+h7F32zoLXug38dzi4LWnwHzjeg8UGPGbFfySvuSSbh9QRqZ6H+Gqrm3Z7l9zm+h+iafNUV7O5Bu5z3L/wAdthn7QIme0YAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAJTSURBVFiF7Zi9axRBGIefEw2IdxFBRQsLWUTBaywSK4ubdSGVIY1Y6HZql8ZKCGIqwX/AYLmCgVQKfiDn7jZeEQMWfsSAHAiKqPiB5mIgELWYOW5vzc3O7niHhT/YZvY37/swM/vOzJbIqVq9uQ04CYwCI8AhYAlYAB4Dc7HnrOSJWcoJcBS4ARzQ2F4BZ2LPmTeNuykHwEWgkQGAet9QfiMZjUSt3hwD7psGTWgs9pwH1hC1enMYeA7sKwDxBqjGnvNdZzKZjqmCAKh+U1kmEwi3IEBbIsugnY5avTkEtIAtFhBrQCX2nLVehqyRqFoCAAwBh3WGLAhbgCRIYYinwLolwLqKUwwi9pxV4KUlxKKKUwxC6ZElRCPLYAJxGfhSEOCz6m8HEXvOB2CyIMSk6m8HoXQTmMkJcA2YNTHm3congOvATo3tE3A29pxbpnFzQSiQPcB55IFmFNgFfEQeahaAGZMpsIJIAZWAHcDX2HN+2cT6r39GxmvC9aPNwH5gO1BOPFuBVWAZue0vA9+A12EgjPadnhCuH1WAE8ivYAQ4ohKaagV4gvxi5oG7YSA2vApsCOH60WngKrA3R9IsvQUuhIGY00K4flQG7gHH/mLytB4C42EgfrQb0mV7us8AAMeBS8mGNMR4nwHamtBB7B4QRNdaS0M8GxDEog7iyoAguvJ0QYSBuAOcAt71Kfl7wA8DcTvZ2KtOlJEr+ByyQtqqhTyHTIeB+ONeqi3brh+VgIN0fohUgWGggizZFTplu12yW8iy/YLOGWMpDMTPXnl+Az9vj2HERYqPAAAAAElFTkSuQmCC"
        type="image/png">

    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/iconly.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/extensions/sweetalert2/sweetalert2.min.css') }}">
    <style>
        .select2-container--default .select2-selection--single {
            height: 38px !important;
            border: 1px solid #dce7f1 !important;
            border-radius: 4px !important;
            padding: 8px 12px !important;
            box-sizing: border-box;
            color: #607080;
            display: flex;
            align-items: center;
        }


        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 0 !important;
            line-height: normal !important;
            color: #607080 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            display: none !important;
        }


        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100% !important;
            top: 0 !important;
        }
    </style>
    <script src="{{ asset('assets/helper.js') }}"></script>

</head>

<body>
    <script src="{{ asset('assets/static/js/initTheme.js') }}"></script>
    <div id="app">
        <div id="sidebar">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header position-relative">
                    <div class="d-flex justify-content-center align-items-center">
                        <div>
                            <a href="index.html">
                                <div>
                                    <a href="index.html">
                                        <img src="{{ asset('assets/images/logo.png') }}" alt=""
                                            style="height: 50px; width: auto;">
                                    </a>
                                </div>
                            </a>
                        </div>

                        <div class="sidebar-toggler  x">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-item {{ request()->segment(1) == '' ? 'active' : '' }}">
                            <a href="{{ route('dashboard') }}" class='sidebar-link'>
                                <i style="color: {{ request()->segment(1) == '' ? '#000040' : 'black' }}"
                                    class="bi bi-grid-fill"></i>
                                <span
                                    style="color: {{ request()->segment(1) == '' ? '#000040' : 'black' }}">Dashboard</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ request()->segment(1) == 'sales' ? 'active' : '' }}">
                            <a href="{{ route('sales.index') }}" class='sidebar-link'>
                                <i style="color: {{ request()->segment(1) == 'sales' ? '#000040' : 'black' }}"
                                    class="bi bi-people-fill"></i>
                                <span
                                    style="color:{{ request()->segment(1) == 'sales' ? '#000040' : 'black' }}">Pengguna</span>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->segment(1) == 'purchases' ? 'active' : '' }}">
                            <a href="{{ route('purchases.index') }}" class='sidebar-link'>
                                <i style="color: {{ request()->segment(1) == 'purchases' ? '#000040' : 'black' }}"
                                    class="bi bi-cart-plus"></i>
                                <span
                                    style="color:{{ request()->segment(1) == 'purchases' ? '#000040' : 'black' }}">Pembelian</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ request()->segment(1) == 'stock-opnames' ? 'active' : '' }}">
                            <a href="{{ route('stock-opnames.index') }}" class='sidebar-link'>
                                <i style="color: {{ request()->segment(1) == 'stock-opnames' ? '#000040' : 'black' }}"
                                    class="bi bi-box-seam"></i>
                                <span
                                    style="color:{{ request()->segment(1) == 'stock-opnames' ? '#000040' : 'black' }}">Stock
                                    Opname</span>
                            </a>
                        </li>

                        <li class="sidebar-item has-sub">
                            <a href="#" class='sidebar-link'>
                                <i style="color:black" class="bi bi-stack"></i>
                                <span style="color:black">Referensi</span>
                            </a>

                            <ul
                                class="submenu {{ request()->segment(1) == 'master-data' ? 'submenu-open' : 'submenu-closed' }}">

                                <li class="submenu-item {{ request()->segment(2) == 'customers' ? 'active' : '' }}">
                                    <a href="{{ route('customers.index') }}?tab=group" class='submenu-link'
                                        style="{{ request()->segment(2) == 'customers' ? ' border-radius:8px;' : '' }}">
                                        <span
                                            style="color:{{ request()->segment(2) == 'customers' ? '#000040' : '' }}">Customer</span>
                                    </a>
                                </li>

                                <li class="submenu-item {{ request()->segment(2) == 'products' ? 'active' : '' }}">
                                    <a href="{{ route('products.index') }}" class='submenu-link'
                                        style="{{ request()->segment(2) == 'products' ? ' border-radius:8px;' : '' }}">
                                        <span
                                            style="color:{{ request()->segment(2) == 'products' ? '#000040' : '' }}">Produk</span>
                                    </a>
                                </li>
                                <li class="submenu-item {{ request()->segment(2) == 'suppliers' ? 'active' : '' }}">
                                    <a href="{{ route('suppliers.index') }}" class='submenu-link'
                                        style="{{ request()->segment(2) == 'suppliers' ? ' border-radius:8px;' : '' }}">
                                        <span
                                            style="color:{{ request()->segment(2) == 'suppliers' ? '#000040' : '' }}">Supplier</span>
                                    </a>
                                </li>



                            </ul>


                        </li>

                        <li class="sidebar-item">
                            <a href="#" class='sidebar-link'>
                                <i style="color: white" class="bi bi-gear"></i>
                                <span style="color: white">Pengaturan</span>
                            </a>


                        </li>

                    </ul>
                </div>
            </div>
        </div>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <h3>{{$title}}</h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12 col-lg-12">
                        @yield('content')
                    </div>
                </section>
            </div>

            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p>2025 &copy; Point of Sales</p>
                    </div>
                    <div class="float-end">
                        <p>Crafted with <span class="text-danger"><i class="bi bi-heart-fill icon-mid"></i></span>
                            by <a href="#">Pena Hitam Merah</a></p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="{{ asset('assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>


    <script src="{{ asset('assets/compiled/js/app.js') }}"></script>

</body>
<script src="{{ asset('assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
</script>
@if(session('success'))
<script>
    Toast.fire({
  icon: 'success',
  title: @json(session('success'))
});
</script>
@endif

@if(session('error'))
<script>
    Toast.fire({
  icon: 'error',
  title: @json(session('error'))
});
</script>
@endif

@if(session('info'))
<script>
    Toast.fire({
  icon: 'info',
  title: @json(session('info'))
});
</script>
@endif

@if(session('warning'))
<script>
    Toast.fire({
  icon: 'warning',
  title: @json(session('warning'))
});
</script>
@endif

</html>