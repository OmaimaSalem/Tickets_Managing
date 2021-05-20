<template>
  <v-main>
    <v-container>
      <v-row class="headings">
        <v-col cols="3" class="title">
          <h1>{{ $t("report.member") }} </h1>
          <span class="underline"></span>
        </v-col>

        <v-col cols="8" class="form-group">
          <div class="input-group">
            <div class="input-group-icon">
              <i class="fas fa-search"></i>
            </div>

            <input
              placeholder="Search"
              v-model="args.global_search"
              @keyup.enter="getReportsUsers"
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

      <v-row style="margin-top: -25px; margin-bottom: -25px">
        <v-col cols="12">
          <label for class="float-left col-form-label col-1"
            >{{ $t("report.SearchType") }}</label
          >

          <v-col
            cols="10"
            class="form-group float-left">
            <div class="input-group">

              <ul class="list-unstyled list-inline" cols="5">
                <li class="list-inline-item"><label style="font-weight:500;"><input v-model="args.type" name="search_type" value="ticket_name" type="radio" />&nbsp; {{ $t("report.TicketName") }}</label></li>
                <li class="list-inline-item"><label style="font-weight:500;"><input v-model="args.type" name="search_type" value="task_name" type="radio" />&nbsp; {{ $t("report.TaskName") }}</label></li>
                <li class="list-inline-item"><label style="font-weight:500;"><input v-model="args.type" name="search_type" value="project_name" type="radio" />&nbsp;{{ $t("report.ProjectName") }}</label></li>
              </ul>
              <!-- <select v-model="args.type" class="form-control border-radius">
                <option value="ticket_name">Ticket Name</option>
                <option value="task_name">Task Name</option>
                <option value="project_name">Project Name</option>
              </select> -->


            <input
                style="margin-top: -1rem;"
              placeholder="Search"
              v-model="args.search"
              type="text"
              class="form-control border-radius offset-1 col-8"
               @keyup.enter="getReportsUsers"
            />


              <v-btn
                @click.prevent="getReportsUsers"
                color="#18519e"
                style="margin-left: 10px; border-radius: 10px; color: #fff;margin-top: -1rem;"
              >
                {{ $t("report.ViewReport") }}
              </v-btn>
            </div>
          </v-col>
        </v-col>
      </v-row>

      <!-- headings  -->

      <v-simple-table  style="margin-top: -2.5rem;">
        <template v-slot:default>
          <thead>
            <tr>
              <th class="text-left">{{ $t("report.Employeename") }}</th>
              <th class="text-left">{{ $t("report.Position") }}</th>
              <th class="text-left">{{ $t("report.Mobilenumber") }}</th>
              <th class="text-left">{{ $t("report.EmailAddress") }}</th>
              <th class="text-left">{{ $t("report.Company") }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in ReportsUsers.data" :key="user.id">
              <td>{{ user.name ? user.name : "-----" }}</td>
              <td>
                {{
                  user.metadata && user.metadata.position
                    ? user.metadata.position
                    : "-----"
                }}
              </td>
              <td>
                {{
                  user.metadata && user.metadata.mobile
                    ? user.metadata.mobile
                    : "-----"
                }}
              </td>
              <td>{{ user.email ? user.email : "-----" }}</td>
              <td>
                {{
                  user.metadata && user.metadata.company
                    ? user.metadata.company
                    : "-----"
                }}
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>

      <v-row class="mt-2">
        <v-col cols="4" offset="8" class="float-right">
          <v-col cols="8" class="float-right">
            <pagination
              :data="ReportsUsers"
              :limit="parseInt(10)"
              size="small"
              @pagination-change-page="getReportPage"
            >
              <span slot="prev-nav">&lt; Previous</span>
              <span slot="next-nav">Next &gt;</span>
            </pagination>
          </v-col>
          <v-col v-if="ReportsUsers.last_page > 1" cols="4" class="float-right pagination-input">
            <input
              class="form-control"
              type="text"
              v-model="args.page"
              @input="getReportsUsers"
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

export default {
  data() {
    return {
      args: {
        search: null,
        type: "ticket_name",
        page: 1,
        export_excel: null,
        global_search: null
      },
    };
  },
  methods: {
    getReportPage($page) {
      this.args.page = $page;
      this.getReportsUsers();
    },
    export_to_excel() {
      window.open(
        this.$base_url +
          "/v-api/reports/project-users?filter=" +
          encodeURI(JSON.stringify(this.args)),
        "_blank"
      );
    },
    getReportsUsers() {
         this.$Progress.start();
      this.$store
        .dispatch("report/reportsUsers", this.args)
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
    this.getReportsUsers();
  },

  computed: {
    ...mapGetters({
      ReportsUsers: "report/getReportsUsers",
    }),
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
