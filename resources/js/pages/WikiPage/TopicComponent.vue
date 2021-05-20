<template>
  <section>
    <WikiHeader />
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">{{topic.name}}</h3>
        <span class="small text-muted">
          <router-link
            :to="{name: 'category', params: {id: topic.category.id}}"
            class="ml-2"
          >( {{topic.category.name}} )</router-link>
        </span>
        <topic-action :topic="topic"></topic-action>
      </div>
      <div class="card-body">
        <div v-html="topic.description"></div>
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
    Promise.all([store.dispatch("wiki/FetchTopic", to.params.id)]).then(() => {
      next();
    });
  },
  computed: {
    ...mapGetters({ topic: "wiki/topic" })
  }
};
</script>

<style>
</style>