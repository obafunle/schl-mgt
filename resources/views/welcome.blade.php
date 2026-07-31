<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-800">{{ config('app.name') }}</h1>
            <p class="text-gray-600 mt-2">School Management System</p>
            <div class="mt-6 space-x-4">
                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                       class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        Register
                    </a>
                @endauth
            </div>
        </div>
    </div>
</body>
</html>
