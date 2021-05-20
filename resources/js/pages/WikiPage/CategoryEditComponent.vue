<template>
  <form @submit.prevent="onPublish(activeCategory.id)">
    <fieldset :disabled="inProgress">
      <fieldset class="form-group">
        <input
          type="text"
          class="form-control form-control-lg"
          v-model="form.name"
          :placeholder="$t('Wiki.categoryTitle')"
          :class="{ 'is-invalid': errors.hasOwnProperty('name') }"
        />
        <div v-if="errors.hasOwnProperty('name')" class="invalid-feedback" v-text="errors.name[0]"></div>
      </fieldset>
      <fieldset class="form-group">
        <textarea
          class="form-control"
          rows="8"
          v-model="form.description"
          :placeholder="$t('Wiki.categoryDesc')"
          :class="{ 'is-invalid': errors.hasOwnProperty('description') }"
        ></textarea>
        <div
          v-if="errors.hasOwnProperty('description')"
          class="invalid-feedback"
          v-text="errors.description[0]"
        ></div>
      </fieldset>
      <fieldset class="form-group">
        <label for="allowed_users">{{$t('Wiki.allowedUsers')}}</label>
        <multiselect
          v-model="form.allowed_users"
          :options="regularUsers"
          :multiple="true"
          :close-on-select="false"
          :clear-on-select="false"
          :preserve-search="true"
          :placeholder="$t('Wiki.pickUsers')"
          label="name"
          track-by="name"
          :preselect-first="true"
          @input="selectedUsers"
        >
          <template slot="selection" slot-scope="{ values, search, isOpen }">
            <span
              class="multiselect__single"
              v-if="values.length &amp;&amp; !isOpen"
            >{{ values.length }} {{$t('Wiki.selectedUsers')}}</span>
          </template>
        </multiselect>
        <div
          v-if="errors.hasOwnProperty('allowed_users')"
          class="invalid-feedback"
          v-text="errors.allowed_users[0]"
        ></div>
      </fieldset>
    </fieldset>
    <button class="btn btn-lg pull-xs-right btn-primary" type="submit">{{$t('Wiki.publishCategory')}}</button>
  </form>
</template>

<script>
import store from "../../store/index";
import { mapGetters } from "vuex";

export default {
  data() {
    return {
      inProgress: false,
      form: {
        name: "",
        description: "",
        allowed_users: [],
        selected_users: []
      },
      errors: {}
    };
  },
  async beforeRouteUpdate(to, from, next) {
    // Reset state if user goes from /editor/:id to /editor
    // The component is not recreated so we use to hook to reset the state.
    await store.dispatch("wiki/ResetTopic");
    return next();
  },
  async beforeRouteLeave(to, from, next) {
    await store.dispatch("wiki/ResetTopic");
    next();
  },
  async beforeRouteLeave(to, from, next) {
    await store.dispatch("wiki/ResetTopic");
    next();
  },
  created() {
    this.$store.dispatch("wiki/ResetTopic");
    this.$store.dispatch("regularUser/getRegularUser");
    if (this.$route.params.id !== undefined) {
      this.$store
        .dispatch("wiki/FetchCategory", this.$route.params.id)
        .then(response => {
          this.form = response.data.data;
        });
    }
  },
  methods: {
    onPublish(id) {
      let action = id ? "wiki/EditCategory" : "wiki/PublishCategory";
      this.inProgress = true;
      this.$store
        .dispatch(action, this.form)
        .then(response => {
          this.inProgress = false;
          this.$router.push({
            name: "category",
            params: { id: response.data.data.id }
          });
        })
        .catch(error => {
          this.inProgress = false;
          this.errors = error.response.data.errors;
        });
    },
    selectedUsers(value) {
      let usersId = [];
      value.forEach(item => usersId.push(item.id));
      this.form.selected_users = usersId;
    }
  },
  computed: {
    ...mapGetters({
      activeCategory: "wiki/category",
      regularUsers: "regularUser/activeRegularUser"
    })
  }
};
</script>

<style>
</style>
