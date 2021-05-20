<template>
    <div class="card card-primary">
        <FullCalendar
            defaultView="dayGridMonth"
            :plugins="calendarPlugins"
            selectable="true"
            ref="fullCalendar"
            @eventClick="eventClick"
            @selectOverlap="selectOverlap"
            @select="selectPeriod"
            :events="events"
        />

        <div id="vacationModal" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{$t('Vacation.vacation')}}</h5>
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label v-if="isAdmin()" for="user" class="col-md-12"
                                >{{$t('Vacation.user')}}</label
                            >
                            <div v-if="isAdmin()" class="col-md-12">
                                <select
                                    v-model="vacation.user_id"
                                    id="user"
                                    class="form-control"
                                >
                                    <option
                                        v-for="(user, $columnIndex) of users"
                                        :key="$columnIndex"
                                        :value="user.id"
                                        >{{ user.name }}</option
                                    >
                                </select>
                            </div>

                            <label for="name" class="col-md-12">{{$t('Vacation.form')}}</label>
                            <div class="col-md-6">
                                <date-picker
                                    v-model="vacation.day_from"
                                    lang="en"
                                    type="date"
                                    format="DD-MM-YYYY"
                                    input-class="form-control"
                                    value-type="format"
                                >
                                </date-picker>
                            </div>

                            <div class="col-md-6">
                                <date-picker
                                    v-model="vacation.time_from"
                                    lang="en"
                                    type="time"
                                    format="HH:mm"
                                    input-class="form-control"
                                    value-type="format"
                                >
                                </date-picker>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-md-12">{{$t('Vacation.to')}}</label>
                            <br />
                            <div class="col-md-6">
                                <date-picker
                                    v-model="vacation.day_to"
                                    lang="en"
                                    type="date"
                                    format="DD-MM-YYYY"
                                    input-class="form-control"
                                    value-type="format"
                                >
                                </date-picker>
                            </div>
                            <div class="col-md-6">
                                <date-picker
                                    v-model="vacation.time_to"
                                    lang="en"
                                    type="time"
                                    format="HH:mm"
                                    input-class="form-control"
                                    value-type="format"
                                >
                                </date-picker>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="name">{{$t('Vacation.priorty')}}</label>
                                <br />
                                <div>
                                    <select
                                        class="form-control"
                                        v-model="vacation.priority"
                                    >
                                        <option value="low">{{$t('Vacation.low')}}</option>
                                        <option value="medium">{{$t('Vacation.med')}}</option>
                                        <option value="high">{{$t('Vacation.high')}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label
                                    >{{$t('Vacation.sickVacation')}}
                                    <input
                                        type="checkbox"
                                        v-model="vacation.sick_vacation"
                                    />
                                </label>
                                <div v-if="vacation.sick_vacation">
                                    <input
                                        type="file"
                                        accept="image/*"
                                        @change="assignFile"
                                    />
                                </div>

                                <div>
                                    <div
                                        v-if="
                                            vacation.sick_image_file &&
                                                !this.vacation.upload
                                        "
                                    >
                                        <a
                                            :href="
                                                '/storage/sick_images/' +
                                                    vacation.sick_image_file
                                            "
                                            target="_blank"
                                        >
                                            <img
                                                :src="
                                                    '/storage/sick_images/' +
                                                        vacation.sick_image_file
                                                "
                                                height="200px"
                                                width="200px"
                                            />
                                        </a>
                                    </div>

                                    <div
                                        v-if="
                                            vacation.sick_image_file &&
                                                this.vacation.upload
                                        "
                                    >
                                        <a
                                            :href="vacation.imageUrl"
                                            target="_blank"
                                        >
                                            <img
                                                :src="vacation.imageUrl"
                                                height="200px"
                                                width="200px"
                                            />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="vacation.sick_vacation"
                            class="form-group row"
                        >
                            <div class="col-md-12">
                                <label for="name">{{$t('Vacation.consultation')}}</label>
                                <br />
                                <date-picker
                                    v-model="vacation.consultation"
                                    lang="en"
                                    type="date"
                                    format="DD-MM-YYYY"
                                    input-class="form-control col-md-12"
                                    value-type="format"
                                >
                                </date-picker>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-md-12">{{$t('Vacation.reason')}}</label>
                            <div class="col-md-12">
                                <textarea
                                    class="form-control"
                                    v-model="vacation.reason"
                                ></textarea>
                                <div v-if="!vacation.reason" class="">
                                    {{$t('Vacation.fieldReq')}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            @click="saveVacation()"
                            type="button"
                            class="btn btn-primary"
                        >
                            {{$t('Vacation.saveChanges')}}
                        </button>
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal"
                        >
                            {{$t('Vacation.close')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
import FullCalendar from "@fullcalendar/vue";
import interactionPlugin from "@fullcalendar/interaction";
import dayGridPlugin from "@fullcalendar/daygrid";
import DatePicker from "vue2-datepicker";

export default {
    components: { FullCalendar, DatePicker },
    data() {
        return {
            calendarPlugins: [dayGridPlugin, interactionPlugin],
            validRange: {
                start: new Date()
            },
            events: [],
            vacation: {
                day_from: null,
                time_from: null,
                day_to: null,
                time_to: null,
                reason: null,
                priority: null,
                sick_vacation: false,
                sick_image_file: null,
                imageUrl: null
            }
        };
    },
    methods: {
        eventClick(args) {
            this.vacation.user_id = args.event.extendedProps.user_id;
            this.vacation.day_from = args.event.extendedProps.day_from;
            this.vacation.day_to = args.event.extendedProps.day_to;
            this.vacation.time_from = args.event.extendedProps.time_from;
            this.vacation.time_to = args.event.extendedProps.time_to;
            this.vacation.reason = args.event.extendedProps.reason;
            this.vacation.priority = args.event.extendedProps.priority;
            this.vacation.sick_vacation =
                args.event.extendedProps.sick_vacation;
            this.vacation.sick_image_file = args.event.extendedProps.sick_image;
            this.vacation.user_id = args.event.extendedProps.user_id;
            this.vacation.upload = false;
            this.vacation.imageUrl = null;
            $("#vacationModal").modal("show");
        },
        selectOverlap(args) {
            console.log("selectOverlap args", args);
        },
        selectPeriod(args) {
            this.resetVacation();
            let start = new Date(args.start);
            let end = new Date(args.end);
            if (start.isPast() || end.isPast()) {
                alert("select dates in the future");
                return false;
            }

            let $selectedEvents = this.events.filter(function(event) {
                let EventStart = new Date(event.start);
                let EventEnd = new Date(event.end);
                return (
                    (EventStart >= start || EventEnd >= start) &&
                    (EventStart <= end || EventEnd <= end)
                );
            });

            if ($selectedEvents.length > 0 && !this.isAdmin()) {
                alert("You have vacations in this day");
                return false;
            }

            this.vacation.day_from = this.formatedDate(start);
            this.vacation.day_to = this.formatedDate(end.removeDays(1));
            this.vacation.time_from = "00:00:01";
            this.vacation.time_to = "23:59:59";
            this.vacation.reason = "";
            $("#vacationModal").modal("show");
        },
        saveVacation() {
            if (this.vacation.sick_vacation && !this.vacation.sick_image_file) {
                alert("image is required");
                return false;
            }
            if (!this.vacation.priority || this.vacation.priority == "") {
                alert("priority is required");
                return false;
            }

            if (!this.vacation.reason || this.vacation.reason == "") {
                alert("reason is required");
                return false;
            }

            if (this.isAdmin()) {
                let start = new Date(
                    this.vacation.day_from + "T" + this.vacation.time_from
                );
                let end = new Date(
                    this.vacation.day_to + "T" + this.vacation.time_to
                );
                let $selectedEvents = this.events.filter(function(event) {
                    let EventStart = new Date(event.start);
                    let EventEnd = new Date(event.end);
                    return (
                        (EventStart >= start || EventEnd >= start) &&
                        (EventStart <= end || EventEnd <= end)
                    );
                });
                let $userEvents = $selectedEvents.filter(
                    event => event.user_id == this.vacation.user_id
                );

                if ($userEvents.length > 0) {
                    alert("this user has vacation in this day");
                    return false;
                }
            }

            let form = new FormData();

            Object.keys(this.vacation).forEach(key => {
                form.append(key, this.vacation[key]);
            });

            this.$Progress.start();
            this.$store
                .dispatch("vacation/addVacation", form)
                .then(response => {
                    let $color = "#026d9e";
                    if (this.isAdmin()) {
                        $color = "#005e13";
                        this.vacation.reason =
                            response.data.data.reason +
                            "-" +
                            response.data.data.user.name;
                    }
                    this.vacation.upload = false;
                    this.events.push({
                        id: this.vacation.id,
                        title: this.vacation.reason,
                        start:
                            this.vacation.day_from +
                            "T" +
                            this.vacation.time_from,
                        end: this.vacation.day_to + "T" + this.vacation.time_to,
                        day_from: this.vacation.day_from,
                        time_from: this.vacation.time_from,
                        day_to: this.vacation.day_to,
                        time_to: this.vacation.time_to,
                        reason: this.vacation.reason,
                        priority: this.vacation.priority,
                        sick_vacation: this.vacation.sick_vacation,
                        consultation: this.vacation.consultation,
                        sick_image: response.data.sick_image,

                        color: $color,
                        textColor: "#fff"
                    });

                    if (this.vacation.sick_vacation) {
                        this.events.push({
                            id: this.vacation.id,
                            title: this.vacation.reason + " - consultation",
                            start: this.vacation.consultation + "T00:00:01",
                            end: this.vacation.consultation + "T23:59:59",
                            day_from: this.vacation.consultation,
                            time_from: "00:00:01",
                            day_to: this.vacation.consultation,
                            time_to: "24:59:59",
                            reason: this.vacation.reason + " - consultation",
                            priority: this.vacation.priority,
                            color: $color,
                            sick_vacation: false,
                            textColor: "#fff"
                        });
                    }
                    this.resetVacation();
                    $("#vacationModal").modal("hide");
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },
        getVacations() {
            this.$Progress.start();
            this.$store
                .dispatch("vacation/getVacations")
                .then(response => {
                    response.data.data.forEach(element => {
                        let $color = "#026d9e";

                        if (element.status == "confirmed") {
                            $color = "#005e13";
                        } else if (element.status == "rejected") {
                            $color = "#990d00";
                        }

                        if (this.isAdmin()) {
                            element.reason =
                                element.reason + "-" + element.user.name;
                        }

                        this.events.push({
                            id: element.id,
                            title: element.reason,
                            start: element.day_from + "T" + element.time_from,
                            end: element.day_to + "T" + element.time_to,
                            day_from: element.day_from,
                            time_from: element.time_from,
                            day_to: element.day_to,
                            time_to: element.time_to,
                            reason: element.reason,
                            priority: element.priority,
                            sick_vacation: element.sick_vacation,
                            consultation: element.consultation,
                            sick_image: element.sick_image,
                            color: $color,
                            textColor: "#fff",
                            user_id: element.user.id
                        });
                    });

                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },

        formatedDate(MyDate) {
            return (
                MyDate.getFullYear() +
                "-" +
                ("0" + (MyDate.getMonth() + 1)).slice(-2) +
                "-" +
                ("0" + MyDate.getDate()).slice(-2)
            );
        },
        getUsers() {
            this.$Progress.start();
            this.$store
                .dispatch("regularUser/getRegularUser",{rules:false,dropdown:true})
                .then(res => {
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },

        assignFile(event) {
            this.vacation.sick_image_file = event.target.files[0];
            this.vacation.imageUrl = URL.createObjectURL(event.target.files[0]);
            this.vacation.upload = true;
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
        resetVacation() {
            this.vacation = {
                day_from: null,
                time_from: null,
                day_to: null,
                time_to: null,
                reason: null,
                priority: null,
                sick_vacation: false,
                sick_image_file: null,
                imageUrl: null
            };
        }
    },
    mounted() {
        this.getVacations();
        if (this.isAdmin()) {
            this.getUsers();
        }
    },

    computed: {
        ...mapGetters({
            users: "regularUser/getRegularUsers"
        })
    }
};

Date.prototype.addDays = function(daysToAdd) {
    var dateAdd = new Date(this.valueOf());
    dateAdd.setDate(dateAdd.getDate() + daysToAdd);
    return dateAdd;
};

Date.prototype.removeDays = function(daysToRemove) {
    var dateRemove = new Date(this.valueOf());
    dateRemove.setDate(dateRemove.getDate() - daysToRemove);
    return dateRemove;
};

Date.prototype.isPast = function() {
    var date = new Date(this.valueOf());
    return new Date(date.toDateString()) < new Date(new Date().toDateString());
};
</script>
<style>
.fc-day-grid-event .fc-content {
    white-space: pre-wrap;
}
</style>
