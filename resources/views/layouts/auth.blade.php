<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>IELTS Master - Login</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|outfit:400,500,600,700,800&display=swap"
        rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Outfit', sans-serif;
        }

        .bg-glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .dark .bg-glass {
            background: rgba(17, 24, 39, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>

<body
    class="antialiased min-h-screen bg-gradient-to-br from-indigo-100 via-white to-purple-100 dark:from-gray-900 dark:via-gray-800 dark:to-indigo-950 text-gray-800 dark:text-gray-200">

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div
            class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-purple-300/30 dark:bg-purple-900/30 blur-3xl rounded-full mix-blend-multiply dark:mix-blend-lighten animate-blob">
        </div>
        <div
            class="absolute top-[20%] -right-[10%] w-[50%] h-[50%] rounded-full bg-indigo-300/30 dark:bg-indigo-900/30 blur-3xl rounded-full mix-blend-multiply dark:mix-blend-lighten animate-blob animation-delay-2000">
        </div>
        <div
            class="absolute -bottom-[20%] left-[20%] w-[50%] h-[50%] rounded-full bg-pink-300/30 dark:bg-pink-900/30 blur-3xl rounded-full mix-blend-multiply dark:mix-blend-lighten animate-blob animation-delay-4000">
        </div>
    </div>

    <main class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        {{ $slot }}
    </main>

    @livewireScripts
</body>

</html>