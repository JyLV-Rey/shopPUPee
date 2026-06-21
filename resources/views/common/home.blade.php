<!DOCTYPE html>
<html lang="en" data-theme="cupcake">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <link href="frutiger.css" rel="stylesheet">
  <link href="index.css" rel="stylesheet">

  <title>@yield('title')</title>
</head>

<body>
  <div>
    <div>
      <button class="btn btn-xs">Xsmall</button>
<button class="btn btn-sm">Small</button>
<button class="btn">Medium</button>
<button class="btn btn-lg">Large</button>
<button class="btn btn-xl">Xlarge</button>
    </div>
  </div>
</body>