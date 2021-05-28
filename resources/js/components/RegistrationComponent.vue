<template>
    <div class="registration">
        <h3>Registration</h3>
        <b-row class="d-flex justify-content-center">
            <b-col class="calendar"><FullCalendar v-if="game_dates" ref="fullCalendar" :options="calendarOptions" /></b-col>
        </b-row>
    </div>
</template>
<script>
import FullCalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";

export default {
        props: ['gamedates', 'calstart'],
        components: {
            FullCalendar,
        },
        data() {
            return {
                game_dates: null,
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
                            click: function() {
                                console.log('clicked addEvent');
                            }
                        }
                    },
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
                    eventClick: (info) => {
                        this.showDate(info.event.id)
                    },
                    eventMouseEnter: function(info) {
                        $(info.el).tooltip({title: info.event.extendedProps.description}); 
                    },
                    loading: function( isLoading, view ) {
                        if(isLoading) {// isLoading gives boolean value
                            // console.log('loading')
                        } else {
                            // console.log('not loading')
                        }
                    }
                }
            }
        },
        mounted() {
            this.game_dates = this.formatDates(this.gamedates)
        },
        methods:{
            addReg(){
                
            },
            register(){

            },
            showDate(id){
                console.log('showDate', id)
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
                    let num = date.regs.length
                    new_dates.push(
                        {
                            id: date.id,
                            title: 'Step' + date.step,
                            start: date.date.replace(' ', 'T'),
                            description: 'Admin: ' + admin + ', Players: ' + num + '/10'
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
                height: 570px!important;
                .fc-event{
                    cursor: pointer;
                }
                .fc-daygrid-body{
                    height: auto!important;
                    table{
                        height: auto!important;
                        tbody{
                            tr{
                                height: 90px!important;
                                overflow: hidden;
                            }
                        }
                    }
                    
                }
            }


        }
    }
}
</style>