<template>
  <section>
    <div class="col-md-10 offset-md-1 col-xs-12">
      <ListErrors :errors="errors" />
      <form @submit.prevent="onPublish(activeTopic.id)">
        <fieldset :disabled="inProgress">
          <fieldset class="form-group">
            <input
              type="text"
              class="form-control form-control-lg"
              v-model="name"
              :placeholder="$t('Wiki.topicTitle')"
              :class="{ 'is-invalid': errors.hasOwnProperty('name') }"
            />
            <div
              v-if="errors.hasOwnProperty('name')"
              class="invalid-feedback"
              v-text="errors.name[0]"
            ></div>
          </fieldset>
          <fieldset class="form-group">
            <wysiwyg
              v-model="description"
              :options="wysiwygOptions"
              :class="{ 'is-invalid': errors.hasOwnProperty('description') }"
            />
            <div
              v-if="errors.hasOwnProperty('description')"
              class="invalid-feedback"
              v-text="errors.description[0]"
            ></div>
          </fieldset>
          <fieldset class="form-group">
            <multiselect
              v-model="category"
              :options="categories"
              :allow-empty="false"
              :show-labels="false"
              :placeholder="$t('Wiki.selectOne')"
              label="name"
              track-by="name"
            ></multiselect>
            <div
              v-if="errors.hasOwnProperty('category_id')"
              class="invalid-feedback"
              v-text="errors.category_id[0]"
            ></div>
          </fieldset>
        </fieldset>
        <button class="btn btn-lg pull-xs-right btn-primary" type="submit">{{$t('Wiki.publishTopics')}}</button>
      </form>
    </div>
  </section>
</template>

<script>
import store from "../../store/index";
import { mapGetters, mapActions, mapState } from "vuex";

export default {
  data() {
    return {
      inProgress: false,
      errors: {},
      wysiwygOptions: {
        image: {
          uploadURL: "/v-api/topics/upload-image",
          dropzoneOptions: {
            params: {
              _token: null
            }
          }
        },
        maxHeight: "500px"
      }
    };
  },
  async beforeRouteUpdate(to, from, next) {
    // Reset state if user goes from /editor/:id to /editor
    // The component is not recreated so we use to hook to reset the state.
    await store.dispatch("wiki/ResetTopic");
    return next();
  },
  async beforeRouteEnter(to, from, next) {
    // SO: https://github.com/vuejs/vue-router/issues/1034
    // If we arrive directly to this url, we need to fetch the article
    await store.dispatch("wiki/ResetTopic");
    await store.dispatch("wiki/FetchAllCategories");
    if (to.params.id !== undefined) {
      await store.dispatch("wiki/FetchTopic", to.params.id);
    }
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
  methods: {
    onPublish(id) {
      let action = id ? "wiki/EditTopic" : "wiki/PublishTopic";
      this.inProgress = true;
      this.$store
        .dispatch(action)
        .then(response => {
          this.inProgress = false;
          this.$router.push({
            name: "topic",
            params: { id: response.data.data.id }
          });
        })
        .catch(error => {
          this.inProgress = false;
          this.errors = error.response.data.errors;
        });
    }
  },
  computed: {
    ...mapGetters({
      activeTopic: "wiki/topic",
      categories: "wiki/allCategories"
    }),
    name: {
      get() {
        return this.$store.state.wiki.activeTopic.name;
      },
      set(value) {
        this.$store.commit("wiki/UPDATE_NAME", value);
      }
    },
    description: {
      get() {
        return this.$store.state.wiki.activeTopic.description;
      },
      set(value) {
        this.$store.commit("wiki/UPDATE_DISCREPTION", value);
      }
    },
    category: {
      get() {
        return this.$store.state.wiki.activeTopic.category;
      },
      set(value) {
        this.$store.commit("wiki/UPDATE_CATEGORY_VALUE", value);
      }
    }
  },
  mounted() {
    this.wysiwygOptions.image.dropzoneOptions.params._token = document.querySelector(
      'meta[name="csrf-token"]'
    ).content;
  }
};
</script>

<style>
@import "~vue-wysiwyg/dist/vueWysiwyg.css";
.editr--content {
  background: #fff;
}
</style>
