<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel App') }}</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Add Google Fonts or other meta if needed -->
</head>
<body class="bg-gray-100 text-gray-800 antialiased leading-relaxed min-h-screen">

    <div class="min-h-screen flex flex-col">
        <header class="bg-white shadow-md py-4">
            <div class="container mx-auto px-4">
                <h1 class="text-xl font-semibold">
                    <a href="{{ url('/') }}">{{ config('app.name', 'Laravel App') }}</a>
                </h1>
            </div>
        </header>

        <main class="flex-grow">
            @yield('content')
        </main>

        <footer class="bg-white py-4 mt-10 border-t">
            <div class="container mx-auto px-4 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} {{ config('app.name', 'Laravel App') }}. All rights reserved.
            </div>
        </footer>
    </div>

</body>
</html>
