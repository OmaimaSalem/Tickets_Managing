<template>
  <div class="active tab-pane">
    <!-- <div class="card-body table-responsive p-0" style="height:330px;overflow-y:auto;">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>ID</th>
            <th>{{$t('User.project')}}</th>
            <th>created at</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="project in projects.data" :key="project.id">
            <td>{{ project.id }}</td>
            <td><router-link :to="'/admin/project/' + project.id">{{ project.name }}</router-link></td>
            <td>{{ project.created_at | DateWithTime }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <pagination
      class="mt-1"
      align="center"
      size="small"
      :show-disabled="true"
      :data="projects"
      :limit="3"
      @pagination-change-page="getProjectPerClient"
    ></pagination> -->


              <v-simple-table dense fixed-header>
                    <template v-slot:default >
                      <thead>
                        <tr>
                          <th width="5%"  class="text-left">{{ $t('Project.Number') }}</th>
                          <th width="15%" class="text-left">{{ $t('Project.name') }}</th>
                          <th width="20%" class="text-left">{{ $t('Project.description') }}</th>
                          <th width="5%"  class="text-left">Status</th>
                          <!-- <th width="15%" class="text-left">{{ $t('Ticket.client') }}</th> -->
                          <th width="20%" class="text-left"> {{ $t('Project.ProjectAssigns') }}</th>
                          <th width="5%"  class="text-left" data-placement="bottom" title="estimated time to complete">ETC</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="project in projects.data" :key="project.id">
                          <td>
                            <router-link
                              :to="{
                                name: 'project',
                                params: { id: project.id },
                              }"
                            >
                              {{ project.id }}
                            </router-link>
                          </td>
                          <td>
                            <router-link
                                data-placement="top" :title="project.name "
                              :to="{
                                name: 'project',
                                params: { id: project.id },
                              }"
                            >
                              {{ project.name }}
                            </router-link>
                          </td>
                          <td>{{ project.description }}</td>
                          <td>{{ project.status.name}}</td>
                          <!-- <td v-if="project.owner">
                            <router-link
                                data-placement="top" :title="project.owner[0].name"
                              v-if="project.owner[0]"
                              :to="'/admin/profile/' + project.owner[0].id"
                              >{{ project.owner[0].name }}

                            </router-link
                            >
                          </td>
                          <td v-else>----</td> -->
                          <td v-if="project.project_assign[0]" data-placement="top" :title="project.project_assign[0].name">
                            {{ project.project_assign[0].name }}
                          </td>
                          <td v-else>----</td>
                          <td>
                            <span :title="`Due Date: ${project.estimated_time}`">{{ project.estimated_time}}</span>
                          </td>
                        </tr>
                      </tbody>
                    </template>
                  </v-simple-table>
                  <pagination
                    :data="projects"
                    :limit="parseInt(2)"
                    size="small"
                    @pagination-change-page="getProjectPerClient"
                  >
                    <span slot="prev-nav">&lt; Previous</span>
                    <span slot="next-nav">Next &gt;</span>
                  </pagination>


 
                  <v-btn color="#2F5298" dark fab large bottom right fixed @click="CreateProjectModal">
                      <v-icon>fas fa-plus</v-icon>
                  </v-btn>


        <!-- Create Modal -->
        <div
            class="modal fade"
            id="ProjectModal"
            tabindex="-1"
            role="dialog"
            aria-labelledby="newModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5
                            class="modal-title"
                            id="newModalLabel"
                        >
                            {{ $t("Project.createProject") }}
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
                        @submit.prevent="createProject()"
                        @keydown="form.onKeydown($event)"
                    >
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name">{{
                                    $t("Project.name")
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
                                    $t("Project.desc")
                                }}</label>
                                <textarea
                                    v-model="form.description"
                                    type="text"
                                    name="description"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.has(
                                            'description'
                                        )
                                    }"
                                ></textarea>
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
                                    :placeholder="$t('Task.selectOne')"
                                    label="name"
                                    track-by="id"
                                ></multiselect>
                                <has-error
                                    :form="form"
                                    field="status_id"
                                ></has-error>
                            </div>

                            <div class="form-group">
                                <label for="name">{{
                                    $t("Project.assignedUsers")
                                }}</label>
                                <multiselect
                                    v-model="form.project_assigns"
                                    :options="responsible"
                                    :multiple="true"
                                    :close-on-select="false"
                                    :clear-on-select="false"
                                    :preserve-search="true"
                                    :placeholder="$t('Project.pickSome')"
                                    label="name"
                                    track-by="id"
                                    :preselect-first="true"
                                    @input="
                                        opt => (form.responsible_id = opt.id)
                                    "
                                >
                                    <template
                                        slot="selection"
                                        slot-scope="{ values, isOpen }"
                                    >
                                        <span
                                            class="multiselect__single"
                                            v-if="values.length && !isOpen"
                                            >{{ values.length }}
                                            {{ $t("Project.options") }}</span
                                        >
                                    </template>
                                </multiselect>
                                <has-error
                                    :form="form"
                                    field="project_assign"
                                ></has-error>
                            </div>
                                 <div class="form-group">
                                <label for="estimated_time">{{
                                    $t("Project.estimatedTime")
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
                            <div class="form-group">
                                <label for="budget">{{
                                    $t("Project.budget")
                                }}</label>
                                <input
                                    v-model="form.budget"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    name="budget"
                                    class="form-control"
                                    :class="{
                            'is-invalid': form.errors.has(
                                'budget'
                            )
                        }"
                                />
                                <has-error
                                    :form="form"
                                    field="budget"
                                ></has-error>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <v-btn type="submit" color="#ffffff" style="background-color:#234FA3 " text> {{ $t("Project.save") }}</v-btn>
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

export default {
  data(){
    return {
    form: new Form({
              id: "",
              name: "",
              owner: [],
              description: "",
              task_rate: "",
              budget_hours: "",
              project_assigns: [],
              project_assign: [],
              estimated_time : "",
              budget: "",
              status: {
                  id: 1,
                  name: "open"
              }
          }),
    }
  },
  props: {
    userId: {
      type: Number,
      required: true
    }
  },
  methods: {
    getProjectPerClient(page = 1) {
      this.$Progress.start();
      this.$store
        .dispatch("project/getProjectPerClient", { id: this.userId, page: page })
        .then(() => {
          this.$Progress.finish();
        })
        .catch(error => {
          this.$Progress.fail();
        });
    },
    CreateProjectModal(){
      $("#ProjectModal").modal("show");
    },
  getStatus() {
      this.$store
          .dispatch("ticket/getStatus")
          .then(response => {})
          .catch(error => {});
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
  getResponsibles() {
    this.$store
        .dispatch("regularUser/getRegularUser", {
            roles: true
        })
        .then(()=>{
        })
        .catch(error => {
            console.log(error);
        });
  },

  createProject() {
            this.$Progress.start();
            // get user id only form assigned users
            this.form.project_assigns.forEach(element => {
                this.form.project_assign.push(element.id);
            });
            this.form.owner = [];

            this.form.owner.push(this.owners.find(option => option.id === this.userId).id);

            this.form.status_id = this.form.status.id;
            this.$store
                .dispatch("project/createProjectPerClient", this.form)
                .then(response => {
                    $("#ProjectModal").modal("hide");
                    this.$Progress.finish();
                    Toast.fire({
                        type: "success",
                        title: response.data.message
                    });
                    this.getProjectPerClient();
                })
                .catch(error => {
                  console.log(error);
                    this.$Progress.fail();
                    if (error.response) {
                        this.form.errors.errors = error.response.data.errors;
                    }
                });
        },
},
  mounted() {
    this.getProjectPerClient();
    this.getOwners();
    this.getStatus();
    this.getResponsibles();
  },
  computed: {
    ...mapGetters({
      projects: "user/ProjectPerClient",
      status: "ticket/activeStatus",
      user: "user/activeSingleUser",
      owners: "owner/activeOwners",
      responsible: "regularUser/activeRegularUser",
    })
  }
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
</style>
