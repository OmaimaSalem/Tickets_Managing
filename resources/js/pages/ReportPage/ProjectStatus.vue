<template>
  <v-main>
    <v-container>
      <v-row class="headings">
        <v-col cols="3" class="title">
          <h3>{{ $t("Project.ProjectStatus") }}</h3>
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
              @keyup.enter="getProjectStatus"
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



      <!-- headings  -->
      <v-simple-table v-if="projectsStatus"  style="margin-top: -1rem;">
        <template v-slot:default>
          <thead>
            <tr>
              <th class="text-left">{{ $t("Project.ProjectName") }}</th>
              <th class="text-left">{{ $t("Project.Open") }}</th>
              <th class="text-left">{{ $t("Project.Inprogress") }}</th>
              <th class="text-left">{{ $t("Project.Pending") }}</th>
              <th class="text-left">{{ $t("Project.Done") }}</th>
              <th class="text-left">{{ $t("Project.AssignTo") }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="project in projectsStatus.data" :key="project.id">
              <td>{{ project.name ? project.name : "-----" }}</td>
              <td>
                <select  @change="gotoOpen" class="form-control">
                    <option value="">All Open</option>
                    <option :value="openticket.id+'-ticket'" v-for="openticket in filteredTickets(project,1)" :key="openticket.id">{{openticket.name}}</option>
                    <option :value="openticket.id+'-task'" v-for="openticket in filteredTasks(project,1)" :key="openticket.id">{{openticket.name}}</option>
                </select>
              </td>
              <td>
                <select  @change="gotoInprogress" class="form-control">
                    <option value="">All In-progress</option>
                    <option :value="openticket.id+'-ticket'" v-for="openticket in filteredTickets(project,3)" :key="openticket.id">{{openticket.name}}</option>
                    <option :value="openticket.id+'-task'" v-for="openticket in filteredTasks(project,3)" :key="openticket.id">{{openticket.name}}</option>
                </select>
              </td>
              <td>
                <select  @change="gotoPending" class="form-control">
                    <option value="">All Pending</option>
                    <option :value="openticket.id+'-ticket'" v-for="openticket in filteredTickets(project,2)" :key="openticket.id">{{openticket.name}}</option>
                    <option :value="openticket.id+'-task'" v-for="openticket in filteredTasks(project,2)" :key="openticket.id">{{openticket.name}}</option>

                </select>
              </td>
              <td>
                <select @change="gotoDone" class="form-control">
                    <option value="">All Done</option>
                    <option  :value="openticket.id+'-ticket'" v-for="openticket in filteredTickets(project,4)" :key="openticket.id">{{openticket.name}}</option>
                    <option  :value="openticket.id+'-task'" v-for="openticket in filteredTasks(project,4)" :key="openticket.id">{{openticket.name}}</option>
                </select>
              </td>

              <td>
                <select class="form-control">
                    <option :value="assign.id" v-for="assign in project.project_assign" :key="assign.id">{{assign.name}}</option>
                </select>
              </td>

            </tr>
          </tbody>
        </template>
      </v-simple-table>

      <v-row class="mt-2">
        <v-col cols="4" offset="8" class="float-right">
          <v-col cols="8" class="float-right">
            <pagination
              :data="projectsStatus"
              :limit="parseInt(10)"
              size="small"
              @pagination-change-page="getProjectStatusPage"
            >
              <span slot="prev-nav">&lt; Previous</span>
              <span slot="next-nav">Next &gt;</span>
            </pagination>
          </v-col>


          <v-col v-if="projectsStatus.last_page > 1" cols="4" class="float-right pagination-input">
            <input
              class="form-control"
              type="text"
              v-model="args.page"
              @input="getProjectStatus"
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
      open_selected: null,
      inprogress_selected: null,
      pending_selected: null,
      done_selected: null,

      args: {
        search: null,
        page: 1,
        export_excel: null,
        projectsStatus: true,
      },
    };
  },
  methods: {
    gotoOpen(event) {
        if(event.target.value =="") return false;
        let $value = event.target.value.split('-');

        this.$router.push({
                path: `/admin/${$value[1]}/${$value[0]}`
        });
    },
    gotoInprogress() {
        if(event.target.value =="") return false;
        let $value = event.target.value.split('-');

        this.$router.push({
                path: `/admin/${$value[1]}/${$value[0]}`
        });

    },
    gotoPending() {
        if(event.target.value =="") return false;
        let $value = event.target.value.split('-');

        this.$router.push({
                path: `/admin/${$value[1]}/${$value[0]}`
        });

    },
    gotoDone() {
        if(event.target.value =="") return false;
        let $value = event.target.value.split('-');

        this.$router.push({
                path: `/admin/${$value[1]}/${$value[0]}`
        });

    },
    // open 1 , pending 2, in-progress 3 , done 4
    filteredTickets(project,status) {
        return project.tickets.filter((element) => {
            return element.status_id == status;
        })
    },

    filteredTasks(project,status) {
        return project.tasks.filter((element) => {
            return element.status_id == status;
        })
    },

    getProjectStatusPage($page) {
      this.args.page = $page;
      this.getProjectStatus();
    },
    export_to_excel() {
      window.open(
        this.$base_url +
          "/v-api/reports/project-status?projectstatus=true&search="+this.args.search+"&export_excel=true",
        "_blank"
      );
    },
    getProjectStatus() {
      this.$Progress.start();
      this.$store
        .dispatch("report/getProjectStatus", this.args)
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
    this.getProjectStatus();
  },

  computed: {
    ...mapGetters({
      projectsStatus: "report/getProjectStatus",
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
