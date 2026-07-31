<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Sidebar Styles */
            .admin-sidebar {
                width: 260px;
                min-height: calc(100vh - 64px);
                background: #ffffff;
                border-right: 1px solid #e5e7eb;
                padding: 1rem 0.75rem;
                overflow-y: auto;
                flex-shrink: 0;
                position: sticky;
                top: 0;
            }
            .admin-sidebar .sidebar-section {
                font-size: 0.65rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: #9ca3af;
                padding: 0.75rem 1rem 0.5rem;
                font-weight: 600;
            }
            .admin-sidebar .sidebar-link {
                display: flex;
                align-items: center;
                padding: 0.6rem 1rem;
                color: #374151;
                border-radius: 0.5rem;
                transition: all 0.2s ease;
                text-decoration: none;
                margin-bottom: 0.125rem;
                font-size: 0.875rem;
            }
            .admin-sidebar .sidebar-link:hover {
                background: #f3f4f6;
                color: #1f2937;
            }
            .admin-sidebar .sidebar-link.active {
                background: #e0e7ff;
                color: #4f46e5;
                font-weight: 500;
            }
            .admin-sidebar .sidebar-link .icon {
                width: 1.75rem;
                margin-right: 0.75rem;
                font-size: 1.1rem;
                text-align: center;
                flex-shrink: 0;
            }
            .admin-content {
                flex: 1;
                padding: 1.5rem;
                background: #f9fafb;
                min-height: calc(100vh - 64px);
            }
            .admin-layout {
                display: flex;
                min-height: calc(100vh - 64px);
            }
            @media (max-width: 768px) {
                .admin-sidebar {
                    width: 200px;
                    font-size: 0.8rem;
                }
                .admin-sidebar .sidebar-link {
                    padding: 0.4rem 0.75rem;
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <div class="admin-layout">
                <!-- Admin Sidebar -->
                <aside class="admin-sidebar">
                    <!-- Main -->
                    <div class="sidebar-section">Main</div>
                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <span class="icon">📊</span> Dashboard
                    </a>

                    <!-- Academic -->
                    <div class="sidebar-section">Academic</div>
                    <a href="{{ route('admin.students.index') }}" class="sidebar-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                        <span class="icon">👨‍🎓</span> Students
                    </a>
                    <a href="{{ route('admin.staff.index') }}" class="sidebar-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
                        <span class="icon">👨‍🏫</span> Staff
                    </a>
                    <a href="{{ route('admin.classes.index') }}" class="sidebar-link {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
                        <span class="icon">🏫</span> Classes
                    </a>
                    <a href="{{ route('admin.subjects.index') }}" class="sidebar-link {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
                        <span class="icon">📚</span> Subjects
                    </a>
                    <a href="{{ route('admin.academic-years.index') }}" class="sidebar-link {{ request()->routeIs('admin.academic-years.*') ? 'active' : '' }}">
                        <span class="icon">📅</span> Academic Years
                    </a>
                    <a href="{{ route('admin.examinations.index') }}" class="sidebar-link {{ request()->routeIs('admin.examinations.*') ? 'active' : '' }}">
                        <span class="icon">📝</span> Examinations
                    </a>

                    <!-- Finance -->
                    <div class="sidebar-section">Finance</div>
                    <a href="{{ route('admin.fees.index') }}" class="sidebar-link {{ request()->routeIs('admin.fees.*') ? 'active' : '' }}">
                        <span class="icon">💰</span> Fee Structures
                    </a>
                    <a href="{{ route('admin.invoices.index') }}" class="sidebar-link {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
                        <span class="icon">📄</span> Invoices
                    </a>

                    <!-- Facilities -->
                    <div class="sidebar-section">Facilities</div>
                    <a href="{{ route('admin.hostels.index') }}" class="sidebar-link {{ request()->routeIs('admin.hostels.*') ? 'active' : '' }}">
                        <span class="icon">🏠</span> Hostels
                    </a>
                    <a href="{{ route('admin.timetable.index') }}" class="sidebar-link {{ request()->routeIs('admin.timetable.*') ? 'active' : '' }}">
                        <span class="icon">📋</span> Timetable
                    </a>
                    <a href="{{ route('admin.transport.index') }}" class="sidebar-link {{ request()->routeIs('admin.transport.*') ? 'active' : '' }}">
                        <span class="icon">🚌</span> Transport
                    </a>
                    <a href="{{ route('admin.library.index') }}" class="sidebar-link {{ request()->routeIs('admin.library.*') ? 'active' : '' }}">
                        <span class="icon">📖</span> Library
                    </a>
                    <a href="{{ route('admin.inventory.index') }}" class="sidebar-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                        <span class="icon">📦</span> Inventory
                    </a>

                    <!-- System -->
                    <div class="sidebar-section">System</div>
                    <a href="{{ route('admin.parents.index') }}" class="sidebar-link {{ request()->routeIs('admin.parents.*') ? 'active' : '' }}">
                        <span class="icon">👨‍👩‍👧</span> Parents
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <span class="icon">📊</span> Reports
                    </a>
                    <a href="{{ route('admin.updates.index') }}" class="sidebar-link {{ request()->routeIs('admin.updates.*') ? 'active' : '' }}">
                        <span class="icon">🔄</span> Updates
                    </a>
                </aside>

                <!-- Main Content -->
                <main class="admin-content">
                    @if(isset($header))
                        <header class="bg-white shadow-sm rounded-lg p-4 mb-6">
                            {{ $header }}
                        </header>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
