<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}" />
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon_192.png') }}" />
    <link rel="apple-touch-icon" href="{{ asset('favicon_192.png') }}" />

    <title>{{ config('app.name') }} @hasSection('title') - @yield('title') @endif</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <script>
        localStorage.getItem("_x_darkMode_on") === "true" &&
            document.documentElement.classList.add("dark");
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

            {{-- SweetAlert: los mensajes flash y errores se muestran via JS abajo --}}

            @yield('content')
        </div>
    </div>

    <div id="x-teleport-target"></div>

    <script>
        window.addEventListener("DOMContentLoaded", () => Alpine.start());
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: @json(session('success')),
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
        });
        @endif

        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: @json(session('error')),
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 6000,
            timerProgressBar: true,
        });
        @endif

        @if(session('warning'))
        Swal.fire({
            icon: 'warning',
            title: 'Advertencia',
            text: @json(session('warning')),
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
        });
        @endif

        @if($errors->any())
        Swal.fire({
            icon: 'warning',
            title: 'Revisa el formulario',
            html: '<ul class="text-left text-sm space-y-1">' +
                @json($errors->all()).map(e => `<li>• ${e}</li>`).join('') +
                '</ul>',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#3b82f6',
        });
        @endif
    });
    </script>

    @stack('scripts')

</body>
</html>
