<template>
<span>
  <vue-headful
      :title="'Edit Ticket: '+ticket.name"
  />
  <span v-if="!loading">
    <div class="row">
      <div class="col-md-12">
        <div class="card h-30">
          <div class="card-header">
            <h3 class="card-title"><span style="font-weight: bold;font-size: 1.1rem;">Title: </span>{{ticket.name}}</h3>
            <div class="card-tools">
              <div class="btn btn-sm btn-red" @click="cancelEdit"><i class="text-yellow fas fa-times-circle"></i>&nbsp;{{ $t("Ticket.cancel") }}</div>
              <div class="btn btn-sm btn-blue" @click="editTicket(form)"><i class="text-yellow fas fa-save"></i>&nbsp;{{ $t("Ticket.update") }}</div>
              <div class="btn btn-sm" @click="deleteTicket(ticketId)" title="Delete Ticket"><i class="text-red fas fa-trash"></i></div>
            </div>
          </div>
          <div class="card-body pa-0">
            <div class="row">
              <div class="form-group col-md-3 col-sm-6 pt-0 pb-0">
                <label for="name">{{ $t("Ticket.ticketName") }}</label>
                <input
                  v-model="form.name"
                  type="text"
                  name="name"
                  :placeholder="$t('Ticket.ticketName')"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('name') }"
                  autocomplete="off"
                />
                <has-error :form="form" field="name"></has-error>
              </div>
              <div class="form-group col-md-3 col-sm-6 pt-0 pb-0">
                <label for="status">{{ $t("Ticket.status") }}</label>
                <multiselect
                  v-model="form.status"
                  :options="status"
                  :placeholder="$t('Ticket.selectOne')"
                  label="name"
                  track-by="name"
                  @input="(opt) => (form.status_id = opt.id)"
                ></multiselect>
                <has-error :form="form" field="name"></has-error>
              </div>
              <div class="form-group col-md-3 col-sm-6 pt-0 pb-0">
                <label for="name">{{ $t("Ticket.project") }}</label>
                <multiselect
                    v-model="form.project"
                    :options="projects"
                    :close-on-select="true"
                    :clear-on-select="false"
                    :preserve-search="true"
                    :placeholder="$t('Ticket.selectOne')"
                    label="name"
                    @input="(opt) => (form.project_id = opt.id)"
                ></multiselect>
                <has-error :form="form" field="project_id"></has-error>
              </div>
              <div class="form-group col-md-3 col-sm-6 pt-0 pb-0">
                <label for="name">Estimated time</label>
                <input
                  class="form-control"
                  v-model="form.estimated_time"
                  :placeholder="$t('Ticket.selectOne')"
                  label="Estimated time"
                />
                <has-error :form="form" field="estimated_time"></has-error>
              </div>
              <div class="form-group col-md-3 col-sm-6 pt-0 pb-0">
                <label for="name">{{ $t("Ticket.client") }}</label>
                <multiselect
                  v-model="form.owner"
                  :multiple="true"
                  :options="owners"
                  :searchable="true"
                  :close-on-select="true"
                  :clear-on-select="false"
                  :preserve-search="true"
                  track-by="id"
                  :preselect-first="true"
                  :placeholder="$t('Ticket.selectOne')"
                  label="name"
                  :allow-empty="false"
                  @input="getProjectsByOwner(form.owner.id)"
                  deselect-label="Can't remove this value"
                ></multiselect>
                <has-error :form="form" field="client_id"></has-error>
              </div>
              <div class="form-group col-md-3 col-sm-6 pt-0 pb-0" v-if="ticket.manager">
                <label for="name"> Responsible (Assigned) </label>
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
              <div class="form-group col-md-3 col-sm-6 pt-0 pb-0">
                <label for="end_at">{{ $t("Task.endAt") }}</label>
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
            </div>
            <div class="row">
              <div class="col-md-12 pt-0 pb-0">
                <label for="description">{{ $t("Ticket.ticketDesc") }}</label>
                <quill-editor
                    v-model="form.description"
                    ref="myQuillEditor"
                    :options="editorOption"
                ></quill-editor>
                <has-error :form="form" field="description"></has-error>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </span>
  <span v-else>
    <div class="box" style="height: 400px;">
      <scale-loader :loading="loader.loading" :color="loader.color" :size="loader.size"></scale-loader>
    </div>
  </span>
</span>
</template>
<script>
import { mapActions, mapGetters } from "vuex";
import { quillEditor,Quill } from "vue-quill-editor";
import ScaleLoader from 'vue-spinner/src/ScaleLoader.vue'
import DatePicker from "vue2-datepicker";
import { ImageDrop } from 'quill-image-drop-module'

Quill.register('modules/imageDrop', ImageDrop)

// require styles
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";

export default {
  data() {
    return {
      loader: {
        loading: true,
        color: '#2959B5',
        size: '70px',
      },
      cc: '',
      form: new Form({
        id: "",
        name: "",
        description: null,
        due_date: "",
        owner: "",
        project: {
          id: "",
          name: "",
        },
        estimated_time : 0,
        status: {},
        manager: null,
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
            [{ list: "ordered" }, { list: "bullet" }],
          ],
          imageDrop: true
        },
      },
      ticketId: this.ticketid,
      loading: true,
    };
  },
  methods: {
    ...mapActions("ticket", ["getTicketById"]),
    cancelEdit() {
      this.$emit('cancelEdit');
    },
    editTicket(data) {
      this.$Progress.start();
      this.form.manager_id = this.form.manager.map((man) => man.id);
      this.form.owner_id = this.form.owner.map((man) => man.id);
      if (this.form.project) {
        this.form.project_id = this.form.project.id;
      }
      this.$store
        .dispatch("ticket/editTicket", data)
        .then((response) => {
          this.$Progress.finish();
          Toast.fire({
            type: "success",
            title: response.data.message,
          });
          this.$router.push({ name: "ticket", params: { id: this.ticketId } });
            this.$emit('updated');
        })
        .catch((error) => {
          console.log(error);
          this.$Progress.fail();
          if (error.response) {
            this.form.errors.errors = error.response.data.errors;
          }
        });
    },
    getProjectsByOwner(ownerId) {
      this.form.project = [];

      let owners = this.form.owner
        ? this.form.owner.map((owner) => owner.id)
        : [];
      owners = JSON.stringify(owners);

      if (ownerId !== null && ownerId !== "") {
        this.$store
          .dispatch("project/getProjectsByOwner", owners)
          .then(() => {
            this.$Progress.finish();
          })
          .catch((error) => {
            this.$Progress.fail();
          });
      }
    },
      getAllProjects() {
          this.$store
              .dispatch("project/getAllProjects",{dropdown:true})
              .then(() => {
                  this.$Progress.finish();
              })
              .catch((error) => {
                  this.$Progress.fail();
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
        confirmButtonText: "Yes, delete it!",
      }).then((result) => {
        if (result.value) {
          this.$Progress.start();
          this.$store
            .dispatch("ticket/deleteTicket", id)
            .then((response) => {
              this.$Progress.finish();
              Toast.fire({
                type: "success",
                title: response.data.message,
              });
              this.$router.push({ name: "tickets.list" });
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

    async loadEditData() {
      this.$Progress.start();
      this.loading = true;
      let response = await this.getTicketById(this.ticketId)
        .then(() => {
          this.$Progress.finish();
          this.loading = false;
          this.form.fill(this.ticket);
          this.form.estimated_time = 0;
          this.cc = this.ticket.cc;
          this.getAllProjects();
        })
        .catch((error) => {
          this.$Progress.fail();
        });
    },
  },
    props : ['ticketid'],
  mounted() {
    this.loadEditData();
  },
  watch: {
    // newManager(newValue) {
    //   this.form.manager_id = newValue;
    // },
    newStatus(newValue) {
      this.form.status_id = newValue;
    },
    // newOwner(newValue) {
    //   this.form.owner_id = newValue;
    // },
  },
  computed: {
    ...mapGetters({
      ticket: "ticket/activeTicket",
      owners: "owner/activeOwners",
      projects: "project/projectByOwners",
      status: "ticket/activeStatus",
      employees: "user/getRegularUsers",
      allProjects: "project/allProjects",

    }),
    // newManager() {a
    //   return this.form.manager.id;
    // },
    newStatus() {
      return this.form.status.id;
    },
    // newOwner() {
    //   return this.form.owner.id;
    // },
  },
  components: {
    quillEditor,
    DatePicker,
    ScaleLoader
  },
};
</script>
<style scoped>
.text-green {
  color: #276D30;
}
.text-red {
  color: #BB3E3E;
}
.text-yellow {
  color: #FAB10B;
}
.btn-blue {
  background-color: #234FA3;
  color: #ffffff;
}
.btn-blue:hover {
  text-decoration: none;
  background-color: #2959B5;
  color: #ffffff;
}
.box{
    display: flex;
    justify-content: center;
    align-items: center;
    padding-top: 4rem;
}
.card-body { height: 400px; overflow-y: scroll; }

</style>
