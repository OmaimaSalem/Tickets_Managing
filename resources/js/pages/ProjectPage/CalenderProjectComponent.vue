<template>
    <v-container>
        <h3>{{$t('Project.calender')}}</h3>
        <div class="line"></div>
        <v-row class="fill-height">
            <FullCalendar
                defaultView="dayGridMonth"
                :plugins="calendarPlugins"
                :events="events"
                event-limit="3"
            />
        </v-row>
    </v-container>
</template>


<script>
import { mapActions, mapGetters } from "vuex";
import FullCalendar from "@fullcalendar/vue";
import DatePicker from "vue2-datepicker";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";

import { DateEnv } from "@fullcalendar/core";
import tickets from '../../api/tickets';
import VueRouter from 'vue-router';

export default {
    components: {
        FullCalendar,
    },
    data () {
        return {
            calendarPlugins: [dayGridPlugin, interactionPlugin],
            events: [],
            selectable: true,
        };
    },
    mounted () {
        this.getCalender();
    },
    methods: {
        getCalender(){
            let project_id = this.$route.params.id
            this.$Progress.start();
            this.$store.dispatch("project/getCalender", project_id)
                .then(response => {
                    this.$Progress.finish();
                    this.addEvents(response.data);
                })
                .catch(error => {
                    this.$Progress.fail();
                    if (error.response) {
                        this.form.errors.errors = error.response.data.errors;
                    }
                });
        },
        addEvents(data){
            const events = []
            const ticketsData = data[0];
            const tasksData = data[1];
            //Pushing Tickets events

            ticketsData.forEach(ticket =>{
                let status = '';
                if(ticket.status_id === 1){
                    status = '#AE69BE'
                }
                if(ticket.status_id === 2){
                    status = '#DDB456'
                }
                if(ticket.status_id === 3){
                    status = '#3A64A5'
                }
                if(ticket.status_id === 4){
                    status = '#81B488'
                }
                let temp = {
                    title: 'Start: ' + ticket.name,
                    start: ticket.created_at,
                    backgroundColor: '#ffffff',
                    textColor: '#000000',
                    url: '/admin/ticket/'+ ticket.id,
                    className: 'shadow mt-1',
                    borderColor: status,
                }
                events.push(temp)
                if(ticket.due_date !== null){
                    events.push({
                        title: 'End: '+ticket.name,
                        start: ticket.due_date,
                        backgroundColor: '#ffffff',
                        textColor: '#000000',
                        className: 'shadow mt-1',
                        url: '/admin/ticket/'+ ticket.id,
                        borderColor: status

                    })
                }
            })
            //Pushing Tasks events
            tasksData.forEach(task =>{
                 let status = '';
                if(task.status_id === 1){
                    status = '#AE69BE'
                }
                if(task.status_id === 2){
                    status = '#DDB456'
                }
                if(task.status_id === 3){
                    status = '#3A64A5'
                }
                if(task.status_id === 4){
                    status = '#81B488'
                }
                events.push({
                    title: 'Start: '+task.name,
                    start: task.start_at,
                    backgroundColor: '#ffffff',
                    textColor: '#000000',
                    url: '/admin/task/'+ task.id,
                    borderColor: status,
                    className: 'shadow mt-1',

                })
                events.push({
                    title: 'End: '+task.name,
                    start: task.deadline,
                    backgroundColor: '#ffffff',
                    textColor: '#000000',
                    url: '/admin/task/'+ task.id,
                    borderColor: status,
                    className: 'shadow mt-1',

                })
            })
            this.events = events
        }
    },
    computed: {
        ...mapGetters({
            calender: "project/getCalender",
        }),
    }
}
</script>
<style scoped>
.fc-event{
    border: 3px solid #000000;
}
.line{
    background-color: #234FA3;
    width: 5.3rem;
    height: .2rem;
    border-radius: 5px;
}
</style>
