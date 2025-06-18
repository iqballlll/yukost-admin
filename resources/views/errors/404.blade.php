<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Mazer Admin Dashboard</title>


    {{--
    <link rel="shortcut icon" href="./assets/compiled/svg/favicon.svg" type="image/x-icon"> --}}
    {{--
    <link rel="shortcut icon" href="#" type="image/png"> --}}
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/error.css') }}">
</head>

<body>
    <script src="{{ asset('assets/static/js/initTheme.js') }}"></script>
    <div id="error">


        <div class="error-page container">
            <div class="col-md-8 col-12 offset-md-2">
                <div class="text-center">
                    <img class="img-error" src="{{ asset('assets/compiled/svg/error-404.svg') }}" alt="Not Found">
                    <h1 class="error-title">Oopss.. Tidak ditemukan</h1>
                    <p class='fs-5 text-gray-600'>Halaman yang kamu cari tidak ditemukan</p>
                    <a href="{{ route('dashboard') }}" class="btn btn-lg btn-outline-primary mt-3">Beranda</a>
                </div>
            </div>
        </div>


    </div>
</body>

</html>