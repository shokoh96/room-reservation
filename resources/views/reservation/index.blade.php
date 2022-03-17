<html>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div id="app">
        <div class="p-3">
            <div class="btn-group mb-4">
                <button type="button" class="btn btn-outline-secondary" @click="moveDate(-1)">&lt;</button>
                <button type="button" class="btn btn-outline-secondary" v-text="date"></button>
                <button type="button" class="btn btn-outline-secondary" @click="moveDate(1)">&gt;</button>
            </div>
            <div class="mb-5" v-for="room in rooms">
                <h4>
                    <span class="badge rounded-pill bg-info text-dark" v-text="room.name">Info</span>
                </h4>
                <div v-for="hours in allHours">
                    <div class="row">
                        <div class="col-auto pr-5 py-2">
                            <span v-text="getPaddedNumber(hours)"></span>時
                        </div>
                        <div class="col-auto p-2" v-for="minutes in room.time_step_values">
                            <button class="btn btn-outline-dark" data-toggle="tooltip"
                                :title="getTimeRange(hours, minutes, room.time_steps)" v-text="getPaddedNumber(minutes)"
                                :disabled="!isReservationAvailable(room.id, hours, minutes)"
                                @click="reserve(room.id, hours, minutes)">
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/vue@3.0.11/dist/vue.global.prod.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/luxon/1.27.0/luxon.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
    <script>
        Vue.createApp({
            data() {
                return {
                    reservations: [],
                    allHours: [],
                    dt: null,
                    rooms: @json($rooms)
                }
            },
            methods: {
                getReservations() {

                    const date = this.dt.toFormat('yyyy-MM-dd');
                    const url = '{{ route('reservation.reservation_list') }}?date=' + date;

                    axios.get(url)
                        .then(response => {

                            this.reservations = response.data.reservations;

                        });

                },
                getPaddedNumber(number) {

                    return number.toString().padStart(2, '0'); // ゼロ埋めして必ず２ケタにする

                },
                getTimeRange(hours, minutes, timeSteps) {

                    const startDt = this.dt.set({
                        hours: hours,
                        minutes: minutes
                    })
                    const endDt = startDt.plus({
                        minutes: timeSteps
                    });
                    return startDt.toFormat('H:mm') + ' 〜 ' + endDt.toFormat('H:mm') + ' のご予約';

                },
                isReservationAvailable(roomId, hours, minutes) {

                    const today = luxon.DateTime.now().startOf('day');

                    if (this.dt < today) {

                        return false;

                    }

                    const dt = this.dt.set({
                        hours: hours,
                        minutes: minutes
                    })
                    const startsAt = dt.toFormat('yyyy-MM-dd HH:mm:00');
                    const hasReservation = this.reservations.some(reservation => { // 指定した条件が存在してたら true

                        return (
                            parseInt(reservation.room_id) === parseInt(roomId) &&
                            reservation.starts_at === startsAt
                        )

                    });

                    return !hasReservation;

                },
                moveDate(days) {

                    this.dt = this.dt.plus({
                        days: days
                    });

                },
                reserve(roomId, hours, minutes) {

                    if (confirm('予約します。よろしいですか？')) {

                        const dt = this.dt.set({
                            hours: hours,
                            minutes: minutes
                        });

                        const url = '{{ route('reservation.store') }}';
                        const params = {
                            room_id: roomId,
                            start_at: dt.toFormat('yyyy-MM-dd HH:mm')
                        };
                        axios.post(url, params)
                            .then(response => {

                                if (response.data.result === true) {

                                    this.getReservations();

                                }

                            });

                    }

                }
            },
            computed: {
                date() {

                    if (this.dt) {

                        return this.dt.toFormat('yyyy/MM/dd');

                    }

                    return '';

                }
            },
            watch: {
                dt() {

                    this.getReservations();

                }
            },
            mounted() {

                for (let i = 0; i < 24; i++) {

                    this.allHours.push(i);

                }

                this.dt = luxon.DateTime.now().startOf('day');

                Vue.nextTick(() => {

                    $('[data-toggle="tooltip"]').tooltip({
                        placement: 'right'
                    });

                });

            }
        }).mount('#app');
    </script>
</body>

</html>
