<template>
<div>
                <a href="javascript:void(0)" @click="CreateYearHoliday()">
                    <i class="fas fa-plus fa-fw"></i> {{$t('Vacation.create')}}
                </a>

    <vue-bootstrap4-table
        :rows="holidays"
        :columns="columns"
    >

        <template slot="action-buttons" slot-scope="props">

                <button
                  type="button"
                  class="btn btn-primary dropdown-toggle"
                  data-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false"
                >{{$t('Vacation.action')}}</button>
                <span class="sr-only">{{$t('Vacation.toogleDrop')}}</span>

                <div class="dropdown-menu" role="menu">
                    <a href="javascript:void(0)" @click="editYearHoliday(props.row)" class="dropdown-item">
                    <i class="fas fa-edit fa-fw"></i> {{$t('Vacation.edit')}}
                    </a>

                    <a href="javascript:void(0)" @click="deleteYearHoliday(props.row.id)" class="dropdown-item">
                    <i class="fas fa-trash fa-fw"></i> {{$t('Vacation.delete')}}
                    </a>
                </div>

        </template>

    </vue-bootstrap4-table>





    <!-- Modal -->
    <div
      class="modal fade"
      id="Modal"
      tabindex="-1"
      role="dialog"
      aria-labelledby="newModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form
            @submit.prevent="editMode ? editHoliday(holidayform) : createHoliday(holidayform)"
          >
            <div class="modal-body">

              <div class="form-group">
                <label for="name">{{$t('Vacation.holiday')}}</label>
                 <input
                  v-model="holidayform.name"
                  type="text"
                  name="name"
                  class="form-control"
                />
              </div>


              <div class="row">

                <div class="form-group col-md-6">
                    <label for="name">{{$t('Vacation.form')}}</label>
                    <br />
                    <date-picker
                        v-model="holidayform.from"
                        lang="en"
                        type="date"
                        format="DD-MM-YYYY"
                        input-class="form-control"
                        value-type="format"
                    >
                    </date-picker>
                </div>



                <div class="form-group col-md-6">
                    <label for="name">{{$t('Vacation.zu')}}</label>
                    <br />
                    <date-picker
                        v-model="holidayform.to"
                        lang="en"
                        type="date"
                        format="DD-MM-YYYY"
                        input-class="form-control"
                        value-type="format"
                    >
                    </date-picker>
                </div>

              </div>




            </div>

            <div class="modal-footer">
              <button
                v-if="newMode"
                type="submit"
                class="btn btn-primary"
                :disabled="!newMode"
              >{{$t('Vacation.save')}}</button>
              <button
                v-if="editMode"
                type="submit"
                class="btn btn-success"
                :disabled="!editMode"
              >{{$t('Vacation.update')}}</button>
            </div>
          </form>
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
        editMode: false,
        newMode: false,
        year:null,
        holidayform:{},
        rows: [],
        columns: [
        {
          label: "Name",
          name: "name",
          sort: true,
          row_text_alignment: "text-center"
        },
        {
          label: this.$t('Vacation.form'),
          name: "from",
          sort: true,
          row_text_alignment: "text-center"
        },
        {
          label: this.$t('Vacation.to'),
          name: "to",
          sort: true,
          row_text_alignment: "text-center"
        },
        {
          label: this.$t('Vacation.actions'),
          name: "action-buttons"
        }

      ],
    }
   },
   methods: {
       getYearHolidays(){
            this.year = new Date().getFullYear().toString();
            this.$Progress.start();
            this.$store.dispatch("vacation/getYearHolidays", { 'year' : this.year})
            .then(response => {
                this.$Progress.finish();
            }).catch(error => {
                this.$Progress.fail();
            })
       },
        editYearHoliday(holiday){
            this.editMode = true;
            this.newMode  = false;
           $("#Modal").modal("show");
           this.holidayform = holiday;
       },
        CreateYearHoliday(){
            this.holidayform = {};
            this.editMode = false;
            this.newMode  = true;
            $("#Modal").modal("show");
       },
       createHoliday(){
           this.holidayform.year = this.year;
            this.$Progress.start();
            this.$store.dispatch("vacation/createYearHoliday", { 'holiday' : this.holidayform})
            .then(response => {
                this.$Progress.finish();
                $("#Modal").modal("hide");
            }).catch(error => {
                this.$Progress.fail();
            })

       },
       editHoliday(){
            this.$Progress.start();
            this.$store.dispatch("vacation/editYearHoliday", { 'holiday' : this.holidayform})
            .then(response => {
                this.$Progress.finish();
                $("#Modal").modal("hide");

            }).catch(error => {
                this.$Progress.fail();
            })
       },
       deleteYearHoliday(id){
            this.$Progress.start();
            this.$store.dispatch("vacation/deleteYearHoliday", { 'id' : id})
            .then(response => {
                this.$Progress.finish();
            }).catch(error => {
                this.$Progress.fail();
            })
       }
   },
    computed: {
        ...mapGetters({
            holidays: "vacation/getYearHolidays",
        }),
    },
   mounted() {
       this.getYearHolidays();
   }
}
</script>
