<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Birthday Calendar
        </x-slot>

        <div wire:ignore>
            <div id="birthday-calendar"></div>
        </div>
    </x-filament::section>

    @push('scripts')
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('birthday-calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,dayGridWeek'
                    },
                    events: @json($this->getBirthdays()),
                    eventClick: function(info) {
                        alert('Birthday: ' + info.event.title +
                              (info.event.extendedProps.age ? '\nAge: ' + info.event.extendedProps.age : ''));
                    },
                    height: 'auto',
                    eventColor: '#ef4444'
                });
                calendar.render();
            });
        </script>
    @endpush
</x-filament-widgets::widget>
