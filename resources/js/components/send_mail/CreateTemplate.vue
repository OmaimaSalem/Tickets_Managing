<template>
<div class="modal fade" id="CreateTemplate" tabindex="-1" role="dialog" aria-labelledby="CreateTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="CreateTemplateModalLabel">Create Template</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form @submit.prevent="createTemplate" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="titel">Title</label>
                        <input type="text" name="title" class="form-control" v-model="template.title">
                        <has-error :form="template" field="title"></has-error>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" name="subject" class="form-control" v-model="template.subject">
                        <has-error :form="template" field="subject"></has-error>
                    </div>
                    <div class="form-group">
                        <label for="body">Body</label>
                        <quill-editor
                          id="comments-editor"
                          v-model="template.body"
                          ref="myQuillEditor"
                          :options="editorOption"
                        ></quill-editor>
                        <has-error :form="template" field="body"></has-error>
                    </div>
                    <div class="form-group">
                      <label for="category_id">Category</label>
                      <select class="form-control" name="" v-model='template.mail_template_category_id'>
                        <option v-for="category in resultCategorires" :key="category.id" :value="category.id">{{category.name}}</option>
                      </select>
                      <has-error :form="template" field="mail_template_category_id"></has-error>
                    </div>
                    <div class="form-group">
                      <label for="uploadfile">Upload Files</label>
                      <input type="file" class="form-control" @change="assignFile">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>
</template>
<script>
import {mapGetters} from "vuex";
import axios from "axios";
import { quillEditor } from "vue-quill-editor";
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";

export default {
    data() {
      return {
        template: new Form({
          title: '',
          subject: '',
          body: '',
          mail_template_category_id: '',
          file: null,
          fileUrl: null,
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
      }
    },
    methods: {
        createTemplate() {
            let form = new FormData();

            Object.keys(this.template).forEach(key => {
                form.append(key, this.template[key]);
            });
            this.$Progress.start();
            this.$store
                .dispatch("template/createTemplate", form)
                .then(response => {
                    $("#CreateTemplate").modal("hide");
                    this.$Progress.finish();
                    Toast.fire({
                        type: "success",
                        title: response.data.message
                    });
                })
                .catch(error => {
                    this.$Progress.fail();
                    if (error.response) {
                        this.template.errors.errors = error.response.data.errors;
                    }
                });
        },
        assignFile(event) {
            this.template.file = event.target.files[0];
            this.template.fileUrl = URL.createObjectURL(event.target.files[0]);
            this.template.upload= true;
        },
    },
    computed: {
        ...mapGetters({
            categories: "template/allCategories",
        }),
        resultCategorires() {
            return this.categories.data;
        }
    },
    components: {
        quillEditor
      }
}
</script>
