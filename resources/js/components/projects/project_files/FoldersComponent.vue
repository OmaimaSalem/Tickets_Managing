<template>

    <v-container >
        <v-row>
            <v-col>
                <h3>{{$t('folder.folders')}}</h3>
                <div class="line"></div>
            </v-col>
            <v-col>
                <v-btn small color="#ffffff" class="float-right m-1" style="color: #ffffff" @click="uploadFileModal">{{$t('folder.up')}}</v-btn>
                <v-btn small color="#ffffff" class="float-right m-1" style="color: #ffffff" @click="newFolderModal">{{$t('folder.createFolder')}}</v-btn>
                <v-btn small color="#ffffff" class="float-right m-1" style="color: #ffffff" @click="copyToModal" :disabled="selectedFiles.length < 1">{{$t('folder.copy')}}</v-btn>
                <v-btn small color="#ffffff" class="float-right m-1" style="color: #ffffff" @click="moveToModal" :disabled="selectedFiles.length < 1">{{$t('folder.move')}}</v-btn>
            </v-col>
        </v-row>
        <v-row>
            <v-col>
                <v-card rounded="4px">
                    <v-simple-table dense fixed-header>
                    <template v-slot:default>
                      <thead>
                        <tr>
                          <th width='10px' ></th>
                          <th style="font-size:1.5rem;color: #234FA3" class="p-0" >{{$t('folder.name')}}</th>
                          <th style="font-size:1.5rem;color: #234FA3">{{$t('folder.desc')}}</th>
                          <th style="font-size:1.5rem;color: #234FA3">{{$t('folder.date')}}</th>
                          <th style="font-size:1.5rem;color: #234FA3" class="p-0">{{$t('folder.creator')}}</th>
                          <th style="font-size:1.5rem;color: #234FA3">{{$t('folder.actions')}}</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="folder in folders" :key="folder.id">
                          <td>

                          </td>
                          <td class="p-0">
                              <router-link
                                  :to="'/admin/project/' + folder.project_id +'/folder/'+folder.id"
                                  class="project-name">
                                  {{ folder.name}}
                              </router-link>

                          </td>
                          <td>
                            {{folder.description}}
                          </td>
                          <td>{{folder.created_at | created_at}}</td>
                            <td v-if="folder.created_by" class="p-0">
                                <router-link
                                    :to="'/admin/profile/' + folder.created_by.id" >
                                    {{ folder.created_by.name}}
                                </router-link>
                            </td>
                          <td>
                            <i
                              data-widget="collapse"
                              data-toggle="tooltip"
                              title="Edit"
                              class="text-success icon fas fa-edit text-center"
                              style=":hover {color: #ffffff}"
                              @click="editFolderModal(folder)"
                            ></i>
                            <i
                              data-widget="collapse"
                              data-toggle="tooltip"
                              title="Edit"
                              class="text-danger icon pl-1 fas fa-trash text-center"
                              @click="deleteFolder(folder.id)"
                            ></i>
                          </td>
                        </tr>
                        <tr v-for="file in project_files" :key="'0'+file.id">
                            <td >
                                <div class="fixedLine">
                                    <input type="checkbox" v-model="selectedFiles" :value="file.id"/>
                                </div>
                            </td>
                            <td class="p-0">
                                <a
                                    target="_plank"
                                    :href=" file.path | filePath"
                                    class="mailbox-attachment-name"
                                >
                                    <i class="fas fa-paperclip"></i>
                                    {{ file.name }}
                                </a>
                            </td>
                            <td>
                                {{file.type}}
                            </td>
                            <td>{{file.created_at | created_at}}</td>
                            <td v-if="file.uploaded_by" class="p-0">
                                <router-link
                                    :to="'/admin/profile/' + file.uploaded_by.id" >
                                    {{ file.uploaded_by.name}}
                                </router-link>
                            </td>
                            <td>
                                <a
                                    data-widget="collapse"
                                    data-toggle="tooltip"
                                    title="Edit"
                                    class="icon fas fa-download"
                                    style=":hover {color: #ffffff}"
                                    :href=" file.path | filePath"
                                    download
                                ></a>
                                <i
                                    data-widget="collapse"
                                    data-toggle="tooltip"
                                    title="Edit"
                                    class="text-danger icon pl-1 fas fa-trash"
                                    @click="deleteFile(file.id)"
                                ></i>
                            </td>
                        </tr>
                      </tbody>
                    </template>
                  </v-simple-table>
                </v-card>
            </v-col>
<!--            <v-col>-->
<!--                <v-card v-for="file in project_files" :key="file.id">-->

<!--                </v-card>-->
<!--            </v-col>-->
        </v-row>
        <!-- Create Folder Modal -->
        <div
            class="modal fade"
            id="newFolder"
            tabindex="-1"
            role="dialog"
            aria-labelledby="newFolderLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newFolderLabel">{{$t('folder.createFolder')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="createFolder(form)" @keydown="form.onKeydown($event)">
                        <div class="modal-body">
                            <div class="form-group">
                                <span for="name">{{$t('folder.name')}}</span>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('name') }"
                                />
                                <has-error :form="form" field="name"></has-error>
                            </div>
                            <div class="form-group">
                                <label for="description">{{$t('folder.desc')}}</label>
                                <input
                                    v-model="form.description"
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('description') }"
                                />
                                <has-error :form="form" field="description"></has-error>
                            </div>
                        </div>
                        <div class="modal-footer">

                            <v-btn type="submit" color="#ffffff" style="background-color:#234FA3 " text>{{$t('folder.create')}}</v-btn>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Edit Folder Modal -->
        <div
            class="modal fade"
            id="editFolder"
            tabindex="-1"
            role="dialog"
            aria-labelledby="newFolderLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editFolderLabel">{{$t('folder.editFolder')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="editFolder()" @keydown="form.onKeydown($event)">
                        <div class="modal-body">
                            <div class="form-group">
                                <span for="name">Name</span>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('name') }"
                                />
                                <has-error :form="form" field="name"></has-error>
                            </div>
                            <div class="form-group">
                                <label for="description">{{$t('folder.desc')}}</label>
                                <input
                                    v-model="form.description"
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('description') }"
                                />
                                <has-error :form="form" field="description"></has-error>
                            </div>
                        </div>
                        <div class="modal-footer">
<!--                            <button-->
<!--                                type="submit"-->
<!--                                class="btn btn-success"-->
<!--                            >{{$t('folder.edit')}}</button>-->
                            <v-btn type="submit" color="#ffffff" style="background-color:#234FA3 " text>{{$t('folder.edit')}}</v-btn>

                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!--Upload File Modal-->
        <div
            class="modal fade"
            id="uploadFileModal"
            tabindex="-1"
            role="dialog"
            aria-labelledby="uploadFileLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadFileLabel">{{$t('folder.up')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="uploadFile()" @keydown="form.onKeydown($event)">
                        <div class="modal-body">
                            <div class="form-group">
                                <v-file-input v-model="files" show-size counter multiple :label="$t('folder.up')"></v-file-input>
                            </div>
                        </div>
                        <div class="modal-footer">
<!--                            <button-->
<!--                                type="submit"-->
<!--                                class="btn btn-success"-->
<!--                            >{{$t('folder.upload')}}</button>-->
                            <v-btn type="submit" color="#ffffff" style="background-color:#234FA3 " text>{{$t('folder.upload')}}</v-btn>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--CopyTo Modal-->
        <div
            class="modal fade"
            id="copyToModal"
            tabindex="-1"
            role="dialog"
            aria-labelledby="copyToModal"
            aria-hidden="true"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="copyToModal">Copy File</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="copyFiles()" @keydown="form.onKeydown($event)">
                        <div class="modal-body">
                            <multiselect
                                v-model="copyTo"
                                :options="folders"
                                :placeholder="$t('Ticket.selectOne')"
                                label="name"
                                track-by="id"
                            ></multiselect>
                        </div>
                        <div class="modal-footer">
                            <button
                                type="submit"
                                class="btn btn-success"
                            >Copy</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!--MoveTo Modal-->
        <div
            class="modal fade"
            id="moveToModal"
            tabindex="-1"
            role="dialog"
            aria-labelledby="moveToModal"
            aria-hidden="true"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="moveToModal">Move File</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="moveFiles()" @keydown="form.onKeydown($event)">
                        <div class="modal-body">
                            <multiselect
                                v-model="moveTo"
                                :options="folders"
                                :placeholder="$t('Ticket.selectOne')"
                                label="name"
                                track-by="id"
                            ></multiselect>
                        </div>
                        <div class="modal-footer">
                            <button
                                type="submit"
                                class="btn btn-success"
                            >Move</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </v-container>
</template>

<script>
import {mapGetters} from "vuex";
import moment from "moment";

export default {
name: "FolderComponent",
    data() {
        return {
            form: new Form({
                id:'',
                name: "",
                description: "",
                project_id: "",
            }),
            files:[],
            selectedFiles:[],
            copyTo:'',
            moveTo:'',
        };
    },
methods: {
    createFolder(){
        this.form.project_id = this.$route.params.id;
        this.$store
            .dispatch("project_files/createFolder", this.form)
            .then(response => {
                this.getFolders();
                $("#newFolder").modal("hide");
                Toast.fire({
                    type: "success",
                    title: response.data.message,
                });
                this.$Progress.finish();
                this.getFolders();
            })
            .catch(error => {
                console.log(error)
                this.$Progress.fail();
            });
    },
    editFolder(){
        this.form.project_id = this.$route.params.id;
        this.$store
            .dispatch("project_files/editFolder", this.form)
            .then(response => {
                this.getFolders();
                $("#editFolder").modal("hide");
                Toast.fire({
                    type: "success",
                    title: response.data.message,
                });
                this.$Progress.finish();
            })
            .catch(error => {
                this.$Progress.fail();
            });
    },
    deleteFolder(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
        }).then((result) => {
            if (result.value) {
                this.$Progress.start();
                this.$store
                    .dispatch("project_files/deleteFolder", id)
                    .then((response) => {
                        this.$Progress.finish();
                        Toast.fire({
                            type: "success",
                            title: response.data.message,
                        });
                    })
                    .catch((error) => {
                        this.$Progress.fail();
                    });
            }
        });
    },
    deleteFile(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
        }).then((result) => {
            if (result.value) {
                this.$Progress.start();
                this.$store
                    .dispatch("project_files/deleteFile", id)
                    .then((response) => {
                        this.$Progress.finish();
                        Toast.fire({
                            type: "success",
                            title: response.data.message,
                        });
                    })
                    .catch((error) => {
                        this.$Progress.fail();
                    });
            }
        });
    },
    uploadFile() {
        this.$Progress.start();
        let formData = new FormData();
        if(this.files){
            for(var i = 0; i < this.files.length; i++ ){
                let file = this.files[i];
                formData.append('files[' + i + ']', file);
            }
        }else{
            formData.append('files',[]);
        }
        formData.append('project_id', this.$route.params.id);
        this.$store
            .dispatch("project_files/uploadFile", formData)
            .then(response => {
            this.dialog = false;
            this.$Progress.finish();
            Toast.fire({
                type: "success",
                title: response.data.message
            });
            $("#uploadFileModal").modal("hide");
            this.form.reset();
            this.form.clear();
            this.getFiles();
        })
            .catch(error => {
            });
    },
    newFolderModal() {
        this.form.reset();
        this.form.clear();
        $("#newFolder").modal("show");
    },
    uploadFileModal() {
        this.files = [];
        $("#uploadFileModal").modal("show");
    },
    editFolderModal(folder) {
        this.form.reset();
        this.form.clear();
        this.form.fill(folder);
        $("#editFolder").modal("show");
    },
    copyToModal() {
        $("#copyToModal").modal("show");
    },
    moveToModal() {
        $("#moveToModal").modal("show");
    },
    getFolders() {
        this.$store
            .dispatch("project_files/getFolders", this.$route.params.id)
            .then()
            .catch(error => {
                console.log(error);
            });
    },
    getFiles() {
        this.$store
            .dispatch("project_files/getFiles", this.$route.params.id)
            .then()
            .catch(error => {
                console.log(error);
            });
    },
    copyFiles(){
        this.copyTo = this.copyTo.id;
        let data = {
            selected_files : this.selectedFiles,
            destination: this.copyTo
        }
        this.$store
            .dispatch("project_files/copyToFolder", data)
            .then(response => {
                this.$Progress.finish();
                Toast.fire({
                    type: "success",
                    title: response.data.message
                });
                $("#copyToModal").modal("hide");
                this.selectedFiles=[]
                this.copyTo=''
                this.getFolders();
                this.getFiles();
            })
            .catch(error => {
                console.log(error);
            });
    },
    moveFiles(){
        this.moveTo = this.moveTo.id;
        let data = {
            selected_files : this.selectedFiles,
            destination: this.moveTo
        }
        this.$store
            .dispatch("project_files/moveToFolder", data)
            .then(response => {
                this.$Progress.finish();
                Toast.fire({
                    type: "success",
                    title: response.data.message
                });
                $("#moveToModal").modal("hide");
                this.selectedFiles=[]
                this.moveTo=''
                this.getFolders();
                this.getFiles();
            })
            .catch(error => {
                console.log(error);
            });
    }
},
    mounted() {
        this.getFolders();
        this.getFiles();
    },
    computed: {
        ...mapGetters({
            folders: "project_files/allFolders",
            project_files: "project_files/allFiles",
        }),
    },
    filters:{
        filePath(path) {
            let str = path;
            let n = str.indexOf("public");
            return "/storage" + str.substring(n + 6);
        },
        fileName(path) {
            let str = path;
            let n = str.lastIndexOf("-");
            return str.substring(n + 1);
        },
        created_at: (value) => {
            return moment(value).fromNow();
        },
    }
}
</script>

<style scoped>
.line{
    background-color: #234FA3;
    width: 5.3rem;
    height: .2rem;
    border-radius: 5px;
}
.separator{
    border-right: 6px solid black;
}
tbody tr:nth-of-type(odd) {
    background-color: #dcdcdc;
}
thead tr th {
    color: #2f5298 !important;
    font-size: 18px !important;
}
</style>
