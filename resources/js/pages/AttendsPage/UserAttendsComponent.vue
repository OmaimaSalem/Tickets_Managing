<template>
    <div>
        <label
            ><b
                >{{ $t("UserAttends.reportFor") }} {{ employeeData.name }}</b
            ></label
        ><br />
        <label>
            From
            <date-picker
                v-model="dateFrom"
                lang="en"
                type="date"
                format="DD-MM-YYYY"
                :minute-step="1"
                input-class="form-control"
                value-type="format"
            ></date-picker>
        </label>

        <label>
            To
            <date-picker
                v-model="dateTo"
                lang="en"
                type="date"
                format="DD-MM-YYYY"
                :minute-step="1"
                input-class="form-control"
                value-type="format"
            ></date-picker>
        </label>

        <button @click="filterDates()" class="btn btn-secondary">
            {{ $t("UserAttends.filter") }}
        </button>
        <button @click="filterDatesandsend()" class="btn btn-secondary">
            {{ $t("UserAttends.send_to_admin") }}
        </button>
        <vue-bootstrap4-table
            @reset-data="resetData"
            :rows="employeeAttends"
            :columns="columns"
        >
            <template slot="breaks-area" slot-scope="props">
                <div>
                    <b>{{ props.row.breaks_total }} min</b>
                </div>
            </template>

            <template slot="work-time-area" slot-scope="props">
                <div>
                    <b
                        >{{
                            (props.row.day_time - props.row.breaks_total) / 60
                        }}
                        hr</b
                    >
                </div>
            </template>

            <template slot="action-buttons" slot-scope="props">
                <a @click="showModel(props.row)" class="btn btn-info btn-sm">{{
                    $t("UserAttends.edit")
                }}</a>
                <a
                    @click="previewAttend(props.row.attend)"
                    v-if="!props.row.active"
                    class="btn btn-info btn-sm"
                    >{{ $t("UserAttends.preview") }}</a
                >
            </template>
        </vue-bootstrap4-table>
        <table class="table table-dark">
            <tbody>
                <tr>
                    <th scope="row" colspan="2" class="text-right" width="50%">
                        Total
                    </th>
                    <td width="25%">
                        {{ $t("UserAttends.work") }}:
                        {{
                            (employeeData.day_time_total -
                                employeeData.break_time_total) /
                                60
                        }}
                        hr
                    </td>
                    <td width="25%">
                        {{ $t("UserAttends.breaks") }}:
                        {{ employeeData.break_time_total / 60 }} hr
                    </td>
                </tr>
            </tbody>
        </table>

        <div
            class="modal fade"
            id="editAttends"
            tabindex="-1"
            role="dialog"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            {{ $t("UserAttends.editAttend") }} {{ time.date }}
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
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label
                                        >{{
                                            $t("UserAttends.startFrom")
                                        }}:</label
                                    >

                                    <date-picker
                                        v-model="time.from"
                                        lang="en"
                                        type="time"
                                        format="HH:mm"
                                        :minute-step="1"
                                        value-type="format"
                                        input-class="form-control"
                                        @input="calculate_day"
                                    ></date-picker>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>{{ $t("UserAttends.to") }}:</label>

                                    <date-picker
                                        v-model="time.to"
                                        lang="en"
                                        type="time"
                                        format="HH:mm"
                                        :minute-step="1"
                                        value-type="format"
                                        input-class="form-control"
                                        @input="calculate_day"
                                    ></date-picker>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label
                                        >{{
                                            $t("UserAttends.workTime")
                                        }}:</label
                                    >
                                    <input
                                        disabled
                                        v-model="time.day_time"
                                        type="text"
                                        class="form-control"
                                    />
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div v-if="time.breaks">
                            <div
                                class="row"
                                v-for="(breake, index) in time.breaks"
                                :key="index"
                            >
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label
                                            >{{
                                                $t("UserAttends.breakFrom")
                                            }}:</label
                                        >
                                        <!-- <input
                                            v-model="time.breaks[index].from"
                                            type="text"
                                            class="form-control"
                                        /> -->

                                        <date-picker
                                            v-model="time.breaks[index].from"
                                            lang="en"
                                            type="time"
                                            format="HH:mm"
                                            :minute-step="1"
                                            value-type="format"
                                            input-class="form-control"
                                            @input="
                                                calculate_breaks_by_index(index)
                                            "
                                        ></date-picker>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label
                                            >{{ $t("UserAttends.to") }}:</label
                                        >
                                        <!-- <input
                                            v-model="time.breaks[index].to"
                                            type="text"
                                            class="form-control"
                                        /> -->

                                        <date-picker
                                            v-model="time.breaks[index].to"
                                            lang="en"
                                            type="time"
                                            format="HH:mm"
                                            :minute-step="1"
                                            value-type="format"
                                            input-class="form-control"
                                            @input="
                                                calculate_breaks_by_index(index)
                                            "
                                        ></date-picker>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label
                                            >{{
                                                $t("UserAttends.breakTime")
                                            }}:</label
                                        >
                                        <input
                                            v-model="
                                                time.breaks[index].break_time
                                            "
                                            disabled
                                            type="text"
                                            class="form-control"
                                        />
                                    </div>
                                    <hr />
                                </div>
                            </div>
                        </div>

                        <button
                            @click="add_break"
                            class="btn btn-success btn-sm"
                        >
                            {{ $t("UserAttends.add_break") }}
                        </button>
                    </div>

                    <div class="modal-footer">
                        <button
                            @click="resetTime(time)"
                            class="btn btn-default btn-sm"
                            data-dismiss="modal"
                        >
                            {{ $t("UserAttends.cancle") }}
                        </button>
                        <button
                            @click="saveTime(time)"
                            class="btn btn-success btn-sm"
                            data-dismiss="modal"
                        >
                            {{ $t("UserAttends.save") }}
                        </button>
                    </div>
                </div>
            </div>
        </div>







<div class="modal fade" id="attendPreviewModel" tabindex="-1" role="dialog" aria-labelledby="attendPreviewModelLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" v-if="attendPreview.attend" id="attendPreviewModelLabel">Attend Preview ({{ attendPreview.attend.date }})</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div>
            <div v-if="attendPreview.attend"> 
            <div class="row">
                <div class="col-sm-6">
                    <strong
                        >{{
                            $t("Attends.startDay")
                        }}:</strong
                    >
                    {{ attendPreview.attend.from }}
                </div>
                <div class="col-sm-6">
                    <strong
                        >{{ $t("Attends.endDay") }}:</strong
                    >
                    {{ attendPreview.attend.to }}
                </div>
                <hr />
            </div>

            <div
                class="row"
                v-for="(breake, index) in attendPreview.attend.breaks"
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
        </div>
            <hr />
           
            
        <div v-if="attendPreview.attend_old">
             Updates
            <div class="row">
                <div class="col-sm-6">
                    <strong
                        >{{
                            $t("Attends.startDay")
                        }}:</strong
                    >
                    {{ attendPreview.attend_old.from }}
                </div>
                <div class="col-sm-6">
                    <strong
                        >{{ $t("Attends.endDay") }}:</strong
                    >
                    {{ attendPreview.attend_old.to }}
                </div>
                <hr />
            </div>

            <div
                class="row"
                v-for="(breake, index) in attendPreview.attend_old.breaks"
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
        </div>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">cancel</button>
        <button type="button" @click="rejectAttend" class="btn btn-danger">reject</button>
        <button type="button" @click="approveAttend" class="btn btn-success">approved</button>
      </div>
    </div>
  </div>
</div>




    </div>
</template>
<script>
import VueBootstrap4Table from "vue-bootstrap4-table";
import { mapGetters } from "vuex";
import DatePicker from "vue2-datepicker";

export default {
    components: {
        VueBootstrap4Table,
        DatePicker
    },
    data() {
        return {
            total_breaks: 0,
            total_time: 0,
            time: {},
            originalTime: {},
            dateFrom: null,
            dateTo: null,
            columns: [
                {
                    label: this.$t("UserAttends.date"),
                    name: "date",
                    sort: true,
                    row_text_alignment: "text-center"
                },
                {
                    label: this.$t("UserAttends.startedAt"),
                    name: "from",
                    sort: true,
                    row_text_alignment: "text-center"
                },
                {
                    label: this.$t("UserAttends.endAt"),
                    name: "to",
                    sort: true,
                    row_text_alignment: "text-center"
                },
                {
                    label: this.$t("UserAttends.breaks"),
                    name: "breaks-area",
                    sort: true,
                    row_text_alignment: "text-center"
                },
                {
                    label: this.$t("UserAttends.workTime"),
                    name: "work-time-area",
                    sort: true,
                    row_text_alignment: "text-center"
                },
                {
                    label: this.$t("UserAttends.actions"),
                    name: "action-buttons"
                }
            ]
        };
    },
    methods: {
        add_break() {
            this.time.breaks.push({
                from: 0,
                to: 0,
                break_time: 0
            });
        },
        calculate_day() {
            this.time.day_time = this.calculate_hr_time(
                this.time.from,
                this.time.to
            );
        },
        calculate_breaks_by_index(index) {
            this.time.breaks[index].break_time = this.calculate_hr_time(
                this.time.breaks[index].from,
                this.time.breaks[index].to
            );
        },
        calculate_hr_time(From, To) {
            if (!From || !To) return false;
            var today = new Date();
            let $toArr = To.split(":");
            let to = today.setHours($toArr[0]);
            to = today.setMinutes($toArr[1]);
            let $fromArr = From.split(":");
            let from = today.setHours($fromArr[0]);
            from = today.setMinutes($fromArr[1]);
            return (to - from) / 60000;
        },
        showModel(time) {
            this.time = Object.assign({}, time);
            this.originalTime = Object.assign({}, time);
            $("#editAttends").modal("show");
        },
        resetTime(time) {
            time = this.originalTime;
            $("#editAttends").modal("hide");
        },
        saveTime(time) {
            this.$Progress.start();
            this.$store
                .dispatch("attend/setEmployeeAttend", this.time)
                .then(response => {
                    this.getEmployeeAttends(this.dateFrom, this.dateTo);
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },
        previewAttend(id){
            this.$Progress.start();
            this.$store
                .dispatch("attend/getAttendPreview", id)
                .then(response => {
                    this.$Progress.finish();
                    $("#attendPreviewModel").modal("show");
                })
                .catch(error => {
                    this.$Progress.fail();
                });
  
        },
        approveAttend() {
            this.$Progress.start();
            this.$store
                .dispatch("attend/setAttendActive", { id:this.attendPreview.attend.id})
                .then(response => {
                    this.getEmployeeAttends(this.dateFrom, this.dateTo);
                    $("#attendPreviewModel").modal("hide");
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },
        rejectAttend() {
            this.$Progress.start();
            this.$store
                .dispatch("attend/setAttendRejected", { id:this.attendPreview.attend.id})
                .then(response => {
                    this.getEmployeeAttends(this.dateFrom, this.dateTo);
                    $("#attendPreviewModel").modal("hide");
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },

        getEmployeeAttends(from = null, to = null, send = false) {
            this.$Progress.start();
            this.$store
                .dispatch("attend/getEmployeeAttends", {
                    id: this.$route.params.userId,
                    from: from,
                    to: to,
                    send: send
                })
                .then(response => {
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },

        getAttends(from = null, to = null, send = false) {
            this.$Progress.start();
            this.$store
                .dispatch("attend/getEmployeeAttends", {
                    id: null,
                    from: from,
                    to: to,
                    send: send
                })
                .then(response => {
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },
        total_breacks(breaks) {
            if (breaks.length <= 0) return 0;
            let sum = 0;
            return breaks.reduce(
                (sum, item) => sum + parseInt(item.break_time),
                0
            );
        },
        calculate_time(work_time) {
            if (work_time == undefined) return 0;
            this.total_time += Number.isNaN(work_time)
                ? 0
                : parseInt(work_time);
            return Number.isNaN(work_time) ? 0 : parseInt(work_time);
        },
        calculate_breaks(break_time) {
            if (break_time == undefined) return 0;
            this.total_breaks += Number.isNaN(break_time)
                ? 0
                : parseInt(break_time);
            return Number.isNaN(break_time) ? 0 : parseInt(break_time);
        },
        time_diff(item) {
            var timeStart = new Date("01/01/2010 " + item.from);
            var timeEnd = new Date("01/01/2010 " + item.to);
            var difference = timeEnd - timeStart;
            var diff_result = new Date(difference);
            return diff_result.getHours();
        },
        filterDates() {
            this.total_time = 0;
            this.total_breaks = 0;
            this.$store.commit("attend/setEmployeeAttendsEmpty");
            this.getEmployeeAttends(this.dateFrom, this.dateTo, false);
        },
        filterDatesandsend() {
            this.total_time = 0;
            this.total_breaks = 0;
            this.$store.commit("attend/setEmployeeAttendsEmpty");
            this.getEmployeeAttends(this.dateFrom, this.dateTo, true);
        },

        resetData() {
            this.$store.commit("attend/setEmployeeAttendsEmpty");
        }
    },
    computed: {
        ...mapGetters({
            employeeAttends: "attend/getEmployeeAttends",
            employeeData: "attend/getEmployeeData",
            attendPreview: "attend/getAttendPreview"
        })
    },
    mounted() {
        this.dateFrom =
            this.$route.params.from == "@" ? null : this.$route.params.from;
        this.dateTo =
            this.$route.params.to == "@" ? null : this.$route.params.to;
        this.getEmployeeAttends(this.dateFrom, this.dateTo);
    }
};
</script>
