<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} @hasSection('title') - @yield('title') @endif</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <script>
        localStorage.getItem("_x_darkMode_on") === "true" &&
            document.documentElement.classList.add("dark");
    </script>

    @stack('styles')
</head>

<body x-data x-bind="$store.global.documentBody" class="is-sidebar-open">

    <x-app-preloader></x-app-preloader>

    <div id="root" class="min-h-100vh flex grow bg-slate-50 dark:bg-navy-900" x-cloak>
        <div class="sidebar print:hidden">
            <x-app-partials.main-sidebar></x-app-partials.main-sidebar>
            <x-app-partials.sidebar-panel></x-app-partials.sidebar-panel>
        </div>

        <x-app-partials.header></x-app-partials.header>
        <x-app-partials.mobile-searchbar></x-app-partials.mobile-searchbar>

        <div class="main-content w-full px-[var(--margin-x)] pb-8">
            <div class="flex items-center py-5">
                <div class="flex min-w-0 items-center gap-2">
                    <h2 class="text-base font-medium tracking-wide text-slate-700 line-clamp-1 dark:text-navy-100">
                        @yield('title', config('app.name'))
                    </h2>
                </div>
            </div>

            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                    class="alert flex rounded-lg border border-success/30 bg-success/10 py-4 px-4 text-success sm:px-5 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert flex rounded-lg border border-error/30 bg-error/10 py-4 px-4 text-error sm:px-5 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert flex rounded-lg border border-error/30 bg-error/10 py-4 px-4 text-error sm:px-5 mb-4">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <div id="x-teleport-target"></div>

    <script>
        window.addEventListener("DOMContentLoaded", () => Alpine.start());
    </script>

    @stack('scripts')

</body>
</html>
