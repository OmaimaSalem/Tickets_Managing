<template>
    <div data-app>

        <div class="row">
            <button :disabled="!enableSettings" @click="showSettingsModel" class="btn btn-primary pull-right">
                <i class="fa fa-cog"></i>
            </button>
            <button @click="showTimesModel" class="btn btn-primary pull-right">
                <i class="fa fa-clock"></i>
            </button>
           <div class="col-md-6">
                 <select v-if="isAdmin()"
                    class="form-control"
                    @change="change_users_switcher"
                    v-model="users_switcher"
                >
                    <option
                        v-for="(user, user_index) in users"
                        :key="user_index"
                        :value="user.id"
                        >{{ user.name }}</option
                    >
                </select>

                <select 
                
                    class="form-control"
                    @change="change_users_switcher"
                    v-model="users_switcher"
                    v-if="!isAdmin() && Object.values(showCalendars).length > 0">
                      <option
                        v-for="(user, user_index) in showCalendars"
                        :key="user_index"
                        :value="user.id"
                        >{{ user.name }}</option>
                </select>
            </div> 
        </div>


        <br />
        share calendar link 
        <input type="text" class="form-control" readonly v-model="ShareYourCalendarLink"/>
        <br />
        <!-- wrapping dev -->
        <FullCalendar
            defaultView="dayGridMonth"
            :plugins="calendarPlugins"
            @dateClick="handleDateClick"
            @eventClick="eventClick"
            @selectOverlap="selectOverlap"
            selectable="true"
            :events="events"
            @datesRender="changeMonth"
        />

        <div
            class="modal fade"
            id="addTaskModal"
            tabindex="-1"
            role="dialog"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5
                            v-if="isOwner()"
                            class="modal-title"
                            id="exampleModalLabel"
                        >
                            {{ $t("Calender.addEvent") }}
                        </h5>
                        <h5
                            v-if="!isOwner() && users"
                            class="modal-title"
                            id="exampleModalLabel"
                        >
                            {{ $t("Calender.addEventTo") }}
                            {{ selectedUser() ? selectedUser().name : "" }}
                            {{ $t("Calender.events") }}
                        </h5>

<!--                        <button-->
<!--                            type="button"-->
<!--                            class="close"-->
<!--                            data-dismiss="modal"-->
<!--                            aria-label="Close"-->
<!--                        >-->
<!--                            <span aria-hidden="true">&times;</span>-->
<!--                        </button>-->
                        <v-btn type="button"  v-if="!editing" @click="addEvent" color="#ffffff" style="background-color:#234FA3 " text>  {{ $t("Calender.add") }}</v-btn>
                        <v-btn type="button"  v-if="editing && isOwner()"  @click="updateEvent" color="#ffffff" style="background-color:#234FA3 " text> {{ $t("Calender.update") }}</v-btn>
                        <v-btn type="button" v-if="editing && isOwner()"  @click="deleteEvent" color="#ffffff" style="background-color:#FF0000 " text> {{ $t("Calender.delete") }}</v-btn>
                        <v-btn data-dismiss="modal" data-bind="click: CancelAddNewTask" style="background-color:#717171" color="#ffffff" dark text >   {{ $t("Calender.cancle") }}</v-btn>
                    </div>

                    <div class="modal-body">
                        <div class="form-group" style="margin-top:-1rem">
                            <input
                                type="text"
                                id="eventtitle"
                                v-model="form.title"
                                :placeholder="$t('Calender.enterEvent')"
                                class="form-control"
                            />
                            <div class="error" v-if="submit && !form.title">
                                {{ $t("Calender.titleReq") }}
                            </div>
                        </div>
                        {{ errorMessage }}
                        <div class="form-group">
                            <div class="mb-3" style="margin-top:-1rem">
                                {{ $t("Calender.date") }}
                                <br />
                                <date-picker
                                    v-model="form.date"
                                    ref="date"
                                    :placeholder="$t('Calender.enterStart')"
                                    lang="en"
                                    type="date"
                                    format="DD-MM-YYYY"
                                    value-type="format"
                                    input-class="form-control"
                                ></date-picker
                                >&nbsp;&nbsp;&nbsp;
                                <label>
                                    {{ $t("Calender.allDay") }}
                                    <input
                                        type="checkbox"
                                        v-model="form.all_day"
                                    />
                                </label>
                            </div>

                            <div v-if="!form.all_day" class="row mb-3" style="margin-top:-1rem">
                                <div class="col-md-6" style="margin-top:-1rem">
                                    {{ $t("Calender.form") }}
                                    <br />
                                    <date-picker
                                        v-model="form.from"
                                        :placeholder="$t('Calender.enterEnd')"
                                        :minute-step="15"
                                        lang="en"
                                        type="time"
                                        format="HH:mm"
                                        value-type="format"
                                        input-class="form-control"
                                        :open.sync="openFrom"
                                        @change="handleChangeFrom"
                                    ></date-picker>
                                    <div class="error" v-if="submit && !form.from">
                                       Required
                                    </div>

                                </div>

                                <div class="col-md-6" style="margin-top:-1rem">
                                    {{ $t("Calender.to") }}
                                    <br />
                                    <date-picker
                                        v-model="form.to"
                                        :minute-step="15"
                                        :placeholder="$t('Calender.enterEnd')"
                                        lang="en"
                                        type="time"
                                        format="HH:mm"
                                        value-type="format"
                                        input-class="form-control"
                                        :open.sync="openTo"
                                        @change="handleChangeTo"
                                    ></date-picker>
                                    <div class="error" v-if="submit && !form.to">
                                       Required
                                    </div>

                                </div>
                            </div>

                            <div style="margin-top:-1rem">
                                {{ $t("Calender.location") }}
                                <br />
                                <input
                                    v-model="form.location"
                                    :placeholder="$t('Calender.enterLocation')"
                                    lang="en"
                                    type="text"
                                    class="form-control"
                                />
                            </div>





                              <div>

                                    <v-select
                                        v-model="form.category_id"
                                        :items="categories"
                                        item-text="title"
                                        item-value="id"
                                        label="Category"
                                        clearable
                                    ></v-select>

                                <div
                                    class="error"
                                    v-if="submit && !form.category_id"
                                >
                                    {{ $t("Calender.categoryReq") }}
                                </div>

                                <router-link to="calendar/categories">New category</router-link>
                            </div>



                        </div>

                        <div>
                            {{ $t("Calender.attachment") }}
                            <br />
                                <input 
                                    type="file" 
                                    @change="uploadAttchment" 
                                    class="form-control"
                                />                            

                            <div v-if="attachment != ''">
                                <a @click="downloadAttachment" target="_blank">Attachment</a>
                                <button @click="deleteAttachment"><i class="fa fa-trash" aria-hidden="true"></i></button>
                            </div>
                        </div>


                        <div>
                            {{ $t("Calender.desc") }}
                            <br />
                            <quill-editor

                                v-model="form.description"
                                ref="myQuillEditor"
                                :options="editorOption"
                            ></quill-editor>
                            <div
                                class="error"
                                v-if="submit && !form.description"
                            >
                                {{ $t("Calender.descReq") }}
                            </div>
                        </div>




                        <div>
                            <label>
                                {{ $t("Calender.repeat") }}
                                <input type="checkbox" v-model="form.repeat" />
                            </label>
                        </div>

                        <div class="row" v-if="form.repeat">
                            <div class="col-md-6" style="margin-top:-1rem">
                                <label class="col-md-12">
                                    {{ $t("Calender.type") }}
                                    <select
                                        class="form-control"
                                        v-model="form.repeat_type"
                                    >
                                        <option value="daily">{{
                                            $t("Calender.daily")
                                        }}</option>
                                        <option value="weakly">{{
                                            $t("Calender.weakly")
                                        }}</option>
                                        <option value="monthly">{{
                                            $t("Calender.monthly")
                                        }}</option>
                                    </select>
                                </label>
                            </div>

                            <div class="col-md-6" style="margin-top:-1rem">
                                <label>
                                    {{ $t("Calender.untill") }}
                                    <date-picker
                                        v-model="form.end_at"
                                        :placeholder="$t('Calender.enterStart')"
                                        lang="en"
                                        type="date"
                                        format="DD-MM-YYYY"
                                        value-type="format"
                                        input-class="form-control"
                                    ></date-picker>
                                </label>
                            </div>
                        </div>

                        {{ form.name}}<br />
                        {{ form.email}}<br />
                        {{ form.mobile}}<br />
                        
                        <div v-if="form.mobile">
                            <button class="btn btn-primary" @click="approve(form.id)">Approve</button>
                            <button class="btn btn-danger" @click="reject(form.id)">Reject</button>
                        </div>

                        <ul class="list-unstyled">
                            <li
                                v-for="(attendee, index) in form.attendees"
                                :key="index + 1"
                                class
                            >
                                {{ $t("Calender.user") }} {{ index + 1 }}
                                <select
                                    style="margin-top:-1rem"
                                    class="form-control"
                                    v-model="form.attendees[index]"
                                >
                                    <option
                                        v-for="(user, user_index) in users"
                                        :key="user_index"
                                        :value="user.id"
                                        >{{ user.name }}</option
                                    >
                                </select>
                                <span @click="removeAttendee(index)"
                                    >&times;</span
                                >
                            </li>
                        </ul>

<!--                        <v-btn type="button"   @click="increaseAttendee" color="#ffffff" style="background-color:#234FA3 " text> </v-btn>-->
                        <v-btn
                            color="#234FA3"
                            dark
                            fab
                            style=" ;z-index: 100;width: 2rem;height: 2rem;text-decoration: none;margin-left: 1rem;margin-top:-.3rem"
                            :title="$t('Calender.attendees')"
                            @click.stop="increaseAttendee">
                            <v-icon style="font-size: .5rem">fas fa-plus</v-icon>
                        </v-btn>

                    </div>

                </div>
            </div>
        </div>





        <!-- Settings Modal -->
        <div class="modal fade" id="settingsModel" tabindex="-1" role="dialog" aria-labelledby="settingsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="settingsModalLabel">Settings Model</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
        <form>
            <div class="form-group">
                <span for="name">can see my calendar</span>

                <multiselect
                    v-model="settingsform.canshow"
                    :options="users"
                    placeholder="Select"
                    label="name"
                    track-by="id"
                    :multiple="true"
                ></multiselect>

                <has-error :form="settingsform" field="canshow"></has-error>


                <span for="name">can edit my calendar</span>

                <multiselect
                    v-model="settingsform.canedit"
                    :options="users"
                    placeholder="Select"
                    label="name"
                    track-by="id"
                    :multiple="true"
                ></multiselect>

                <has-error :form="settingsform" field="canedit"></has-error>



                <span for="name">can see my events names</span>

                <multiselect
                    v-model="settingsform.canshowevents"
                    :options="users"
                    placeholder="Select"
                    label="name"
                    track-by="id"
                    :multiple="true"
                ></multiselect>

                <has-error :form="settingsform" field="canshowevents"></has-error>



                <span for="name">caldav settings</span><br />
                <span for="name">Username</span>
                <input type="text"
                    class="form-control"
                    v-model="settingsform.remoteusername"
                />
                <span for="name">password</span>
                <input type="password"
                    class="form-control"
                    v-model="settingsform.remotepassword"
                />

                <has-error :form="settingsform" field="canshowevents"></has-error>

            </div>
        </form>



            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" @click="saveSettigs">Save changes</button>
            </div>
            </div>
        </div>
        </div>
        <!-- Settings Modal -->

        <!-- times Model -->
        <div class="modal fade" id="timesModel" tabindex="-1" role="dialog" aria-labelledby="timesModelLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="timesModelLabel">Available Times modal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                

                <div v-for="(time,tindex) in times" :key="tindex">
                    <div class="form-group row">
                        <label class="col-md-2" >
                            {{ times[tindex].error ? "Error" : ""}}
                            {{tindex | capitalize}}:
                        </label>

                    <div v-for="(montime,index) in times[tindex]" :key="index" class="row">    
                        <div class="col-md-5">
                            <date-picker
                                v-model="montime.from"
                                placeholder="Start"
                                :minute-step="15"
                                lang="en"
                                type="time"
                                format="HH:mm"
                                value-type="format"
                                input-class="form-control"
                            ></date-picker>
                        </div>                   
                        
                        <div class="col-md-5">
                            <date-picker
                                v-model="montime.to"
                                placeholder="end"
                                :minute-step="15"
                                lang="en"
                                type="time"
                                format="HH:mm"
                                value-type="format"
                                input-class="form-control"
                            ></date-picker>
                        </div>

                        <button class="btn btn-danger" @click="decrease(tindex,index)">-</button>
                    </div>    
                    <button class="btn btn-success" @click="increase(tindex)">+</button>
                    </div><!-- row -->
                </div>








            </div>


            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" @click="savetimes">Save times</button>
            </div>
            </div>
        </div>
        </div>

        <!-- times Model -->

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
import 'vue2-datepicker/index.css';

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
            calendarPlugins: [dayGridPlugin, interactionPlugin],
            editing: false,
            selectable: true,
            form: { attendees: []},
            value: null,
            openFrom: false,
            openTo: false,
            enableSettings: false,
            times: {
                mon:[],
                tue:[],
                wed:[],
                thu:[],
                fri:[],
                sat:[],
                sun:[],
            },
            settingsform: new Form({
                canshow:null,
                canedit:null,
                canshowevents:null,
                remoteusername:null,
                remoteuserpassword:null
            }),
            eventtitle: null,
            original_title: null,
            errorMessage: "",
            attendees: [],
            events: [],
            ShareYourCalendarLink: null,
            submit: false,
            users_switcher: null,
            editorOption: {
                modules: {
                    toolbar: [
                        ["bold", "italic", "underline", "strike"],
                        ["blockquote", "code-block"],
                        [{ list: "ordered" }, { list: "bullet" }]
                    ]
                }
            },
            attachment: null,
            attachment_type: null,
            attachment_name: null,
        }
    },
    methods: {
        deleteAttachment(id){
            this.attachment = null;
            this.attachment_type = null;
            this.attachment_name = null;
        },
        downloadAttachment(){
            var link = document.createElement("a");
            document.body.appendChild(link);
            link.setAttribute("type", "hidden");
            link.href = "data:"+this.attachment_type+";base64," + this.attachment;
            link.download = this.attachment_name;
            link.click();
            document.body.removeChild(link);
        },
        uploadAttchment(event){
            this.attachment = event.target.files.item(0);
        },
        increase(day){
            const days = {
                mon:1,
                tue:2,
                wed:3,
                thu:4,
                fri:5,
                sat:6,
                sun:0,
            };
            this.times[day].push({
                    day_number: days[day],
                    from:null,
                    to: null
                });
        },
        decrease(day,index){
            this.times[day].splice(index,1);
        },
        // getDayDate(target){
        //     var curr = new Date; // get current date
        //     var day = curr.getDate() + (curr.getDay() + target - 1); // First day is the day of the month - the day of the week
        //     return new Date(curr.setDate(day)).toUTCString();
        // },
        // getTimeWithDate(day,time) {
        //     let date = new Date(day);
        //     var _t = time.split(":");
        //     date.setHours(_t[0], _t[1], 0, 0);
        //     return date;
        // },
        // validate(timesArray,date,sTime, eTime) {
        //     if (+this.getTimeWithDate(date,sTime) < +this.getTimeWithDate(date,eTime)) {
        //         var len = timesArray.length;
        //             console.log(len);
        //         } else {
        //         return false;
        //     }
        // },
        checkOverlap(timeSegments){
            if (timeSegments.length === 1) return false;

            timeSegments.sort((timeSegment1, timeSegment2) =>
                timeSegment1[0].localeCompare(timeSegment2[0])
            );

            for (let i = 0; i < timeSegments.length - 1; i++) {
                const currentEndTime = timeSegments[i][1];
                const nextStartTime = timeSegments[i + 1][0];

                if (currentEndTime > nextStartTime) {
                  return true;
                }
            }

            return false;
        },


        async savetimes(){

            let $items = {  
                mon:[],
                tue:[],
                wed:[],
                thu:[],
                fri:[],
                sat:[],
                sun:[],
            };
            Object.keys(this.times).forEach(day => {
                 this.times[day].forEach(element => {
                    $items[day].push([
                        element.from,
                        element.to
                    ]);
                 });
                if($items[day].length > 0){

                    if(this.checkOverlap($items[day])){
                        this.times[day].error = true;
                        alert('invalid times');
                        throw new Error('invalid times');
                    }
                    else {
                        this.times[day].error = false;
                    }

                }
            });

            $vm.$forceUpdate();


            this.$Progress.start();
            this.$store
                .dispatch("calendar/addFreeTimes", {times:this.times})
                .then(response => {
                    console.log(response);
                    this.$Progress.finish();
                    $("#timesModel").modal("hide");
                })
                .catch(error => {
                    console.log(error);
                    this.$Progress.fail();
                });
        },
        getFreeTimes(){
            this.$Progress.start();
            const $this = this;
            this.$store
                .dispatch("calendar/getFreeTimes")
                .then(response => {
                    response.data.data.forEach(function(time){
                        $this.times[time.day].push({
                            from: time.from,
                            to: time.to,
                            day_number: time.day_number
                        });
                    });
                })
                .catch(error => {
                    console.log(error);
                    this.$Progress.fail();
                });
        },
        approve(id){
            this.$Progress.start();
            this.$store
                .dispatch("calendar/approveEvent", {id})
                .then(response => {

                    let event_id = response.data.data;
                    let index = this.events.findIndex(
                        event => parseInt(event.id) === parseInt(event_id)
                    );

                    this.events[index].backgroundColor = "#ffffff";
                    this.events[index].textColor = "#000000";
                    
                    this.$Progress.finish();
                    $("#addTaskModal").modal("hide");
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },
        reject(id){
            this.$Progress.start();
            this.$store
                .dispatch("calendar/rejectEvent", {id})
                .then(response => {

                    let event_id = response.data.data;
                    this.events = this.events.filter(
                        event => parseInt(event.id) !== parseInt(event_id)
                    );

                    
                    this.$Progress.finish();
                    $("#addTaskModal").modal("hide");
                })
                .catch(error => {
                    this.$Progress.fail();
                });

        },
        handleChangeFrom(value, type){
            if(type === "minute")
                this.openFrom = false;
        },
        handleChangeTo(value, type){
            if(type === "minute")
                this.openTo = false;
        },
        showSettingsModel(){
            $("#settingsModel").modal("show");
        },
        showTimesModel(){
            $("#timesModel").modal("show");
        },
        saveSettigs(){
            this.$Progress.start();
            this.$store
                .dispatch("calendar/saveSettings", this.settingsform)
                .then(response => {
                    this.$Progress.finish();
                    $("#settingsModel").modal("hide");
                })
                .catch(error => {
                    this.$Progress.fail();
                    if (error.response) {
                        this.errorMessage = error.response.data.message.error;
                        this.form.errors.errors = error.response.data.errors;
                    }
                });
        },
        getSettigs(){
            this.$store
                .dispatch("calendar/getSettings")
                .then(response => {
                    let $res = response.data.data;
                    this.settingsform.canshow        = this.users.filter(item => $res.canshow.includes(item.id));
                    this.settingsform.canedit        = this.users.filter(item => $res.canedit.includes(item.id));
                    this.settingsform.canshowevents  = this.users.filter(item => $res.canshowevents.includes(item.id));
                    this.settingsform.remoteusername = $res.username;
                    this.settingsform.remotepassword = $res.password;
                    this.enableSettings = true;
                })
                .catch(error => {
                    console.log(error);
                });

        },
        handleDateClick(args) {
            this.editing = false;
            this.form = { attendees: [] };
            let date_arr = args.dateStr.split("-");
            this.form = {
                date:  date_arr[2]+"-"+date_arr[1]+"-"+date_arr[0],
                from:  this.getTime(new Date()),
                to:    this.getTime(new Date()),
                attendees: []
            };
            $("#addTaskModal").modal("show");
        },

        increaseAttendee() {
            if (this.form.attendees.length >= this.users.length) return false;
            this.form.attendees.push({ value: "" });
        },
        removeAttendee(index) {
            this.form.attendees.splice(index, 1);
        },
        selectOverlap(args) {
            console.log(args);
        },
        changeMonth(args) {
            this.events = [];
            var currentStart = args.view.currentStart;
            let $userid = this.$route.params.userid;
            this.getCalendar(currentStart.getMonth() + 1, $userid);
        },
        addEvent() {
            if (
                !this.form.description ||
                !this.form.title ||
                (!this.form.all_day && (!this.form.from || !this.form.to))
            ) {
                this.submit = true;
                return false;
            } else {
                this.submit = false;
            }

            this.form.owner = this.theOwner();
            this.form.creator = this.theCreator();
            // this.form.attachment = this.attachment;

            let formData = new FormData();
            Object.keys(this.form).forEach(($key)=>{
                formData.append($key, this.form[$key]);
            });
            formData.append('attachment', this.attachment);

            this.errorMessage = "";
            this.$Progress.start();
            this.$store
                .dispatch("calendar/addEvent", formData)
                .then(response => {
                    this.events.push(response.data.data);
                    this.$Progress.finish();
                    this.loading = false;
                    $("#addTaskModal").modal("hide");
                })
                .catch(error => {
                    this.$Progress.fail();
                    if (error.response) {
                        this.errorMessage = error.response.data.message.error;
                        this.form.errors.errors = error.response.data.errors;
                    }
                    this.loading = false;
                });
        },
        eventClick(args, date) {
            if (!this.isOwner()) return false;
            let event = args.event;
            let props = event.extendedProps;
            this.form = {
                id: args.event.id,
                title: args.event.title,
                date: this.getDate(event.start),
                from: this.getTime(event.start),
                to: this.getTime(event.end),
                all_day: props.all_day,
                location: props.location,
                description: props.description,
                repeat: props.repeat,
                repeat_type: props.repeat_type,
                end_at: props.end_at,
                attendees: props.attendees,
                category_id: props.category_id,
                approved: props.approved,
                name: props.name,
                email: props.email,
                mobile: props.mobile,
                user_id:this.theOwner()
            };
            this.attachment      = props.attachment;
            this.attachment_type = props.attachment_type;
            this.attachment_name = props.attachment_name;
            this.editing = true;
            $("#addTaskModal").modal("show");
        },
        updateEvent() {
            this.errorMessage = "";
            this.$Progress.start();


            let formData = new FormData();
            Object.keys(this.form).forEach(($key)=>{
                formData.append($key, this.form[$key]);
            });
            formData.append('attachment', this.attachment);



            this.$store
                .dispatch("calendar/updateEvent", formData)
                .then(response => {
                    let scope = this;

                    let dataEvent = response.data.data.event;

                    let index = this.events.findIndex(
                        event => parseInt(event.id) === parseInt(scope.form.id)
                    );

                    this.events.splice(index, 1);
                    this.events.push(dataEvent);
                    $("#addTaskModal").modal("hide");
                    this.loading = false;
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                    if (error.response) {
                        this.errorMessage = error.response.data.message.error;
                        this.form.errors.errors = error.response.data.errors;
                    }
                    this.loading = false;
                });
        },
        deleteEvent() {
            this.errorMessage = "";
            let event = {
                id: this.form.id,
                user_id:this.theOwner()
            };

            this.$Progress.start();
            this.$store
                .dispatch("calendar/deleteEvent", event)
                .then(response => {
                    this.$Progress.finish();
                    this.loading = false;
                    let scope = this;
                    this.events = this.events.filter(function(revent) {
                        return revent.id != event.id;
                    });
                    $("#addTaskModal").modal("hide");
                })
                .catch(error => {
                    this.$Progress.fail();
                    if (error.response) {
                        this.errorMessage = error.response.data.message.error;
                        this.form.errors.errors = error.response.data.errors;
                    }
                    this.loading = false;
                });
        },

        selectPeriod(args) {
            this.attendees = [];
            this.datetimefrom = args.startStr + " 00:00:00";
            this.datetimeto = args.endStr + " 00:00:00";
            $("#addTaskModal").modal("show");
        },
        getCalendar(month, userid = null) {
            this.$Progress.start();
            this.$store
                .dispatch("calendar/getCalendar", {
                    month: month,
                    userid: userid
                })
                .then(response => {
                    this.events = [];
                    response.data.data.events.forEach(event => {
                        this.events.push(event);
                    });
                    this.$Progress.finish();
                    this.loading = false;
                })
                .catch(error => {
                    this.$Progress.fail();
                    if (error.response) {
                        this.form.errors.errors = error.response.data.errors;
                    }
                    this.loading = false;
                });
        },

        getDate(date) {
            let newdate = new Date(date);
            let year = newdate.getFullYear();
            let month = `${newdate.getMonth() + 1}`.padStart(2, "0");
            let day = `${newdate.getDate()}`.padStart(2, "0");
            let stringDate = [day, month, year].join("-");

            let hours = `${newdate.getHours()}`.padStart(2, "0");
            let minutes = `${newdate.getMinutes()}`.padStart(2, "0");
            let seconds = `${newdate.getSeconds()}`.padStart(2, "0");
            let stringTime = [hours, minutes, seconds].join(":");

            let fullDate = stringDate + " " + stringTime;
            return fullDate;
        },
        getTime(date) {
            let newdate = new Date(date);
            let hours = `${newdate.getHours()}`.padStart(2, "0");
            let minutes = `${newdate.getMinutes()}`.padStart(2, "0");
            //let seconds = `${newdate.getSeconds()}`.padStart(2, "0");
            //let stringTime = [hours, minutes, seconds].join(":");
            let stringTime = [hours, minutes].join(":");

            return stringTime;
        },

        getUsers() {
            this.$Progress.start();
            this.$store
                .dispatch("regularUser/getRegularUser",{rules:false,dropdown:true})
                .then(res => {
                    this.getSettigs();
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },
        change_users_switcher() {
            let userId = this.users_switcher;
            this.$router.push({ path: `/admin/calendar/${userId}` });
        },
        selectedUser() {
            let $this = this;
            return this.users.filter(function($user) {
                return $user.id == $this.users_switcher;
            })[0];
        },
        isOwner() {
            if (localStorage.getItem("alferp")) {
                let user = JSON.parse(localStorage.getItem("alferp")).user
                    .singleUser;
                return this.users_switcher
                    ? user.id == this.users_switcher
                    : true;
            }
        },
        theOwner() {
            return this.$route.params.userid
                ? parseInt(this.$route.params.userid)
                : parseInt(this.theCreator());
        },
        theCreator() {
            if (localStorage.getItem("alferp")) {
                let user = JSON.parse(localStorage.getItem("alferp")).user
                    .singleUser;
                return parseInt(user.id);
            }
        },

        isAdmin() {
            if (localStorage.getItem("alferp")) {
                let user = JSON.parse(localStorage.getItem("alferp")).user
                    .singleUser;
                let roles = user.roles
                    ? user.roles.map(role => role.name)
                    : null;
                return roles ? roles.includes("Full Administrator") : false;
            }
            return false;
        },

        getCategories() {
            this.$Progress.start();
            this.$store
                .dispatch("calendar/getCategories")
                .then(response => {
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                    if (error.response) {
                        this.form.errors.errors = error.response.data.errors;
                    }
                });
        },
        getShowCalendars(){
            this.$Progress.start();
            this.$store
                .dispatch("calendar/getShowCalendars")
                .then(response => {
                    console.log(response.data.data);
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },

        resetCalendarNotifications(){
            this.$Progress.start();
            this.$store
                .dispatch("notification/resetLayoutNotifications")
                .then(response => {
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },

    },
    beforeRouteLeave: function(to, from, next) {
        $("#addTaskModal").modal("hide");
        next();
    },
    mounted() {
        let $userid = this.$route.params.userid;
        this.users_switcher = $userid;
        this.getUsers();
        this.getCategories();
        this.getShowCalendars();
        this.form.from = this.getTime(new Date());
        this.form.to   = this.getTime(new Date()) 
        this.getFreeTimes();
        this.resetCalendarNotifications();
        var url = window.location.href
        var arr = url.split("/");
        var share = arr[0] + "//" + arr[2]+"/share-calendar/"+this.theCreator();
        this.ShareYourCalendarLink = share;
    },
    computed: {
        ...mapGetters({
            calendar: "calendar/Calendar",
            showCalendars: "calendar/showCalendars",
            users: "regularUser/getRegularUsers",
            categories: "calendar/Categories",
        })
    },
    watch: {
        // 'form.from' (newDate, oldDate) {
        //     console.log("fff");
        //     this.$refs.from.closePopup()
        // },
        // 'form.to' (newDate, oldDate) {
        //     this.$refs.to.closePopup()
        // },
        // 'form.date' (newDate, oldDate) {
        //     this.$refs.date.closePopup()
        // },
    }
    

};
</script>

<style scoped>
.error {
    color: red;
}

.fc-button-group .fc-prev-button, .fc-button-group .fc-next-button{
   background-color: transparent !important;
}


.mx-datepicker{
    width:100% !important;
}
</style>








