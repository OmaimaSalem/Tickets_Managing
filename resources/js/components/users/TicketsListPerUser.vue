<template>
  <div class="active tab-pane">
    <!-- <div class="card-body table-responsive p-0" style="height:330px;overflow-y:auto;">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>ID</th>
            <th>{{$t('User.ticket')}}</th>
            <th>{{$t('User.createdAt')}}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="ticket in tickets.data" :key="ticket.id">
            <td>{{ ticket.id }}</td>
            <td>
              <router-link :to="'/admin/ticket/' + ticket.id">{{ ticket.name }}</router-link>
            </td>
            <td>{{ ticket.created_at | DateWithTime }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <pagination
      class="mt-1"
      align="center"
      size="small"
      :show-disabled="true"
      :data="tickets"
      :limit="3"
      @pagination-change-page="getTicketsPerClient"
    ></pagination> -->


    <v-simple-table dense fixed-header>
          <template v-slot:default >
            <thead>
              <tr>
                <th width="5%" class="text-left"> </th>
                <th width="5%" class="text-left">{{ $t('Ticket.Number') }}</th>
                <th width="20%" class="text-left">{{ $t('Task.title') }}</th>
                <th class="text-left">{{ $t('Ticket.status') }}</th>
                <th width="15%" class="text-left">{{ $t('Ticket.client') }}</th>
                <th width="12%" class="text-left">{{ $t('Ticket.project') }}</th>
                <th width="22%" class="text-left">{{ $t('Ticket.lastUpdate') }}</th>
                <th width="3%" class="text-left" data-placement="bottom" title="estimated time to complete">ETC</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="ticket in tickets.data" :key="ticket.id">
                <td>
                  <div class="fixedLine">
                    <i
                      title="Not Replyed"
                      v-if="!ticket.reply"
                      style="font-size: .8rem"
                      class="text-danger icon fas fa-reply"
                    ></i>
                    <i
                      v-else
                      data-widget="collapse"
                      data-toggle="tooltip"
                      title="Replyed"
                      style="font-size: .8rem"
                      class="text-success icon fas fa-reply"
                    ></i>
                    <i
                      v-if="!ticket.read"
                      data-widget="collapsec"
                      data-toggle="tooltip"
                      title="Not Reed"
                      style="font-size: .8rem"
                      class="text-danger icon pl-1 fas fa-exclamation-triangle"
                    ></i>
                    <i
                      v-else
                      data-widget="collapse"
                      data-toggle="tooltip"
                      style="font-size: .8rem"
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
                      data-placement="top" :title="ticket.name "
                    :to="{
                      name: 'ticket',
                      params: { id: ticket.id },
                    }"
                  >
                    {{ ticket.name }}
                  </router-link>
                </td>
                <td>{{ ticket.status.name}}</td>
                <td v-if="ticket.owner">
                  <router-link
                      data-placement="top" :title="ticket.owner[0].name"
                    v-if="ticket.owner[0]"
                    :to="'/admin/profile/' + ticket.owner[0].id"
                    >{{ ticket.owner[0].name }}

                  </router-link
                  >
                </td>
                <td v-else>----</td>
                <td v-if="ticket.project" data-placement="top" :title="ticket.project.name">
                  {{ ticket.project.name }}
                </td>
                <td v-else>----</td>
                <td>{{ ticket.updated_at }}</td>
                <td>
                  <span :title="`Due Date: ${ticket.due_date}`">{{ ticket.estimated_time}}</span>
                </td>
              </tr>
            </tbody>
          </template>
        </v-simple-table>
        <pagination
          :data="tickets"
          :limit="parseInt(2)"
          size="small"
          @pagination-change-page="getTicketsPerClient"
        >
          <span slot="prev-nav">&lt; Previous</span>
          <span slot="next-nav">Next &gt;</span>
        </pagination>



                  <v-btn color="#2F5298" dark fab large bottom right fixed @click="CreateTicketModal">
                      <v-icon>fas fa-plus</v-icon>
                  </v-btn>


      <!-- modal -->

          <div
      class="modal fade"
      id="newTicketModel"
      tabindex="-1"
      role="dialog"
      aria-labelledby="newModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="newTicketLabel">
              {{ $t("Ticket.createNew") }} {{ $t("Ticket.ticket") }}
            </h5>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            />
          </div>
          <!-- /.card-header -->
          <div class="modal-body">
            <form
              @submit.prevent="createTicket(form)"
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
<!--                  We Get all project at entering the name to light te wight of the request-->
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
                  track-by="id"
                ></multiselect>

                <has-error :form="form" field="status_id"></has-error>
              </div>
              <!-- Showing all clients and getting project related to this client 
              <div class="form-group" v-if="form.project">
                      <label for="name">{{ $t("Task.client") }}</label>
                      <multiselect
                          v-model="form.owner"
                          :options="owners"
                          :close-on-select="true"
                          :clear-on-select="true"
                          :preserve-search="true"
                          :multiple="true"
                          :placeholder="$t('Task.selectOne')"
                          label="name"
                          track-by="id"
                          :preselect-first="true"
                          :allow-empty="true"
                          deselect-label="Can't remove this value"
                          @input="getProjectsByOwner(form.owner[0] ? form.owner[0].id : null)"
                      ></multiselect>
                      <has-error
                          :form="form"
                          field="owner_id"
                      ></has-error>
              </div> -->
              <!-- Showing Clients related to the selected project 
              <div class="form-group" v-else>
                  <label for="name">{{ $t("Task.client") }}</label>
                    <multiselect
                        v-model="form.owner"
                        :options="owners"
                        :close-on-select="true"
                        :clear-on-select="true"
                        :preserve-search="true"
                        :multiple="true"
                        track-by="id"
                        :placeholder="$t('Task.selectOne')"
                        label="name"
                        :preselect-first="true"
                        :allow-empty="true"
                        deselect-label="Can't remove this value"
                        @input="getProjectsByOwner(form.owner[0] ? form.owner[0].id : null)"
                    ></multiselect>
                    <has-error
                        :form="form"
                        field="client_id"
                    ></has-error>
              </div> -->
              <!-- Showing all projects and getting clients related to this project 
              <div class="form-group" v-if="form.owner && form.owner.length > 0">
                  <label for="name">{{
                      $t("Task.project")
                  }}</label>
                  <multiselect
                      :loading="LoadOwnerProjects"
                      v-model="form.project"
                      :options="projects"
                      :close-on-select="true"
                      :clear-on-select="false"
                      :preserve-search="true"
                      :placeholder="$t('Task.selectOne')"
                      label="name"
                      track-by="id"
                      :preselect-first="true"
                      :allow-empty="false"
                  ></multiselect>
                  <has-error
                      :form="form"
                      field="project_id"
                  ></has-error>
              </div>-->
              <!-- Showing Projects related to the selected project 
              <div class="form-group" v-else>
                  <label for="name">{{
                      $t("Task.project")
                  }}</label>

                     <v-autocomplete
                        v-model="form.project"
                        :items="projects"
                        :search-input.sync="projectSync"
                        color="white"
                        hide-no-data
                        hide-selected
                        item-text="name"
                        item-value="id"
                        :placeholder="$t('Task.selectOne')"
                        prepend-icon="mdi-database-search"
                        return-object
                    ></v-autocomplete>
                  <has-error
                      :form="form"
                      field="project_id"
                  ></has-error>
              </div>-->
              
              <div class="form-group">
                <label for="name"> Responsible (Manager) </label>
                <multiselect
                  v-model="form.manager"
                  :multiple="true"
                  :options="employees"
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
                <label for="end_at">{{ $t("Task.endAt") }}</label>
                <date-picker
                  v-model="form.due_date"
                  lang="en"
                  type="datetime"
                  format="DD-MM-YYYY HH:mm"
                  :minute-step="15"
                  input-class="form-control"
                  value-type="format"
                ></date-picker>
                <has-error :form="form" field="due_date"></has-error>
              </div>
                <div class="form-group">
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
            </form>
          </div>

          <div class="modal-footer">
            <button
              type="submit"
              class="btn btn-primary"
              @click="createTicket(form)"
            >
              {{ $t("Ticket.save") }}
            </button>
          </div>
        </div>
      </div>
    </div>            

  </div>
  <!-- /.tab-pane -->
</template>

<script>
import { mapGetters } from "vuex";
import { quillEditor } from "vue-quill-editor";
import DatePicker from "vue2-datepicker";

export default {
  data(){

    return {
      priorityList: ["normal", "high", "low"],
      projectSync: null,
      form: new Form({
        id: "",
        name: "",
        description: null,
        due_date: "",
        owner: null,
        project: {
          id: "",
          name: "",
        },
          status: {
              id: 1,
              name: "open"
          },
        estimated_time: "",
        manager: null,
        manager_id: "",
        project_id: "",
        owner_id: "",
        status_id: "",
        cc: '',

      }),
            editorOption: {
        modules: {
          toolbar: [
            ["bold", "italic", "underline", "strike"],
            ["blockquote", "code-block"],
            [{ list: "ordered" }, { list: "bullet" }],
          ],
        },
      },
    }

  },
  props: {
    userId: {
      type: Number,
      required: true
    },
  },
  methods: {
    getTicketsPerClient(page) {
      this.$Progress.start();
      this.$store
        .dispatch("ticket/getTicketsPerClient", { id: this.userId, page: page,user_profile: true,index: true,dropdown:true })
        .then(() => {
          this.$Progress.finish();
        })
        .catch(error => {
          this.$Progress.fail();
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
  getOwners() {
    this.$Progress.start();
    this.$store
        .dispatch("owner/getOwners")
        .then(() => {
            this.$Progress.finish();
        })
        .catch(error => {
            this.$Progress.fail();
        });
  },
getProjectsByOwner(ownerId) {

      if(!ownerId){
        this.$store.commit('project/setProjectByOwners', []);
        this.$store.commit('project/setAllProjects', []);
        return false;
      }



      this.form.owner_id = ownerId;
      this.LoadOwnerProjects = true;
      // this.form.project = [];
      if (ownerId !== null && ownerId !== "") {
        this.$store
          .dispatch("project/getProjectsByOwner", ownerId)
          .then(() => {
            console.log(this.form);
            this.LoadOwnerProjects = false;
            this.$Progress.finish();
          })
          .catch((error) => {
                  console.log(error)
            this.$Progress.fail();
          });
      }
    },


    CreateTicketModal(){
      $("#newTicketModel").modal("show");
    },

    createTicket(data) {
      this.$Progress.start();
      data.manager_id = data.manager ? data.manager.map((man) => man.id) : null;
      // data.owner_id = data.owner ? data.owner.map((own) => own.id) : null;

      data.owner_id = [this.owners.find(option => option.id === this.userId).id];
      data.status_id = data.status.id;
      this.$store
        .dispatch("ticket/createTicket", data)
        .then((response) => {
          $("#newTicketModel").modal("hide");
          this.$Progress.finish();
          this.getTicketsPerClient(1);
          Toast.fire({
            type: "success",
            title: response.data.message,
          });
        })
        .catch((error) => {
          console.log(error);
          this.$Progress.fail();
          if (error.response) {
            this.form.errors.errors = error.response.data.errors;
          }
        });
    },

    getEmployees() {
      this.$store
        .dispatch("user/getRegularUsers", {
          roles: true,
          dropdown:true
        })
        .then(() => {})
        .catch(() => {});
    },
  },
  mounted() {
    this.getTicketsPerClient();
    this.getStatus();
    this.getOwners();
    this.getEmployees();
  },
  watch: {
        projectSync (val) {
            if(!val) return false;
            if (this.timer) {
                clearTimeout(this.timer);
                this.timer = null;
            }
            this.timer = setTimeout(() => {
                 this.getAllProjects(val);
            }, 800);

    },
  },
  computed: {
    ...mapGetters({
      tickets: "user/TicketsPerClient",
      status: "ticket/activeStatus",
      owners: "owner/activeOwners",
      projects: "project/projectByOwners",
      employees: "user/getRegularUsers",

    })
  },
  components:{
    quillEditor,
    DatePicker,
  }
};
</script>

<style scoped>
.col {
  padding-top: 2px;
  padding-bottom: 2px;
}
tbody tr:nth-of-type(odd) {
  background-color: #dcdcdc;
}

thead tr th {
  color: #2f5298 !important;
  font-size: 18px !important;
}
</style>
