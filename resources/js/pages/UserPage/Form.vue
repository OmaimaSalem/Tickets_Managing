<template>
  <div class="row justify-content-center">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="card-title font-weight-light">{{$t('User.accountSetting')}}</div>
        </div>
        <form @submit.prevent="editUser(form)" @keydown="form.onKeydown($event)" autocomplete="off">
          <div class="card-body">
            <div class="row">
              <div class="form-group col-sm-12 col-md-3">
                <label for="name">{{$t('User.username')}}</label>
                <input
                  v-model="form.name"
                  type="text"
                  name="name"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('name') }"
                  autocomplete="off"
                />
                <has-error :form="form" field="name"></has-error>
              </div>
              <div class="form-group col-sm-12 col-md-3">
                <label for="email">{{$t('User.email')}}</label>
                <input
                  v-model="form.email"
                  type="email"
                  name="email"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('email') }"
                  autocomplete="off"
                />
                <has-error :form="form" field="email"></has-error>
              </div>
              <div class="form-group col-sm-12 col-md-3">
                <label for="password">{{$t('User.password')}}</label>
                <input
                  v-model="form.password"
                  type="password"
                  name="password"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('password') }"
                  autocomplete="off"
                />
                <has-error :form="form" field="password"></has-error>
              </div>
              <div v-if="form.type != 'client'" class="form-group col-sm-12 col-md-3">
                <label for="role">{{$t('User.role')}}</label>
                <multiselect
                  v-model="form.roles"
                  :multiple="true"
                  :options="roles"
                  :close-on-select="true"
                  :placeholder="$t('User.selectOne')"
                  label="name"
                  track-by="name"
                ></multiselect>
                <has-error :form="form" field="role"></has-error>
              </div>
              <div class="form-group col-sm-12 col-md-3">
                <label for="type">{{$t('User.type')}}</label>
                <multiselect
                  v-model="form.type"
                  :options="types"
                  :searchable="false"
                  :close-on-select="true"
                  :show-labels="false"
                  :placeholder="$t('User.pickValue')"
                ></multiselect>
                <has-error :form="form" field="type"></has-error>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">{{$t('User.update')}}</button>
          </div>
        </form>
      </div>
      <div class="card">
        <div class="card-header">
          <div class="card-title font-weight-light">{{$t('User.accountDetails')}}</div>
        </div>
        <form
          @submit.prevent="editMode ? editMetadata(metadata) : createMetadata(metadata)"
          @keydown="form.onKeydown($event)"
          autocomplete="off"
        >
          <div class="card-body">
            <div class="row">
              <div class="form-group col-sm-12 col-md-3">
                <label for="first_name">{{$t('User.firstName')}}</label>
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
              <div class="form-group col-sm-12 col-md-3">
                <label for="last_name">{{$t('User.lastName')}}</label>
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
              <div class="form-group col-sm-12 col-md-3">
                <label for="company">{{$t('User.companyName')}}</label>
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
              <div class="form-group col-sm-12 col-md-3">
                <label for="address">{{$t('User.address')}}</label>
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
              <div class="form-group col-sm-12 col-md-3">
                <label for="language">{{$t('User.lang')}}</label>
                <multiselect
                  v-model="metadata.language"
                  :options="language"
                  :searchable="false"
                  :close-on-select="true"
                  :show-labels="false"
                  :placeholder="$t('User.pickValue')"
                ></multiselect>
                <has-error :form="form" field="language"></has-error>
              </div>
              <div class="form-group col-sm-12 col-md-3">
                <label for="gender">{{$t('User.gender')}}</label>
                <multiselect
                  v-model="metadata.gender"
                  :options="gender"
                  :searchable="false"
                  :close-on-select="true"
                  :show-labels="false"
                  :placeholder="$t('User.pickValue')"
                ></multiselect>
                <has-error :form="form" field="gender"></has-error>
              </div>
              <div class="form-group col-sm-12 col-md-3">
                <label for="telephone">{{$t('User.tele')}}</label>
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
              <div class="form-group col-sm-12 col-md-3">
                <label for="mobile">{{$t('User.mobile')}}</label>
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
              <div class="form-group col-sm-12 col-md-3">
                <label for="fax">{{$t('User.fax')}}</label>
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
              <div class="form-group col-sm-12 col-md-3">
                <label for="website">{{$t('User.website')}}</label>
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
              <div class="form-group col-sm-12 col-md-3">
                <label for="eBay_user">{{$t('User.eBayUser')}}</label>
                <input
                  v-model="metadata.eBay_user"
                  type="text"
                  name="eBay_user"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('eBay_user') }"
                  autocomplete="off"
                />
                <has-error :form="form" field="eBay_user"></has-error>
              </div>
              <div class="form-group col-sm-12 col-md-3">
                <label for="tax_number">{{$t('User.taxNum')}}</label>
                <input
                  v-model="metadata.tax_number"
                  type="text"
                  name="tax_number"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('tax_number') }"
                  autocomplete="off"
                />
                <has-error :form="form" field="eBay_user"></has-error>
              </div>
              <div class="form-group col-sm-12 col-md-3">
                <label for="tax_id">{{$t('User.tax')}} ID</label>
                <input
                  v-model="metadata.tax_id"
                  type="text"
                  name="tax_id"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('tax_id') }"
                  autocomplete="off"
                />
                <has-error :form="form" field="tax_id"></has-error>
              </div>
              <div class="form-group col-sm-12 col-md-3">
                <label for="commerical_register">{{$t('User.commercialRegister')}}</label>
                <input
                  v-model="metadata.commerical_register"
                  type="text"
                  name="commerical_register"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('commerical_register') }"
                  autocomplete="off"
                />
                <has-error :form="form" field="commerical_register"></has-error>
              </div>
              <div class="form-group col-sm-12 col-md-3">
                <label for="street_number">{{$t('User.streetNumber')}}</label>
                <input
                  v-model="metadata.street_number"
                  type="text"
                  name="street_number"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('street_number') }"
                  autocomplete="off"
                />
                <has-error :form="form" field="street_number"></has-error>
              </div>
              <div class="form-group col-sm-12 col-md-3">
                <label for="additional_address">{{$t('User.additionalAddress')}}</label>
                <input
                  v-model="metadata.additional_address"
                  type="text"
                  name="additional_address"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('additional_address') }"
                  autocomplete="off"
                />
                <has-error :form="form" field="additional_address"></has-error>
              </div>
              <div class="form-group col-sm-12 col-md-3">
                <label for="postal_code">{{$t('User.postalCode')}}</label>
                <input
                  v-model="metadata.postal_code"
                  type="text"
                  name="postal_code"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('postal_code') }"
                  autocomplete="off"
                />
                <has-error :form="form" field="postal_code"></has-error>
              </div>
              <div class="form-group col-sm-12 col-md-3">
                <label for="city_code">{{$t('User.cityCode')}}</label>
                <input
                  v-model="metadata.city_code"
                  type="text"
                  name="city_code"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('city_code') }"
                  autocomplete="off"
                />
                <has-error :form="form" field="city_code"></has-error>
              </div>
              <div class="form-group col-sm-12 col-md-3">
                <label for="country">{{$t('User.country')}}</label>
                <input
                  v-model="metadata.country"
                  type="text"
                  name="country"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('country') }"
                  autocomplete="off"
                />
                <has-error :form="form" field="country"></has-error>
              </div>
              <div class="form-group col-sm-12 col-md-3">
                <label for="state">{{$t('User.state')}}</label>
                <input
                  v-model="metadata.state"
                  type="text"
                  name="state"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('state') }"
                  autocomplete="off"
                />
                <has-error :form="form" field="state"></has-error>
              </div>
              <div class="form-group col-sm-12 col-md-3">
                <label for="birth_date">{{$t('User.birthDate')}}</label>
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
                <label for="fax">{{$t('User.signature')}}</label>
                <quill-editor
                  id="comments-editor"
                  v-model="metadata.signature"
                  ref="myQuillEditor"
                  :options="editorOption"
                ></quill-editor>
                <has-error :form="form" field="fax"></has-error>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">{{$t('User.update')}}</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</template>

<script>
import roleApi from "../../api/roles";
import DatePicker from "vue2-datepicker";
import { mapActions, mapGetters } from "vuex";

import { quillEditor } from "vue-quill-editor";

// require styles
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";

export default {
  components: { DatePicker, quillEditor },
  data() {
    return {
      editMode: true,
      userId: this.$route.params.id,
      form: new Form({
        id: "",
        name: "",
        email: "",
        password: "",
        roles: "",
        type: ""
      }),
      metadata: new Form({
        user_id: this.$route.params.id,
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
        eBay_user:"",
        tax_number:"",
        tax_id:"",
        postal_code:"",
        city_code: "",
        country: "",
        state: "",
        additional_address: "",
        commerical_register:"",
        street_number:""
      }),
      editorOption: {
        modules: {
          toolbar: [
            ["bold", "italic", "underline", "strike"],
            ["blockquote", "code-block"],
            [{ list: "ordered" }, { list: "bullet" }],
            ['link', 'image']
          ]
        }
      },
      roles: [],
      types: ["regular-user", "client"],
      language: ["de", "en"],
      gender: ["male", "female"]
    };
  },
  methods: {
    ...mapActions("user", ["getUserProfile"]),
    editUser(data) {
      this.$Progress.start();
      this.$store
        .dispatch("user/editUser", data)
        .then(response => {
          this.$Progress.finish();
          Toast.fire({
            type: "success",
            title: response.data.message
          });
          this.$router.push({
            name: "profile",
            params: { id: this.userId }
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
    createMetadata(data) {
      this.$Progress.start();
      this.$store
        .dispatch("user/createMetadata", data)
        .then(response => {
          this.$Progress.finish();
          Toast.fire({
            type: "success",
            title: response.data.message
          });
          this.$router.push({
            name: "profile",
            params: { id: this.userId }
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
    editMetadata(data) {
      this.$Progress.start();
      this.$store
        .dispatch("user/editMetadata", data)
        .then(response => {
          this.$Progress.finish();
          Toast.fire({
            type: "success",
            title: response.data.message
          });
          this.$router.push({
            name: "profile",
            params: { id: this.userId }
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
    getAllRoles() {
      roleApi
        .getAll()
        .then(response => {
          this.roles = _.map(response.data.data, function(key, value) {
            return { id: key.id, name: key.name };
          });
          this.$Progress.finish();
        })
        .catch(error => {
          this.$Progress.fail();
        });
    },
    async loadEditData() {
      this.$Progress.start();
      let response = await this.getUserProfile(this.userId)
        .then(() => {
          this.$Progress.finish();
          this.loading = false;
          this.form.fill(this.user);
          if (this.user.metadata !== null) {
            this.metadata.fill(this.user.metadata);
            this.metadata.user_id = this.$route.params.id;
          } else {
            this.editMode = false;
          }
        })
        .catch(error => {
          this.$Progress.fail();
        });
    }
  },
  mounted() {
    this.getAllRoles();
    this.loadEditData();
  },
  computed: {
    ...mapGetters({
      user: "user/userProfile"
    })
  }
};
</script>

<style scoped>
.mx-datepicker {
  display: block;
  width: 100%;
}
</style>
