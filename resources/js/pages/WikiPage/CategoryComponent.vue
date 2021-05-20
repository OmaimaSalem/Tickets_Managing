<template>
  <section>
    <CategoriesHeader />
    <div class="card">
      <div class="card-header">
        <h3 class="card-title mr-2">{{category.name}}</h3>
        <span class="small text-muted">({{category.topics_count}}) {{$t('Wiki.topic')}}</span>
        <category-action :category="category"></category-action>
      </div>
      <div class="card-body">
        <p>{{category.description}}</p>
        <ul v-if="category.topics">
          <li v-for="(topic, index) in category.topics" :key="index">
            <router-link
              :to="{name: 'topic', params: {id: topic.id}}"
              class="preview-link"
            >{{topic.name}}</router-link>
          </li>
        </ul>
      </div>
    </div>
  </section>
</template>

<script>
import { mapGetters } from "vuex";
import store from "../../store/index";

export default {
  props: {
    id: {
      required: true
    }
  },
  beforeRouteEnter(to, from, next) {
    Promise.all([store.dispatch("wiki/FetchCategory", to.params.id)]).then(
      () => {
        next();
      }
    );
  },
  computed: {
    ...mapGetters({ category: "wiki/category" }),
    topicLink() {
      return {
        name: "topic",
        params: {
          id: this.topic.id
        }
      };
    }
  }
};
</script>

<style>
</style>
