<template>
  <div class="row justify-content-center" v-if="!loading">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title font-weight-light">{{ form.name }}</div>
          <div class="card-tools">
          </div>
        </div>
        <form @submit.prevent="editProject()" @keydown="form.onKeydown($event)">
          <div class="card-body">
            <div class="row">
              <div class="form-group col-sm-12 col-md-3">
                <label for="name">{{$t('Project.name')}}</label>
                <input
                  v-model="form.name"
                  type="text"
                  name="name"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('name') }"
                />
                <has-error :form="form" field="name"></has-error>
              </div>
              <div class="form-group col-sm-12 col-md-3">
                <label for="client">{{$t('Project.client')}}</label>
                <multiselect
                  v-model="form.owner"
                  :multiple="true"
                  :options="owners"
                  :searchable="true"
                  :close-on-select="true"
                  :clear-on-select="false"
                  :preserve-search="true"
                  :placeholder="$t('Project.selectOne')"
                  label="name"
                  track-by="name"
                  :preselect-first="true"
                  @input="opt => form.owner_id = opt.id"
                ></multiselect>
                <has-error :form="form" field="owner_id"></has-error>
              </div>
              <div class="form-group col-sm-12 col-md-3">
                <label for="name">{{$t('Project.assignedUsers')}}</label>
                <multiselect
                  v-model="form.project_assign"
                  :options="responsible"
                  :multiple="true"
                  :close-on-select="false"
                  :clear-on-select="false"
                  :preserve-search="true"
                  :placeholder="$t('Project.pickSome')"
                  label="name"
                  track-by="id"
                  :preselect-first="true"
                >
                  <template slot="selection" slot-scope="{ values, search, isOpen }">
                    <span
                      class="multiselect__single"
                      v-if="values.length &amp;&amp; !isOpen"
                    >{{ values.length }}{{$t('Project.options')}}</span>
                  </template>
                </multiselect>
                <has-error :form="form" field="project_assign"></has-error>
              </div>
                <div class="form-group col-sm-12 col-md-3">
                    <label for="status">Status</label>
                    <multiselect
                        v-model="form.status"
                        :options="status"
                        :multiple="false"
                        :clear-on-select="false"
                        :preserve-search="true"
                        :placeholder="$t('Project.selectOne')"
                        label="name"
                        track-by="id"
                        :preselect-first="true"
                    >
                        <template slot="selection" slot-scope="{ values, search, isOpen }">
                    <span
                        class="multiselect__single"
                        v-if="values.length &amp;&amp; !isOpen"
                    >{{ values.length }}{{$t('Project.options')}}</span>
                        </template>
                    </multiselect>
                    <has-error :form="form" field="project_assign"></has-error>
                </div>

              <!-- <div class="form-group col-sm-12 col-md-3">
                <label for="budget_hours">{{$t('Project.budgetHours')}}</label>
                <input
                  v-model="form.budget_hours"
                  type="number"
                  min="0"
                  step="0.01"
                  name="budget_hours"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('budget_hours') }"
                />
                <has-error :form="form" field="budget_hours"></has-error>
              </div> -->
              <div class="form-group col-sm-12 col-md-3">
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
                <has-error :form="form" field="estimated_time"></has-error>
              </div>
              <div class="form-group col-sm-12 col-md-3">
                <label for="budget">Budget</label>
                <input
                    v-model="form.budget"
                    type="number"
                    min="0"
                    step="0.01"
                    name="budget_hours"
                    class="form-control"
                    :class="{'is-invalid': form.errors.has('budget')}"
                />
                <has-error :form="form" field="budget"></has-error>
            </div>
              <div class="form-group col-sm-12 col-md-12">
                <label for="description">{{$t('Project.desc')}}</label>
                <quill-editor
                  id="comments-editor"
                  v-model="form.description"
                  ref="myQuillEditor"
                  :options="editorOption"
                ></quill-editor>
                <has-error :form="form" field="description"></has-error>
              </div>

            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">{{$t('Project.update')}}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="card" v-else>
    <div class="card-body justify-content-center">{{$t('Project.loading')}}</div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import { quillEditor } from "vue-quill-editor";

// require styles
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";

export default {
  components: {
    quillEditor
  },
  data() {
    return {
      loading: true,
      projectId: this.$route.params.id,
      form: new Form({
        id: "",
        name: "",
        owner: [],
        description: "",
        budget_hours: "",
        project_assign: [],
        estimated_time: "",
        budget: '',
          status: {
              id: 1,
              name: "open"
          }
      }),
      owner_id: "",
      selected: null,
      editorOption: {
        modules: {
          toolbar: [
            ["bold", "italic", "underline", "strike"],
            ["blockquote", "code-block"],
            [{ list: "ordered" }, { list: "bullet" }]
          ]
        }
      }
    };
  },
  methods: {
    ...mapActions("project", ["getProjectById"]),
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
        .dispatch("regularUser/getRegularUser")
        .then()
        .catch(error => {
          console.log(error);
        });
    },
    async loadEditData() {
      this.$Progress.start();
      let response = await this.getProjectById(this.projectId)
        .then(() => {
          this.$Progress.finish();
          this.form.fill(this.singleProject);
          this.form.owner = this.singleProject.owners;
        //   this.form.selected = _.map(this.form.project_assign, function(
        //     value,
        //     key
        //   ) {
        //     return value.name;
        //   });
          this.loading = false;
        })
        .catch(error => {
          this.$Progress.fail();
        });
    },
    editProject() {
      this.$Progress.start();
      // get user id only form assigned users
        const owner_temp =[]
        if (this.form.project_assign){
            this.form.project_assign.forEach(element => {
                this.form.project_assign = this.form.project_assign.filter(function(obj) {
                    return obj.id !== element.id;
                });
                this.form.project_assign.push(element.id);
            });
        }
        this.form.owner.forEach( element=>{
            owner_temp.push(parseInt(element.id))
        } )
        this.form.owner = owner_temp;
        this.form.status_id = this.form.status.id;
      this.$store
        .dispatch("project/editProject", this.form)
        .then(response => {
          $("#Modal").modal("hide");
          this.$Progress.finish();
          Toast.fire({
            type: "success",
            title: response.data.message
          });
          this.$router.push({
            name: "project",
            params: { id: this.projectId }
          });
        })
        .catch(error => {
          this.$Progress.fail();
          if (error.response) {
            this.form.errors.errors = error.response.data.errors;
          }
        });
    }
  },
  mounted() {
    this.getOwners();
    this.getResponsibles();
    this.loadEditData();
      this.getStatus();
  },
  computed: {
    ...mapGetters({
      singleProject: "project/activeSingleProject",
      owners: "owner/activeOwners",
      responsible: "regularUser/activeRegularUser",
        status: "ticket/activeStatus"
    })
  }
};
</script>

<style>
</style>
