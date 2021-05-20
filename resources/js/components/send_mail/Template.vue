<template>
<span class="send-mail">
    <!-- Button trigger modal -->
    <div data-toggle="modal" data-target="#sendMailModal" class="btn btn-sm btn-blue"><i class="text-yellow fas fa-envelope"></i>&nbsp;&nbsp;{{$t("Template.send_mail")}}</div>

    <!-- Modal -->
    <div class="modal fade" id="sendMailModal" tabindex="-1" role="dialog" aria-labelledby="sendMailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="form-group">
                      <select class="form-control" @change="categorySlected($event)">
                        <option value="">Select Category</option>
                        <option v-for="category in categories.data"  :key="category.id" :value="category.id">{{ category.name }}</option>
                      </select>
                    </div>
                    <div v-if="category" class="form-group ml-3">
                      <select class="form-control" @change="templateSelected($event)">
                        <option value="">Select Template</option>
                        <option v-for="template in category.templates.data"  :key="template.id" :value="template.id">{{ template.title }}</option>
                      </select>
                    </div>
                    <div class="row">
                      <div class="form-group col-sm-12">
                        <div class="bg-white" v-if="template_files && template_files.length > 0">
                          <ul class="mailbox-attachments d-flex align-items-stretch clearfix">
                            <li v-for="file in template_files" :key="file.id">
                              <div class="mailbox-attachment-info">
                                <a
                                  target="_plank"
                                  :href="file.attachment_path | filePath"
                                  class="mailbox-attachment-name"
                                >
                                  <i class="fas fa-paperclip"></i>
                                  {{ file.attachment_path | fileName }}
                                </a>
                              </div>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                  <div class="row mb-3">
                    <div class="col">
                      <div class="form-group mt-3 col-sm-12">
                        <Multiselect
                          v-model="form.to"
                          :multiple="true"
                          :options="users"
                          :close-on-select="true"
                          placeholder="Send To"
                          label="name"
                          track-by="name"
                        ></Multiselect>
                      </div>
                      <div class="input-group">
                        <input type="text" placeholder="Subject" v-model="form.subject" name="subject" class="form-control">
                        <span class="input-group-append">
                          <button type="button" @click="sendMail" class="btn btn-primary">
                            <div v-if="loading" class="spinner-grow spinner-grow-sm" role="status">
                              <span class="sr-only">Loading...</span>
                            </div>
                            Send
                          </button>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      <div class="form-group">
                        <label for="uploadfile">Upload Files</label>
                        <input type="file" multiple class="form-control" :loading="loading" @change="assignFile">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      <wysiwyg v-model="form.body" />
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</span>
</template>
<script>
import {
    mapGetters
} from "vuex";
import Multiselect from 'vue-multiselect';
import mailApi from "../../api/mail_templates";
export default {
    name: 'SendMail',
    data() {
        return {
          form: new Form({
            subject: '',
            body: this.$t("Template.hi_message"),
            to: '',
            touser: [],
          }),
          files: [],
          category: '',
          template_files: null,
          loading: false,
        }
    },
    props: ['users','user'],
    methods: {
        assignFile(event) {

            let selectedFiles = event.target.files;

            if(!selectedFiles.length) {
              return false;
            }
            for(let i=0;i<selectedFiles.length;i++) {
              this.files.push(selectedFiles[i])
            }
            console.log(this.files);
        },
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
        getUsers() {
          this.$store
              .dispatch("user/getUsers",{dropdown:true})
              .then(() => {
                  this.$Progress.finish();
                  this.isLoading = false;
              })
              .catch(error => {
                  this.$Progress.fail();
                  this.isLoading = false;
              });
        },
        sendMail() {
          if(!this.form.to) {
            Toast.fire({
                type: "error",
                title: "Please select user to send mail to."
            });
          } else {
            if(this.form.to.length > 1) {
              this.form.touser = [];
              Object(this.form.to).forEach(usermail => {
                this.form.touser.push(usermail.email)
              })
            } else {
              this.form.touser = [];
              this.form.touser.push(this.form.to[0].email)
            }
            let formData = new FormData();
            for( var i = 0; i < this.files.length; i++ ){
              let file = this.files[i];

              formData.append('files[' + i + ']', file);
            }
            formData.append('touser', this.form.touser);
            formData.append('subject', this.form.subject);
            formData.append('body', this.form.body);
            this.loading = true;
            axios.post( '/v-api/mail-templates/send',
              formData,
                  {
                  headers: {
                      'Content-Type': 'multipart/form-data'
                  }
                }
              ).then(response => {
                    this.$Progress.finish();
                    Toast.fire({
                        type: "success",
                        title: response.data.message
                    });
                    this.loading = false
                    $("#sendMailModal").modal("hide");
                })
                .then(() => {
                  this.form.subject = '';
                  this.form.body = '';
                  this.form.to = '';
                  this.files = [];
                  this.category = '';
                  this.template_files = null;
                })
                .catch(error => {
                    this.$Progress.fail();
                    Toast.fire({
                        type: "error",
                        title: error.response.data.message
                    });
                });
          }
        },
        categorySlected(event) {
          this.categories.data.filter(category => {
            if(category.id == event.target.value) {
              this.category = category
            }
          });
        },
        templateSelected(event) {
          this.category.templates.data.filter(template => {
            if(template.id == event.target.value) {
              this.form.subject = template.subject;
              this.form.body = template.body;
              this.template_files = template.files;
            }
          });
        },
    },
    computed: {
        ...mapGetters({
            categories: "template/allCategories",
        }),
    },
    components: {
      Multiselect,
    },
    filters: {
      filePath(path) {
        let str = path;
        let n = str.indexOf("public");
        return "/storage" + str.substring(n + 9);
      },
      fileName(path) {
        let str = path;
        let n = str.lastIndexOf("/");
        return str.substring(n + 1);
      }
    },
    mounted() {
            if(this.$can('mail-template')){
                this.getCategories();
            }
    }
}
</script>
