<!DOCTYPE html>
<html lang="en" data-theme="garden">

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
    <div class="flex flex-col min-h-screen relative z-10 bg-base-100/80 backdrop-blur-sm">
        @include('common.navbar')

        <main class="w-fit self-center min-h-screen">
            @yield('content')
        </main>


        @include('common.footer')
    </div>

    <canvas id="bg-balls"
        style="position: fixed; inset: 0; width: 100vw; height: 100vh; pointer-events: none; z-index: 0;"></canvas>

    @vite('resources/js/bg-balls.js')

    @stack('scripts')
</body>
