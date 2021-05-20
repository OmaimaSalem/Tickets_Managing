<template>
  <section>
    <div class="card card-default">
      <div class="card-header">
          <router-link :to="topicLink" class="preview-link">{{topic.name.substring(0,120)+"..."}}</router-link>
        <topic-action :topic="topic"></topic-action>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  props: {
    topic: { type: Object, required: true }
  },
  computed: {
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

<style scoped>
.card-body img {
    width: 100%;
}
</style>