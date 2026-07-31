<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center space-x-4">
                <a href="{{ route('parent.dashboard') }}" class="text-xl font-bold text-blue-600">
                    {{ config('app.name') }}
                </a>
                <span class="text-sm text-gray-500">Parent Portal</span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('parent.dashboard') }}" class="text-gray-700 hover:text-blue-600">Dashboard</a>
                <a href="{{ route('parent.children') }}" class="text-gray-700 hover:text-blue-600">Children</a>
                <a href="{{ route('parent.exeats') }}" class="text-gray-700 hover:text-blue-600">Exeats</a>
                <a href="{{ route('parent.profile') }}" class="text-gray-700 hover:text-blue-600">Profile</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-red-600 hover:text-red-800">Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav>
