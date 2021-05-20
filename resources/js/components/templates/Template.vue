<template>
  <div class="row">
    <div class="col-md-12 py-0">
        <ul class="mailbox-attachments d-flex align-items-stretch clearfix mb-0">
            <li v-for="category in categories.data"  :key="category.id">
              <div class="mailbox-attachment-info">
                <span
                  href="#"
                  class="mailbox-attachment-name"
                >
                  <i class="fas fa-paperclip"></i>
                  {{ category.name }}
                </span>
              </div>
              <span>
                <select class="form-control" @change="templateSelected($event)">
                  <option value="">Select Template</option>
                  <option v-for="template in category.templates.data"  :key="template.id" :value="template.id">{{ template.title }}</option>
                </select>
              </span>
            </li>
          </ul>
    </div>
  </div class="row">
</template>
<script>
import { mapGetters } from "vuex";
import moment from "moment";
import {quillEditor} from "vue-quill-editor";
// require styles
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";
export default {
  name: 'TemplateComponent',
  data() {
    return {
      category: '',
      editorOption: {
        placeholder: 'Type here..',
        modules: {
          toolbar: [
            ["bold", "italic", "underline", "strike"],
            ["blockquote", "code-block"],
            [{
              list: "ordered"
            }, {
              list: "bullet"
            }]
          ]
        }
      },
    }
  },
  methods: {
    getCategories() {
      this.$store
          .dispatch("template/getCategories")
          .then(() => {
              this.$Progress.finish();
              this.isLoading = false;
          })
          .catch(error => {
              this.$Progress.fail();
              this.isLoading = false;
          });
    },
    getTemplates() {
      this.$store
          .dispatch("template/getTemplates")
          .then(response => {
              this.$Progress.finish();
              this.isLoading = false;
          })
          .catch(error => {
              this.$Progress.fail();
              this.isLoading = false;
          });
    },
    templateSelected(event) {
      this.templates.data.filter(template => {
        if(template.id == event.target.value) {
          this.$emit('selectedTemplate', template.body);
        }
      });
    },
  },
  computed: {
      ...mapGetters({
          user: "user/activeSingleUser",
          categories: "template/allCategories",
          templates: "template/allTemplates",
      }),
  },
  components: {
    quillEditor
  },
  mounted() {
    this.getCategories();
    this.getTemplates();
  }
}  
</script>
