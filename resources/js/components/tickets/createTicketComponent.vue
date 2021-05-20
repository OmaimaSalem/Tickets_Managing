<template>
  <v-row justify="center">
    <v-btn
      color="#A0466F"
      dark
      absolute
      bottom
      right
      fab
      @click.stop="activeDialog"
    >
      <v-icon>fas fa-plus</v-icon>
    </v-btn>
    <div data-app="true">
    <v-dialog v-model="dialog" persistent max-width="700px">
      <v-card>
        <v-card-title>
          <span class="headline"> {{ $t("Ticket.createNewTicket") }}</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row justify="center">
              <v-col cols="12">
                <v-text-field label="Ticket Name" v-model="form.name" required></v-text-field>
              </v-col>
              <v-col cols="12">
                <quill-editor
                  v-model="form.description"
                  ref="myQuillEditor"
                  :options="editorOption"
                ></quill-editor>
              </v-col>
              <v-col cols="6">
                <v-select
                  @change="onStatusChange($event)"
                  :items="statusArr"
                  label="Status"
                ></v-select>
              </v-col>
              <v-col cols="6">
                <v-select
                  @change="onOwnerChange($event)"
                  :items="ownersArr"
                  label="Client"
                ></v-select>
              </v-col>
              <v-col cols="6">
                <v-select
                  @change="onEmployeeChange($event)"
                  :items="employeesArr"
                  label="Responsible"
                ></v-select>
              </v-col>
              <v-col cols="6" v-if="form.owner_id">
                <v-select
                  @change="onProjectChange($event)"
                  :items="projectsArr"
                  label="Projects"
                ></v-select>
              </v-col>
              <v-col cols="6" v-else>
              </v-col>
              <v-col cols="6">
                  <v-date-picker color="#A0466F" @change="handleDate" v-model="date"></v-date-picker>
              </v-col>
              <v-col cols="6">
                  <v-time-picker color="#A0466F" format="24hr" @change="handleDate" v-model="time"></v-time-picker>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="dialog = false">{{ $t("Vacation.close") }}</v-btn>
          <v-btn color="blue darken-1" text @click="createTicket">{{ $t("Client.save") }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
  </v-row>
</template>


<script>
import {mapGetters} from "vuex";
import {quillEditor} from "vue-quill-editor";
// require styles
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";
  export default {
    data: () => ({
      dialog: false,
      menu: false,
      date: new Date().toISOString().substr(0, 10),
      time: new Date().toISOString().substr(12, 7),
      statusArr: [],
      projectsArr: [],
      ownersArr: [],
      employeesArr: [],
      form: new Form({
        id: "",
        name: "",
        description: null,
        owner_id: "",
        manager_id:"",
        status_id: '',
        project_id: "",
        due_date: ''
      }),
      editorOption: {
        placeholder: 'Ticket Description',
        modules: {
          toolbar: [
            ["bold", "italic", "underline", "strike"],
            ["blockquote", "code-block"],
            [{
                list: "ordered"
            }, {
                list: "bullet"
            }]
          ]
        }
      },
    }),
    methods: {
      handleDate() {
        const date = new Date(this.date);
        if(typeof this.time === 'string') {
          let hours = this.time.match(/^(\d+)/)[1]
          const minutes = this.time.match(/:(\d+)/)[1]
          date.setHours(hours)
          date.setMinutes(minutes)
          let formatted_date = date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate() + " " + date.getHours() + ":" + date.getMinutes()
          this.form.due_date = formatted_date;
        } else {
          this.form.due_date = date;
        }

      },
      onStatusChange(event) {
        this.status.forEach(stat => {
          if(stat.name == event) {
            return this.form.status_id = stat.id;
          }
        })
      },
      onProjectChange(event) {
        this.projects[0].forEach(project => {
          if(project.name == event) {
            return this.form.project_id = project.id;
          }
        })
      },
      onEmployeeChange(event) {
        this.employees.data.forEach(employee => {
          if(employee.name == event) {
            return this.form.manager_id = employee.id;
          }
        })
      },
      onOwnerChange(event) {
        this.owners.forEach(owner => {
          if(owner.name == event) {
            return this.form.owner_id = owner.id;
          }
        })
      },
      activeDialog() {
        this.dialog = true;
        this.setStatusArr(this.status);
        this.setProjectsArr(this.projects[0]);
        this.setOwnersArr(this.owners);
        this.setEmployeesArr(this.employees.data);
      },
      createTicket() {
        this.$Progress.start();
          this.$store
            .dispatch("ticket/createTicket", this.form)
            .then(response => {
                this.dialog = false;
                this.$Progress.finish();
                this.$emit('TicketCreated');
                Toast.fire({
                    type: "success",
                    title: response.data.message
                });
            })
            .catch(error => {
                this.$Progress.fail();
                if (error.response) {
                    this.form.errors.errors = error.response.data.errors;
                }
            });
      },
      getEmployees() {
          this.$store
            .dispatch('user/getEmployees')
            .then(() => {
            })
            .catch(() => {
            })
      },
      getProjects() {
        let queryParams = {
              filters: [],
              global_search: "",
              per_page: 15,
              page: 1
            }
        this.$store
          .dispatch("project/getProjects", queryParams)
          .then(() => {
          })
          .catch(() => {
          });
      },
      setStatusArr(status) {
        status.forEach(stat => {
          this.statusArr.push(stat.name);
        })
      },
      setProjectsArr(projects) {
        projects.forEach(project => {
          this.projectsArr.push(project.name);
        })
      },
      setEmployeesArr(employees) {
        employees.forEach(employee => {
          this.employeesArr.push(employee.name);
        })
      },
      setOwnersArr(owners) {
        owners.forEach(owner => {
          this.ownersArr.push(owner.name);
        })
      }
    },
    components: {
        quillEditor,
    },
    computed: {
      ...mapGetters({
          owners: "owner/activeOwners",
          projects: "project/projectByOwners",
          status: "ticket/activeStatus",
          user: "user/activeSingleUser",
          employees: "user/getEmployees",
      })
    },
    mounted() {
      this.getProjects();
      this.getEmployees();
    }
  }
</script>
