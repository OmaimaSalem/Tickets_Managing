<template>
  <v-main>
    <v-container>
      <v-row class="headings">
        <v-col cols="3" class="title">
          <h3>{{ $t("Financial.Financial")}}</h3>
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
              @keyup.enter="getFinancial"
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
      <v-simple-table v-if="financial"  style="margin-top: -1rem;">
        <template v-slot:default>
          <thead>
            <tr>
              <th class="text-left">{{ $t("Financial.Projectname") }}</th>
              <th class="text-left">{{ $t("Financial.Client") }}</th>
              <th class="text-left">{{ $t("Financial.ActualTime") }}</th>
              <th class="text-left">{{ $t("Financial.RemaningTime") }}</th>
              <th class="text-left">{{ $t("Financial.Budget") }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="project in financial.data" :key="project.id">
              <td>{{ project.name ? project.name : "-----" }}</td>
              <td>
                {{ project.owners ? project.owners.map(own => own.name).join(',') : "-----" }}
              </td>
              <td>
                {{ project.project_actual_time  }}
              </td>
              <td>
                {{ project.project_remaining_time  }}

              </td>
              <td>
                {{ project.budget }} $
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>

      <v-row class="mt-2">
        <v-col cols="4" offset="8" class="float-right">
          <v-col cols="8" class="float-right">
            <pagination
              :data="financial"
              :limit="parseInt(10)"
              size="small"
              @pagination-change-page="getFinancialPage"
            >
              <span slot="prev-nav">&lt; Previous</span>
              <span slot="next-nav">Next &gt;</span>
            </pagination>
          </v-col>

          <v-col v-if="financial.last_page > 1" cols="4" class="float-right pagination-input">
            <input
              class="form-control"
              type="text"
              v-model="args.page"
              @input="getFinancial"
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
      args: {
        search: null,
        page: 1,
        export_excel: null,
        financial: true,
      },
    };
  },
  methods: {



    getFinancialPage($page) {
      this.args.page = $page;
      this.getFinancial();
    },
    export_to_excel() {
      window.open(
        this.$base_url +
          "/v-api/reports/financial?financial=true&search="+this.args.search+"&export_excel=true",
        "_blank"
      );
    },
    getFinancial() {
      this.$Progress.start();
      this.$store
        .dispatch("report/financial", this.args)
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
    this.getFinancial();
  },

  computed: {
    ...mapGetters({
      financial: "report/getFinancial",
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
