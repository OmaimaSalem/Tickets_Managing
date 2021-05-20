<template>
  <v-main data-app>
    <v-container>
      <v-row class="headings">
        <v-col cols="3" class="title">
          <h3>{{ $t('Layout.Employee_project')}}</h3>
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
              @keyup.enter="getUsersTasks"
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


      <v-row style="margin-top:-2rem">
        <v-col cols="12">
          <label for class="mt-2 float-left col-form-label"
            >{{ $t('report.Employeename')}}</label>

          <v-col
            cols="10"
            class="form-group float-left"
            style="margin-top: -10px; margin-left: 10px"
          >
            <div class="input-group">
              <!-- date picker -->
              <v-col cols="4">


                <multiselect
                  v-model="args.user"
                  :options="users"
                  :multiple="false"
                  :close-on-select="true"
                  :clear-on-select="false"
                  :preserve-search="true"
                  :placeholder="$t('Project.pickSome')"
                  label="name"
                  track-by="id"
                  :preselect-first="true"
                  @change="getUsersTasks"
                >
                </multiselect>

              </v-col>

            <v-col cols="4">

            <!-- <label  cols="1" style="float:left">Duration:</label> -->


                <date-picker
                  v-model="args.dates"
                  lang="en"
                  type="date"
                  format="DD-MM-YYYY"
                  :minute-step="15"
                  value-type="format"
                  input-class="form-control border-radius"
                  @change="getUsersTasks"
                ></date-picker>



      <!-- <v-menu
        ref="menu"
        v-model="menu"
        :close-on-content-click="false"
        :return-value.sync="args.date"
        transition="scale-transition"
        offset-y
        min-width="290px"
      >
        <template v-slot:activator="{ on, attrs }">
          <v-text-field
            v-model="dateRangeText"
            label="Picker in menu"
            prepend-icon="mdi-calendar"
            readonly
            v-bind="attrs"
            v-on="on"
          ></v-text-field>
        </template>
        <v-date-picker
          v-model="args.dates"
          no-title
          range
          scrollable
        >
          <v-spacer></v-spacer>
          <v-btn
            text
            color="primary"
            @click="menu = false"
          >
            Cancel
          </v-btn>
          <v-btn
            text
            color="primary"
            @click="$refs.menu.save(args.date)"
          >
            OK
          </v-btn>
        </v-date-picker>
      </v-menu> -->


              </v-col>


              <v-btn
                @click.prevent="getUsersTasks"
                color="#18519e"
                style="margin-top: 12px; border-radius: 10px; color: #fff"
              >
                {{ $t('report.ViewReport')}}
              </v-btn>
            </div>
          </v-col>
        </v-col>
      </v-row>



      <!-- headings  -->
      <v-simple-table v-if="usersTasks"  style="margin-top: -2.5rem;">
        <template v-slot:default>
          <thead>
            <tr>
              <th class="text-left">{{ $t('report.Subject')}}</th>
              <th class="text-left">{{ $t('report.Type')}}</th>
              <th class="text-left">{{ $t('report.DateFrom')}}</th>
              <th class="text-left">{{ $t('report.DateTo')}}</th>
              <th class="text-left">{{ $t('report.Timeconsumed')}}</th>
              <th class="text-left">{{ $t('report.Status')}}</th>
              <th class="text-left">{{ $t('report.Client')}}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="task in usersTasks.data" :key="task.id">
              <td>{{ task.name ? task.name : "-----" }}</td>
              <td>{{ task.type ? task.type : "-----" }}</td>
              <td>{{ task.date_form ? task.date_form : "-----" }}</td>
              <td>{{ task.date_to ? task.date_to : "-----" }}</td>
              <td>{{ task.time_consumed ? task.time_consumed : "-----" }}</td>
              <td>{{ task.status ? task.status : "-----" }}</td>
              <td>{{ task.client ? task.client.map(function(cli){
                 return cli.name }).join(',') : "-----" }}</td>

            </tr>
          </tbody>
        </template>
      </v-simple-table>

      <v-row class="mt-2">
        <v-col cols="4" offset="8" class="float-right">
          <v-col cols="8" class="float-right">
            <pagination
              :data="usersTasks"
              :limit="parseInt(10)"
              size="small"
              @pagination-change-page="getUsersTasksPage"
            >
              <span slot="prev-nav">&lt; Previous</span>
              <span slot="next-nav">Next &gt;</span>
            </pagination>
          </v-col>


          <v-col v-if="usersTasks.last_page > 1" cols="4" class="float-right pagination-input">
            <input
              class="form-control"
              type="text"
              v-model="args.page"
              @input="getUsersTasks"
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
      menu: false,
      modal: false,
      menu2: false,

      args: {
        search: null,
        page: 1,
        export_excel: null,
        user:null,
        date:null,
        dates: []
      },
    };
  },
  methods: {
    getUsersTasksPage($page) {
      this.args.page = $page;
      this.getUsersTasks();
    },
    export_to_excel() {
      window.open(
        this.$base_url +
          "/v-api/reports/project-employee-tasks?projectemployeetasks=true&search="+this.args.search+"&export_excel=true",
        "_blank"
      );
    },
    getUsersTasks() {
      this.$Progress.start();
      this.$store
        .dispatch("report/getUsersTasks", this.args)
        .then((response) => {
          this.$Progress.finish();
        })
        .catch((error) => {          this.$Progress.fail();
          if (error.response) {
            this.form.errors.errors = error.response.data.errors;
          }
        });
    },


    getUsers() {
    this.$store
        .dispatch("regularUser/getRegularUser", {
            roles: false
        })
        .then()
        .catch(error => {
            console.log(error);
        });
    },

  },
  mounted() {
    this.getUsersTasks();
    this.getUsers();
  },

  computed: {
    ...mapGetters({
      usersTasks: "report/getUsersTasks",
      users: "regularUser/activeRegularUser",
    }),

    dateRangeText () {
       return this.args.dates.join(' ~ ')
    },


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

<style>
.theme--light.v-btn:not(.v-btn--flat):not(.v-btn--text):not(.v-btn--outlined){
    background-color:#2f5298 !important;
}

.v-date-picker-table .v-btn.v-btn--active{
    color:#fff !important;
    background-color:#2f5298 !important;
}

</style>
