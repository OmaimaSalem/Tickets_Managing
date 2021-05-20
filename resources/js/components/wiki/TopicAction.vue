<template>
  <div class="card-tools">
    <div class="btn-group">
      <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
        <i class="fas fa-bars"></i>
      </button>
      <div class="dropdown-menu dropdown-menu-right" role="menu">
        <router-link :to="editArticleLink" class="dropdown-item">{{$t('Wiki.edit')}}</router-link>
        <div @click="deleteTopic(topic.id)" class="dropdown-item">{{$t('Wiki.delete')}}</div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  data() {
    return {
      topic_id: this.topic.id
    }
  },
  props: {
    topic: { type: Object, required: true }
  },
  methods: {
    deleteTopic(id) {
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
      }).then(result => {
        if (result.value) {
          this.$store
            .dispatch("wiki/DeleteTopic", id)
            .then(response => {
              this.$router.push({ name: "topics.list"});
              Toast.fire({
                type: "success",
                title: response.data.message
              });
            })
            .catch(error => {
              this.$Progress.fail();
              console.log(error);
              Toast.fire({
                type: "error",
                title: error.response.data.message
              });
            });
        }
      });
    }
  },
  computed: {
    editArticleLink() {
      return { name: "topic-edit", params: { id: this.topic.id } };
    }
  }
};
</script>

<style>
</style>
