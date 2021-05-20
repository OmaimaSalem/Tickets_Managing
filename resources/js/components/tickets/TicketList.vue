<template>
  <div class>

<!--    <div class="card">-->
<!--      <div class="card-header">-->
<!--        <h3 class="card-title">{{$t('Ticket.ticketsTable')}}</h3>-->


<!--      </div>-->


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

      <vue-bootstrap4-table
          v-if="activeTickets"
          :rows="activeTickets"
          :columns="columns"
          :config="config"
          :classes="classes"
      >
          <template slot="sort-asc-icon">
              <i class="fas fa-sort-up"></i>
          </template>
          <template slot="sort-desc-icon">
              <i class="fas fa-sort-down"></i>
          </template>
          <template slot="no-sort-icon">
              <i class="fas fa-sort"></i>
          </template>
          <template slot="name" slot-scope="props">
              <router-link
                  :to="{ name: 'ticket', params: { id: props.row.id }}"
              >{{ props.cell_value }}</router-link>
          </template>
          <template slot="created_at" slot-scope="props">
              {{ props.cell_value | DateOnly }}
          </template>
          <template slot="read" slot-scope="props">
              <i v-if="!props.cell_value" data-widget="collapse" data-toggle="tooltip" title="Not Reed" class="text-danger icon fas fa-exclamation-triangle"></i>
              <i v-else data-widget="collapse" data-toggle="tooltip" title="Reed" class="text-success icon fas fa-check"></i>
          </template>
          <template slot="reply" slot-scope="props">
              <i data-widget="collapse" data-toggle="tooltip" title="Not Replyed" v-if="!props.cell_value" class="text-danger icon fas fa-reply"></i>
              <i v-else data-widget="collapse" data-toggle="tooltip" title="Replyed" class="text-success icon fas fa-reply"></i>
          </template>
          <template slot="action-buttons" slot-scope="props">
              <a href="#" @click="editModal(props.row)" class="btn btn-primary btn-xs">
                  <i class="fas fa-edit fa-fw"></i>
              </a>
              <a href="#" @click="deleteTicket(props.row.id)" class="btn btn-danger btn-xs">
                  <i class="fas fa-trash fa-fw"></i>
              </a>
          </template>
      </vue-bootstrap4-table>




        <!-- /.card-header -->
<!--      <div class="card-body table-responsive p-0">-->

<!--      </div>-->
      <!-- /.card-body -->
<!--    </div>-->
    <!-- /.card -->
    <!-- Modal -->
<!--    <div-->
<!--      class="modal fade"-->
<!--      id="Modal"-->
<!--      tabindex="-1"-->
<!--      role="dialog"-->
<!--      aria-labelledby="newModalLabel"-->
<!--      aria-hidden="true"-->
<!--    >-->
<!--      <div class="modal-dialog" role="document">-->
<!--        <div class="modal-content">-->
<!--          <div class="modal-header">-->
<!--            <h5 v-show="!editMode" class="modal-title" id="newTicketLabel">{{$t('Ticket.createNewTicket')}}</h5>-->
<!--            <h5 v-show="editMode" class="modal-title" id="newTicketLabel">{{$t('Ticket.editTicket')}}</h5>-->
<!--            <button type="button" class="close" data-dismiss="modal" aria-label="Close">-->
<!--              <span aria-hidden="true">&times;</span>-->
<!--            </button>-->
<!--          </div>-->
<!--          <form-->
<!--            @submit.prevent="editMode ? editTicket(form) : createTicket(form)"-->
<!--            @keydown="form.onKeydown($event)"-->
<!--          >-->
<!--            <div class="modal-body">-->
<!--              <div class="form-group">-->
<!--                <label for="name">{{$t('Ticket.ticketName')}}</label>-->
<!--                <input-->
<!--                  v-model="form.name"-->
<!--                  type="text"-->
<!--                  name="name"-->
<!--                  class="form-control"-->
<!--                  :class="{ 'is-invalid': form.errors.has('name') }"-->
<!--                />-->
<!--                <has-error :form="form" field="name"></has-error>-->
<!--              </div>-->
<!--              <div class="form-group">-->
<!--                <label for="description">{{$t('Ticket.ticketDesc')}}</label>-->
<!--                <quill-editor-->
<!--                  v-model="form.description"-->
<!--                  ref="myQuillEditor"-->
<!--                  :options="editorOption"-->
<!--                ></quill-editor>-->
<!--                <has-error :form="form" field="description"></has-error>-->
<!--              </div>-->
<!--              <div class="form-group">-->
<!--                <label for="name">{{$t('Ticket.status')}}</label>-->
<!--                <multiselect-->
<!--                  v-model="form.status"-->
<!--                  :options="status"-->
<!--                  :placeholder="$t('Ticket.selectOne')"-->
<!--                  label="name"-->
<!--                  track-by="name"-->
<!--                  @input="opt => form.status_id = opt.id"-->
<!--                ></multiselect>-->

<!--                <has-error :form="form" field="status_id"></has-error>-->
<!--              </div>-->
<!--              <div class="form-group" v-if="user.type != 'client'">-->
<!--                <label for="name">{{$t('Ticket.client')}}</label>-->
<!--                <multiselect-->
<!--                  v-model="form.owner"-->
<!--                  :options="owners"-->
<!--                  @input="getProjectsByOwner(form.owner.id)"-->
<!--                  :close-on-select="true"-->
<!--                  :clear-on-select="false"-->
<!--                  :preserve-search="true"-->
<!--                  :allow-empty="false"-->
<!--                  label="name"-->
<!--                  deselect-label="Can't remove this value"-->
<!--                  :placeholder="$t('Ticket.selectOne')"-->
<!--                ></multiselect>-->
<!--                <has-error :form="form" field="client_id"></has-error>-->
<!--              </div>-->

<!--            </div>-->

<!--            <div class="modal-footer">-->
<!--              <button-->
<!--                v-show="!editMode"-->
<!--                type="submit"-->
<!--                class="btn btn-primary"-->
<!--                :disabled="form.project_id == ''"-->
<!--              >{{$t('Ticket.save')}}</button>-->
<!--              <button-->
<!--                v-show="editMode"-->
<!--                type="submit"-->
<!--                class="btn btn-success"-->
<!--                :disabled="form.project_id == ''"-->
<!--              >{{$t('Ticket.update')}}</button>-->
<!--            </div>-->
<!--          </form>-->
<!--        </div>-->
<!--      </div>-->
<!--    </div>-->

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
                      <h5
                          v-show="!editMode"
                          class="modal-title"
                          id="newTicketLabel"
                      >{{ $t("Ticket.createNew") }} {{ $t("Ticket.ticket") }}</h5>
                      <h5 v-show="editMode" class="modal-title" id="newTicketLabel">Edit Ticket</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <!-- /.card-header -->
                  <div class="modal-body">
                      <form
                          @submit.prevent="editMode ? editTicket(form) : createTicket(form)"
                          @keydown="form.onKeydown($event)"
                      >
                          <div class="form-group">
                              <label for="name">
                                  {{
                                      $t("Ticket.ticketName")
                                  }}
                              </label>
                              <input
                                  v-model="form.name"
                                  type="text"
                                  name="name"
                                  class="form-control"
                                  :class="{
                                        'is-invalid': form.errors.has('name')
                                    }"
                              />
                              <has-error :form="form" field="name"></has-error>
                          </div>
                          <div v-if="!editMode" class="form-group">
                              <label for="description">
                                  {{
                                      $t("Ticket.ticketDesc")
                                  }}
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
                                  {{
                                      $t("Ticket.status")
                                  }}
                              </label>
                              <multiselect
                                  v-model="form.status"
                                  :options="status"
                                  :placeholder="$t('Ticket.selectOne')"
                                  label="name"
                                  track-by="name"
                              ></multiselect>


                              <has-error :form="form" field="status_id"></has-error>
                          </div>
                          <div class="form-group" v-if="user.type == 'regular-user'">
                              <label for="name">
                                  {{
                                      $t("Ticket.client")
                                  }}
                              </label>
                              <multiselect
                                  v-model="form.owner"
                                  :options="owners"
                                  @input="getProjectsByOwner(form.owner.id)"
                                  :close-on-select="true"
                                  :clear-on-select="false"
                                  :preserve-search="true"
                                  :allow-empty="false"
                                  label="name"
                                  deselect-label="Can't remove this value"
                                  :placeholder="$t('Ticket.selectOne')"
                              ></multiselect>
                              <has-error :form="form" field="client_id"></has-error>
                          </div>

                          <div class="form-group">
                              <label for="name">
                                  Responsible (Manager)
                              </label>
                              <multiselect
                                  v-model="form.manager"
                                  :options="employees.data"
                                  :close-on-select="true"
                                  :clear-on-select="false"
                                  :preserve-search="true"
                                  :placeholder="$t('Ticket.selectOne')"
                                  label="name"
                              ></multiselect>
                              <has-error :form="form" field="manager_id"></has-error>
                          </div><div class="form-group">
                            <label for="estimated_time">Estimated Time</label>
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
                          <div class="form-group">
                              <label for="end_at">{{$t('Task.endAt')}}</label>
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
                      </form>
                  </div>


                  <div class="modal-footer">
                      <button
                          v-show="!editMode"
                          type="submit"
                          class="btn btn-primary"
                          @click="createTicket(form)"
                      >{{ $t("Ticket.save") }}</button>
                      <button
                          v-show="editMode"
                          type="submit"
                          class="btn btn-success"
                          @click="editTicket(form)"
                      >{{ $t("Ticket.update") }}</button>
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
            owner: "",
            project: {
                id: "",
                name: ""
            },
            status: {},
            manager: {
                id: "",
                name: ""
            },
            manager_id: "",
            project_id: "",
            owner_id: "",
            status_id: "",
            estimated_time: ""
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
      columns: [
      {
        label:  this.$t('Ticket.ticket'),
        name: "number",
        filter: {
          type: "simple",
          placeholder: this.$t('Ticket.enterTicketNum')
        },
        sort: true,
        row_text_alignment: "text-left"
      },
      {
        label: this.$t('Ticket.read'),
        name: "read",
        sort: true,
        row_text_alignment: "text-center"
      },
      {
        label: this.$t('Ticket.title'),
        name: "name",
        filter: {
          type: "simple",
          placeholder: this.$t('Ticket.enterTicketTitle')
        },
        sort: true,
        row_text_alignment: "text-left"
      },
      {
        label: this.$t('Ticket.reply'),
        name: "reply",
        sort: true,
        row_text_alignment: "text-center"
      },
      {
        label: this.$t('Ticket.status'),
        name: "status.name",
        filter: {
          type: "simple",
          placeholder: this.$t('Ticket.enterStatus')
        },
        sort: true
      },
      {
        label: this.$t('Ticket.client'),
        name: "project.owner.name",
        filter: {
          type: "simple",
          placeholder: this.$t('Ticket.enterClient')
        },
        sort: true
      },
      {
        label: this.$t('Ticket.project'),
        name: "project.name",
        filter: {
          type: "simple",
          placeholder: this.$t('Ticket.enterProject')
        },
        sort: true
      },
      {
        label: this.$t('Ticket.createdAt'),
        name: "created_at",
        sort: true
      },
      {
        label: this.$t('Ticket.action'),
        name: "action-buttons"
      }
    ],
    config: {
      server_mode: true,
      card_mode: false,
      show_refresh_button: false,
      pagination: true,
      pagination_info: true,
      per_page: 15,
      global_search: {
          placeholder: this.$t('Ticket.enterCustom'),
          init: {
              value : ""
          }
      },
    },
    classes: {
      table: {
        "table-sm": true
      }
    }
    };
  },
  components: {
    quillEditor,
    VueBootstrap4Table,
    DatePicker,
  },
  props: {
    tickets: {
      type: Object,
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
      employees: "user/getEmployees",
    }),
    ...mapGetters("project", {
      project: "activeSingleProject",
      ownerOfProject: "ownerOfProject",
    }),
    activeTickets() {
      return this.tickets.data;
    },
      newManager() {
          return this.form.manager.id
      },
      newStatus() {
          return this.form.status.id
      },
      newOwner() {
          return this.form.owner.id
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
      this.getTickets();
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
          this.getProjectsByOwner(ticket.owner.id);
          $("#Modal").modal("show");
          this.form.fill(ticket);
          this.form.owner = ticket.owner;
          this.form.manager_id = this.form.manager.id;
          this.form.owner_id = this.form.owner.id;
          this.form.status_id = this.form.status.id;
          if (this.singlePage) {
              if(this.form.project) {
                  this.form.project = this.project;
                  this.form.project_id = this.project.id;
              }
          }
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
      createTicket(data) {
          this.$Progress.start();
          this.$store
              .dispatch("ticket/createTicket", data)
              .then(response => {
                  $("#Modal").modal("hide");
                  this.$Progress.finish();
                  this.getTickets();
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
      this.$store
        .dispatch("ticket/editTicket", data)
        .then(response => {
          $("#Modal").modal("hide");
          this.$Progress.finish();
          Toast.fire({
            type: "success",
            title: response.data.message
          });
        })
        .catch(error => {
          console.log(error);
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
        confirmButtonColor: "#3085d6",
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
        .dispatch("owner/getOwners")
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
    getEmployees() {
          this.$store
              .dispatch('user/getEmployees', {
                  roles : true
              })
              .then(() => {
              })
              .catch(() => {
              })
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
        this.getEmployees();
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
        }
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
