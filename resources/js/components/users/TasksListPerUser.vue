<template>
  <div class="active tab-pane"  data-app>
    <v-simple-table dense fixed-header>
      <template v-slot:default>
      <thead>
          <tr>
          <th class="text-left">#</th>
          <th class="text-left">{{$t('Task.title')}}</th>
          <th class="text-left">Status</th>
          <th class="text-left">{{$t('Task.priorty')}}</th>
          <th class="text-left">{{$t('Task.project')}}</th>
          <th class="text-left">{{$t('Task.deadline')}}</th>
          <th width="3%" class="text-left">ETC</th>
          </tr>
      </thead>
      <tbody>
          <tr v-for="task in tasks.data" :key="task.id">
              <td>{{task.id}}</td>
              <td>
                  <router-link :title="task.name" :to="{ name: 'task', params: { id: task.id } }">
                  {{ task.name }}
                  </router-link>
              </td>
              <td>{{task.status.name}}</td>
              <td> {{task.priority}} </td>
              <td>
                  <router-link
                      v-if="task.project"
                      :title="task.project.name"
                      :to="'/admin/project/'+task.project.id"
                  >{{task.project.name}}</router-link>
                  <span v-else>------</span>
              </td>

              
              <td>{{ task.deadline }}</td>
              <td>{{task.estimated_time}}</td>
          </tr>
      </tbody>
      </template>
  </v-simple-table>
  <pagination
      :data="tasks"
      :limit="parseInt(2)"
      size="small"
      @pagination-change-page="getTasksPerClient"
  >
      <span slot="prev-nav">&lt; Previous</span>
      <span slot="next-nav">Next &gt;</span>
  </pagination>



              <v-btn color="#2F5298" dark fab large bottom right fixed @click="CreateTaskModal">
                <v-icon>fas fa-plus</v-icon>
            </v-btn>


         <div
            class="modal fade"
            id="newTaskModal"
            tabindex="-1"
            role="dialog"
            aria-labelledby="newTaskLabel"
            aria-hidden="true"
        >

            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5
                            class="modal-title"
                            id="newTaskLabel"
                        >
                            {{ $t("Task.createNewTask") }}
                        </h5>

                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form
                        @submit.prevent="
                            createTask(form)
                        "
                        @keydown="form.onKeydown($event)"
                    >
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name">{{
                                    $t("Task.taskName")
                                }}</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.has('name')
                                    }"
                                />
                                <has-error
                                    :form="form"
                                    field="name"
                                ></has-error>
                            </div>
                            <div class="form-group">
                                <label for="description">{{
                                    $t("Task.taskDesc")
                                }}</label>
                                <quill-editor
                                    id="comments-editor"
                                    v-model="form.description"
                                    ref="myQuillEditor"
                                    :options="editorOption"
                                ></quill-editor>
                                <has-error
                                    :form="form"
                                    field="description"
                                ></has-error>
                            </div>
                            <div class="form-group">
                                <label for="name">{{
                                    $t("Task.status")
                                }}</label>
                                <multiselect
                                    v-model="form.status"
                                    :options="status"
                                    label="name"
                                    track-by="name"
                                    @input="opt => (form.status_id = opt.id)"
                                ></multiselect>

                                <has-error
                                    :form="form"
                                    field="status_id"
                                ></has-error>
                            </div>





                            <!-- Showing all clients and getting project related to this client
                            <div class="form-group" v-if="form.project">
                                    <label for="name">{{
                                        $t("Task.client")
                                    }}</label>
                                    <multiselect
                                        v-model="form.owner"
                                        :options="owners"
                                        @input="getProjects(form.owner.id)"
                                        :close-on-select="true"
                                        :clear-on-select="true"
                                        :preserve-search="true"
                                        :placeholder="$t('Task.selectOne')"
                                        label="name"
                                        :preselect-first="true"
                                        :allow-empty="false"
                                        deselect-label="Can't remove this value"
                                    ></multiselect>
                                    <has-error
                                        :form="form"
                                        field="client_id"
                                    ></has-error>
                            </div>
                             Showing Clients related to the selected project 
                            <div class="form-group" v-else>
                                <label for="name">{{
                                        $t("Task.client")
                                    }}</label>
                                    <multiselect
                                        v-model="form.owner"
                                        :options="owners"
                                        @input="getProjects(form.owner.id)"
                                        :close-on-select="true"
                                        :clear-on-select="true"
                                        :preserve-search="true"
                                        :placeholder="$t('Task.selectOne')"
                                        label="name"
                                        :preselect-first="true"
                                        :allow-empty="false"
                                        deselect-label="Can't remove this value"
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
                                    v-model="form.project"
                                    :options="projects"
                                    :close-on-select="true"
                                    :clear-on-select="false"
                                    :preserve-search="true"
                                    :placeholder="$t('Task.selectOne')"
                                    label="name"
                                    :preselect-first="true"
                                    :allow-empty="false"
                                     @input="getClientsPerProject(form.project.id)"
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


                               <multiselect
                                    v-model="form.project"
                                    :options="projects"
                                    :close-on-select="true"
                                    :clear-on-select="false"
                                    :preserve-search="true"
                                    :placeholder="$t('Task.selectOne')"
                                    label="name"
                                    :preselect-first="true"
                                    :allow-empty="false"
                                ></multiselect>-->
<div class="form-group">
                    <v-autocomplete
                        v-model="form.project"
                        :items="projects"
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
                            </div>

                                <!-- 
                             <div class="form-group">
                                <label for="name">{{
                                    $t("Task.client")
                                }}</label>
                                <multiselect
                                    v-model="form.owner"
                                    :options="owners"
                                    @input="getProjects(form.owner.id)"
                                    :close-on-select="true"
                                    :clear-on-select="false"
                                    :preserve-search="true"
                                    :placeholder="$t('Task.selectOne')"
                                    label="name"
                                    :preselect-first="true"
                                    :allow-empty="false"
                                    deselect-label="Can't remove this value"
                                ></multiselect>
                                <has-error
                                    :form="form"
                                    field="client_id"
                                ></has-error>
                            </div> -->



                            <!-- <div class="form-group">
                                <label for="name">{{
                                    $t("Task.project")
                                }}</label>
                                <multiselect
                                    v-model="form.project"
                                    :options="projects"
                                    :close-on-select="true"
                                    :clear-on-select="false"
                                    :preserve-search="true"
                                    :placeholder="$t('Task.selectOne')"
                                    label="name"
                                    :preselect-first="true"
                                    :allow-empty="false"
                                ></multiselect>
                                <has-error
                                    :form="form"
                                    field="project_id"
                                ></has-error>
                            </div> -->



                            <!-- <div class="form-group" v-if="form.project.tickets">
                                <label for="name">{{
                                    $t("Ticket.ticket")
                                }}</label>
                                <multiselect
                                    v-model="form.ticket"
                                    :options="form.project.tickets"
                                    :placeholder="$t('Task.selectOne')"
                                    label="name"
                                ></multiselect>
                                <has-error
                                    :form="form"
                                    field="ticket_id"
                                ></has-error>
                            </div> -->
                            <div class="form-group">
                                <label for="name">{{
                                    $t("Task.responsible")
                                }}</label>
                                <multiselect
                                    v-model="form.responsible"
                                    :options="responsible"
                                    :close-on-select="true"
                                    :clear-on-select="false"
                                    :preserve-search="true"
                                    :placeholder="$t('Task.selectOne')"
                                    label="name"
                                    :preselect-first="true"
                                    @input="
                                        opt => (form.responsible_id = opt.id)
                                    "
                                >
                                    <template
                                        slot="selection"
                                        slot-scope="{ values, search, isOpen }"
                                    >
                                        <span
                                            class="multiselect__single"
                                            v-if="values.length && !isOpen"
                                            >{{ values.length }}
                                            {{ $t("Task.options") }}</span
                                        >
                                    </template>
                                </multiselect>
                                <has-error
                                    :form="form"
                                    field="responsible_id"
                                ></has-error>
                            </div>
                            <div class="form-group" >
                                <label for="priority">{{
                                    $t("Task.priorty")
                                }}</label>
                                <multiselect
                                    class="clearfix"
                                    v-model="form.priority"
                                    :options="priorityList"
                                    :close-on-select="true"
                                    :allow-empty="false"
                                    :show-labels="false"
                                    :placeholder="$t('Task.selectOne')"
                                ></multiselect>
                                <has-error
                                    :form="form"
                                    field="priority"
                                ></has-error>
                            </div>
                            <div class="form-group">
                                <label for="deadline">{{
                                    $t("Task.deadline")
                                }}</label>
                                <date-picker
                                    v-model="form.deadline"
                                    lang="en"
                                    type="datetime"
                                    format="DD-MM-YYYY HH:mm"
                                    :minute-step="15"
                                    value-type="format"
                                    input-class="form-control"
                                ></date-picker>
                                <has-error
                                    :form="form"
                                    field="deadline"
                                ></has-error>
                            </div>
                            <div class="form-group">
                                <label for="estimated_time">{{
                                    $t("Task.estimatedTime")
                                    }}</label>
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
                        </div>

                        <div class="modal-footer">
                            <v-btn  type="submit" color="#ffffff" style="background-color:#234FA3 " text>  {{ $t("Task.save") }}</v-btn>

                        </div>
                    </form>
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
  data() {
        return {

form: new Form({
                id: "",
                name: "",
                description: "",
                status: {
                    id: 1,
                    name: "open"
                },
                estimated_time : "",
                project: {},
                project_id: '',
                ticket: [],
                ticket_id: '',
                owner: {},
                responsible: {},
                priority: "",
                deadline: ""
            }),
            priorityList: ["normal", "high", "low"],
            editorOption: {
                modules: {
                    toolbar: [
                        ["bold", "italic", "underline", "strike"],
                        ["blockquote", "code-block"],
                        [{ list: "ordered" }, { list: "bullet" }]
                    ]
                }
            },
      }
  },
  props: {
    userId: {
      type: Number,
      required: true
    }
  },
  methods: {
    CreateTaskModal(){
      $("#newTaskModal").modal("show");
    },
    getTasksPerClient(page = 1) {
    
      this.$Progress.start();
      this.$store
        .dispatch("task/getTasksPerClient", { id: this.userId, page: page,user_profile:true,index:true,dropdown:true })
        .then((res) => {
          this.$Progress.finish();
        })
        .catch(error => {
          this.$Progress.fail();
        });
    },
    createTask(data) {
          this.isLoading = true;
          data.owner_id = [this.owners.find(option => option.id === this.userId).id];
          data.project_id = data.project.id;
          this.$Progress.start();
          this.$store
            .dispatch("task/createTasksForClient", data)
            .then(response => {
              $("#newTaskModal").modal("hide");
              this.getTasksPerClient();
              this.$Progress.finish();
              this.isLoading = false;

              Toast.fire({
                type: "success",
                title: response.data.message
              });
            })
            .catch(error => {
              this.$Progress.fail();
              if (error.response) {
                this.isLoading = false;
                this.form.errors.errors = error.response.data.errors;
              }

            });
        },
    getStatus() {
          this.$store
            .dispatch("task/getStatus")
            .then(response => {})
            .catch(error => {
            });
    },
    getOwners() {
          this.$store
            .dispatch("owner/getOwners")
            .then(response => {})
            .catch(error => {
              console.log(error);
            });
    },
    getResponsibles() {
          this.$store
            .dispatch("regularUser/getRegularUser",{dropdown:true})
            .then()
            .catch(error => {
              console.log(error);
            });
    },
    getProjects() {
          this.form.project = [];
          this.$store
            .dispatch("project/getProjectsByOwner", this.userId)
            .then()
            .catch(error => {
              console.log(error);
            });
        },  },
  mounted() {
    this.getTasksPerClient();
    this.getStatus();
    this.getOwners();
    this.getResponsibles();
    this.getProjects();
  },
  computed: {
    ...mapGetters({
      tasks: "user/TasksPerClient",
      status: "task/activeStatus",
      owners: "owner/activeOwners",
      responsible: "regularUser/activeRegularUser",
      projects: "project/projectByOwners",
    })
  },
  components: {
      quillEditor,
      DatePicker,
  },
  
}
</script>

<style scoped>
tbody tr:nth-of-type(odd) {
  background-color: #dcdcdc;
}

thead tr th {
  color: #2f5298 !important;
  font-size: 18px !important;
}
</style>
