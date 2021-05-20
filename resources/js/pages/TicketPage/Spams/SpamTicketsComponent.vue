<template>
  <v-container>
    <h3>Spam Tickets</h3>
    <div class="line"></div>
    <div class="card" style="margin-top:1rem;">
      <!-- /.card-header -->
      <div class="card-body table-responsive p-0">
        <v-tabs v-model="ticketstab">
          <v-tabs-slider></v-tabs-slider>
          <v-tab href="#ticketstable">Table view</v-tab>
<!--          <v-tab href="#ticketscards">Card view</v-tab>-->
          <v-spacer></v-spacer>
          <v-btn small class="mt-2 mr-2" style="color: #ffffff" color="#234FA3" @click.prevent="newModel" rounded :disabled="selectedRows.length !== 1">Not Spam</v-btn>
          <v-btn small class="mt-2 mr-2" style="color: #ffffff" color="#234FA3" @click.prevent="deleteSpamTickets" rounded :disabled="selectedRows.length < 1">Delete</v-btn>
        </v-tabs>
        <v-tabs-items v-model="ticketstab">
          <v-tab-item value="ticketstable">
            <table class="table table-hover table-sm">
              <thead>
                <tr>
                  <th width="2%"><input @click.prevent="selectAll" type="checkbox"></th>
                  <th width="6%">Id</th>
                  <th width="40%">Title</th>
                  <th width="22%">Client</th>
                  <th width="30%">Start Date</th>
<!--                  <th>Delete</th>-->
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="ticket in tickets"
                  :key="ticket.id"
                  style="border-radius: 25px;margin:1rem 0"
                >
                  <td>
<!--                    <input type="radio" v-model="selected" :value="ticket.id" />-->
                      <input type="checkbox" v-model="selectedRows" :value="ticket.id" />

                  </td>
                  <td>
                    <div class="small">{{ ticket.id }}</div>
                  </td>
                  <td>
                    <div class="small">{{ ticket.name }}</div>
                  </td>
                  <td>
                    <div class="small">{{ ticket.owner.name }}</div>
                  </td>
                  <td>
                    <div class="small">{{ ticket.created_at | DateWithTime }}</div>
                  </td>
<!--                  <td>-->
<!--                    <input type="checkbox" v-model="delete_selected" :value="ticket.id" />-->
<!--                  </td>-->
                </tr>
              </tbody>
              <tfoot>
                  <pagination
                    :data="mytickets"
                    :limit="parseInt(3)"
                    size="small"
                    @pagination-change-page="getResults"
                  >
                    <span slot="prev-nav">&lt;</span>
                    <span slot="next-nav">&gt;</span>
                  </pagination>
              </tfoot>
            </table>
          </v-tab-item>
<!--          <v-tab-item value="ticketscards">card</v-tab-item>-->
        </v-tabs-items>
      </div>
      <!-- /.card-body -->
    </div>

    <!-- Convert To Ticket Model -->
    <div data-app="true">
      <v-dialog v-model="dialog" persistent max-width="700px">
        <v-card>
          <v-card-title>
            <h3>Convert To Ticket</h3>
            <div style="margin-top: 3rem;margin-left: -42%;" class="line"></div>
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
                <v-col cols="12">
                   <v-autocomplete
                    v-model="form.status"
                    :items="statusArr"
                    attach
                    chips
                    label="Status"
                    clearable
                  ></v-autocomplete>
                </v-col>
                <v-col cols="12">
                   <v-autocomplete
                    v-model="form.managers_name"
                    :items="employeeArr"
                    attach
                    chips
                    label="Assigned"
                    clearable
                    multiple
                  ></v-autocomplete>
                </v-col>
                <v-col cols="12">
                  <v-text-field label="Client" v-model="form.owner" required disabled></v-text-field>
                </v-col>
                <v-col cols="12">
                   <v-autocomplete
                    v-model="form.project"
                    :items="projectArr"
                    attach
                    chips
                    label="Project"
                    clearable
                  ></v-autocomplete>
                </v-col>
                <v-col cols="12">
                   <v-text-field
                    v-model="form.estimated_time"
                    label="Estimated Time"
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" text @click="dialog = false">Close</v-btn>
            <v-btn color="#ffffff" style="background-color:#54A6AC" @click="convertToTicket(form)" text>Save</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </div>
  </v-container>
</template>

<script>
import moment from "moment";
import pagination from "laravel-vue-pagination";
import { mapGetters } from "vuex";
import { quillEditor } from "vue-quill-editor";
// require styles
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";
export default {
  name: 'SpamTickets',
  components: {
      pagination,
      quillEditor
  },
  data() {
    return {
      tickets: [],
      allTickets : false,
      selected: null,
      delete_selected: [],
      selectedRows : [],
      ticketstab: "ticketstable",
      mytickets: {},
      dialog: false,
      notSpamTicket: '',
      ticketSelected: true,
      form: new Form({
        id: "",
        name: "",
        description: null,
        due_date: "",
        project: {
          id: "",
          name: "",
        },
        project_id: "",
        manager: [],
        managers_name: [],
        owner: '',
        owner_id: [],
        status: "open",
        status_id: 1,
        estimated_time: 0
      }),
      statusArr: [],
      employeeArr: [],
      projectArr: [],
      editorOption: {
        placeholder: "Email..",
        modules: {
          toolbar: [
            ["bold", "italic", "underline", "strike"],
            ["blockquote", "code-block"],
            [
              {
                list: "ordered",
              },
              {
                list: "bullet",
              },
            ],
          ],
        },
      },
    };
  },
  methods: {
    selectAll(){
          this.allTickets = !this.allTickets;
          this.selectedRows = []
          if(this.allTickets === true){
              this.tickets.forEach(ticket => {
                  this.selectedRows.push(ticket.id)
              })
          }
          else{
              this.selectedRows = []
          }

      },
    deleteSpamTickets() {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
        }).then((result) => {
            if (result.value) {
                this.$Progress.start();
                this.$store
                    .dispatch("ticket/removeTicketSpam",  this.selectedRows )
                    .then((response) => {
                        this.$Progress.finish();
                        Toast.fire({
                            type: "success",
                            title: response.data.message,
                        });
                        this.getTickets();
                        // this.selected = null;
                        this.selectedRows = [];
                    })
                    .catch((error) => {
                        console.log(error);
                        this.$Progress.fail();
                        Toast.fire({
                            type: "error",
                            title: error.response.data.message,
                        });
                    });
            }
        });
    },
    newModel() {
      this.setModelArr()
      this.dialog = true;
    },
    getResults(page) {
      this.getTickets(page);
    },
    getTickets(page = 1) {
      this.$store
        .dispatch("ticket/getSpamTickets", page)
        .then((response) => {
          this.tickets = response.data.data.data;
          this.mytickets = response.data.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getStatus() {
      this.$store
        .dispatch("ticket/getStatus")
        .then((response) => {})
        .catch((error) => {
          console.log(error);
      });
    },
    setModelArr(){
      this.status.forEach(stat => {
        this.statusArr.push(stat.name)
      })
      this.employees.data.forEach(employee => {
        this.employeeArr.push(employee.name)
      })
      this.projects.forEach(project => {
        this.projectArr.push(project.name)
      })
    },
    getEmployees() {
      this.$store
        .dispatch("user/getEmployees", {
          roles: true,
        })
        .then(() => {})
        .catch(() => {});
    },
    getProjectsByOwner(ownerId) {
      this.form.project = [];
      if (ownerId !== null && ownerId !== "") {
        this.$store
          .dispatch("project/getProjectsByOwner", ownerId)
          .then(() => {
            this.$Progress.finish();
          })
          .catch((error) => {
            this.$Progress.fail();
          });
      }
    },
    convertToTicket(data) {
      this.$Progress.start();
      data.manager_id = data.manager.map((man) => man.id);
        console.log(data)
      this.$store
        .dispatch("ticket/createTicket", data)
        .then(() => {
          $("#Modal").modal("hide");
          this.$Progress.finish();
          this.tickets.total++;
        })
        .catch((error) => {
          this.$Progress.fail();
          console.log(error)
        });
          this.removeFromSpam();
    },
    removeFromSpam() {
      this.$store
        .dispatch('ticket/removeTicketSpam', [this.form.id])
        .then( (response) => {
          this.getTickets();
          Toast.fire({
            type: "success",
            title: response.data.message,
          });
        })
        .catch( error => {
          this.$Progress.fail();
          console.log(error)
        })
        this.dialog = false
        this.selectedRows = []
    }
  },
  watch: {
      notSpamTicketId() {
        if(this.selectedRows !== []) {
          this.ticketSelected = false;
          this.tickets.forEach(ticket => {
            if(ticket.id == this.selectedRows[0]) {
                this.notSpamTicket = ticket
                this.form.id = ticket.id
                this.form.name = ticket.name
                this.form.description = ticket.description
                this.form.owner = ticket.owner.name
                this.form.owner_id.push(ticket.owner.id)
                this.getProjectsByOwner(ticket.owner.id)

            }
          })
        }
      },
      selectedStatus() {
        if(this.form.status != null) {
          this.status.forEach(status => {
            if(status.name == this.form.status) {
              this.form.status_id = status.id
            }
          })
        }
      },
      selectedEmployee() {
        this.form.manager = []
        this.employees.data.forEach(employee => {
            if(this.form.managers_name.includes(employee.name)) {
                this.form.manager.push(employee)
            }
        })
      },
      selectedProject() {
        if(this.form.project != null) {
          this.projects.forEach(project => {
            if(project.name == this.form.project) {
              this.form.project_id = project.id
            }
          })
        }
      },
  },
  computed: {
    ...mapGetters({
      status: "ticket/activeStatus",
      user: "user/activeSingleUser",
      projects: "project/projectByOwners",
      employees: "user/getEmployees",
    }),
    notSpamTicketId() {
        return this.selectedRows;
    },
    selectedStatus() {
      return this.form.status
    },
    selectedEmployee() {
      return this.form.managers_name
    },
    selectedProject() {
      return this.form.project
    },
  },
  filters: {
    fromNow: (value) => {
      return moment(value).fromNow();
    },
    subStr: (value) => {
      return value.substring(0, 25) + "...";
    },
  },
  created() {
    this.getTickets();
    this.getStatus();
    this.getEmployees();
  },
};
</script>

<style scoped>
.line {
  background-color: #234FA3;
  width: 5.3rem;
  height: 0.2rem;
  border-radius: 5px;
}
tr th {
  font-weight: bold;
  color: #234FA3;
}
</style>
