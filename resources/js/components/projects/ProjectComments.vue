<template>
  <div class="card" id="comments">
    <div class="card-header">
      <h5 class="card-title m-0">{{$t('Project.comments')}}</h5>
    </div>
    <div class="card-body">
      <comments v-for="comment in comments.data" :key="comment.id" :comment="comment"></comments>
    </div>
    <div class="p-4">
      <hr />
      <form @submit.prevent="createComment(form)">
        <quill-editor
          id="comments-editor"
          v-model="form.comment"
          ref="myQuillEditor"
          :options="editorOption"
        ></quill-editor>

        <has-error :form="form" field="comment"></has-error>

        <br />
        <button class="btn btn-primary mt-4" style="background-color: #3490dc">
          Send
          <i class="fab fa-telegram-plane fa-fw"></i>
        </button>
      </form>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import { quillEditor } from "vue-quill-editor";

// require styles
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";

export default {
  data() {
    return {
      project_id: this.$route.params.id,
      form: new Form({
        project_id: this.$route.params.id,
        comment: ""
      }),
      editorOption: {
        modules: {
          toolbar: [
            ["bold", "italic", "underline", "strike"],
            ["blockquote", "code-block"],
            [{ list: "ordered" }, { list: "bullet" }]
          ]
        }
      }
    };
  },
  methods: {
    getComments(id) {
      this.$store
        .dispatch("comment/getCommentsPerProject", id)
        .then()
        .catch();
    },
    createComment(data) {
      this.$store
        .dispatch("comment/createCommentForProject", data)
        .then(response => {
          this.form.comment = null;
        })
        .catch(error =>{
          this.$Progress.fail();
          this.form.errors.errors = error.response.data.errors;
        });
    }
  },
  mounted() {
    // get all comments for the project
    this.getComments(this.project_id);
  },
  computed: {
    ...mapGetters({
      comments: "comment/activeComments"
    })
  },
  components: {
    quillEditor
  }
};
</script>

<style scoped>
#comments-editor {
  height: 100px;
}
</style>
