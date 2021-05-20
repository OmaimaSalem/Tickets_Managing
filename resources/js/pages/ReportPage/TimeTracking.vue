<template>
  <v-main>
    <v-container>
      <v-row class="headings">
        <v-col cols="3" class="title">
          <h3>{{ $t("TimeTracking.TimeTracking") }}</h3>
          <span class="underline"></span>
        </v-col>

        <v-col cols="8" class="form-group">
          <div class="input-group">
            <div class="input-group-icon">
              <i class="fas fa-search"></i>
            </div>

            <input
              placeholder="Search"
              v-model="args.search"
              @keyup.enter="getTracking"
              type="text"
              class="form-control border-left-less border-radius"
            />

            <v-btn
              @click.prevent="export_to_excel"
              color="#18519e"
              style="margin-left: 10px; border-radius: 10px; color: #fff"
            >
              <v-icon
                color="#f1b40c"
                style="
                  background-color: #fff;
                  border-radius: 15px;
                  padding: 5px;
                  margin-right: 5px;
                  margin-left: -5px;
                "
                size="13px"
                >fas fa-file-export</v-icon
              >
              Export
            </v-btn>
          </div>
        </v-col>
      </v-row>

      <v-row style="margin-top: -2.5rem;">
        <v-col cols="12">
          <label for class="mt-2 float-left col-form-label col-1"
            >{{ $t("TimeTracking.FilteredDate") }}</label
          >

          <v-col
            cols="10"
            class="form-group float-left"
            style="margin-top: -10px; margin-left: 10px"
          >
            <div class="input-group">
              <!-- date picker -->
              <v-col cols="10">
                <date-picker
                  v-model="args.date"
                  lang="en"
                  type="date"
                  format="DD-MM-YYYY"
                  :minute-step="15"
                  value-type="format"
                  input-class="form-control border-radius"
                  @keyup.enter="getTracking"
                  @change="getTracking"
                ></date-picker>
              </v-col>

              <v-btn
                @click.prevent="getTracking"
                color="#18519e"
                style="font-size:12px; margin-top: 12px; border-radius: 10px; color: #fff"
              >
                {{ $t("TimeTracking.ViewReport") }}
              </v-btn>
            </div>
          </v-col>
        </v-col>
      </v-row>

      <!-- headings  -->

      <v-simple-table style="margin-top: -2.5rem;">
        <template v-slot:default>
          <thead>
            <tr>
              <th class="text-left">{{ $t("TimeTracking.Employeename") }}</th>
              <th class="text-left">{{ $t("TimeTracking.ProjectName") }}</th>
              <th class="text-left">{{ $t("TimeTracking.TicketName") }}</th>
              <th class="text-left">{{ $t("TimeTracking.TaskName") }}</th>
              <th class="text-left">{{ $t("TimeTracking.totalhours") }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in timeTracking.data" :key="user.id">
              <td>{{ user.name ? user.name : "-----" }}</td>
              <td>
                  <select class="form-control"  v-if="user.time_projects">
                      <option>All Projects</option>
                      <option :value="project.id" v-for="project in user.time_projects" :key="project.id">
                          {{ project.name }}
                      </option>
                  </select>
              </td>
              <td>
                  <select class="form-control" v-if="user.tickets">
                      <option>All Tickets</option>
                      <option :value="ticket.id" v-for="ticket in user.tickets" :key="ticket.id">
                          {{ ticket.name }}
                      </option>
                  </select>


              </td>
              <td>

                  <select class="form-control" v-if="user.tasks">
                      <option>All Tasks</option>
                      <option :value="task.id" v-for="task in user.tasks" :key="task.id">
                          {{ task.name }}
                      </option>
                  </select>


              </td>
              <td>
                {{ user.total_time }}
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>

      <v-row class="mt-2">
        <v-col cols="4" offset="8" class="float-right">
          <v-col cols="8" class="float-right">
            <pagination
              :data="timeTracking"
              :limit="parseInt(10)"
              size="small"
              @pagination-change-page="getTrackingPage"
            >
              <span slot="prev-nav">&lt; Previous</span>
              <span slot="next-nav">Next &gt;</span>
            </pagination>
          </v-col>

          <v-col v-if="timeTracking.last_page > 1" cols="4" class="float-right pagination-input">
            <input
              class="form-control"
              type="text"
              v-model="args.page"
              @input="getTrackingPage"
              placeholder="Page number"
            />
            <i class="fa fa-search icon"></i>
          </v-col>


        </v-col>
      </v-row>
    </v-container>
  </v-main>
</template>

<script>
import pagination from "laravel-vue-pagination";
import { mapGetters } from "vuex";
import DatePicker from "vue2-datepicker";

export default {
  data() {
    return {
      showEmpProject:false,
      showEmpProjectId: null,
      project_id:null,
      ticket_id: null,
      task_id: null,
      args: {
        search: null,
        date: null,
        page: 1,
        export_excel: null,
        time: true,
      },
    };
  },
  methods: {
    gotoProjectPage(event){
        this.$router.push({
            path: `/admin/project/${event.target.value}`
        });
    },
    gotoTicketPage(event){
        this.$router.push({
            path: `/admin/ticket/${event.target.value}`
        });
    },
    gotoTaskPage(event){

        this.$router.push({
            path: `/admin/task/${event.target.value}`
        });
    },


    toggleUserPrjects(user_id){
        this.showEmpProjectId = user_id;
        this.showEmpProject = !this.showEmpProject;
    },
    getTrackingPage($page) {
      this.args.page = $page;
      this.getTracking();
    },
    export_to_excel() {
      window.open(
        this.$base_url +
          "/v-api/reports/time-tracking?time=true&month="+this.args.date+"&page="+this.args.page+"&search="+this.args.search+"&export_excel=true",
        "_blank"
      );
    },
    getTracking() {
      this.$Progress.start();
      this.$store
        .dispatch("report/timeTracking", this.args)
        .then((response) => {
          this.$Progress.finish();
        })
        .catch((error) => {
          this.$Progress.fail();
          if (error.response) {
            this.form.errors.errors = error.response.data.errors;
          }
        });
    },
  },
  mounted() {
    this.getTracking();
  },
  computed: {
    ...mapGetters({
      timeTracking: "report/getTimeTracking",
    }),
  },

  components: {
    DatePicker,
  },
};
</script>

<style scoped>
tbody tr:nth-of-type(odd) {
  background-color: #dcdcdc;
}
thead tr th {
  color: #2f5298 !important;
  font-size: 18px !important;
}
.underline {
  display: block;
  height: 2px;
  border-bottom: 2px solid #2f5298;
  width: 120px;
}

.input-group-icon {
  background-color: #fff !important;
  margin-right: -10px;
  border-radius: 10px 0px 0px 10px;
  border: 1px solid #ced4da;
  border-right: none;
  padding: 6px 10px;
  z-index: 100;
}

.form-control.border-left-less {
  border-left: none;
}

.border-radius {
  border-radius: 10px;
}

.input-group > .form-control:not(:last-child) {
  border-radius: 10px;
}

:focus {
  outline: none !important;
  box-shadow: none !important;
  border: 1px solid #ced4da;
}

.pagination-input .icon {
  position: absolute;
  top: 25px;
  left: 20px;
}

.pagination-input input {
  padding-left: 30px;
}
</style>
