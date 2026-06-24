<!DOCTYPE html>
<html lang="en" data-theme="cupcake">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="frutiger.css" rel="stylesheet">
    <link href="index.css" rel="stylesheet">

    <link rel="icon" type="image/png" href="/logo.png" />
    <title>@yield('title')</title>
    @stack('head')
</head>

<body>
    <div class="flex flex-col min-h-screen">
        @include('common.navbar')

        <main class="flex-grow">
            @yield('content')
        </main>

        @include('common.footer')
    </div>
    @stack('scripts')
</body>
