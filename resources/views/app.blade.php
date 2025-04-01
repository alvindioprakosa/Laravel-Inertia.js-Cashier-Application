<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <title>@yield('title', 'Dashboard')</title>

    {{-- CSS --}}
    <link href="{{ asset('/admin/css/style.css') }}" rel="stylesheet" />
    <link href="{{ asset('/admin/css/custom.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Font & Icon --}}
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

    {{-- JS --}}
    <script src="{{ asset('/admin/js/coreui.bundle.min.js') }}" defer></script>
    @vite(['resources/js/app.js']) {{-- Pakai Vite jika Laravel 9+ --}}
</head>

<body>
    @inertia

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
