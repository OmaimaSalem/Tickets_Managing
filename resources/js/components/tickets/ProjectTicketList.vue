<template>
  <div class>
      <div class="card" style="border-radius: 15px">
          <div class="card-header">
              <h3 class="card-title">{{$t('Task.tasksTable')}}</h3>

              <div class="card-tools">
                  <v-btn
                      color="#234FA3"
                      dark
                      absolute
                      top
                      right
                      fab
                      @click="newModal"
                      style="float:right;z-index: 100;"
                  >
                      <v-icon>fas fa-plus</v-icon>
                  </v-btn>
              </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body table-responsive p-0">
              <table class="table table-hover table-sm">
                  <thead>
                  <tr>
                      <th width="20%" class="text-left"></th>
                      <th class="text-left">{{$t('Ticket.Number')}}</th>
                      <th width="25%" class="text-left">{{$t('Ticket.title')}}</th>
                      <th class="text-left">Status</th>
                      <th width="15%" class="text-left">{{$t('Ticket.client')}}</th>
                      <th width="15%" class="text-left">{{$t('Ticket.project')}}</th>
                      <th width="22%" class="text-left">{{$t('Ticket.lastUpdate')}}</th>
                      <th width="3%" class="text-left" data-placement="bottom" title="estimated time to complete">ETC</th>
                      <th width="10%" class="text-left">{{$t('Ticket.action')}}</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr v-for="ticket in tickets" :key="ticket.id">
                      <td>
                          <div class="fixedLine">
                              <i
                                  title="Not Replyed"
                                  v-if="!ticket.reply"
                                  class="text-danger icon fas fa-reply"
                              ></i>
                              <i
                                  v-else
                                  data-widget="collapse"
                                  data-toggle="tooltip"
                                  title="Replyed"
                                  class="text-success icon fas fa-reply"
                              ></i>
                              <i
                                  v-if="!ticket.read"
                                  data-widget="collapsec"
                                  data-toggle="tooltip"
                                  title="Not Reed"
                                  class="text-danger icon pl-1 fas fa-exclamation-triangle"
                              ></i>
                              <i
                                  v-else
                                  data-widget="collapse"
                                  data-toggle="tooltip"
                                  title="Reed"
                                  class="text-success icon pl-1 fas fa-check"
                              ></i>
                          </div>
                      </td>
                      <td>
                          <router-link
                              :to="{
                                name: 'ticket',
                                params: { id: ticket.id },
                              }"
                          >
                              {{ ticket.number }}
                          </router-link>
                      </td>
                      <td>
                          <router-link
                              data-placement="top" :title="ticket.name"
                              :to="{
                                name: 'ticket',
                                params: { id: ticket.id },
                              }"
                          >
                              {{ ticket.name | subStrname}}
                          </router-link>
                      </td>
                      <td>{{ ticket.status.name | returnDoing}}</td>
                      <td v-if="ticket.owner">
                          <router-link
                              data-placement="top" :title="ticket.owner[0].name"
                              v-if="ticket.owner[0]"
                              :to="'/admin/profile/' + ticket.owner[0].id"
                          >{{ ticket.owner[0].name | subStrProjectTable }}

                          </router-link
                          >
                      </td>
                      <td v-else>----</td>
                      <td v-if="ticket.project" data-placement="top" :title="ticket.project.name">
                          {{ ticket.project.name | subStrProjectTable }}
                      </td>
                      <td v-else>----</td>
                      <td>{{ ticket.updated_at | ticketUpdatedAt }}</td>
                      <td><span :title="`Due Date: ${$options.filters.ticketDueDate(ticket.due_date) }`">{{ ticket.estimated_time}}</span></td>
                      <td>
                          <i
                              @click="editModal(ticket)"
                              data-widget="collapse"
                              data-toggle="tooltip"
                              title="Edit"
                              class="text-success icon fas fa-edit fa-xs"
                              style="
                                :hover {
                                  color: #ffffff;
                                }
                              "
                          ></i>
                          <i
                              @click="deleteTicket(ticket.id)"
                              data-widget="collapse"
                              data-toggle="tooltip"
                              title="Edit"
                              class="text-danger icon pl-1 fas fa-trash fa-xs"
                          ></i>
                      </td>
                  </tr>
                  </tbody>
              </table>
          </div>
          <!-- /.card-body -->
      </div>
      <pagination
          :data="tickets"
          :limit="parseInt(2)"
          size="small"
          @pagination-change-page="getResults"
      >
          <span slot="prev-nav">&lt; Previous</span>
          <span slot="next-nav">Next &gt;</span>
      </pagination>

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
            <h5 v-show="!editMode" class="modal-title" id="newTicketLabel">
              {{ $t("Ticket.createNew") }} {{ $t("Ticket.ticket") }}
            </h5>
            <h5 v-show="editMode" class="modal-title" id="newTicketLabel">
              Edit Ticket
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form
              @submit.prevent="editMode ? editTicket(form) : createTicket(form)"
              @keydown="form.onKeydown($event)"
            >
              <div class="form-group">
                <label for="name">
                  {{ $t("Ticket.ticketName") }}
                </label>
                <input
                  v-model="form.name"
                  type="text"
                  name="name"
                  class="form-control"
                  :class="{
                    'is-invalid': form.errors.has('name'),
                  }"
                />
                <has-error :form="form" field="name"></has-error>
              </div>
              <div class="form-group">
                <label for="description">
                  {{ $t("Ticket.ticketDesc") }}
                </label>
                <quill-editor
                  v-model="form.description"
                  ref="myQuillEditor"
                  :options="editorOption"
                ></quill-editor>
                <has-error :form="form" field="description"></has-error>
              </div>
              <div class="form-group">
                <label for="name">
                  {{ $t("Ticket.status") }}
                </label>
                <multiselect
                  v-model="form.status"
                  :options="status"
                  label="name"
                  track-by="name"
                ></multiselect>

                <has-error :form="form" field="status_id"></has-error>
              </div>
              <div class="form-group" v-if="user.type == 'regular-user'">
                <label for="name">
                  {{ $t("Ticket.client") }}
                </label>
                <multiselect
                  v-model="form.owner"
                  :options="project.owners"
                  @input="getProjectsByOwner(form.owner.id)"
                  :close-on-select="true"
                  :clear-on-select="false"
                  :preserve-search="true"
                  :allow-empty="false"
                  label="name"
                  track-by="id"
                  :multiple="true"
                  deselect-label="Can't remove this value"
                  :placeholder="$t('Ticket.selectOne')"
                ></multiselect>
                <has-error :form="form" field="owner_id"></has-error>
              </div>
              <div class="form-group">
                <label for="name">{{ $t("Ticket.responsible(manager)") }}</label>
                <multiselect
                  v-model="form.manager"
                  :multiple="true"
                  :options="project.project_assign"
                  :searchable="true"
                  :close-on-select="true"
                  :clear-on-select="false"
                  :preserve-search="true"
                  track-by="id"
                  :preselect-first="true"
                  :placeholder="$t('Ticket.selectOne')"
                  label="name"
                ></multiselect>
                <has-error :form="form" field="manager_id"></has-error>
              </div>
              <div class="form-group">
                <label for="end_at">{{ $t("Ticket.endAt") }}</label>
                <date-picker
                  v-model="form.due_date"
                  lang="en"
                  type="datetime"
                  format="DD-MM-YYYY HH:mm"
                  :minute-step="1"
                  input-class="form-control"
                  value-type="format"
                ></date-picker>
                <has-error :form="form" field="due_date"></has-error>
              </div>
                <div class="form-group">
                    <label for="estimated_time">{{ $t("Ticket.estimatedTime") }}</label>
                    <input
                        v-model="form.estimated_time"
                        type="number"
                        min="0"
                        name="estimated_time"
                        class="form-control"
                        :class="{
                            'is-invalid': form.errors.has(
                                'estimated_time'
                            )
                        }"
                    />
                    <has-error
                        :form="form"
                        field="estimated_time"
                    ></has-error>
                </div>
            </form>
          </div>

          <div class="modal-footer">
            <button
              v-show="!editMode"
              type="submit"
              class="btn btn-primary"
              @click="createTicket(form)"
            >
              {{ $t("Ticket.save") }}
            </button>
            <button
              v-show="editMode"
              type="submit"
              class="btn btn-success"
              @click="editTicket(form)"
            >
              {{ $t("Ticket.update") }}
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import { quillEditor } from "vue-quill-editor";
import VueBootstrap4Table from "vue-bootstrap4-table";
import { mapGetters, mapState } from "vuex";
import DatePicker from "vue2-datepicker";
import pagination from "laravel-vue-pagination";
import moment from "moment";
// require styles
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";

export default {
  data() {
    return {
      editMode: false,
      isDisabled: false,
      ticketstab: 'ticketstable',
        form: new Form({
          id: "",
          name: "",
          description: null,
          due_date: "",
          owner: [],
          estimated_time: 0,
            status: {
                id: 1,
                name: "open"
            },
          manager: [],
          manager_id: "",
          project_id: "",
          owner_id: "",
          status_id: "",
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
  components: {
    quillEditor,
    pagination,
    VueBootstrap4Table,
    DatePicker,
  },
  props: {
    tickets: {
      type: Object,
      required: true
    },
    projectId: {
      type: Number,
      required: true
    },
    singlePage: false
  },
  computed: {
    ...mapGetters({
      owners: "owner/activeOwners",
      projects: "project/projectByOwners",
      status: "ticket/activeStatus",
      user: "user/activeSingleUser",
    }),
    ...mapGetters("project", {
      project: "activeSingleProject",
      ownerOfProject: "ownerOfProject",
    }),
    activeTickets() {
      return this.tickets.data;
    },
      newManager() {
        return this.form.manager ? this.form.manager.id : null;
      },
      resultTickets() {
          return this.tickets.data;
      },
      today_date() {
          return moment().format('ddd MMM D YYYY');
      },
  },
  methods: {
    onChangeQuery(queryParams) {
      this.queryParams = queryParams;
      this.getTicketsByProjectId();
    },
    getResults(page) {
      this.queryParams.page = page;
      this.getTicketsByProjectId();
    },
    getTicketsByProjectId(page = 1) {
      this.$Progress.start();
      this.$store
        .dispatch("ticket/getTicketsByProjectId", {
          id: this.projectId,
          page: page
        })
        .then(response => {
          this.showTickets = true;
          this.$Progress.finish();
        })
        .catch(error => {
          this.$Progress.fail();
        });
    },
      newModal() {
          this.editMode = false;
          this.form.reset();
          this.form.clear();
          if (this.singlePage) {
              if(this.form.project) {
                  this.form.project = this.project;
                  this.form.project_id = this.project.id;
              }
          }
          if (this.user.type == "client") {
              this.form.owner = { name: this.user.name, id: this.user.id };
              this.getProjectsByOwner(this.form.owner.id);
          }
          $("#Modal").modal("show");
      },
      editModal(ticket) {
          this.editMode = true;
          this.form.reset();
          this.form.clear();
          this.getTicketDescription(ticket.id)
          this.getProjectsByOwner(ticket.owner.id);
          this.form.fill(ticket);
          this.form.status_id = this.form.status.id;
          $("#Modal").modal("show");
      },
    getProjectsByOwner(ownerId) {
      this.form.project = [];
      if (ownerId !== null && ownerId !== "") {
        this.$store
          .dispatch("project/getProjectsByOwner", ownerId)
          .then(() => {
            this.$Progress.finish();
          })
          .catch(error => {
            this.$Progress.fail();
          });
      }
    },
    getTicketDescription(id){
      this.$store
        .dispatch("ticket/getTicketDescription", id)
        .then((response) => {
          console.log(response)
          this.form.description = response.data.data.description
          this.$Progress.finish();
        })
        .catch(error => {
          this.$Progress.fail();
        });
    },
      createTicket(data) {
      this.form.manager_id = this.form.manager.map((man) => man.id);
      this.form.owner_id = this.form.owner.map((own) => own.id);
      this.form.status_id = this.form.status.id;
      this.form.project_id = this.projectId;
          this.$Progress.start();
          this.$store
              .dispatch("ticket/createTicket", data)
              .then(response => {
                  $("#Modal").modal("hide");
                  this.$Progress.finish();
                  this.getTicketsByProjectId();
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

    editTicket(data) {
      this.$Progress.start();
      this.form.manager_id = this.form.manager.map((man) => man.id);
      this.form.owner_id = this.form.owner.map((own) => own.id);
      this.form.project_id = this.projectId;
      this.$store
        .dispatch("ticket/editTicket", this.form)
        .then((response) => {
          $("#Modal").modal("hide");
          this.getTicketsByProjectId();
          this.$Progress.finish();
          Toast.fire({
            type: "success",
            title: response.data.message,
          });
        })
        .catch((error) => {
          this.$Progress.fail();
          if (error.response) {
            this.form.errors.errors = error.response.data.errors;
          }
        });
    },
    deleteTicket(id) {
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#234FA3",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
      }).then(result => {
        if (result.value) {
          this.$Progress.start();
          this.$store
            .dispatch("ticket/deleteTicket", id)
            .then(response => {
              this.$Progress.finish();
              Toast.fire({
                type: "success",
                title: response.data.message
              });
            })
            .catch(error => {
              console.log(error);
              this.$Progress.fail();
              Toast.fire({
                type: "error",
                title: error.response.data.message
              });
            });
        }
      });
    },
    getOwners() {
      this.$store
        .dispatch("owner/getOwners", {
                  roles : false,
                  dropdown:true
              })
        .then(() => {})
        .catch(error => {
          console.log(error);
        });
    },
    getStatus() {
      this.$store
        .dispatch("ticket/getStatus")
        .then(response => {})
        .catch(error => {
          console.log(error);
        });
    },
   },
    watch: {
        newManager(newValue) {
            this.form.manager_id = newValue;
        },
        newStatus(newValue) {
            this.form.status_id = newValue;
        },
        newOwner(newValue) {
            this.form.owner_id = newValue;
        },

    },
    created() {
        this.getOwners();
    },
    mounted() {
        this.getOwners();
        this.getStatus();
    },

    filters: {
        ticketDueDate: (value) => {
            return moment(value).fromNow()
        },
        subStr: (value) => {
            return string.substring(0, 25) + '...';
        },
        subStrname: value => {
          if (value.length > 13) {
              return value.substring(0, 13) + "...";
          }
          return value;
        },
        returnDoing : value => {
          if(value == 'in-progress'){
              return 'doing';
          }else {
              return value;
          }
        },
        subStrProjectTable : value => {
          if (value.length > 8) {
              return value.substring(0, 8) + "..";
          }
          return value;
        },
        ticketUpdatedAt: (value) => {
          return moment(value).format('MMMM Do YYYY, H:mm')
        },
    },
  directives: {
    trim: {
      inserted: function(el, maxWords = 4) {
        var str = el.innerHTML;
        var resultString =
          str
            .split(" ")
            .slice(0, maxWords.value)
            .join(" ") + "...";
        el.innerHTML = resultString;
      }
    }
  }
};
</script>

<style scoped>
.col {
    padding-top: 0px;
    padding-bottom: 0px;
}
.stat {
    color: #ffffff;
    font-weight: bold;
    padding: 0.7rem 0.7rem 0px 0px;
    text-align: center;
    height:2.8rem;
}
.stat-name {
    position: relative;
    margin-top: -1rem;
}
#open {
    background-color: #A0466F;
}
#done {
    background-color: #67A046;
}
#in-progress {
    background-color: #3490dc;
}
#pending {
    background-color: #EEA24C;
}

</style>
