<div class="main-sidebar">
    <div class="flex h-full w-full flex-col items-center border-r border-slate-150 bg-white dark:border-navy-700 dark:bg-navy-800">

        <!-- Logo -->
        <div class="flex pt-4">
            <a href="{{ route('dashboard') }}">
                <img class="size-11 transition-transform duration-500 ease-in-out hover:rotate-[360deg]"
                    src="{{ asset('img/logo-clinica.svg') }}" alt="Logo Clínica" />
            </a>
        </div>

        <!-- Section Icons -->
        <div class="is-scrollbar-hidden flex grow flex-col space-y-4 overflow-y-auto pt-6">

            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}"
                class="flex size-11 items-center justify-center rounded-lg outline-hidden transition-colors duration-200
                    {{ request()->routeIs('dashboard') && !request()->routeIs('dashboard.*') ? 'bg-primary/10 text-primary dark:bg-navy-600 dark:text-accent-light' : 'text-slate-400 hover:bg-primary/20 focus:bg-primary/20 dark:text-navy-300 dark:hover:bg-navy-300/20' }}"
                x-tooltip.placement.right="'Dashboard'">
                <svg class="size-7" fill="none" viewBox="0 0 24 24">
                    <path fill="currentColor" fill-opacity=".3" d="M5 14.059c0-1.01 0-1.514.222-1.945.221-.43.632-.724 1.453-1.31l4.163-2.974c.56-.4.842-.601 1.162-.601.32 0 .601.2 1.162.601l4.163 2.974c.821.586 1.232.88 1.453 1.31.222.43.222.935.222 1.945V19c0 .943 0 1.414-.293 1.707C18.414 21 17.943 21 17 21H7c-.943 0-1.414 0-1.707-.293C5 20.414 5 19.943 5 19v-4.94Z"/>
                    <path fill="currentColor" d="M3 12.387c0 .267 0 .4.084.441.084.041.19-.04.4-.204l7.288-5.669c.59-.459.885-.688 1.228-.688.343 0 .638.23 1.228.688l7.288 5.669c.21.163.316.245.4.204.084-.04.084-.174.084-.441v-.409c0-.48 0-.72-.102-.928-.101-.208-.291-.355-.67-.65l-7-5.445c-.59-.459-.885-.688-1.228-.688-.343 0-.638.23-1.228.688l-7 5.445c-.379.295-.569.442-.67.65-.102.208-.102.448-.102.928v.409Z"/>
                    <path fill="currentColor" d="M11.5 15.5h1A1.5 1.5 0 0 1 14 17v3.5h-4V17a1.5 1.5 0 0 1 1.5-1.5Z"/>
                </svg>
            </a>

            {{-- Doctor: Estadísticas --}}
            @if(auth()->user()->esMedico())
            <a href="{{ route('dashboard.analytics') }}"
                class="flex size-11 items-center justify-center rounded-lg outline-hidden transition-colors duration-200
                    {{ request()->routeIs('dashboard.analytics') ? 'bg-primary/10 text-primary dark:bg-navy-600 dark:text-accent-light' : 'text-slate-400 hover:bg-primary/20 focus:bg-primary/20 dark:text-navy-300 dark:hover:bg-navy-300/20' }}"
                x-tooltip.placement.right="'Mis Estadísticas'">
                <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </a>

            {{-- Doctor: Mi Agenda --}}
            <a href="{{ route('dashboard.agenda') }}"
                class="flex size-11 items-center justify-center rounded-lg outline-hidden transition-colors duration-200
                    {{ request()->routeIs('dashboard.agenda') ? 'bg-primary/10 text-primary dark:bg-navy-600 dark:text-accent-light' : 'text-slate-400 hover:bg-primary/20 focus:bg-primary/20 dark:text-navy-300 dark:hover:bg-navy-300/20' }}"
                x-tooltip.placement.right="'Mi Agenda'">
                <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </a>
            @endif

            {{-- Citas --}}
            @can('acceso_citas')
            <a href="{{ route('citas.index') }}"
                class="flex size-11 items-center justify-center rounded-lg outline-hidden transition-colors duration-200
                    {{ request()->routeIs('citas.*') || request()->routeIs('agenda') ? 'bg-primary/10 text-primary dark:bg-navy-600 dark:text-accent-light' : 'text-slate-400 hover:bg-primary/20 focus:bg-primary/20 dark:text-navy-300 dark:hover:bg-navy-300/20' }}"
                x-tooltip.placement.right="'Citas'">
                <svg class="size-7" fill="none" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" fill="currentColor" fill-opacity=".3"/>
                    <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <rect x="7" y="14" width="3" height="3" rx=".5" fill="currentColor"/>
                    <rect x="10.5" y="14" width="3" height="3" rx=".5" fill="currentColor" fill-opacity=".5"/>
                    <rect x="14" y="14" width="3" height="3" rx=".5" fill="currentColor" fill-opacity=".5"/>
                </svg>
            </a>
            @endcan

            {{-- Médicos --}}
            @can('acceso_medicos')
            <a href="{{ route('medicos.index') }}"
                class="flex size-11 items-center justify-center rounded-lg outline-hidden transition-colors duration-200
                    {{ request()->routeIs('medicos.*') || request()->routeIs('especialidades.*') || request()->routeIs('horarios.*') ? 'bg-primary/10 text-primary dark:bg-navy-600 dark:text-accent-light' : 'text-slate-400 hover:bg-primary/20 focus:bg-primary/20 dark:text-navy-300 dark:hover:bg-navy-300/20' }}"
                x-tooltip.placement.right="'Medicos'">
                <svg class="size-7" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="8" r="4" fill="currentColor" fill-opacity=".3"/>
                    <path d="M4 20c0-3.314 3.582-6 8-6s8 2.686 8 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M17 13v2m0 2v2m-1-3h2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </a>
            @endcan

            {{-- Pacientes --}}
            @can('acceso_pacientes')
            <a href="{{ route('pacientes.index') }}"
                class="flex size-11 items-center justify-center rounded-lg outline-hidden transition-colors duration-200
                    {{ request()->routeIs('pacientes.*') || request()->routeIs('historial.*') ? 'bg-primary/10 text-primary dark:bg-navy-600 dark:text-accent-light' : 'text-slate-400 hover:bg-primary/20 focus:bg-primary/20 dark:text-navy-300 dark:hover:bg-navy-300/20' }}"
                x-tooltip.placement.right="'Pacientes'">
                <svg class="size-7" fill="none" viewBox="0 0 24 24">
                    <path d="M17 21H7a4 4 0 0 1-4-4v-1a5 5 0 0 1 5-5h8a5 5 0 0 1 5 5v1a4 4 0 0 1-4 4Z" fill="currentColor" fill-opacity=".3"/>
                    <circle cx="12" cy="7" r="4" fill="currentColor"/>
                </svg>
            </a>
            @endcan

            {{-- Pagos (oculto para médicos) --}}
            @if(!auth()->user()->esMedico() || auth()->user()->esSuperAdmin())
            @can('acceso_citas')
            <a href="{{ route('pagos.index') }}"
                class="flex size-11 items-center justify-center rounded-lg outline-hidden transition-colors duration-200
                    {{ request()->routeIs('pagos.*') ? 'bg-primary/10 text-primary dark:bg-navy-600 dark:text-accent-light' : 'text-slate-400 hover:bg-primary/20 focus:bg-primary/20 dark:text-navy-300 dark:hover:bg-navy-300/20' }}"
                x-tooltip.placement.right="'Pagos'">
                <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </a>
            @endcan
            @endif

            {{-- Administración --}}
            @can('acceso_usuarios')
            <a href="{{ route('usuarios.index') }}"
                class="flex size-11 items-center justify-center rounded-lg outline-hidden transition-colors duration-200
                    {{ request()->routeIs('usuarios.*') || request()->routeIs('roles.*') || request()->routeIs('permisos.*') ? 'bg-primary/10 text-primary dark:bg-navy-600 dark:text-accent-light' : 'text-slate-400 hover:bg-primary/20 focus:bg-primary/20 dark:text-navy-300 dark:hover:bg-navy-300/20' }}"
                x-tooltip.placement.right="'Administracion'">
                <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </a>
            @endcan

            {{-- Notificaciones (oculto para médicos) --}}
            @if(!auth()->user()->esMedico() || auth()->user()->esSuperAdmin())
            @can('acceso_notificaciones')
            <a href="{{ route('notificaciones.index') }}"
                class="flex size-11 items-center justify-center rounded-lg outline-hidden transition-colors duration-200
                    {{ request()->routeIs('notificaciones.*') ? 'bg-primary/10 text-primary dark:bg-navy-600 dark:text-accent-light' : 'text-slate-400 hover:bg-primary/20 focus:bg-primary/20 dark:text-navy-300 dark:hover:bg-navy-300/20' }}"
                x-tooltip.placement.right="'Notificaciones WhatsApp'">
                <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
            </a>
            @endcan
            @endif

            {{-- Sistema (Reportes / Auditoria) (oculto para médicos) --}}
            @if(!auth()->user()->esMedico() || auth()->user()->esSuperAdmin())
            @if(auth()->user()->tienePermiso('acceso_reportes') || auth()->user()->tienePermiso('acceso_auditoria'))
            <a href="{{ route('reportes.index') }}"
                class="flex size-11 items-center justify-center rounded-lg outline-hidden transition-colors duration-200
                    {{ request()->routeIs('reportes.*') || request()->routeIs('auditoria.*') ? 'bg-primary/10 text-primary dark:bg-navy-600 dark:text-accent-light' : 'text-slate-400 hover:bg-primary/20 focus:bg-primary/20 dark:text-navy-300 dark:hover:bg-navy-300/20' }}"
                x-tooltip.placement.right="'Sistema'">
                <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </a>
            @endif
            @endif

        </div>

        <!-- Avatar / Logout -->
        <div class="flex flex-col items-center space-y-3 py-3">
            <div x-data="usePopper({ placement: 'right-end', offset: 12 })" @click.outside="if(isShowPopper) isShowPopper = false" class="flex">
                <button @click="isShowPopper = !isShowPopper" x-ref="popperRef" class="avatar size-12 cursor-pointer">
                    <img class="rounded-full" src="{{ asset('images/200x200.png') }}" alt="avatar" />
                    <span class="absolute right-0 size-3.5 rounded-full border-2 border-white bg-success dark:border-navy-700"></span>
                </button>
                <div :class="isShowPopper && 'show'" class="popper-root fixed" x-ref="popperRoot">
                    <div class="popper-box w-56 rounded-lg border border-slate-150 bg-white shadow-soft dark:border-navy-600 dark:bg-navy-700">
                        <div class="flex items-center space-x-3 rounded-t-lg bg-slate-100 py-4 px-4 dark:bg-navy-800">
                            <div>
                                <p class="font-medium text-slate-700 dark:text-navy-100">
                                    {{ auth()->user()->nombre ?? '' }} {{ auth()->user()->apellido ?? '' }}
                                </p>
                                <p class="text-xs text-slate-400 dark:text-navy-300 line-clamp-1">
                                    {{ auth()->user()->email ?? '' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-col pt-2 pb-4 px-4">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="btn mt-2 h-9 w-full space-x-2 bg-error text-white hover:bg-error-focus focus:bg-error-focus active:bg-error-focus/90">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <span>Cerrar Sesion</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
