<template>
  <div v-if="isLoading" class="topics-preview">{{$t('Wiki.loadingTopics')}}</div>
  <div v-else>
    <WikiHeader />
    <div v-if="topics.data.length === 0" class="topics-preview">{{$t('Wiki.noTopic')}}.</div>
    <section v-else>
      <TopicPreview v-for="(topic, index) in topics.data" :topic="topic" :key="topic.name + index" />
      <pagination
        align="right"
        size="small"
        :show-disabled="true"
        :data="topics"
        :limit="3"
        @pagination-change-page="onPaginate"
      ></pagination>
    </section>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  data() {
    return {
      isLoading: true,
      query: ''
    };
  },
  beforeRouteUpdate(to, from, next) {
    this.fetchTopics(to.params.page, this.$route.query.q);
    next();
  },
  mounted() {
    this.fetchTopics(
      this.$route.params.page ? this.$route.params.page : 1,
      this.$route.query.q
    );
  },
  methods: {
    onPaginate(page) {
      this.$router.push({
        name: "topics.list",
        params: { page },
        query: { q: this.$route.query.q }
      });
    },
    fetchTopics(page = 1, query = '') {
      this.isLoading = false;
      this.$Progress.start();
      this.$store
        .dispatch("wiki/FetchTopics", { page: page, query: query })
        .then(response => {
          this.$Progress.finish();
        });
    }
  },
  computed: {
    ...mapGetters({
      topics: "wiki/topics"
    })
  }
};
</script>

<style>
</style>
