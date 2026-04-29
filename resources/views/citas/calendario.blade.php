@extends('layouts.app')
@section('title', 'Calendario de Citas')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js"></script>
<style>
    #calendar-container .fc {
        --fc-border-color: #e2e8f0;
        --fc-today-bg-color: rgba(99, 102, 241, 0.06);
        --fc-event-border-color: transparent;
        font-family: 'Inter', sans-serif;
    }
    .dark #calendar-container .fc {
        --fc-border-color: #384766;
        --fc-today-bg-color: rgba(99, 102, 241, 0.1);
        --fc-page-bg-color: #1e293b;
        --fc-neutral-bg-color: #1e293b;
        color: #c8d2dc;
    }

    /* ── View buttons: Mes / Semana / Dia / Hoy ── */
    #calendar-container .fc .fc-button {
        background-color: var(--color-primary, #6366f1);
        border-color: var(--color-primary, #6366f1);
        color: #fff;
        font-size: 0.75rem;
        font-weight: 500;
        height: 1.5rem;
        padding: 0 0.75rem;
        border-radius: 0.125rem;
        text-transform: capitalize;
        box-shadow: none;
        transition: background-color 0.15s, border-color 0.15s;
    }
    #calendar-container .fc .fc-button:hover,
    #calendar-container .fc .fc-button:focus {
        background-color: var(--color-primary-focus, #4f46e5);
        border-color: var(--color-primary-focus, #4f46e5);
    }
    #calendar-container .fc .fc-button:active {
        opacity: 0.9;
    }
    #calendar-container .fc .fc-button-active {
        background-color: var(--color-primary-focus, #4338ca) !important;
        border-color: var(--color-primary-focus, #4338ca) !important;
    }
    .dark #calendar-container .fc .fc-button {
        background-color: var(--color-accent, #5f5af6);
        border-color: var(--color-accent, #5f5af6);
    }
    .dark #calendar-container .fc .fc-button:hover,
    .dark #calendar-container .fc .fc-button:focus {
        background-color: var(--color-accent-focus, #4d47f5);
        border-color: var(--color-accent-focus, #4d47f5);
    }

    /* ── Prev / Next nav buttons ── */
    #calendar-container .fc .fc-prev-button,
    #calendar-container .fc .fc-next-button {
        background-color: rgba(14, 165, 233, 0.1) !important;
        border-color: transparent !important;
        color: #0ea5e9 !important;
        width: 2.25rem;
        height: 2.25rem;
        padding: 0;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    #calendar-container .fc .fc-prev-button:hover,
    #calendar-container .fc .fc-next-button:hover {
        background-color: rgba(14, 165, 233, 0.2) !important;
    }
    #calendar-container .fc .fc-prev-button:focus,
    #calendar-container .fc .fc-next-button:focus {
        background-color: rgba(14, 165, 233, 0.2) !important;
        box-shadow: none;
    }
    #calendar-container .fc .fc-prev-button:active,
    #calendar-container .fc .fc-next-button:active {
        background-color: rgba(14, 165, 233, 0.25) !important;
    }
    #calendar-container .fc .fc-prev-button .fc-icon,
    #calendar-container .fc .fc-next-button .fc-icon {
        font-size: 1.1rem;
    }

    /* ── Toolbar title ── */
    #calendar-container .fc .fc-toolbar-title {
        font-size: 1.15rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    /* ── Toolbar spacing ── */
    #calendar-container .fc .fc-toolbar {
        gap: 0.5rem;
        padding: 0.75rem 0;
    }
    #calendar-container .fc .fc-toolbar .fc-button-group {
        gap: 0.25rem;
    }

    /* ── Events ── */
    #calendar-container .fc .fc-daygrid-event {
        border-radius: 0.375rem;
        padding: 1px 4px;
        font-size: 0.75rem;
        cursor: pointer;
    }
    #calendar-container .fc .fc-timegrid-event {
        border-radius: 0.375rem;
        font-size: 0.75rem;
        cursor: pointer;
    }
    .fc-event-title { font-weight: 500; }

    /* ── Day cells ── */
    #calendar-container .fc .fc-daygrid-day-number {
        font-size: 0.85rem;
        padding: 4px 8px;
    }
    #calendar-container .fc .fc-col-header-cell-cushion {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    #calendar-container .fc .fc-daygrid-day:hover {
        background-color: rgba(99, 102, 241, 0.04);
    }
    .dark #calendar-container .fc .fc-daygrid-day:hover {
        background-color: rgba(99, 102, 241, 0.08);
    }
</style>
@endpush

@section('content')
<div x-data="calendarioApp()" x-init="initCalendar()">
    {{-- Leyenda de estados --}}
    <div class="card mb-4 px-4 py-3 sm:px-5">
        <div class="flex flex-wrap items-center gap-3 text-xs">
            <span class="font-medium text-slate-600 dark:text-navy-200">Estados:</span>
            <div class="badge space-x-2.5 text-warning">
                <div class="size-2 rounded-full bg-current"></div>
                <span>Pendiente</span>
            </div>
            <div class="badge space-x-2.5 text-info">
                <div class="size-2 rounded-full bg-current"></div>
                <span>Confirmada</span>
            </div>
            <div class="badge space-x-2.5 text-success">
                <div class="size-2 rounded-full bg-current"></div>
                <span>Atendida</span>
            </div>
            <div class="badge space-x-2.5 text-error">
                <div class="size-2 rounded-full bg-current"></div>
                <span>Cancelada</span>
            </div>
            <div class="badge space-x-2.5 text-slate-800 dark:text-navy-100">
                <div class="size-2 rounded-full bg-current"></div>
                <span>Reprogramada</span>
            </div>
            <div class="badge space-x-2.5 text-secondary dark:text-secondary-light">
                <div class="size-2 rounded-full bg-current"></div>
                <span>No asistio</span>
            </div>
            @if(!auth()->user()->esMedico() || auth()->user()->esSuperAdmin())
            <a href="{{ route('citas.create') }}" class="btn ml-auto h-6 rounded-sm bg-primary px-3 text-xs font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                + Nueva Cita
            </a>
            @endif
        </div>
    </div>

    {{-- Calendario --}}
    <div class="card px-4 pb-4 pt-2 sm:px-5" id="calendar-container">
        <div id="fullcalendar"></div>
    </div>

    {{-- Modal detalle de cita --}}
    <template x-teleport="#x-teleport-target">
        <div x-show="showModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 px-4"
             @click.self="showModal = false" @keydown.escape.window="showModal = false" style="display:none;">
            <div x-show="showModal" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="w-full max-w-lg rounded-xl bg-white shadow-xl dark:bg-navy-700" @click.stop>

                {{-- Header --}}
                <div class="flex items-center justify-between rounded-t-xl border-b border-slate-200 px-5 py-4 dark:border-navy-500">
                    <h3 class="text-base font-semibold text-slate-700 dark:text-navy-100">Detalle de Cita</h3>
                    <button @click="showModal = false" class="btn size-7 rounded-full p-0 text-slate-400 hover:bg-slate-200 dark:hover:bg-navy-500">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="space-y-3 px-5 py-4">
                    {{-- Estado badge --}}
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono text-slate-400 dark:text-navy-300" x-text="modalData.codigo"></span>
                        <span class="badge rounded-full text-xs px-2.5 py-0.5"
                              :class="{
                                  'bg-warning/10 text-warning': modalData.estado === 'pendiente',
                                  'bg-info/10 text-info': modalData.estado === 'confirmada',
                                  'bg-success/10 text-success': modalData.estado === 'atendida',
                                  'bg-error/10 text-error': modalData.estado === 'cancelada',
                                  'bg-slate-100 text-slate-500 dark:bg-navy-500 dark:text-navy-200': modalData.estado === 'reprogramada',
                                  'bg-warning/20 text-orange-600': modalData.estado === 'no_asistio',
                              }"
                              x-text="modalData.estado_label">
                        </span>
                    </div>

                    {{-- Info rows --}}
                    <div class="grid grid-cols-[auto,1fr] gap-x-4 gap-y-2.5 text-sm">
                        <span class="font-medium text-slate-500 dark:text-navy-300">Paciente</span>
                        <span class="text-slate-700 dark:text-navy-100" x-text="modalData.paciente"></span>

                        <span class="font-medium text-slate-500 dark:text-navy-300">Medico</span>
                        <span class="text-slate-700 dark:text-navy-100" x-text="modalData.medico"></span>

                        <span class="font-medium text-slate-500 dark:text-navy-300">Horario</span>
                        <span class="text-slate-700 dark:text-navy-100" x-text="modalData.hora_inicio + ' – ' + modalData.hora_fin"></span>

                        <span class="font-medium text-slate-500 dark:text-navy-300">Motivo</span>
                        <span class="text-slate-700 dark:text-navy-100" x-text="modalData.motivo"></span>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex flex-wrap items-center justify-end gap-2 rounded-b-xl border-t border-slate-200 px-5 py-3 dark:border-navy-500">
                    <a :href="modalData.url_show"
                       class="btn h-8 rounded-lg bg-slate-150 px-3 text-xs font-medium text-slate-800 hover:bg-slate-200 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450">
                        <svg class="mr-1.5 size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Ver detalle
                    </a>
                    <a :href="modalData.url_edit"
                       x-show="!['cancelada','atendida','reprogramada'].includes(modalData.estado)"
                       class="btn h-8 rounded-lg bg-primary px-3 text-xs font-medium text-white hover:bg-primary-focus">
                        <svg class="mr-1.5 size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Editar
                    </a>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection

@push('scripts')
<script>
function calendarioApp() {
    return {
        calendar: null,
        showModal: false,
        modalData: {
            codigo: '', paciente: '', medico: '', hora_inicio: '', hora_fin: '',
            motivo: '', estado: '', estado_label: '', url_show: '#', url_edit: '#'
        },

        initCalendar() {
            const calendarEl = document.getElementById('fullcalendar');
            const self = this;

            this.calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'es',
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana',
                    day: 'Dia'
                },
                firstDay: 1,
                height: 'auto',
                navLinks: true,
                editable: false,
                selectable: true,
                dayMaxEvents: 3,
                moreLinkText: function(num) {
                    return '+' + num + ' mas';
                },
                events: function(info, successCallback, failureCallback) {
                    fetch('{{ route("api.citas.eventos") }}?start=' + info.startStr.split('T')[0] + '&end=' + info.endStr.split('T')[0], {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => successCallback(data))
                    .catch(error => {
                        console.error('Error cargando eventos:', error);
                        failureCallback(error);
                    });
                },
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    const props = info.event.extendedProps;
                    self.modalData = {
                        codigo:       props.codigo,
                        paciente:     props.paciente,
                        medico:       props.medico,
                        hora_inicio:  props.hora_inicio,
                        hora_fin:     props.hora_fin,
                        motivo:       props.motivo,
                        estado:       props.estado,
                        estado_label: props.estado_label,
                        url_show:     props.url_show,
                        url_edit:     props.url_edit,
                    };
                    self.showModal = true;
                },
                @if(!auth()->user()->esMedico() || auth()->user()->esSuperAdmin())
                dateClick: function(info) {
                    const fecha = info.dateStr.split('T')[0];
                    window.location.href = '{{ route("citas.create") }}?fecha=' + fecha;
                },
                @endif
                eventDidMount: function(info) {
                    info.el.title = info.event.extendedProps.hora_inicio + ' – ' + info.event.extendedProps.hora_fin + ' | ' + info.event.title;
                },
            });

            this.calendar.render();
        }
    };
}
</script>
@endpush
