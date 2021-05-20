<template>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">{{ $t("User.usersTable") }}</h3>

          <div class="card-tools">
            <button
              type="submit"
              class="btn btn-success btn-sm"
              @click="newModal"
            >
              <i class="fas fa-plus fa-fw"></i>
              <span class="d-none d-lg-inline">{{ $t("User.newUser") }}</span>
            </button>
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive p-0">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>name</th>
                <th>{{ $t("User.email") }}</th>
                <th>{{ $t("User.role") }}</th>
                <th>{{ $t("User.userType") }}</th>
                <th>{{ $t("User.createdAt") }}</th>
                <th>{{ $t("User.action") }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users.data" :key="user.id">
                <td>{{ user.id }}</td>
                <td>
                  <router-link :to="'/admin/profile/' + user.id">{{
                    user.name
                  }}</router-link>
                </td>
                <td>{{ user.email }}</td>
                <td>
                  <div
                    v-show="user.roles"
                    v-for="role in user.roles"
                    :key="role.id"
                    class="badge badge-primary mr-1"
                  >
                    {{ role.name }}
                  </div>
                </td>
                <td>{{ user.type }}</td>
                <td>{{ user.created_at | DateWithTime }}</td>
                <td>
                  <a
                    href="#"
                    class="btn btn-primary btn-xs"
                    @click="editModal(user)"
                  >
                    <i class="fas fa-edit fa-fw"></i>
                  </a>
                  <a
                    href="#"
                    class="btn btn-danger btn-xs"
                    @click="deleteUser(user.id)"
                  >
                    <i class="fas fa-trash fa-fw"></i>
                  </a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="card-footer clear-fix">
          <pagination
            align="right"
            size="small"
            :show-disabled="true"
            :data="users"
            :limit="3"
            @pagination-change-page="getResults"
          ></pagination>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->
    </div>
    <!-- Modal -->
    <div
      class="modal fade"
      id="Modal"
      tabindex="-1"
      role="dialog"
      aria-labelledby="newUserLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="newUserLabel">
              {{ $t("User.createNewUser") }}
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
          <div class="modal-body">
            <ul class="nav nav-pills ml-auto p-2">
              <li class="nav-item">
                <a class="nav-link active" href="#tab_1" data-toggle="tab">{{
                  $t("User.accountSetting")
                }}</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#tab_2" data-toggle="tab">{{
                  $t("User.accountDetails")
                }}</a>
              </li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab_1">
                <form
                  @submit.prevent="editMode ? editUser(form.id) : createUser()"
                  @keydown="form.onKeydown($event)"
                >
                  <div class="modal-body">
                    <div class="form-group">
                      <label for="name">{{ $t("User.username") }}</label>
                      <input
                        v-model="form.name"
                        type="text"
                        name="name"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.has('name') }"
                      />
                      <has-error :form="form" field="name"></has-error>
                    </div>
                    <div class="form-group">
                      <label for="email">{{ $t("User.email") }}</label>
                      <input
                        v-model="form.email"
                        type="email"
                        name="email"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.has('email') }"
                      />
                      <has-error :form="form" field="email"></has-error>
                    </div>
                    <div class="form-group">
                      <label for="password">{{ $t("User.password") }}</label>
                      <input
                        v-model="form.password"
                        type="password"
                        name="password"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.has('password') }"
                      />
                      <has-error :form="form" field="password"></has-error>
                    </div>
                    <div class="form-group">
                      <label for="role">{{ $t("User.role") }}</label>
                      <multiselect
                        v-model="form.roles"
                        :multiple="true"
                        :options="roles"
                        :close-on-select="true"
                        :placeholder="$t('Vacation.selectOne')"
                        label="name"
                        track-by="name"
                      ></multiselect>
                      <has-error :form="form" field="role"></has-error>
                    </div>
                    <div class="form-group">
                      <label for="type">{{ $t("User.type") }}</label>
                      <multiselect
                        v-model="form.type"
                        :options="types"
                        :searchable="false"
                        :close-on-select="true"
                        :show-labels="false"
                        :placeholder="$t('Vacation.pickValue')"
                      ></multiselect>
                      <has-error :form="form" field="type"></has-error>
                    </div>

                    <div class="form-group">
                      <label for="vacation">{{
                        $t("Vacation.vacation")
                      }}</label>
                      <input
                        v-model="form.vacation"
                        type="number"
                        name="vacation"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.has('vacation') }"
                        autocomplete="off"
                      />
                      <has-error :form="form" field="vacation"></has-error>
                    </div>

                    <div class="form-group">
                      <label for="vacation"
                        >{{ $t("Vacation.skipVacation") }}
                        <input
                          v-model="form.skip_vacation_limit"
                          type="checkbox"
                          name="skip_vacation_limit"
                        />
                      </label>
                      <has-error
                        :form="form"
                        field="skip_vacation_limit"
                      ></has-error>
                    </div>
                  </div>

                  <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                      {{ $t("User.save") }}
                    </button>
                  </div>
                </form>
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="tab_2">
                <form
                  @submit.prevent="
                    editMode ? editMetadata(metadata) : createMetadata(metadata)
                  "
                  @keydown="form.onKeydown($event)"
                >
                  <div class="row">
                    <div class="form-group col-sm-12 col-md-6">
                      <label for="first_name">{{ $t("User.firstName") }}</label>
                      <input
                        v-model="metadata.first_name"
                        type="text"
                        name="first_name"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.has('first_name') }"
                        autocomplete="off"
                      />
                      <has-error :form="form" field="first_name"></has-error>
                    </div>
                    <div class="form-group col-sm-12 col-md-6">
                      <label for="last_name">{{ $t("User.lastName") }}</label>
                      <input
                        v-model="metadata.last_name"
                        type="text"
                        name="last_name"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.has('last_name') }"
                        autocomplete="off"
                      />
                      <has-error :form="form" field="last_name"></has-error>
                    </div>
                    <div class="form-group col-sm-12 col-md-6">
                      <label for="company">{{ $t("User.companyName") }}</label>
                      <input
                        v-model="metadata.company"
                        type="text"
                        name="company"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.has('company') }"
                        autocomplete="off"
                      />
                      <has-error :form="form" field="company"></has-error>
                    </div>

                    <div class="form-group col-sm-12 col-md-6">
                      <label for="address">{{ $t("User.address") }}</label>
                      <input
                        v-model="metadata.address"
                        type="text"
                        name="address"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.has('address') }"
                        autocomplete="off"
                      />
                      <has-error :form="form" field="address"></has-error>
                    </div>
                    <div class="form-group col-sm-12 col-md-6">
                      <label for="language">{{ $t("User.lang") }}</label>
                      <multiselect
                        v-model="metadata.language"
                        :options="language"
                        :searchable="false"
                        :close-on-select="true"
                        :show-labels="false"
                        :placeholder="$t('Vacation.pickValue')"
                      ></multiselect>
                      <has-error :form="form" field="language"></has-error>
                    </div>
                    <div class="form-group col-sm-12 col-md-6">
                      <label for="gender">{{ $t("User.gender") }}</label>
                      <multiselect
                        v-model="metadata.gender"
                        :options="gender"
                        :searchable="false"
                        :close-on-select="true"
                        :show-labels="false"
                        :placeholder="$t('Vacation.pickValue')"
                      ></multiselect>
                      <has-error :form="form" field="gender"></has-error>
                    </div>
                    <div class="form-group col-sm-12 col-md-6">
                      <label for="telephone">{{ $t("User.tele") }}</label>
                      <input
                        v-model="metadata.telephone"
                        type="text"
                        name="telephone"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.has('telephone') }"
                        autocomplete="off"
                      />
                      <has-error :form="form" field="telephone"></has-error>
                    </div>
                    <div class="form-group col-sm-12 col-md-6">
                      <label for="mobile">{{ $t("User.mobile") }}</label>
                      <input
                        v-model="metadata.mobile"
                        type="text"
                        name="mobile"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.has('mobile') }"
                        autocomplete="off"
                      />
                      <has-error :form="form" field="mobile"></has-error>
                    </div>
                    <div class="form-group col-sm-12 col-md-6">
                      <label for="fax">{{ $t("User.fax") }}</label>
                      <input
                        v-model="metadata.fax"
                        type="text"
                        name="fax"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.has('fax') }"
                        autocomplete="off"
                      />
                      <has-error :form="form" field="fax"></has-error>
                    </div>
                    <div class="form-group col-sm-12 col-md-6">
                      <label for="website">{{ $t("User.website") }}</label>
                      <input
                        v-model="metadata.website"
                        type="text"
                        name="website"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.has('website') }"
                        autocomplete="off"
                      />
                      <has-error :form="form" field="website"></has-error>
                    </div>
                    <div class="form-group col-sm-12 col-md-6">
                      <label for="birth_date">{{ $t("User.birthDate") }}</label>
                      <date-picker
                        v-model="metadata.birth_date"
                        lang="en"
                        type="date"
                        format="DD-MM-YYYY"
                        :minute-step="1"
                        value-type="format"
                        input-class="form-control"
                      ></date-picker>
                      <has-error :form="form" field="birth_date"></has-error>
                    </div>

                    <div class="form-group col-sm-12">
                      <label for="fax">{{ $t("User.signature") }}</label>
                      <quill-editor
                        id="comments-editor"
                        v-model="metadata.signature"
                        ref="myQuillEditor"
                        :options="editorOption"
                      ></quill-editor>
                      <has-error :form="form" field="fax"></has-error>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-success">
                    {{ $t("User.update") }}
                  </button>
                </form>
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import userApi from "../../api/users";
import roleApi from "../../api/roles";
import { mapGetters } from "vuex";
import DatePicker from "vue2-datepicker";

import { quillEditor } from "vue-quill-editor";

// require styles
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";

export default {
  data() {
    return {
      editMode: false,
      users: {},
      form: new Form({
        id: "",
        name: "",
        email: "",
        password: "",
        roles: "",
        vacation: "",
        type: "",
        skip_vacation_limit: false,
      }),
      metadata: new Form({
        user_id: "",
        id: "",
        first_name: "",
        last_name: "",
        company: "",
        address: "",
        language: "",
        gender: "",
        telephone: "",
        mobile: "",
        fax: "",
        website: "",
        birth_date: "",
        signature: "",
      }),
      editorOption: {
        modules: {
          toolbar: [
            ["bold", "italic", "underline", "strike"],
            ["blockquote", "code-block"],
            [{ list: "ordered" }, { list: "bullet" }],
            ["link", "image"],
          ],
        },
      },
      language: ["de", "en"],
      gender: ["male", "female"],
      roles: [],
      types: ["regular-user", "client"],
    };
  },
  methods: {
    getResults(page = 1) {
      this.$Progress.start();
      userApi
        .getEmployeesPaginated({ page: page })
        .then((response) => {
          this.users = response.data.data;
          this.$Progress.finish();
        })
        .catch((error) => {
          this.$Progress.fail();
        });
    },
    newModal() {
      this.editMode = false;
      this.form.reset();
      $("#Modal").modal("show");
    },
    editModal(item) {
      this.editMode = true;
      this.form.reset();
      $("#Modal").modal("show");
      this.form.fill(item);
      this.metadata.fill(item.metadata);
      this.metadata.user_id = item.id;
      this.form.roles = _.map(this.form.roles, function (value) {
        return { name: value.name };
      });
    },
    createMetadata(data) {
      this.$Progress.start();
      this.$store
        .dispatch("user/createMetadata", data)
        .then((response) => {
          this.$Progress.finish();
          $("#Modal").modal("hide");
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
    editMetadata(data) {
      // console.log(data);
      this.$Progress.start();
      this.$store
        .dispatch("user/editMetadata", data)
        .then((response) => {
          this.$Progress.finish();
          $("#Modal").modal("hide");
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
    createUser() {
      this.$Progress.start();
      this.form
        .post("/v-api/users")
        .then((response) => {
          $("#Modal").modal("hide");
          this.$Progress.finish();
          this.getResults();
          Toast.fire({
            type: "success",
            title: response.data.message,
          });
        })
        .catch((error) => {
          this.$Progress.fail();
          Toast.fire({
            type: "error",
            title: error.response.data.message,
          });
        });
    },
    editUser(id) {
      this.form.roles = this.form.roles.map(function (item) {
        return {
          id: item.id,
          name: item.name,
        };
      });

      this.$Progress.start();
      this.form
        .put("/v-api/users/" + id)
        .then((response) => {
          $("#Modal").modal("hide");
          this.$Progress.finish();
          this.getResults();
          Toast.fire({
            type: "success",
            title: response.data.message,
          });
        })
        .catch((error) => {
          this.$Progress.fail();
          Toast.fire({
            type: "error",
            title: error.response.data.message,
          });
        });
    },
    getAllRoles() {
      roleApi
        .getAll()
        .then((response) => {
          this.roles = _.map(response.data.data, function (key, value) {
            return { id: key.id, name: key.name };
          });
          this.$Progress.finish();
        })
        .catch((error) => {
          this.$Progress.fail();
        });
    },
    deleteUser(id) {
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
          userApi
            .delete(id)
            .then((response) => {
              this.$Progress.finish();
              this.getResults();
              Toast.fire({
                type: "success",
                title: response.data.message,
              });
            })
            .catch((error) => {
              this.$Progress.fail();
              Toast.fire({
                type: "error",
                title: "can't delete the role",
              });
            });
        }
      });
    },
  },
  mounted() {
    this.getResults();
    this.getAllRoles();
  },
  components: {
    quillEditor,
    DatePicker,
  },
};
</script>
