<template>
  <v-main data-app>
    <v-container>
      <v-row class="headings">
        <v-col cols="4" class="title">
          <h3>{{ $t('Attends.AttendeesTracking')}}</h3>
          <span class="underline"></span>
        </v-col>

        <v-col cols="7" class="form-group">
          <div class="input-group">
            <div class="input-group-icon">
              <i class="fas fa-search"></i>
            </div>

            <input
              placeholder="Search"
              v-model="args.search"
              @keyup.enter="employeeAttendsReport"
              type="text"
              class="form-control border-left-less border-radius"
            />
    <v-dialog
      v-model="dialog"
      persistent
      max-width="600px"
    >
      <template v-slot:activator="{ on, attrs }">
   <v-btn
            v-bind="attrs"
            v-on="on"
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
                >fas fa-envelope-open-text</v-icon
              >
              {{ $t('Attends.SendTo')}}
            </v-btn>
      </template>
      <v-card>
          <v-card-actions style="background-color: #f8f8f8">
              <v-col cols="8" >
                  <h4 style="font-weight: bold" class="headline"> {{ $t('Attends.Sendmail')}}</h4>
              </v-col>
              <v-col cols="4">
                  <v-btn style="background-color:#717171"  color="#ffffff" dark small @click="dialog = false">{{ $t('Attends.Close')}}</v-btn>
                  <v-btn  color="#234FA3"  dark small @click="send_report">{{ $t('Attends.Send')}}</v-btn>
              </v-col>
        </v-card-actions>
        <v-card-text>
          <v-container>
            <v-row>

            <v-row cols="12">

              <v-col cols="8" style="margin-top: -2rem">
                <v-text-field
                  label="To*"
                  required
                  v-model="form.to"
                ></v-text-field>
                <has-error :form="form" field="to"></has-error>
              </v-col>

              <v-col cols="2">
                  <span>CC</span>
                  <input style="margin-left: .2rem" type="checkbox" @click="cc = !cc" />
              </v-col>
              <v-col cols="2" >
                  <span>BCC</span>
                  <input style="margin-left: .2rem" type="checkbox" @click="bcc = !bcc" />
              </v-col>


            <v-col cols="12"  v-if="cc"  style="margin-top: -2rem">
                <v-text-field
                label="Cc*"
                v-model="form.cc"
                required
                ></v-text-field>
                <has-error :form="form" field="cc"></has-error>

            </v-col>

            <v-col  cols="12" v-if="bcc"  style="margin-top: -2rem">
                <v-text-field
                    label="Bcc*"
                    v-model="form.bcc"
                    required
                ></v-text-field>
                <has-error :form="form" field="bcc"></has-error>
            </v-col>

              <v-col cols="12" style="margin-top: -2rem" >
                <v-text-field
                  label="Subject*"
                  v-model="form.subject"

                  required
                ></v-text-field>
              </v-col>


            <v-col cols="12"  style="margin-top: -2rem">
                <v-text-field
                label="Attachment*"
                v-model="form.file"
                readonly
                ></v-text-field>

                <quill-editor
                    v-model="form.message"
                    ref="myQuillEditor"
                    :options="editorOption"
                ></quill-editor>
            </v-col>
            </v-row>

            </v-row>
          </v-container>
        </v-card-text>

      </v-card>
    </v-dialog>

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


                <v-col class="row" cols="12">


                    <v-col cols="3">
                        <!-- date picker -->
                            <multiselect
                            v-model="args.user"
                            :options="users"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="false"
                            :preserve-search="true"
                            placeholder="Employee"
                            label="name"
                            track-by="id"
                            :preselect-first="true"
                            @input="employeeAttendsReport"
                            @keyup.enter="employeeAttendsReport"
                            >
                            </multiselect>
                    </v-col><!-- end of small-column -->




                    <v-col cols="3">
                        <date-picker
                        v-model="args.from"
                        lang="en"
                        type="date"
                        format="DD-MM-YYYY"
                        :minute-step="15"
                        value-type="format"
                        input-class="form-control border-radius"
                        @keyup.enter="employeeAttendsReport"
                        @change="employeeAttendsReport"
                        placeholder="From"
                        ></date-picker>
                    </v-col><!-- end of small-column -->





                    <v-col cols="3">
                        <date-picker
                        v-model="args.to"
                        lang="en"
                        type="date"
                        format="DD-MM-YYYY"
                        :minute-step="15"
                        value-type="format"
                        input-class="form-control border-radius"
                        @keyup.enter="employeeAttendsReport"
                        @change="employeeAttendsReport"
                        placeholder="To"
                        ></date-picker>
                    </v-col><!-- end of small-column -->



                <v-btn
                    @click.prevent="employeeAttendsReport"
                    color="#18519e"
                    style="margin-top: 12px; border-radius: 10px; color: #fff"
                >
                    {{ $t('report.ViewReport') }}
                </v-btn>



                </v-col><!-- end of column -->







      </v-row>


      <!-- headings  -->
      <v-simple-table v-if="attends" ref="table"  style="margin-top: -1rem;">
        <template v-slot:default>
          <thead>
            <tr>
              <th class="text-left">{{ $t('report.Employeename')}}</th>
              <th class="text-left">{{ $t('Attends.date')}}</th>
              <th class="text-left">{{ $t('Attends.Entry')}}</th>
              <th class="text-left">{{ $t('Attends.Exit')}}</th>

              <th class="text-left">{{ $t('Attends.Entrylate')}}</th>
              <th class="text-left">{{ $t('Attends.Entryearly')}}</th>


              <th class="text-left">{{ $t('Attends.Exitlate')}}</th>
              <th class="text-left">{{ $t('Attends.Exitearly')}}</th>

              <th class="text-left">{{ $t('Attends.Break')}}</th>
              <th class="text-left">{{ $t('Attends.worktime')}}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="attend in attends.data" :key="attend.id">
              <td>{{ attend.name }}</td>
              <td>{{ attend.date }}</td>
              <td>{{ attend.entry}}</td>
              <td>{{ attend.exit }}</td>
              <td>{{ attend.entry_late}}</td>
              <td>{{ attend.entry_early}}</td>
              <td>{{ attend.exit_late}}</td>
              <td>{{ attend.exit_early}}</td>
              <td>{{ attend.break }}</td>
              <td>{{ attend.work_time }}</td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>



      <v-row class="mt-2">
        <v-col cols="4" offset="8" class="float-right">
          <v-col cols="8" class="float-right">
            <pagination
              :data="attends"
              :limit="parseInt(10)"
              size="small"
              @pagination-change-page="employeeAttendsReportPage"
            >
              <span slot="prev-nav">&lt; Previous</span>
              <span slot="next-nav">Next &gt;</span>
            </pagination>
          </v-col>


          <v-col v-if="attends.last_page > 1" cols="4" class="float-right pagination-input">
            <input
              class="form-control"
              type="text"
              v-model="args.page"
              @input="employeeAttendsReport"
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
import { quillEditor } from "vue-quill-editor";
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";

export default {
  data() {
    return {
      dialog: false,
      cc:false,
      bcc:false,
      args: {
        search: null,
        page: 1,
        user:null,
        from:null,
        to:null,
        send:null,
      },
      form:new Form({
          to:null,
          cc:null,
          bcc:null,
          subject:null,
          file:'Attendace',
          message: null,
          attachement: null,
      }),
        editorOption: {
          modules: {
            toolbar: [
              ["bold", "italic", "underline", "strike"],
              ["blockquote", "code-block"],
              [{ list: "ordered" }, { list: "bullet" }]
            ]
          }
        },

    };
  },
  methods: {
    async takeScreenshot(){
        let table = this.$refs.table.$el;
        const options = {
            type: 'dataURL'
        }

        this.form.attachement = (await this.$html2canvas(table,options));
     },
    send_report(){


     this.form.send = true;

     this.$Progress.start();
      this.$store
        .dispatch("attend/sendEmail", this.form)
        .then((response) => {
          this.$Progress.finish();
          this.dialog = false;
          this.form.clear();
          this.form.reset();
        })
        .catch((error) => {
            this.$Progress.fail();
          if (error.response) {
            this.form.errors.errors = error.response.data.errors;
          }
        });



    },
    employeeAttendsReportPage($page) {
      this.args.page = $page;
      this.employeeAttendsReport();
    },
    employeeAttendsReport() {
      this.$Progress.start();
      this.$store
        .dispatch("attend/setEmployeeAttendsReport", this.args)
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


    export_to_excel() {
      window.open(
        this.$base_url +
          "/v-api/attends/get-employees-attends-report?time=true&params="+encodeURI(JSON.stringify(this.args))+"&export_excel=true",
        "_blank"
      );
    },


  },
  mounted() {
    this.employeeAttendsReport();
    this.getUsers();
  },

  computed: {
    ...mapGetters({
      attends: "attend/employeeAttendsReport",
      users: "regularUser/activeRegularUser",
    }),
  },
  watch: {
      dialog(value){
          if(value){
              this.takeScreenshot();
          }
      }
  },

  components: {
    DatePicker,
    quillEditor
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
.theme--light.v-btn.bcc-btn:not(.v-btn--flat):not(.v-btn--text):not(.v-btn--outlined),
.theme--light.v-btn.cc-btn:not(.v-btn--flat):not(.v-btn--text):not(.v-btn--outlined)
{
    background-color:transparent !important;
    box-shadow: none;
}
</style>
