<template>
    <div class="row justify-content-center">
        <v-row>
            <v-col class="py-0">
                <v-card style="margin:1rem;">
                    <v-card-title>
                        #{{discussion.id}} {{discussion.title | title}}
                        <a href="#" >
                            <i style="color:#B91E1E;margin-left:1rem" @click.prevent="deleteDis(discussion.id)" class="fas fa-trash fa-fw fa-xs"></i>
                        </a>
                        <v-spacer></v-spacer>
                    </v-card-title>
                    <v-card-text>
                        <v-row style="margin-top: -1rem;">
                            <v-col cols="3">
                                <span style="font-weight: bold">{{$t('disc.from')}}: </span>
                                <span>{{discussion.created_by.name}}</span>
                            </v-col>
                            <v-col cols="2" style="margin-left:-16%">
                                <small>{{$t('disc.posted')}} : {{discussion.created_at | discussionDueDate}}</small>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12">
                                <v-row style="margin-top: -1rem;">
                                    <v-col>
                                        <span v-html="discussion.content"></span>
                                    </v-col>
                                </v-row>
                                <v-row v-if="discussion.files && discussion.files.length > 0" style="margin-top: -1rem;">
                                    <v-col cols="2" v-for="file in discussion.files" :key="file.id">
                                        <div class="mailbox-attachment-info" style="border-radius: 10px;border: 1px solid #8e8e8e;">
                                            <a
                                                target="_plank"
                                                :href=" file.file_path | filePath"
                                                class="mailbox-attachment-name"
                                            >
                                                <i class="fas fa-paperclip"></i>
                                                {{ file.file_path | fileName }}
                                            </a>
                                        </div>
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col cols="7">
                                        <template v-if="discussion.responsibles.length > 0">
                                            <div style="display: inline-flex; width: max-content;margin-top:.1rem">
                                                <small>{{$t('disc.clients')}} :</small>
                                                <div v-for="client in discussion.responsibles" :key="client.id" style="display: inline-flex; width: max-content; position: relative">
                                                    <avatar
                                                        color="#234FA3"
                                                        :fullname="client.name"
                                                        :size="20"
                                                        style="margin-left: .8rem"
                                                    ></avatar>
                                                    <router-link
                                                        :to="'/admin/profile/' +client.id"
                                                        class="ml-1 small"
                                                        style="color: #484848; max-width: 5rem;"
                                                        data-toggle="tooltip" data-placement="top" :title="client.name">
                                                        {{client.name| subStr }}</router-link>
                                                    <i style="font-size: .9rem;margin-top:.2rem;margin-left :.5rem;cursor: pointer" @click.stop="deleteClient(client.id)" class="fas fa-times-circle"></i>
                                                </div>
                                                <v-btn
                                                    color="#234FA3"
                                                    dark
                                                    fab
                                                    style=" ;z-index: 100;width: 2rem;height: 2rem;text-decoration: none;margin-left: 1rem;margin-top:-.3rem"
                                                    @click.stop="addClientModal">
                                                   <v-icon style="font-size: .5rem">fas fa-plus</v-icon>
                                                </v-btn>
                                            </div>
                                        </template>
                                    </v-col>
                                </v-row>
                            </v-col>
                        </v-row>

                        <hr style="margin-top: .4rem;background-color: #000000">
                        <h5 style="color:#000000;margin-top: 1rem">{{$t('disc.reply')}}</h5>
                        <div class="line"></div>
                        <v-row>
                            <v-col cols="12">
                                <ul class="mailbox-attachments d-flex align-items-stretch clearfix">
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
                                      <div>
                                      <span class="form-group ml-3">
                                        <select class="form-control" @change="templateSelected($event)">
                                          <option value="">{{$t('disc.selectTemplate')}}</option>
                                          <option v-for="template in category.templates.data"  :key="template.id" :value="template.id">{{ template.title }}</option>
                                        </select>
                                      </span>
                                      </div>
                                    </li>
                                  </ul>
                            </v-col>
                        </v-row>
                        <v-row style="margin-top: 1rem">
                            <v-col cols="12">
                                <v-text-field :label="$t('disc.replyTitle')" v-model="form.title" required></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <quill-editor
                                    v-model="form.content"
                                    ref="myQuillEditor"
                                    :options="editorOption"
                                ></quill-editor>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12">
                                <v-file-input v-model="form.files" show-size counter multiple :label="$t('disc.up')"></v-file-input>
                            </v-col>
                        </v-row>
                        <v-btn  color="#ffffff" style="background-color:#234FA3 " @click.prevent="CreateReply" text>{{$t('disc.sendReply')}}</v-btn>
                        <v-btn class="comment_btn" @click.prevent="CreateComment" text>{{$t('disc.comment')}}</v-btn>
                        <hr style="margin-top: 1rem;background-color: #000000">
                        <v-row style="margin-top: 1rem;padding-top:0">
                            <v-col cols="12" v-if="discussion.replies" v-for="reply in discussion.replies" :key="reply.id">
                                <v-row >
                                    <v-col cols="3">
                                        <span style="font-weight: bold">{{reply.created_by.name}}</span>
                                        <small style="margin-left: .5rem;">{{$t('disc.posted')}} : {{reply.created_at | discussionDueDate}}</small>
                                        <i v-if="!reply.reply" style="margin-left: 1rem;color: #234FA3" class="fas fa-comment-alt fa-xs"></i>
                                        <i v-if="reply.reply"  style="margin-left: 1rem;color: #234FA3" class="fas fa-reply fa-fw fa-xs"></i>
                                    </v-col>
                                </v-row>
                                <v-row style="margin-top: -1rem;">
                                    <v-col cols="12">
                                        <span v-html="reply.content"></span>
                                    </v-col>
                                </v-row>
                                <hr>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
        <div data-app="true">
            <v-dialog v-model="dialog" persistent max-width="700px">
                <v-card>
                    <v-card-title >
                        <h3>{{$t('disc.addClients')}}</h3>
                        <div style="margin-top: 3rem;margin-left: -70%;" class="line"></div>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row justify="center">
                                <v-col cols="12">
                                    <v-select
                                        v-if="project.owners"
                                        v-model="responsilbes"
                                        :items="clientsArr"
                                        attach
                                        chips
                                        :label="$t('disc.to(clients)')"
                                        multiple
                                        clearable
                                    ></v-select>
<!--                                    <multiselect-->
<!--                                        v-if="project.owners"-->
<!--                                        v-model="responsilbes"-->
<!--                                        :options="clientsArr[0].name"-->
<!--                                        :placeholder="$t('Task.selectOne')"-->
<!--                                        label="name"-->
<!--                                        track-by="name"-->
<!--                                    ></multiselect>-->
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" text @click="dialog = false">{{$t('disc.close')}}</v-btn>
                        <v-btn  color="#ffffff" style="background-color:#234FA3 " text @click="addClients">{{$t('disc.save')}}</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </div>
    </div>
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
    data() {
        return {
            clientsArr : [],
            discussionId: '',
            favorite: false,
            responsilbes: [],
            showTickets: false,
            dialog: false,
            showTasks: false,
            category: '',
            form : new Form({
               id: "",
               title : "",
               content : "",
               files : [],
                responsilbes : [],

            }),

            editorOption: {
                placeholder: 'Reply..',
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
        };
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
                .then(() => {
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
                this.form.content = template.body;
              }
            });
          },
        CreateComment(){
            this.$Progress.start();
            let formData = new FormData();
            if(this.form.files){
                for(var i = 0; i < this.form.files.length; i++ ){
                    let file = this.form.files[i];

                    formData.append('files[' + i + ']', file);
                }
            }
            formData.append('content', this.form.content);
            formData.append('title', this.form.title);
            axios.post( '/v-api/single-discussion/'+this.discussionId,
                formData,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
            ).then(response => {
                this.dialog = false;
                this.$Progress.finish();
                Toast.fire({
                    type: "success",
                    title: "comment created successfully"
                });
                this.form.reset();
                this.form.clear();
                this.getDiscussionById(this.discussionId);
            })
                .catch(error => {
                    this.$Progress.fail();
                    if (error.response) {
                        this.form.errors.errors = error.response.data.errors;
                    }
                });
        },
        CreateReply(){
            this.$Progress.start();
            let formData = new FormData();
            if(this.form.files){
                for(var i = 0; i < this.form.files.length; i++ ){
                    let file = this.form.files[i];

                    formData.append('files[' + i + ']', file);
                }
            }
            formData.append('content', this.form.content);
            formData.append('title', this.form.title);
            formData.append('reply', 1);
            axios.post( '/v-api/single-discussion/'+this.discussionId,
                formData,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
            ).then(response => {
                this.dialog = false;
                this.$Progress.finish();
                Toast.fire({
                    type: "success",
                    title: "Reply created successfully and sent to client"
                });
                this.form.reset();
                this.form.clear();
                this.getDiscussionById(this.discussionId);
            })
                .catch(error => {
                    this.$Progress.fail();
                    if (error.response) {
                        this.form.errors.errors = error.response.data.errors;
                    }
                });
        },
        getDiscussionById(id) {
            this.$Progress.start();
            this.$store
                .dispatch("project/getSingleDiscussion", id)
                .then(response => {
                    this.$Progress.finish();
                    this.getProjectData();
                    let initSub = this.discussion.title;
                    let n = initSub.lastIndexOf("#");
                    let subject = initSub.substring(n + 1);
                    this.form.title = subject;
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },
        deleteDis(id){
            Swal.fire({
                title: "Are you sure you want to delete?",
                showCancelButton: true,
                confirmButtonColor: "#B91E1E",
                cancelButtonColor: "#234FA3",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
            }).then(result => {
                if (result.value) {
                    this.$Progress.start();
                    this.$store
                        .dispatch("project/deleteDiscussion", id)
                        .then(response => {
                            this.$Progress.finish();
                            Toast.fire({
                                type: "success",
                                title: "Discussion deleted successfully"
                            });
                            this.$router.push({ name: "project.discussion" });
                        })
                        .catch(error => {
                            this.$Progress.fail();
                            Toast.fire({
                                type: "error",
                                title: "item not deleted"
                            });
                        });
                }
            });
        },
        addClientModal() {
            this.dialog = true;
            this.setClientsArr();
            this.form.responsilbes.push(this.discussion.responsibles[0].name);
        },
        getProjectData(){
            this.$store
                .dispatch('project/getProjectById', this.discussion.project_id)
                .then(() => {
                })
                .catch(() => {
                })
        },
        setClientsArr(){
            this.project.owners.forEach(owner => {
                this.clientsArr.push(owner.name);
            })
        },
        addClients(){
            let data= {
                id:this.discussion.id ,
                data:this.form.responsilbes
            }
            this.$Progress.start();
            this.$store
                .dispatch('project/addClients', data)
                .then(() => {
                    this.getDiscussionById(this.discussionId);
                    this.dialog = false;
                    this.$Progress.finish();
                    Toast.fire({
                        type: "success",
                        title: "client added to discussion successfully"
                    });
                })
                .catch(() => {
                })
        },
        deleteClient(id) {
            let data = {
                id: this.discussion.id,
                client_id: id
            }
            Swal.fire({
                title: "Are you sure you want to delete?",
                showCancelButton: true,
                confirmButtonColor: "#B91E1E",
                cancelButtonColor: "#234FA3",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
            }).then(result => {
                if (result.value) {
                    this.$Progress.start();
                    this.$store
                        .dispatch('project/deleteClient', data)
                        .then(() => {
                            this.getDiscussionById(this.discussionId);
                            this.dialog = false;
                            this.$Progress.finish();
                            Toast.fire({
                                type: "success",
                                title: "client deleted from discussion successfully"
                            });
                        })
                        .catch(() => {
                        })
                }
            });
        }
    },
    created() {
        this.discussionId = parseInt(this.$route.params.discussionId);
        this.getDiscussionById(this.discussionId);
        this.getCategories();
        this.getTemplates();
    },
    watch: {
        my_responsibles() {
            this.form.responsilbes = []
            this.project.owners.forEach(owner => {
                if(this.responsilbes.includes(owner.name)) {
                    this.form.responsilbes.push(owner.id)
                }
            })
        }
    },
    computed: {
        my_responsibles() {
            return this.responsilbes;
        },
        ...mapGetters({
            discussion : "project/activeSingleDiscussion",
            user: "user/activeSingleUser",
            project : "project/activeSingleProject",
            categories: "template/allCategories",
            templates: "template/allTemplates",
        }),
    },
    filters:{
        subStr: (value) => {
            if(value.length > 9) {
                return value.substring(0, 12) + '...';
            }
            return value;
        },
        filePath(path) {
            let str = path;
            let n = str.indexOf("public");
            return "/storage" + str.substring(n + 6);
        },
        fileName(path) {
            let str = path;
            let n = str.lastIndexOf("-");
            let name =  str.substring(n + 1);
            if(name.length > 15) {
                return name.substring(0, 15) + '...'
            } else {
                return name;
            }
        },
        discussionDueDate: (value) => {
            return moment(value).fromNow()
        },
        projectsDateNow: (value) => {
            return moment(value).fromNow()
        },
        title(title) {
            let str = title;
            let n = str.lastIndexOf("#");
            return str.substring(n + 1);
        },

    },
    components: {
        quillEditor
    },

};
</script>

<style scoped>

.line{
    background-color: #234FA3;
    width: 4.3rem;
    height: .2rem;
    border-radius: 5px;
}

.comment_btn{
    border: 1px solid #234FA3;
    transition: ease .4s;
}
.comment_btn:hover{
    background-color: #234FA3;
    color: #ffffff;
}
</style>
