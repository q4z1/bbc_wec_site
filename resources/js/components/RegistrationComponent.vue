<template>
    <div class="registration">
        <b-alert
            v-if="alert"
            :show="alert"
            dismissible
            :variant="alertVar"
            @dismissed="alert = false"
        >
        {{ alertMsg }}
        </b-alert>
        <h3>Registration</h3>
        <b-row class="d-flex justify-content-center">
            <b-col class="calendar"><FullCalendar v-if="game_dates" ref="fullCalendar" :options="calendarOptions" /></b-col>
        </b-row>
        <b-modal :size="gModalSize" id="modal-date" :title="(date) ? 'Step ' + date.step + ' - ' + date.date : ''"  hide-footer>
            <game-date-component @show-alert="showAlert" @update-dates="updateDates" v-if="date" :date="date" :fp="fp" />
        </b-modal>
        <b-modal id="modal-newdate" title="New Game" v-if="arole === 'a'" hide-footer>
            <game-date-new-component @show-alert="showAlert" @update-dates="updateDates" />
        </b-modal>
    </div>
</template>
<script>
import FullCalendar from "@fullcalendar/vue"
import dayGridPlugin from "@fullcalendar/daygrid"
import interactionPlugin from "@fullcalendar/interaction"
import GameDateComponent from './GameDateComponent.vue'
import FingerprintJS from '@fingerprintjs/fingerprintjs'
export default {
    props: ['gamedates', 'calstart'],
    components: {
        FullCalendar,
        GameDateComponent,
    },
    data() {
        return {
            game_dates: null,
            date: null,
            fp: null,
            gModalSize: 'md',
            alertVar: 'danger',
            alertMsg: '',
            alert: false,
            arole: (window.arole === 's') ? 'a' : window.arole,
        }
    },
    computed: {
        calendarOptions: function () {
            return {
                plugins: [dayGridPlugin, interactionPlugin],
                initialView: "dayGridMonth",
                customButtons: {
                    addEvent: {
                        text: '+',
                        click: () => {
                            this.$bvModal.show('modal-newdate')
                        }
                    }
                },
                firstDay: 1,
                validRange: {
                    start: this.calstart
                },
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: (window.arole === 's') ? 'addEvent' : '',
                },
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: false,
                    hour12: false
                },
                events: this.game_dates,
                eventDidMount: function(info) {
                    $(info.el).data('powertip', info.event.extendedProps.description).powerTip({
                        placement: 'n',
                        smartPlacement: true
                    })
                },
                eventClick: (info) => {
                    this.showDate(info.event.id)
                },
                eventMouseEnter: function(info) { },
                eventMouseLeave: function(info) { },
                loading: function( isLoading, view ) {
                    if(isLoading) {// isLoading gives boolean value

                    } else {

                    }
                }
            }
        }
    },
    mounted() {
        if(window.arole === 's') this.gModalSize = 'xl'
        this.game_dates = this.formatDates(this.gamedates)
        const fpPromise = FingerprintJS.load();(async () => {
            let fp = await fpPromise
            let result = await fp.get()
            this.fp = result.visitorId
        })()
    },
    methods:{
        showAlert(msg, variant, duration=5){
            // console.log('showAlert()', msg, variant, duration)
            this.alertVar = variant
            this.alertMsg = msg
            this.alert = duration
        },
        showDate(id){
            axios.get('/registration/date/get/' + id)
            .then(response => {
                if(response.data.success === true){
                    this.date = response.data.date
                    this.$bvModal.show('modal-date')
                }else{
                    console.log(response.data)
                }
            }, (error) => {
                console.log(error)
            });
        },
        updateDates(dates){
            this.game_dates = false
            this.$nextTick(() => {
                this.game_dates = this.formatDates(dates)
            })
        },
        formatDates(dates){
            let new_dates = []
            for(let i in dates){
                let date = dates[i]
                let admin = 'n/a'
                for(let j in date.regs){
                    let player = date.regs[j].player
                    if(player.admin && admin == 'n/a') admin = player.nickname
                }
                let colors = { 1: 'primary', 2: 'success', 3: 'warning', 4: 'danger'}
                let color = 'var(--' + colors[date.step] + ')'
                
                let num = date.regs.length
                new_dates.push(
                    {
                        id: date.id,
                        title: 'Step' + date.step,
                        start: date.date.replace(' ', 'T'),
                        end: date.date.replace(' ', 'T').replace(date.date.substr(date.date.length - 2, 2), '59'),
                        description: 'Admin: ' + admin + ', Players: ' + num + '/10',
                        color: color,
                    }
                )
            }
            return new_dates
        }
    }  
}
</script>
<style lang="scss">
.registration{
    .calendar{
        .fc{
            .fc-header-toolbar{
                .fc-addEvent-button{
                    font-size: 1.2em;
                    padding: 0.2em 0.65em;
                }
                .fc-icon{
                    font-size: 1.0em;
                }
            }
            .fc-view-harness{
                height: 720px!important;
                .fc-event{
                    cursor: pointer;
                }
                .fc-daygrid-body{
                    height: auto!important;
                    table{
                        height: auto!important;
                        tbody{
                            tr{
                                height: 115px!important;
                                td {
                                    overflow-y: scroll;
                                }
                            }
                        }
                    }
                    
                }
            }


        }
    }
}
</style>