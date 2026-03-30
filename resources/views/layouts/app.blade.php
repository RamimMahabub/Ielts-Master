<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ dark: localStorage.theme === 'dark', open: false }" x-init="$watch('dark', v => { localStorage.theme = v ? 'dark' : 'light'; document.documentElement.classList.toggle('dark', v); }); document.documentElement.classList.toggle('dark', dark)">

<head>
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'IELTS Master') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 transition-colors">
    <div class="min-h-screen bg-gradient-to-br from-indigo-100/70 via-sky-100/40 to-purple-100/70 dark:from-slate-950 dark:via-slate-900 dark:to-indigo-950">
        <div class="flex min-h-screen">
            <aside :class="open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" class="fixed lg:static inset-y-0 z-40 w-72 transform transition-transform duration-300 bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border-r border-white/50 dark:border-slate-800 shadow-xl">
                <div class="h-full flex flex-col">
                    <div class="px-6 py-5 border-b border-slate-200/70 dark:border-slate-800 flex items-center justify-between">
                        <a href="{{ url('/') }}" class="text-2xl font-extrabold bg-gradient-to-r from-indigo-600 to-cyan-500 text-transparent bg-clip-text">IELTS Master</a>
                        <button @click="open=false" class="lg:hidden text-slate-500">?</button>
                    </div>

                    <nav class="p-4 space-y-2 text-sm">
                        @php($role = Auth::user()->getRoleNames()->first())

                        @if($role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="block rounded-xl px-4 py-2 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}">Dashboard</a>
                            <a href="{{ route('admin.profile') }}" class="block rounded-xl px-4 py-2 {{ request()->routeIs('admin.profile') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}">Owner Profile</a>
                            <a href="{{ route('admin.users') }}" class="block rounded-xl px-4 py-2 {{ request()->routeIs('admin.users') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}">User Management</a>
                            <a href="{{ route('admin.students') }}" class="block rounded-xl px-4 py-2 {{ request()->routeIs('admin.students') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}">Students</a>
                            <a href="{{ route('admin.instructors') }}" class="block rounded-xl px-4 py-2 {{ request()->routeIs('admin.instructors') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}">Instructors</a>
                            <a href="{{ route('admin.instructor.verification') }}" class="block rounded-xl px-4 py-2 {{ request()->routeIs('admin.instructor.verification') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}">Instructor Verification</a>
                            <a href="{{ route('admin.question_bank') }}" class="block rounded-xl px-4 py-2 {{ request()->routeIs('admin.question_bank') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}">Question Bank</a>
                            <a href="{{ route('admin.mock_test.create') }}" class="block rounded-xl px-4 py-2 {{ request()->routeIs('admin.mock_test.create') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}">Mock Test Builder</a>
                        @elseif($role === 'instructor')
                            @if(Auth::user()->instructor_status === 'approved')
                                <a href="{{ route('instructor.dashboard') }}" class="block rounded-xl px-4 py-2 {{ request()->routeIs('instructor.dashboard') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}">Dashboard</a>
                                <a href="{{ route('instructor.profile') }}" class="block rounded-xl px-4 py-2 {{ request()->routeIs('instructor.profile') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}">Profile</a>
                                <a href="{{ route('instructor.question_bank') }}" class="block rounded-xl px-4 py-2 {{ request()->routeIs('instructor.question_bank') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}">Question Bank</a>
                                <a href="{{ route('instructor.mock_test.create') }}" class="block rounded-xl px-4 py-2 {{ request()->routeIs('instructor.mock_test.create') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}">Mock Test Builder</a>
                            @else
                                <a href="{{ route('instructor.verification.pending') }}" class="block rounded-xl px-4 py-2 {{ request()->routeIs('instructor.verification.pending') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}">Verification Status</a>
                                <a href="{{ route('instructor.profile') }}" class="block rounded-xl px-4 py-2 {{ request()->routeIs('instructor.profile') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}">Profile</a>
                            @endif
                        @else
                            <a href="{{ route('student.dashboard') }}" class="block rounded-xl px-4 py-2 {{ request()->routeIs('student.dashboard') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}">Dashboard</a>
                            <a href="{{ route('student.profile') }}" class="block rounded-xl px-4 py-2 {{ request()->routeIs('student.profile') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}">Profile</a>
                            <a href="{{ route('student.vocabulary') }}" class="block rounded-xl px-4 py-2 {{ request()->routeIs('student.vocabulary') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}">Vocabulary Builder</a>
                            <a href="{{ route('student.history') }}" class="block rounded-xl px-4 py-2 {{ request()->routeIs('student.history') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}">Test History</a>
                        @endif
                    </nav>

                    <div class="mt-auto p-4 text-xs text-slate-500 dark:text-slate-400">
                        TODO: Features 11-22 placeholders are pending implementation.
                    </div>
                </div>
            </aside>

            <div class="flex-1 min-w-0">
                <header class="sticky top-0 z-30 border-b border-white/30 dark:border-slate-800 bg-white/60 dark:bg-slate-900/60 backdrop-blur-xl">
                    <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <button @click="open = !open" class="lg:hidden rounded-lg bg-slate-100 dark:bg-slate-800 px-3 py-2">?</button>
                            @php($avatar = Auth::user()->profile_photo)
                            @if($avatar)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($avatar) }}" alt="{{ Auth::user()->name }} avatar" class="h-9 w-9 rounded-full object-cover ring-2 ring-indigo-200 dark:ring-indigo-700" style="width:36px;height:36px;min-width:36px;min-height:36px;" />
                            @else
                                <div class="h-9 w-9 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 flex items-center justify-center text-xs font-bold ring-2 ring-indigo-200 dark:ring-indigo-700" style="width:36px;height:36px;min-width:36px;min-height:36px;">
                                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="font-semibold">{{ Auth::user()->name }}</div>
                            <span class="text-xs px-2 py-1 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 uppercase">{{ Auth::user()->getRoleNames()->first() }}</span>
                        </div>

                        <div class="flex items-center gap-3">
                            @livewire('components.notification-bell')
                            <button @click="dark = !dark" class="rounded-xl px-3 py-2 bg-slate-100 dark:bg-slate-800 text-sm transition hover:-translate-y-0.5">
                                <span x-show="!dark">Dark</span>
                                <span x-show="dark">Light</span>
                            </button>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="rounded-xl px-3 py-2 bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300 text-sm transition hover:-translate-y-0.5">Log Out</button>
                            </form>
                        </div>
                    </div>
                </header>

                @isset($header)
                    <div class="px-4 sm:px-6 lg:px-8 py-6">
                        <div class="rounded-2xl bg-white/60 dark:bg-slate-900/60 backdrop-blur-xl border border-white/50 dark:border-slate-800 shadow-sm p-5">
                            {{ $header }}
                        </div>
                    </div>
                @endisset

                <main class="px-4 sm:px-6 lg:px-8 pb-10">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </div>

    @livewireScripts
</body>

</html>
