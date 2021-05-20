<template>
  <div class="row justify-content-center">
      <vue-headful
          :title="'Task: '+task.name"
      />
      <v-row>
          <v-col cols="12">
              <v-card style="border-radius: 15px">
                  <v-container>
                      <v-row>
                          <v-col cols="3">
                              <v-container fill-height>
                                  <v-row style="margin-top: -1.5rem">
                                      <v-col>
                                          <span style="font-weight: bold">{{$t('Task.title')}}:</span>
                                          <span class="font-weight-light">{{ task.name }}</span>
                                      </v-col>
                                  </v-row>
                                  <v-row v-if="task.status">
                                      <v-col class="col stat" :id="task.status.name">
                                          <span class="stat-name ml-2">{{ task.status.name }}</span>
                                      </v-col>
                                  </v-row>
                                  <v-row v-if="task.priority">
                                      <v-col>
                                          <label for="priority">{{$t('Task.priorty')}}:</label>
                                          <span class="font-weight-light">{{ task.priority }}</span>
                                      </v-col>
                                  </v-row>
                              </v-container>
                          </v-col >
                          <v-col cols="3">
                              <v-row class="task-created_by">
                                  <v-col class="task-created_by-col">
                                      <label>
                                          {{$t('Task.createdBy')}}
                                      </label>
                                      <div v-if="task.created_by" style="display: flex">
                                          <avatar color="#F7D2A9" :fullname="task.created_by.name" :size="40"></avatar>
                                          <router-link
                                              :to="'/admin/profile/'+task.created_by.id"
                                              style="color: #484848;margin-left:.5rem;margin-top:.4rem"
                                          >{{task.created_by.name}}</router-link>
                                      </div>
                                  </v-col>
                              </v-row>
                              <v-row class="mt-2" >
                                  <v-col>
                                      <label>
                                          {{$t('Task.assignedUsers')}}
                                      </label>
                                      <div v-if="task.responsible" style="display: flex;">
                                          <avatar color="#90b0fa" :fullname="task.responsible.name" :size="40"></avatar>
                                          <router-link
                                              :to="'/admin/profile/'+task.responsible.id"
                                              style="color: #484848;margin-left:.5rem;margin-top:.4rem"
                                          >{{task.responsible.name}}</router-link>
                                      </div>
                                  </v-col>
                              </v-row>
                          </v-col>

                          <v-col cols="3">
                              <v-container fluid fill-height style="padding: 0;margin-top: -.4rem;">
                                  <v-row>
                                      <v-col>
                                          <span style="font-weight: bold"> {{$t('Task.startAt')}}: </span>
                                          <small >{{task.start_at}}</small>
                                      </v-col>
                                  </v-row>
                                  <v-row>
                                      <v-col>
                                          <span style="font-weight: bold"> {{$t('Task.deadline')}}: </span>
                                          <small>{{task.deadline}}</small>
                                      </v-col>
                                  </v-row>
                                  <v-row>
                                      <v-col>
                                          <span style="font-weight: bold">{{$t('Task.createdAt')}}: </span>
                                          <small>{{task.created_at}}</small>
                                      </v-col>
                                  </v-row>
                                  <v-row>
                                      <v-col>
                                          <span style="font-weight: bold" for="duration">{{$t('Task.totalDuration')}}: </span>
                                          <small>{{ humanReadableFromSecounds(duration).slice(0, -3) }}</small>
                                      </v-col>
                                  </v-row>
                              </v-container>
                          </v-col >
                          <v-col cols="2">
                              <v-container fluid fill-height style="padding: 0;margin-top: -.6rem">
                                  <v-row v-if="task.ticket">
                                      <v-col>
                                          <i class="nav-icon fas fa-ticket-alt pr-1 " style="font-size: 1.5rem;color: #A0466F"></i>
                                          <router-link
                                              :to="'/admin/ticket/'+task.ticket.id"
                                              style="color: #484848;padding: 0"
                                          >{{task.ticket.name | subStr}}</router-link>
                                      </v-col>
                                  </v-row>
                                  <v-row v-if="task.project">
                                      <v-col>
                                          <i class="nav-icon fas fa-briefcase pr-1 " style="font-size: 1.5rem;color: #FF8C00"></i>
                                          <router-link
                                              :to="'/admin/project/'+task.project.id"
                                              style="color: #484848;"
                                          >{{task.project.name}}</router-link>
                                      </v-col>
                                  </v-row>
                              </v-container>
                          </v-col>
                          <v-col cols="1">
                              <div class="actions-btn-cont">
                                  <div class="row action">
                                      <div class="edit actionIcon">
                                          <router-link
                                              :to="{ name: 'task.edit', params: {id: task.id }}"
                                          >
                                              <i style="color:#EEA24C" class="fas fa-edit"></i>
                                          </router-link>
                                      </div>
                                  </div>
                                  <div class="line" ></div>
                                  <div class="row action">
                                      <div class="trash actionIcon">
                                          <a href="#" @click="deleteTask(task.id)">
                                              <i style=" color:#985070;" class="fas fa-trash"></i>
                                          </a>
                                      </div>
                                  </div>
                              </div>
                          </v-col>
                      </v-row>
                      <v-row>
                          <v-col>
                                  <label for="Description" class="col-form-label">{{$t('Task.desc')}}:</label>
                                  <div
                                      class="overflow-auto  bg-light"
                                  >
                                      <p v-html="task.description" id="Description"></p>
                                  </div>
                          </v-col>
                      </v-row>
                      <v-row>
                          <v-col cols="6" v-show="duration">
                              <div class="form-group">

                              </div>
                          </v-col>
                      </v-row>
                      <v-row>
                          <v-col>
                              <center>
                                  <div
                                      id="duration-text"
                                      v-if="activeTimerString"
                                      v-bind:class="{'text-success': activeTimerString}"
                                  >{{ activeTimerString }}</div>
                                  <div
                                      id="duration-text"
                                      v-else
                                      v-bind:class="{'text-danger': counted_time}"
                                  >{{ counted_time }}</div>
                              </center>
                          </v-col>
                      </v-row>
                      <v-row>
                          <v-col>
                              <center class="buttons mb-3">
                                  <button v-show="editMode" type="button" class="btn btn-success btn-lg" id="save-button">
                                      {{$t('Task.save')}}
                                      <i class="fas fa-save fa-fw"></i>
                                  </button>
                                  <div class="btn-group dropup">
                                      <button
                                          class="btn btn-dark dropdown-toggle btn-lg text-white"
                                          data-toggle="dropdown"
                                          type="button"
                                          aria-expanded="false"
                                          style="bac"
                                      >
                                          {{$t('Task.moreActions')}}
                                      </button>
                                      <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                          <li v-show="!activeTimerString">
                                              <a @click="listTrackingTask(task_id)" class="dropdown-item">
                                                  <i class="fas fa-edit fa-fw mr-2"></i>
                                                  {{$t('Task.editHistory')}}
                                              </a>
                                          </li>
                                      </ul>
                                  </div>
                                  <button
                                      @click="startTracking"
                                      type="button"
                                      class="btn btn-primary btn-lg text-white"
                                      id="start-button"
                                      style="background-color: #3490dc"
                                      v-show="!activeTimerString"
                                  >
                                      {{$t('Task.start')}}
                                      <i class="fas fa-play fa-fw"></i>
                                  </button>
                                  <button
                                      @click="stopTracking"
                                      v-show="activeTimerString"
                                      type="button"
                                      class="btn btn-info btn-lg text-white"
                                      id="stop-button"
                                  >
                                      {{$t('Task.stop')}}
                                      <i class="fas fa-stop fa-fw"></i>
                                  </button>
                              </center>
                          </v-col>
                      </v-row>
                  </v-container>
              </v-card>
          </v-col>
      </v-row>
    <div class="col-md-12" v-show="task.id">
<!--      <div class="card">-->
<!--        <div class="card-header">-->
<!--          <span>{{$t('Task.title')}}:</span>-->
<!--          <span class="font-weight-light">{{ task.name }}</span>-->
<!--          <div class="card-tools">-->
<!--            <router-link-->
<!--              :to="{ name: 'task.edit', params: {id: task.id }}"-->
<!--              class="btn btn-primary btn-xs"-->
<!--            >-->
<!--              <i class="fas fa-edit fa-fw"></i>-->
<!--            </router-link>-->
<!--            <a href="#" @click="deleteTask(task.id)" class="btn btn-danger btn-xs">-->
<!--              <i class="fas fa-trash fa-fw"></i>-->
<!--            </a>-->
<!--          </div>-->
<!--        </div>-->

<!--        <div class="card-body">-->
<!--          <div class="form-group">-->
<!--            <label for="Description" class="col-form-label">{{$t('Task.desc')}}:</label>-->
<!--            <div-->
<!--              class="overflow-auto p-3 mb-3 mb-md-0 mr-md-3 bg-light"-->
<!--              style="min-height: 100px; max-height: 600px;"-->
<!--            >-->
<!--              <p v-html="task.description" id="Description"></p>-->
<!--            </div>-->
<!--          </div>-->
<!--          <div class="row">-->
<!--            <div class="col-sm-6">-->
<!--              <div class="form-group">-->
<!--                <label for="Client">{{$t('Task.clientName')}}:</label>-->
<!--                <input-->
<!--                  v-if="task.project"-->
<!--                  v-model="task.project.owner.name"-->
<!--                  type="text"-->
<!--                  class="form-control"-->
<!--                  id="Client"-->
<!--                  disabled-->
<!--                />-->
<!--              </div>-->
<!--            </div>-->
<!--            <div class="col-sm-6">-->
<!--              <div class="form-group">-->
<!--                <label for="status">{{$t('Task.status')}}:</label>-->
<!--                <input-->
<!--                  v-if="task.status"-->
<!--                  v-model="task.status.name"-->
<!--                  type="text"-->
<!--                  class="form-control"-->
<!--                  id="status"-->
<!--                  disabled-->
<!--                />-->
<!--              </div>-->
<!--            </div>-->
<!--          </div>-->
<!--          <div class="row">-->
<!--            <div class="col-sm-6">-->
<!--              <div class="form-group">-->
<!--                <label for="Project">{{$t('Task.projectName')}}:</label>-->
<!--                <input-->
<!--                  v-if="task.project"-->
<!--                  v-model="task.project.name"-->
<!--                  type="text"-->
<!--                  class="form-control"-->
<!--                  id="Project"-->
<!--                  disabled-->
<!--                />-->
<!--              </div>-->
<!--            </div>-->
<!--            <div class="col-sm-6">-->
<!--              <div class="form-group">-->
<!--                <label for="deadline">{{$t('Task.deadline')}}:</label>-->
<!--                <p class="small">{{ task.deadline | DateOnly }}</p>-->
<!--              </div>-->
<!--            </div>-->
<!--          </div>-->
<!--          <div class="row">-->
<!--            <div class="col-sm-6">-->
<!--              <div class="form-group">-->
<!--                <label for="priority">{{$t('Task.priorty')}}:</label>-->
<!--                <input-->
<!--                  v-if="task.priority"-->
<!--                  v-model="task.priority"-->
<!--                  type="text"-->
<!--                  class="form-control"-->
<!--                  id="priority"-->
<!--                  disabled-->
<!--                />-->
<!--              </div>-->
<!--            </div>-->

<!--            <div class="col-sm-6" v-show="duration">-->
<!--              <div class="form-group">-->
<!--                <label for="duration">{{$t('Task.totalDuration')}}:</label>-->
<!--                <div-->
<!--                  class="font-weight-light"-->
<!--                >{{ humanReadableFromSecounds(duration).slice(0, -3) }}</div>-->
<!--              </div>-->
<!--            </div>-->
<!--          </div>-->
<!--          <div class="row">-->
<!--            <div class="col-sm-6">-->
<!--              <div class="form-group">-->
<!--                <label for="created_at">{{$t('Task.createdAt')}}</label>-->
<!--                <div class="font-weight-light">{{ task.created_at | DateOnly }}</div>-->
<!--              </div>-->
<!--            </div>-->
<!--          </div>-->
<!--          <center>-->
<!--            <div-->
<!--              id="duration-text"-->
<!--              v-if="activeTimerString"-->
<!--              v-bind:class="{'text-success': activeTimerString}"-->
<!--            >{{ activeTimerString }}</div>-->
<!--            <div-->
<!--              id="duration-text"-->
<!--              v-else-->
<!--              v-bind:class="{'text-danger': counted_time}"-->
<!--            >{{ counted_time }}</div>-->
<!--          </center>-->
<!--        </div>-->
<!--      </div>-->
<!--      <center class="buttons mb-3">-->
<!--        <button v-show="editMode" type="button" class="btn btn-success btn-lg" id="save-button">-->
<!--          {{$t('Task.save')}}-->
<!--          <i class="fas fa-save fa-fw"></i>-->
<!--        </button>-->
<!--        <div class="btn-group dropup">-->
<!--          <button-->
<!--            class="btn btn-dark dropdown-toggle btn-lg"-->
<!--            data-toggle="dropdown"-->
<!--            type="button"-->
<!--            aria-expanded="false"-->
<!--          >-->
<!--            {{$t('Task.moreActions')}}-->
<!--            <span class="caret"></span>-->
<!--          </button>-->
<!--          <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">-->
<!--            <li v-show="!activeTimerString">-->
<!--              <a @click="listTrackingTask(task_id)" class="dropdown-item">-->
<!--                <i class="fas fa-edit fa-fw mr-2"></i>-->
<!--                {{$t('Task.editHistory')}}-->
<!--              </a>-->
<!--            </li>-->
<!--          </ul>-->
<!--        </div>-->

<!--        <button-->
<!--          @click="startTracking"-->
<!--          type="button"-->
<!--          class="btn btn-primary btn-lg"-->
<!--          id="start-button"-->
<!--          v-show="!activeTimerString"-->
<!--        >-->
<!--          {{$t('Task.start')}}-->
<!--          <i class="fas fa-play fa-fw"></i>-->
<!--        </button>-->
<!--        <button-->
<!--          @click="stopTracking"-->
<!--          v-show="activeTimerString"-->
<!--          type="button"-->
<!--          class="btn btn-info btn-lg"-->
<!--          id="stop-button"-->
<!--        >-->
<!--          {{$t('Task.stop')}}-->
<!--          <i class="fas fa-stop fa-fw"></i>-->
<!--        </button>-->
<!--      </center>-->
        <div v-show="listTracking_Task.length > 0" class="mb-2 overflow-auto shadow-sm">
            <div class="card-header">
              <h5 class="card-title m-0">{{$t('Task.history')}}</h5>
            </div>
            <v-simple-table>
                <thead>
                <tr>
                    <th class="text-left">{{$t('Task.startedAt')}}</th>
                    <th class="text-left">{{$t('Task.endAt')}}</th>
                    <th class="text-left">{{$t('Task.duration')}}</th>
                    <th class="text-left">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in orderedTrack" :key="item.id">
                    <td><span class="font-weight-light">{{ item.start_at | DateWithTime }}</span></td>
                    <td><span class="font-weight-light">{{ item.end_at | DateWithTime }}</span></td>
                    <td><span class="font-weight-light">{{ humanReadableFromSecounds(item.count_time) }}</span></td>
                    <td>
                        <button @click="editModel(item)" class="btn btn-sm">
                            <div class="fa fa-edit fa-fw"></div>
                        </button>
                        <button @click="deleteTrack(task.id, item.id)" class="btn btn-sm">
                            <div class="fa fa-trash fa-fw"></div>
                        </button>
                    </td>
                </tr>
                </tbody>
            </v-simple-table>
        </div>
<!--      <div class="card" id="listTracking" v-show="listTracking_Task.length > 0">-->
<!--        <div class="card-header">-->
<!--          <h5 class="card-title m-0">{{$t('Task.history')}}</h5>-->
<!--        </div>-->
<!--        <div class="card-body">-->
<!--          <div class="callout callout-info" v-for="item in orderedTrack" :key="item.id">-->
<!--            <div class="row">-->
<!--              <div class="col-sm-4">-->
<!--                <label for="started_at" class="col-form-label">{{$t('Task.startedAt')}}:</label>-->
<!--                <span class="font-weight-light">{{ item.start_at | DateWithTime }}</span>-->
<!--              </div>-->
<!--              <div class="col-sm-4">-->
<!--                <label for="end_at" class="col-form-label strong">{{$t('Task.endAt')}}:</label>-->
<!--                <span class="font-weight-light">{{ item.end_at | DateWithTime }}</span>-->
<!--              </div>-->
<!--              <div class="col-sm-2">-->
<!--                <label for="duration" class="col-form-label">{{$t('Task.duration')}}:</label>-->
<!--                <span class="font-weight-light">{{ humanReadableFromSecounds(item.count_time) }}</span>-->
<!--              </div>-->
<!--              <div class="col-sm-2 text-right" id="action-buttons">-->
<!--                <button @click="editModel(item)" class="btn btn-primary btn-sm">-->
<!--                  <div class="fa fa-edit fa-fw"></div>-->
<!--                </button>-->
<!--                <button @click="deleteTrack(task.id, item.id)" class="btn btn-danger btn-sm">-->
<!--                  <div class="fa fa-trash fa-fw"></div>-->
<!--                </button>-->
<!--              </div>-->
<!--            </div>-->
<!--          </div>-->
<!--        </div>-->
<!--      </div>-->
      <task-comment></task-comment>
    </div>
    <!-- Modal -->
    <div
      class="modal fade"
      id="Modal"
      tabindex="-1"
      role="dialog"
      aria-labelledby="Modal"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="newRoleLabel">{{$t('Task.editTrack')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form
            @submit.prevent="editTrackingTime(task_id,form.id)"
            @keydown="form.onKeydown($event)"
          >
            <div class="modal-body">
              <div class="form-group">
                <label for="start_at">{{$t('Task.startedAt')}}</label>
                <date-picker
                  v-model="form.start_at"
                  lang="en"
                  type="datetime"
                  format="DD-MM-YYYY HH:mm:ss"
                  :minute-step="1"
                  value-type="format"
                  input-class="form-control"
                ></date-picker>
                <has-error :form="form" field="start_at"></has-error>
              </div>
              <div class="form-group">
                <label for="end_at">{{$t('Task.endAt')}}</label>
                <date-picker
                  v-model="form.end_at"
                  lang="en"
                  type="datetime"
                  format="DD-MM-YYYY HH:mm:ss"
                  :minute-step="1"
                  input-class="form-control"
                  value-type="format"
                ></date-picker>
                <has-error :form="form" field="end_at"></has-error>
              </div>
            </div>

            <div class="modal-footer">
              <button type="submit" class="btn btn-success">{{$t('Task.update')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from "moment";
import DatePicker from "vue2-datepicker";
import trackApi from "../../api/tracks";
import taskApi from "../../api/tasks";

export default {
  components: { DatePicker },
  data() {
    return {
      editMode: false,
      task_id: this.$route.params.id,
      task: {},
      tracking_task: null,
      counter: { seconds: 0 },
      activeTimerString: null,
      counted_time: null,
      duration: "0",
      listTracking_Task: [],
      form: new Form({
        id: "",
        start_at: "",
        end_at: ""
      })
    };
  },
  methods: {
    deleteTask(id) {
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
          this.$Progress.start();
          this.$store
            .dispatch("task/deleteTask", id)
            .then(response => {
              this.$Progress.finish();
              Toast.fire({
                type: "success",
                title: response.data.message
              });
              this.$router.push({ name: "tasks.list" });
            })
            .catch(error => {
              this.$Progress.fail();
              console.log(error);
              Toast.fire({
                type: "error",
                title: error.response.data.message
              });
            });
        }
      });
    },
    startTracking() {
      // Reset the counter and timer string
      this.counted_time = null;
      trackApi
        .post({
          comment: "new tracking",
          start_at: moment().format("DD-MM-YYYY HH:mm:ss"),
          task_id: this.task_id
        })
        .then(response => {
          this.activeTimerString = this.humanReadableFromSecounds(
            this.duration
          );
          this.tracking_task = response.data.data;
          this.startTimer();
        })
        .catch(error => {
          Toast.fire({
            type: "error",
            title: error.response.data.message
          });
        });
    },
    startTimer() {
      let vm = this;
      let started = moment(this.tracking_task.start_at,'DD-MM-YYYY HH:mm:ss');
      vm.counter.seconds = parseInt(
        moment.duration(moment().diff(started)).asSeconds()
      );
      vm.counter.ticker = setInterval(() => {
        vm.counted_time = null;
        vm.activeTimerString = vm.humanReadableFromSecounds(
          ++vm.duration + vm.counter.seconds
        );
      }, 1000);
    },
    stopTracking() {
      this.$Progress.start();
      trackApi
        .put({
          track_id: this.tracking_task.id,
          end_at: moment().format("DD-MM-YYYY HH:mm:ss"),
          task_id: this.task_id
        })
        .then(response => {
          this.tracking_task = response.data.data;
          // count duration for this task
          this.countTaskDuration(this.task_id);
          this.$Progress.finish();
          // Stop the ticker
          clearInterval(this.counter.ticker);
          this.activeTimerString = null;
        })
        .catch(error => {
          this.$Progress.fail();
          Toast.fire({
            type: "error",
            title: error.response.data.message
          });
        });
    },
    /**
     * Splits seconds into hours, minutes, and seconds.
     */
    _readableTimeFromSeconds: function(seconds) {
      const hours = 3600 > seconds ? 0 : parseInt(seconds / 3600, 10);
      return {
        hours: this._padNumber(hours),
        seconds: this._padNumber(seconds % 60),
        minutes: this._padNumber(parseInt(seconds / 60, 10) % 60)
      };
    },
    /**
     * Conditionally pads a number with "0"
     */
    _padNumber: number =>
      number > 9 ? number : number === 0 ? "00" : "0" + number,

    // Count Duration for a specfic task.
    countTaskDuration(task_id) {
      trackApi
        .countDuration(task_id)
        .then(response => {
          this.duration = response.data.data.tracking;
          this.counted_time = this.humanReadableFromSecounds(this.duration);
        })
        .catch(error => {
          Toast.fire({
            type: "error",
            title: error.response.data.message
          });
        });
    },
    // check if this track is in progress
    checkTrackingInProgress(task_id) {
      trackApi
        .checkTrackingInProgress(task_id)
        .then(response => {
          this.tracking_task = response.data.data;
          this.startTimer();
        })
        .catch(error => {
          // console.log(error);
        });
    },
    // list all Tracking_Task for this task
    listTrackingTask(task_id) {
      this.$Progress.start();
      trackApi
        .getHistory(task_id)
        .then(response => {
          this.$Progress.finish();
          this.listTracking_Task = response.data.data;
        })
        .catch(error => {
          this.$Progress.fail();
        });
    },
    humanReadableFromSecounds(seconds) {
      let duration = this._readableTimeFromSeconds(seconds);
      return `${duration.hours}:${duration.minutes}:${duration.seconds}`;
    },
    editModel(track) {
      console.log(track);
      this.form.reset();
      $("#Modal").modal("show");
      this.form.fill(track);
    },
    editTrackingTime(task_id, track_id) {
      this.$Progress.start();
      this.form
        .patch("/v-api/tracking/" + task_id + "/" + track_id)
        .then(response => {
          $("#Modal").modal("hide");
          this.$Progress.finish();
          this.listTrackingTask(task_id);
          this.countTaskDuration(task_id);
          Toast.fire({
            type: "success",
            title: response.data.message
          });
        })
        .catch(error => {
          this.$Progress.fail();
        });
    },
    deleteTrack(task_id, track_id) {
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
          this.$Progress.start();
          trackApi
            .delete({ task_id, track_id })
            .then(response => {
              this.$Progress.finish();
              this.listTrackingTask(task_id);
              this.countTaskDuration(task_id);
              Toast.fire({
                type: "success",
                title: response.data.message
              });
            })
            .catch(error => {
              this.$Progress.fail();
              Toast.fire({
                type: "error",
                title: error.response.data.message
              });
            });
        }
      });
    }
  },

  mounted() {
    // count total duration
    this.countTaskDuration(this.task_id);
    // check if this track is in progress
    this.checkTrackingInProgress(this.task_id);

    taskApi
      .show(this.task_id)
      .then(response => {
        this.task = response.data.data;
        this.$Progress.finish();
      })
      .catch(error => {
        this.$Progress.fail();
      });
  },
  computed: {
    orderedTrack: function() {
      return this.listTracking_Task.reverse();
    }
  },
    filters:{
        subStr: (value) => {
            if(value && value.length > 9) {
                return value.substring(0, 12) + '...';
            }
            return value;
        },
        tasksDateNow: (value) => {
            return moment(value).fromNow()
        },
    },
};
</script>

<style scoped>
#duration-text {
  font-size: 36px;
  font-weight: 300;
}
.mx-datepicker {
  display: block;
  width: 100%;
}
.invalid-feedback {
  display: inline;
}

.actions-btn-cont{
    height: 70%;
    border-radius: 15px;
    border:1px solid #b8b8b8;
    background-color: #ffffff;
    padding:0 1.2rem;
    margin-top:12%;
    transform: translateY(12%);
}

.actions-btn-cont .action{
    height: 50%;
    width: 100%;
}

.action .actionIcon i{
    margin-top: 17%;
    margin-left: 1.7rem;
    font-size: 1.3rem;

    transform: translateY(50%) translateX(-50%);
}

/*colors*/
#open{
    background-color: #A0466F;
}
#done{
    background-color: #67A046;
}
#in-progress{
    background-color: #3490dc;
}
#pending{
    background-color: #EEA24C;
}
/*end colors*/
.col {
    padding-top: 0;
    padding-bottom: 0;
}

.task-card .card-cont{
    margin-top:1rem;
    margin-bottom:1rem
}

.task-card .f-task-col{
    display:flex;
    flex-direction: column;
}

.task-card .task-status{
    width:70%;
    text-align: center;
    color: #ffffff;
    font-weight: bold;

}

.stat{
    font-weight: bold;
    padding: 0.7rem 0.7rem 0px 0px;
    text-align: left;
    color: #ffffff;
    height:2.8rem;
    margin-left: -.8rem;
}
.task-card .task-created_by{
    margin-top: .4rem;
}

.task-card .task-created_by .task-created_by-col{
    display: flex;
    flex-direction: column;
}

.task-created_by-col small{
    color: #484848;
    max-width:5rem;
    padding-top:.7rem;
    padding-left: .3rem;
}

/*end task card styling*/

</style>
