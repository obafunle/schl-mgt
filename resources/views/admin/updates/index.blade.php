<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('System Updates') }}
            </h2>
            <span class="text-sm text-gray-500">
                Version {{ config('app.version', '1.0.0') }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('info'))
                <div class="mb-6 p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700">
                    {{ session('info') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-700">📌 Current Version</h3>
                        <div class="mt-3 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-2xl font-bold text-blue-600">
                                        v{{ config('app.version', '1.0.0') }}
                                    </span>
                                    <span class="ml-4 text-sm text-gray-500">
                                        {{ config('app.name') }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-500">
                                    Last checked: {{ $status['last_check'] ?? 'Never' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <form action="{{ route('admin.updates.check') }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition">
                                🔍 Check for Updates
                            </button>
                        </form>
                    </div>

                    @if(isset($status['update_available']) && $status['update_available'])
                        @php $update = $status['update_info']; @endphp
                        <div class="mb-8 p-6 bg-gradient-to-r from-yellow-50 to-orange-50 border-2 border-yellow-400 rounded-lg">
                            <div class="flex items-start">
                                <div class="text-4xl mr-4">🔄</div>
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-yellow-800">Update Available!</h3>
                                    <p class="text-yellow-700 mt-1">
                                        <strong>Version {{ $update['version'] }}</strong> is now available
                                    </p>
                                    
                                    @if(isset($update['changelog']) && count($update['changelog']) > 0)
                                        <div class="mt-4 p-4 bg-white rounded-lg border border-yellow-200">
                                            <h4 class="font-semibold text-gray-700 mb-2">📝 What's New:</h4>
                                            <ul class="list-disc list-inside space-y-1 text-sm text-gray-600">
                                                @foreach($update['changelog'] as $change)
                                                    <li>{{ $change }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    
                                    <form action="{{ route('admin.updates.install') }}" method="POST" class="mt-4">
                                        @csrf
                                        <input type="hidden" name="version" value="{{ $update['version'] }}">
                                        <input type="hidden" name="download_url" value="{{ $update['download_url'] ?? '' }}">
                                        <button type="submit" 
                                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg transition transform hover:scale-105">
                                            📥 Download & Install Now
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @elseif(isset($status['update_available']) && !$status['update_available'])
                        <div class="mb-8 p-6 bg-green-50 border-2 border-green-400 rounded-lg">
                            <div class="flex items-center">
                                <span class="text-4xl mr-4">✅</span>
                                <div>
                                    <h3 class="text-xl font-bold text-green-800">System is Up to Date</h3>
                                    <p class="text-green-700">You're running the latest version.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mb-8 p-6 bg-gray-50 border-2 border-gray-300 rounded-lg">
                            <div class="flex items-center">
                                <span class="text-4xl mr-4">ℹ️</span>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-700">Check for Updates</h3>
                                    <p class="text-gray-600">Click "Check for Updates" to see if a new version is available.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-start">
                            <span class="text-2xl mr-3">💡</span>
                            <div>
                                <h4 class="font-semibold text-blue-800">About Updates</h4>
                                <ul class="mt-2 space-y-1 text-sm text-blue-700">
                                    <li>• Updates are delivered securely from our official server</li>
                                    <li>• Your data and settings are always preserved</li>
                                    <li>• A backup is created automatically before each update</li>
                                    <li>• If an update fails, the system will rollback automatically</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>