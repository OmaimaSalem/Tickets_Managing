<template>
    <div class="card card-primary">
        <FullCalendar
            defaultView="dayGridMonth"
            :plugins="calendarPlugins"
            selectable="true"
            :start="start"
            ref="fullCalendar"
            @dateClick="addNewAttend"
            :events="workingDays"
            @eventClick="eventClick"
        />

        <div
            class="modal fade"
            id="addTimeModal"
            tabindex="-1"
            role="dialog"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            {{ $t("Attends.editDay") }}
                        </h5>
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
                        <div class="row mb-2">
                            <div class="col-sm-12 col-md-6">
                                <button
                                    :disabled="day.started"
                                    @click="startDay"
                                    class="btn btn-success btn-sm col-12 ptn-block"
                                >
                                    {{ $t("Attends.startDay") }}
                                </button>
                            </div>
                            <div class="col-6">
                                <button
                                    :disabled="!day.started || day.ended"
                                    @click="endDay"
                                    class="btn btn-info btn-sm col-12 ptn-block"
                                >
                                    {{ $t("Attends.endDay") }}
                                </button>
                            </div>
                        </div>
                        <hr />
                        <div class="row mb-2">
                            <div class="col-sm-12 col-md-6">
                                <button
                                    @click="startBreak"
                                    :disabled="
                                        break_status ||
                                            !day.started ||
                                            day.ended
                                    "
                                    class="btn btn-success btn-sm col-12 ptn-block"
                                >
                                    {{ $t("Attends.startBreak") }}
                                </button>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <button
                                    @click="endBreak"
                                    :disabled="
                                        !break_status ||
                                            day.ended ||
                                            !day.started
                                    "
                                    class="btn btn-info btn-sm col-12 ptn-block"
                                >
                                    {{ $t("Attends.endBreak") }}
                                </button>
                            </div>
                        </div>
                        <hr />
                        <div
                            class="row"
                            v-for="(project, index) in projects"
                            :key="index"
                        >
                            <div class="col-sm-12 col-md-6">
                                <multiselect
                                    v-model="projects[index]"
                                    :options="assignedProjects"
                                    track-by="id"
                                    label="name"
                                    :disabled="
                                        break_status ||
                                            day.ended ||
                                            !day.started
                                    "
                                ></multiselect>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <button
                                    :disabled="
                                        break_status ||
                                            day.ended ||
                                            !day.started ||
                                            project.status
                                    "
                                    class="btn btn-success btn-sm"
                                    @click="startProject(index)"
                                >
                                    {{ $t("Attends.startProject") }}
                                </button>
                                <button
                                    :disabled="
                                        break_status ||
                                            day.ended ||
                                            !day.started ||
                                            !project.status
                                    "
                                    class="btn btn-info btn-sm"
                                    @click="endProject(index)"
                                >
                                    {{ $t("Attends.endProject") }}
                                </button>
                                <button
                                    :disabled="
                                        break_status ||
                                            day.ended ||
                                            !day.started
                                    "
                                    v-if="index == 0"
                                    class="btn btn-primary btn-sm"
                                    @click="addProject()"
                                >
                                    +
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button
                            class="btn btn-default btn-sm"
                            data-dismiss="modal"
                        >
                            {{ $t("Attends.cancle") }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- {{ this.projects }} -->

        <div
            class="modal fade"
            id="dayModal"
            tabindex="-1"
            role="dialog"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            {{ $t("Attends.editDay") }}
                        </h5>
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
                        <div v-if="loading">{{ $t("Attends.loading") }}</div>

                        <div v-if="!loading">
                            <div class="edit" @click="editDay(dayData)">
                                {{ $t("Attends.edit") }}
                            </div>
                            <div v-if="dayData">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <strong
                                            >{{
                                                $t("Attends.startDay")
                                            }}:</strong
                                        >
                                        {{ dayData.from }}
                                    </div>
                                    <div class="col-sm-6">
                                        <strong
                                            >{{ $t("Attends.endDay") }}:</strong
                                        >
                                        {{ dayData.to }}
                                    </div>
                                    <hr />
                                </div>
                                <div
                                    class="row"
                                    v-for="(breake, index) in dayData.breaks"
                                    :key="index + 'b'"
                                >
                                    <div class="col-sm-4">
                                        <strong
                                            >{{
                                                $t("Attends.startBreak")
                                            }}:</strong
                                        >
                                        {{ breake.from }}
                                    </div>
                                    <div class="col-sm-4">
                                        <strong
                                            >{{
                                                $t("Attends.endBreak")
                                            }}:</strong
                                        >
                                        {{ breake.to }}
                                    </div>
                                    <div class="col-sm-4">
                                        <strong
                                            >{{
                                                $t("Attends.breakTime")
                                            }}:</strong
                                        >
                                        {{ breake.break_time }}
                                    </div>
                                    <hr />
                                </div>
                                <div
                                    class="row"
                                    v-for="(project, index) in dayData.projects"
                                    :key="index + 'p'"
                                >
                                    <div class="col-sm-4">
                                        <strong
                                            >{{
                                                $t("Attends.project")
                                            }}:</strong
                                        >
                                        {{ project.project.name }}
                                    </div>
                                    <div class="col-sm-4">
                                        <strong
                                            >{{
                                                $t("Attends.workingTime")
                                            }}:</strong
                                        >
                                        {{ project.work_time }} min
                                    </div>
                                </div>
                            </div>
                            <div v-if="!dayData">
                                {{ $t("Attends.noData") }}
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button
                            class="btn btn-seconday btn-sm"
                            data-dismiss="modal"
                        >
                            {{ $t("Attends.cancle") }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- day model -->

        <div
            class="modal fade"
            id="editdayModal"
            tabindex="-1"
            role="dialog"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            {{ $t("Attends.editDay") }}
                        </h5>
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
                        <div v-if="loading">{{ $t("Attends.loading") }}</div>

                        <div v-if="!loading">
                            <div v-if="editdayData">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <strong
                                            >{{
                                                $t("Attends.startDay")
                                            }}:</strong
                                        >

                                        <!-- <input type="text" class="form-control" v-model="editdayData.from" /> -->

                                        <date-picker
                                            v-model="editdayData.from"
                                            lang="en"
                                            type="time"
                                            format="HH:mm"
                                            :minute-step="1"
                                            value-type="format"
                                            input-class="form-control"
                                            @input="calculate_day_time()"
                                        ></date-picker>
                                    </div>
                                    <div class="col-sm-4">
                                        <strong
                                            >{{ $t("Attends.endDay") }}:</strong
                                        >
                                        <!-- <input type="text" class="form-control" v-model="editdayData.to" /> -->

                                        <date-picker
                                            v-model="editdayData.to"
                                            lang="en"
                                            type="time"
                                            format="HH:mm"
                                            :minute-step="1"
                                            value-type="format"
                                            input-class="form-control"
                                            @input="calculate_day_time()"
                                        ></date-picker>
                                    </div>

                                    <div class="col-sm-4">
                                        <strong
                                            >{{ $t("Attends.endDay") }}:</strong
                                        >
                                        <!-- <input type="text" class="form-control" v-model="editdayData.to" /> -->

                                        <input
                                            v-model="editdayData.day_time"
                                            disabled
                                            type="text"
                                            class="form-control"
                                        />
                                    </div>

                                    <hr />
                                </div>
                                <div
                                    class="row"
                                    v-for="(breake,
                                    index) in editdayData.breaks"
                                    :key="index + 'b'"
                                >
                                    <div class="col-sm-4">
                                        <strong
                                            >{{
                                                $t("Attends.startBreak")
                                            }}:</strong
                                        >
                                        <!-- <input type="text" class="form-control" v-model="breake.from" /> -->

                                        <date-picker
                                            v-model="breake.from"
                                            lang="en"
                                            type="time"
                                            format="HH:mm"
                                            :minute-step="1"
                                            value-type="format"
                                            input-class="form-control"
                                            @input="calculate_break(index)"
                                        ></date-picker>
                                    </div>
                                    <div class="col-sm-4">
                                        <strong
                                            >{{
                                                $t("Attends.endBreak")
                                            }}:</strong
                                        >
                                        <!-- <input type="text" class="form-control" v-model="breake.to" /> -->

                                        <date-picker
                                            v-model="breake.to"
                                            lang="en"
                                            type="time"
                                            format="HH:mm"
                                            :minute-step="1"
                                            value-type="format"
                                            input-class="form-control"
                                            @input="calculate_break(index)"
                                        ></date-picker>
                                    </div>
                                    <div class="col-sm-4">
                                        <strong
                                            >{{
                                                $t("Attends.breakTime")
                                            }}:</strong
                                        >
                                        <input
                                            disabled
                                            type="number"
                                            class="form-control"
                                            v-model="breake.break_time"
                                        />min
                                    </div>
                                    <hr />
                                </div>

                                <button
                                    class="btn btn-primary btn-sm"
                                    @click="addBreak"
                                >
                                    Add Break
                                </button>

                                <div
                                    class="row"
                                    v-for="(project,
                                    index) in editdayData.projects"
                                    :key="index + 'p'"
                                >
                                    <div class="col-sm-4">
                                        <strong
                                            >{{
                                                $t("Attends.project")
                                            }}:</strong
                                        >
                                        {{ project.project.name }}
                                    </div>
                                    <div class="col-sm-4">
                                        <strong
                                            >{{
                                                $t("Attends.workingTime")
                                            }}:</strong
                                        >
                                        <input
                                            type="number"
                                            class="form-control"
                                            v-model="project.work_time"
                                        />
                                        min
                                    </div>
                                </div>
                            </div>
                            <div v-if="!dayData">
                                {{ $t("Attends.noData") }}
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button
                            class="btn btn-primary btn-sm"
                            @click="submitAttendeesToReview()"
                        >
                            {{ $t("Attends.save") }}
                        </button>
                        <button
                            class="btn btn-seconday btn-sm"
                            data-dismiss="modal"
                        >
                         {{ $t("Attends.cancle") }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- edit editday model -->
    </div>
</template>

<style lang="scss">
@import "~@fullcalendar/core/main.css";
@import "~@fullcalendar/daygrid/main.css";
</style>

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
            start: new Date(),
            date: null,
            errorMessage: null,
            loading: true,
            user: null,
            editdayData: null
            // projects : [],
            // form: {}
        };
    },
    methods: {
        calculate_break(index) {
            this.editdayData.breaks[index].break_time = this.calculate_time(
                this.editdayData.breaks[index].from,
                this.editdayData.breaks[index].to
            );
        },

        calculate_day_time() {
            this.editdayData.day_time = this.calculate_time(
                this.editdayData.from,
                this.editdayData.to
            );
        },
        submitAttendeesToReview() {
            this.$Progress.start();
            this.$store
                .dispatch("attend/submitAttendeesToReview", this.editdayData)
                .then(response => {
                    this.$Progress.finish();
                    $("#editdayModal").modal("hide");
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },
        addBreak() {
            this.editdayData.breaks.push({
                from: null,
                to: null,
                break_time: 0
            });
        },
        calculate_time(From, To) {
            if (!From || !To) return;

            var today = new Date();
            let $toArr = To.split(":");
            let to = today.setHours($toArr[0]);
            to = today.setMinutes($toArr[1]);
            let $fromArr = From.split(":");
            let from = today.setHours($fromArr[0]);
            from = today.setMinutes($fromArr[1]);
            return (to - from) / 60000;
        },
        editDay(dayData) {
            this.editdayData = JSON.parse(JSON.stringify(dayData));
            $("#dayModal").modal("hide");
            $("#editdayModal").modal("show");
        },
        getAssignedProjects() {
            this.$Progress.start();
            this.$store
                .dispatch("attend/getAssignedProjects")
                .then(response => {
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },
        addProject() {
            if (this.assignedProjects.length == this.projects.length) {
                return false;
            }
            this.$store.commit("attend/AddProject");
        },
        removeProject(index) {
            if (confirm("Are you sure?")) {
                this.$store.commit("attend/removeProject", index);
            }
        },
        saveAttende() {},
        // getDateData() {
        //     this.$Progress.start();
        //     this.$store.dispatch("attend/getDateData", { 'date' : this.date})
        //     .then(response => {
        //         this.$Progress.finish();
        //     }).catch(error => {
        //         this.$Progress.fail();
        //     })
        // },
        // sendDate(form) {
        //     form.date = this.date;
        //     form.break = this.diff_hours(new Date().setTime(this.m_s(form.break_from)),new Date().setTime(this.m_s(form.break_to)));
        //     this.$Progress.start();
        //     this.$store.dispatch("attend/setDateData", { form})
        //     .then(response => {
        //         this.$Progress.finish();
        //     }).catch(error => {
        //         this.$Progress.fail();
        //     })
        // },
        addNewAttend(args) {
            if (this.isToday(args.date)) {
                $("#addTimeModal").modal("show");
            } else {
                var date = args.date;
                let $date =
                    date.getFullYear() +
                    "-" +
                    `${date.getMonth() + 1}`.padStart(2, "0") +
                    "-" +
                    date.getDate();

                // console.log(now, date);
                if (this.isOldDay(args.date)) {
                    let dayData = {
                        from: "",
                        to: "",
                        date: $date,
                        day_time: 0,
                        breaks: [
                            {
                                from: "",
                                to: "",
                                break_time: 0
                            }
                        ]
                    };
                    this.editdayData = dayData;
                    this.loading = false;
                    $("#editdayModal").modal("show");
                } else {
                    Toast.fire({
                        type: "error",
                        title: "Please, select dates in the past"
                    });
                }
            }
        },

        eventClick(args) {
            let date = args.event.start;
            let $date =
                date.getFullYear() +
                "-" +
                `${date.getMonth() + 1}`.padStart(2, "0") +
                "-" +
                date.getDate();
            let $admin = false;

            if (this.isAdmin()) {
                $admin = false;
            }
            this.getDayData($date, $admin);
        },

        getDayData($date, $admin) {
            this.loading = true;
            this.$Progress.start();
            this.$store
                .dispatch("attend/getDayData", { date: $date, all: $admin })
                .then(response => {
                    this.loading = false;
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
            $("#dayModal").modal("show");
        },
        startProject(index) {
            if (
                this.projects[index] == undefined ||
                this.projects[index].id == undefined
            ) {
                return false;
            }

            this.$Progress.start();
            this.$store
                .dispatch("attend/ProjectStart", {
                    pid: this.projects[index].id
                })
                .then(response => {
                    // this.getAttendProjects();
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },
        endProject(index) {
            if (
                this.projects[index] == undefined ||
                this.projects[index].id == undefined
            ) {
                return false;
            }

            this.$Progress.start();
            this.$store
                .dispatch("attend/ProjectEnd", { pid: this.projects[index].id })
                .then(response => {
                    // this.getAttendProjects();
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },
        startDay() {
            this.$Progress.start();
            this.$store
                .dispatch("attend/setDayStart")
                .then(response => {
                    Toast.fire({
                        type: "success",
                        title: "Day started successfully"
                    });
                    this.getWorkingDays();
                    $("#addTimeModal").modal("hide");
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
            // $("#addTimeModal").modal('hide');
        },
        endDay() {
            Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, end the day!",
        }).then((result) => {

            if (result.value) {

            let $running = [];
            this.projects.forEach(element => {
                if (element.status == true) {
                    $running.push(element.name);
                }
            });

            if ($running.length > 0) {
                Toast.fire({
                    type: "error",
                    title:
                        "There is (" + $running.length + ") project(s) running"
                });

                return false;
            }

            this.$Progress.start();
            this.$store
                .dispatch("attend/setDayEnd")
                .then(response => {
                    this.$Progress.finish();
                    $("#addTimeModal").modal("hide");
                    Toast.fire({
                        type: "success",
                        title: "Day finished successfully"
                    });
                })
                .catch(error => {
                    this.$Progress.fail();
                });

                $("#addTimeModal").modal('hide');
            }

        });




        },
        getDay() {
            this.$Progress.start();
            this.$store
                .dispatch("attend/getDay")
                .then(response => {
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },
        startBreak() {
            let $running = [];
            this.projects.forEach(element => {
                if (element.status == true) {
                    $running.push(element.name);
                }
            });

            if ($running.length > 0) {
                Toast.fire({
                    type: "error",
                    title:
                        "There is (" + $running.length + ") project(s) running"
                });

                return false;
            }

            this.$Progress.start();
            this.$store
                .dispatch("attend/setBreakStart")
                .then(response => {
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
            // $("#addTimeModal").modal('hide');
        },
        endBreak() {
            this.$Progress.start();
            this.$store
                .dispatch("attend/setBreakEnd")
                .then(response => {
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
            // $("#addTimeModal").modal('hide');
        },
        breakStatus($mounted = null) {
            this.$Progress.start();
            this.$store
                .dispatch("attend/getBreakStatus", { mounted: $mounted })
                .then(response => {
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
            // $("#addTimeModal").modal('hide');
        },

        getAttendProjects() {
            this.$Progress.start();
            this.$store
                .dispatch("attend/getAttendProjects")
                .then(response => {
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },
        getWorkingDays($admin) {
            this.$Progress.start();
            this.$store
                .dispatch("attend/getWorkingDays", { all: $admin })
                .then(response => {
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },

        // diff_hours(dt1, dt2)
        // {
        //     var diff =(dt2 - dt1) / 1000;
        //     diff /= (60 * 60);
        //     return Math.abs(Math.round(diff));
        // },
        // m_s(t) {
        //     let ms = Number(t.split(':')[0]) * 60 * 60 * 1000 + Number(t.split(':')[1]) * 60 * 1000;
        //     return ms;
        // },
        isAdmin() {
            if (localStorage.getItem("alferp")) {
                let user = JSON.parse(localStorage.getItem("alferp")).user
                    .singleUser;
                let roles = user.roles.map(role => role.name);
                return roles.includes("Full Administrator");
            }
            return false;
        },
        isToday(someDate) {
            const today = new Date();
            return (
                someDate.getDate() == today.getDate() &&
                someDate.getMonth() == today.getMonth() &&
                someDate.getFullYear() == today.getFullYear()
            );
        },
        isOldDay(someDate) {
            const today = new Date();
            return (
                someDate.getDate() < today.getDate() &&
                someDate.getMonth() <= today.getMonth() &&
                someDate.getFullYear() <= today.getFullYear()
            );
        },
        calendarApi() {
            return this.$refs.fullCalendar.getApi();
        }
    },
    computed: {
        ...mapGetters({
            form: "attend/getDate",
            assignedProjects: "attend/getAssignedProjects",
            day: "attend/getDay",
            break_status: "attend/getBreakStatus",
            projects: "attend/getAttendProjects",
            dayData: "attend/getDayData",
            workingDays: "attend/getWorkingDays"
        })
        // projects: {
        //     get() {
        //         return this.$store.state.attend.attend_projects.length > 0 ? this.$store.state.attend.attend_projects : [{}];
        //     },
        //     set(value) {
        //         this.$store.commit("attend/updateprojects", value);
        //     }
        // }
    },
    mounted() {
        // this.calendarApi().view.props.dateProfile.validRange = {
        //     start: new Date().setDate(new Date().getDate() - 1),
        //     end: new Date().setDate(new Date().getDate() + 1)
        //     // end: new Date().setMonth(new Date().getMonth()+1)
        // };
        this.getAssignedProjects();
        this.getDay();
        this.breakStatus("mounted");
        this.getAttendProjects();

        if (window.location.hash == "#popup") {
            $("#addTimeModal").modal("show");
            const url = new URL(window.location);
            url.hash = "";
            history.replaceState(null, document.title, url);
        }

        if (this.isAdmin()) {
            this.getWorkingDays(true);
        } else {
            this.getWorkingDays(false);
        }
    }
};
</script>
