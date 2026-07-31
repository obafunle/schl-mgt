<x-guest-layout>
    {{-- Page Header --}}
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800">{{ config('app.name') }}</h1>
        <p class="text-gray-600">School Management System</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="p-4 text-red-700 bg-red-100 border-l-4 border-red-500 rounded-r">
                @foreach ($errors->all() as $error)
                    <p class="text-sm">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Session Status --}}
        @if (session('status'))
            <div class="p-4 text-green-700 bg-green-100 border-l-4 border-green-500 rounded-r">
                <p class="text-sm">{{ session('status') }}</p>
            </div>
        @endif

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
                Email Address <span class="text-red-500">*</span>
            </label>
            <input type="email"
                   id="email"
                   name="email"
                   value="{{ old('email') }}"
                   required
                   autofocus
                   autocomplete="username"
                   class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                   placeholder="admin@school.com">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password with Eye Icon --}}
        <div>
            <x-password-input
                name="password"
                label="Password"
                required
                placeholder="Enter your password"
                autocomplete="current-password"
            />
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center">
            <input type="checkbox"
                   id="remember_me"
                   name="remember"
                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <label for="remember_me" class="ml-2 text-sm text-gray-600">
                Remember me
            </label>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-between">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-sm text-indigo-600 hover:text-indigo-800 hover:underline">
                    Forgot your password?
                </a>
            @endif

            <button type="submit"
                    style="display: inline-flex; align-items: center; padding: 10px 28px; font-size: 14px; font-weight: 600; color: #ffffff; background-color: #4f46e5; border: none; border-radius: 8px; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2); cursor: pointer; transition: all 0.25s ease; transform: scale(1);"
                    onmouseover="this.style.backgroundColor='#4338ca'; this.style.boxShadow='0 4px 12px rgba(79, 70, 229, 0.4)'; this.style.transform='scale(1.02)'"
                    onmouseout="this.style.backgroundColor='#4f46e5'; this.style.boxShadow='0 2px 4px rgba(79, 70, 229, 0.2)'; this.style.transform='scale(1)'"
                    onmousedown="this.style.transform='scale(0.97)'; this.style.boxShadow='0 1px 2px rgba(79, 70, 229, 0.2)'"
                    onmouseup="this.style.transform='scale(1.02)'; this.style.boxShadow='0 4px 12px rgba(79, 70, 229, 0.4)'">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: inline-block; vertical-align: middle;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
                Log in
            </button>
        </div>
    </form>
</x-guest-layout>
