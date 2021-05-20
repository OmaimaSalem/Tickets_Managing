<template>
<div>
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
    <button @click="getEmployeesVacations()">Filter</button>
    <vue-bootstrap4-table :rows="employeesVacations" :columns="columns">

      <template slot="from-area" slot-scope="props">
       {{ props.row.day_from+" "+props.row.time_from }}
      </template>

      <template slot="to-area" slot-scope="props">
       {{ props.row.day_to+" "+props.row.time_to }}
      </template>

      <template slot="user-area" slot-scope="props">
       {{ props.row.user.name }}
      </template>


      <template slot="sick-area" slot-scope="props">
       {{ props.row.sick_vacation == 'true' ? 'yes' : 'no' }}
      </template>


      <template slot="action-area" slot-scope="props">

            <span v-if="props.row.status == 'confirmed'"><b>{{$t('Vacation.confirmed')}}</b></span>
            <span v-if="props.row.status != 'confirmed'"><a href="javascript:;" @click="setVacationAction(props.row.id,'confirmed')">{{$t('Vacation.confirm')}}</a></span>
        /
            <span v-if="props.row.status == 'rejected'"><b>{{$t('Vacation.rejected')}}</b></span>
            <span v-if="props.row.status != 'rejected'"><a href="javascript:;" @click="setVacationAction(props.row.id,'rejected')">{{$t('Vacation.reject')}}</a></span>
        /
        <a href="javascript:;"  @click="editVacation(props.row)">{{$t('Vacation.edit')}}</a>
      </template>

    </vue-bootstrap4-table>






<div id="vacationModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{$t('Vacation.vacation')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="form-group row">
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
                            format="HH:mm:ss"
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
                    <label for="name" class="col-md-12">{{$t('Vacation.priorty')}}</label>
                    <br />
                    <div class="col-md-6">
                        <select class="form-control" v-model="vacation.priority">
                            <option value="low">{{$t('Vacation.low')}}</option>
                            <option value="medium">{{$t('Vacation.medium')}}</option>
                            <option value="high">{{$t('Vacation.high')}}</option>
                        </select>
                    </div>
                </div>


                <div v-if="vacation.sick_vacation == 'true'">
                    <a :href="'/storage/sick_images/'+vacation.sick_image" target="_blank">
                        <img :src="'/storage/sick_images/'+vacation.sick_image" height="200px" width="200px" />
                    </a>
                    {{$t('Vacation.sickVacation')}}
                </div>





                <div class="form-group row">
                    <label for="name" class="col-md-12">{{$t('Vacation.reason')}}</label>
                    <div class="col-md-12">
                        <textarea class="form-control" v-model="vacation.reason" disabled></textarea>
                        <div v-if="!vacation.reason" class="">{{$t('Vacation.fieldReq')}}</div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button @click="updateVacation()" type="button" class="btn btn-primary">{{$t('Vacation.updateChanges')}}</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{$t('Vacation.close')}}</button>
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
      dateFrom: null,
      dateTo: null,
      vacation:{

      },
      columns: [
        {
          label: this.$t('Vacation.user'),
          name: "user-area",
          sort: true,
          row_text_alignment: "text-center"
        },
         {
          label: this.$t('Vacation.form'),
          name: "from-area",
          sort: true,
          row_text_alignment: "text-center"
        },
        {
          label: this.$t('Vacation.to'),
          name: "to-area",
          sort: true,
          row_text_alignment: "text-center"
        },
        {
          label: this.$t('Vacation.reason'),
          name: "reason",
          sort: true,
          row_text_alignment: "text-center"
        },
        {
          label: this.$t('Vacation.sickVacation'),
          name: "sick-area"
        },
        {
          label: this.$t('Vacation.priorty'),
          name: "priority",
          sort: true,
          row_text_alignment: "text-center"
        },        {
          label: this.$t('Vacation.status'),
          name: "status",
          sort: true,
          row_text_alignment: "text-center"
        },
        {
          label: this.$t('Vacation.action'),
          name: "action-area"
        }
      ]
    };
  },
  methods: {
    getEmployeesVacations() {
      this.$Progress.start();
      this.$store
        .dispatch("vacation/getEmployeesVacations",{ 'from': this.dateFrom,'to': this.dateTo})
        .then(response => {
          this.$Progress.finish();
        })
        .catch(error => {
          this.$Progress.fail();
        });
    },
    setVacationAction(id,action) {
      this.$Progress.start();
      this.$store
        .dispatch("vacation/setVacationAction", { 'id': id,'action' : action})
        .then(response => {
          this.$Progress.finish();
        })
        .catch(error => {
          this.$Progress.fail();
        });
    },
    editVacation(vacation){
        this.vacation = Object.assign({},vacation);
        $("#vacationModal").modal("show");
    },
    updateVacation(){
        this.$Progress.start();
            this.$store.dispatch("vacation/updateVacation", this.vacation)
          .then(response => {
            $("#vacationModal").modal("hide");
            this.$Progress.finish();
        }).catch(error => {
            this.$Progress.fail();
        })
    }

  },
  computed: {
    ...mapGetters({
        'employeesVacations':'vacation/getEmployeesVacations'
    })
  },
  mounted() {
      this.getEmployeesVacations();
  }
};
</script>
