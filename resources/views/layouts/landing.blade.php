<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SCHEDIFY - Landing Page')</title>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    @stack('styles')
</head>
<body>
    @yield('content')
    
    <script src="{{ asset('js/landing.js') }}"></script>
    @stack('scripts')
</body>
</html>
