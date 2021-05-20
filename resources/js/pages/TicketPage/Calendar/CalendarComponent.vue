<template>
    <div data-app>
        <!-- wrapping dev -->
        <FullCalendar
            defaultView="dayGridMonth"
            :plugins="calendarPlugins"
            selectable="true"
            :events   ="events"
            @dateClick="handleDateClick"
            @eventClick="eventClick"
        />



<div class="modal fade" id="addEventModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header text-center">
        <h4 class="modal-title w-100 font-weight-bold text-left">Add Event</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body mx-3">
        <div class="md-form mb-5">
        <label for="event_title">Event title</label>
          <input v-model="form.title" type="email" id="event_title" class="form-control">
          <has-error :form="form" field="title"></has-error>
        </div>

        <div class="md-form mb-4">
        <label for="defaultForm-pass">Event description</label>
          <textarea v-model="form.description"  class="form-control" name="description" id="event_description" cols="30" rows="5"></textarea>
          <has-error :form="form" field="description"></has-error>
        </div>

      </div>
      <div class="modal-footer d-flex justify-content-center">
        <button v-if="!form.id" class="btn btn-default" @click="createNewEvent">save</button>
        <button v-if="form.id" class="btn btn-default" @click="updateEvent">update</button>
        <button v-if="form.id" class="btn btn-default" @click="deleteEvent">delete</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">cancel</button>
      </div>
    </div>
  </div>
</div>



    </div>
    <!-- end of wrapping dev -->
</template>

<style lang="scss">
@import "~@fullcalendar/core/main.css";
@import "~@fullcalendar/daygrid/main.css";
</style>

<script>
import FullCalendar from "@fullcalendar/vue";
import DatePicker from "vue2-datepicker";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import { mapGetters } from "vuex";
import { quillEditor } from "vue-quill-editor";

import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";

import { DateEnv } from "@fullcalendar/core";

export default {
    components: {
        FullCalendar,
        DatePicker,
        quillEditor
    },
    data() {
        return {
            events: [],
            calendarPlugins: [dayGridPlugin, interactionPlugin],
            editorOption: {
                modules: {
                    toolbar: [
                        ["bold", "italic", "underline", "strike"],
                        ["blockquote", "code-block"],
                        [{ list: "ordered" }, { list: "bullet" }]
                    ]
                }
            },
            form: new Form({
                date_time:null,
                title:null,
                description:null,
                id:null,
                ticket_id: this.$route.params.id
            })
        };
    },
    methods: {

        handleDateClick(args){
            this.form.clear();
            this.form.reset();
            this.form.date_time = args.dateStr + " 00:00:00";
            $("#addEventModal").modal("show");
        },
        createNewEvent(){
            this.$Progress.start();
            this.$store
                .dispatch("ticket/addTicketEvent",this.form)
                .then((response) => {
                    this.events.push(response.data.data.event);
                    this.form.clear();
                    this.form.reset();
                    this.$Progress.finish();
                    $("#addEventModal").modal("hide");

                })
                .catch((error) => {
                    if (error.response) {
                        this.form.errors.errors = error.response.data.errors;
                    }
                });
        },
        eventClick(args){
            this.form.fill({
                title: args.event.title,
                description: args.event.extendedProps.description,
                id:args.event.id,
                ticket_id: this.$route.params.id,
            });
            if(!this.form.id) return false;

            $("#addEventModal").modal("show");
        },

        updateEvent(){
            this.$Progress.start();
            this.$store
                .dispatch("ticket/editTicketEvent",this.form)
                .then((response) => {

                    let scope = this;
                    let dataEvent = response.data.data.event;
                    let index = this.events.findIndex(
                        event => parseInt(event.id) === parseInt(scope.form.id)
                    );

                    this.events.splice(index, 1);
                    this.events.push(dataEvent);



                    this.form.clear();
                    this.form.reset();
                    this.$Progress.finish();
                    $("#addEventModal").modal("hide");
                })
                .catch((error) => {
                    if (error.response) {
                        this.form.errors.errors = error.response.data.errors;
                    }
                });
        },


        deleteEvent() {

      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
      }).then(result => {
        if (result.value) {



            this.errorMessage = "";
            let event = {
                id: this.form.id,
                ticket_id: this.$route.params.id
            };

            this.$Progress.start();
            this.$store
                .dispatch("ticket/deleteTicketEvent", event)
                .then(response => {
                    this.events = this.events.filter(function(revent) {
                        return revent.id != event.id;
                    });
                    this.$Progress.finish();
                    $("#addEventModal").modal("hide");
                })
                .catch(error => {
                });



        }});



        },

        getTicketEvents(){
            this.$Progress.start();
            this.$store
                .dispatch("ticket/getTicketEvents",{id:this.$route.params.id})
                .then((response) => {
                    this.events = [];
                    response.data.data.events.forEach(event => {
                        this.events.push(event);
                    });
                    this.$Progress.finish();
                })
                .catch((error) => {});
        }

    },

    mounted() {
        this.getTicketEvents();
    },
    computed: {
        ...mapGetters({
        })
    }
};
</script>

<style>
.error {
    color: red;
}
</style>
