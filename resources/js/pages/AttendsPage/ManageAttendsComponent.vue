<template>
  <div>
    <div>
      <button class="btn btn-secondary" href="javascript:;" @click="reports()">
        {{$t('ManageAttends.reports')}} &nbsp;
        <i class="fa fa-file"></i>
      </button>
    </div>

    <vue-bootstrap4-table :rows="employees" :columns="columns" :actions="actions">
      <template slot="action-buttons" slot-scope="props">
        <router-link :to="'/admin/user-attends/' + props.row.id">{{$t('ManageAttends.view')}}</router-link>
      </template>

      <template
        slot="skip-vacation-limit-area"
        slot-scope="props"
      >{{ props.row.skip_vacation_limit ? 'Yes' : 'No' }}</template>
    </vue-bootstrap4-table>

    <div
      id="reportModel"
      class="modal fade bd-example-modal-lg"
      tabindex="-1"
      role="dialog"
      aria-labelledby="myLargeModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{$t('ManageAttends.newReport')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-group">
                <label for="recipient-name" class="col-form-label">{{$t('ManageAttends.employee')}}:</label>
                <select
                  :class="{
                                        'form-control': true,
                                        'is-invalid': is_invalid
                                    }"
                  v-model="employeeId"
                >
                  <option
                    v-for="(employee, index) in employees"
                    :key="index"
                    :value="employee.id"
                  >{{ employee.name }}</option>
                </select>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-6">
                    <label for="recipient-name" class="col-form-label">{{$t('ManageAttends.form')}}</label>
                    <div>
                      <date-picker
                        v-model="reportDateFrom"
                        lang="en"
                        type="date"
                        format="DD-MM-YYYY"
                        input-class="form-control"
                        value-type="format"
                      ></date-picker>
                    </div>
                  </div>

                  <div class="col-6">
                    <label for="recipient-name" class="col-form-label">{{$t('ManageAttends.to')}}</label>
                    <div>
                      <date-picker
                        v-model="reportDateTo"
                        lang="en"
                        type="date"
                        format="DD-MM-YYYY"
                        input-class="form-control"
                        value-type="format"
                      ></date-picker>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{$t('ManageAttends.close')}}</button>
            <button @click="report()" type="button" class="btn btn-primary">{{$t('ManageAttends.reports')}}</button>
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
      employeeId: null,
      reportDateFrom: null,
      reportDateTo: null,
      is_invalid: false,
      columns: [
        {
          label: this.$t('ManageAttends.name'),
          name: "name",
          sort: true,
          row_text_alignment: "text-left",
          column_text_alignment: "text-left"
        },
        {
          label: this.$t('ManageAttends.email'),
          name: "email",
          sort: true,
          row_text_alignment: "text-left",
          column_text_alignment: "text-left"
        },
        {
          label: this.$t('ManageAttends.skipVacation'),
          name: "skip-vacation-limit-area",
          sort: true
        },
        {
          label: this.$t('ManageAttends.action'),
          name: "action-buttons"
        }
      ],

      actions: [
        // {
        //     btn_text: "Reports",
        //     event_name: "reports",
        //     class: "btn btn-secondary",
        //     event_payload: {
        //         id: 1
        //     }
        // }
      ]
    };
  },
  methods: {
    getEmployees() {
      this.$Progress.start();
      this.$store
        .dispatch("attend/getEmployees")
        .then(response => {
          this.$Progress.finish();
        })
        .catch(error => {
          this.$Progress.fail();
        });
    },
    reports(id) {
      $("#reportModel").modal("show");
    },
    report() {
      if (!this.employeeId) {
        this.is_invalid = true;
        return false;
      }

      this.is_invalid = false;
      $("#reportModel").modal("hide");

      let $url = "/admin/user-attends/" + this.employeeId;
      let $from,
        $to = false;
      let $from_url,
        $to_url,
        $rest_url = "";

      if (this.reportDateFrom) {
        $from = true;
        $from_url = "/" + this.reportDateFrom;
      }

      if (this.reportDateTo) {
        $to = true;
        $to_url = "/" + this.reportDateTo;
      }

      if (this.reportDateFrom && this.reportDateTo) {
        $url += $from_url + $to_url;
      } else if (!this.reportDateFrom && this.reportDateTo) {
        $rest_url += "/@" + $to_url;
      } else if (this.reportDateFrom && !this.reportDateTo) {
        $rest_url += $from_url + "/@";
      }
      this.$router.push($url + $rest_url);
    }
  },
  computed: {
    ...mapGetters({
      employees: "attend/getEmployees"
    })
  },
  mounted() {
    this.getEmployees();
  }
};
</script>
