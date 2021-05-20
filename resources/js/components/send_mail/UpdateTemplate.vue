<template>
<div class="modal fade" id="UpdateTemplate" tabindex="-1" role="dialog" aria-labelledby="UpdateTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="UpdateTemplateModalLabel">Updatde Template</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form @submit.prevent="updateTemplate" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="titel">Title</label>
                        <input type="text" name="title" class="form-control" v-model="form.title">
                        <has-error :form="form" field="title"></has-error>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" name="subject" class="form-control" v-model="form.subject">
                        <has-error :form="form" field="subject"></has-error>
                    </div>
                    <div class="form-group">
                        <label for="body">Body</label>
                        <quill-editor
                          id="comments-editor"
                          v-model="form.body"
                          ref="myQuillEditor"
                          :options="editorOption"
                        ></quill-editor>
                        <has-error :form="form" field="body"></has-error>

                    </div>
                    <div class="form-group">
                      <label for="category_id">Category</label>
                      <select class="form-control" name="" v-model='form.mail_template_category_id'>
                        <option v-for="category in resultCategorires" :key="category.id" :value="category.id">{{category.name}}</option>
                      </select>
                     <has-error :form="form" field="category_id"></has-error>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
</template>
<script>
import { quillEditor } from "vue-quill-editor";
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";
import {mapGetters} from "vuex";
export default {
    props: ['form'],
    data() {
        return {
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
        updateTemplate() {
            this.$Progress.start();
            this.$store
                .dispatch("template/editTemplate", this.form)
                .then(response => {
                    $("#UpdateTemplate").modal("hide");
                    this.$Progress.finish();
                    Toast.fire({
                        type: "success",
                        title: response.data.message
                    });
                })
                .catch(error => {
                    console.log(error);
                    this.$Progress.fail();
                    if (error.response) {
                        this.form.errors.errors = error.response.data.errors;
                    }
                });
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
