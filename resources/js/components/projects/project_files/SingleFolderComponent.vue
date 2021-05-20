<template>
    <v-container>
        <v-row>
            <v-col>
                <h3>Files</h3>
                <div class="line"></div>
            </v-col>
            <v-col>
                <v-btn class="float-right m-1" @click="uploadFileModal"  color="#ffffff" style="background-color:#234FA3" text>{{$t('folder.files')}}</v-btn>
                <v-btn class="float-right m-1" :disabled="selectedFiles.length < 1" @click="copyToModal" color="#ffffff" style="background-color:#234FA3" text >{{$t('folder.copy')}}</v-btn>
                <v-btn class="float-right m-1" :disabled="selectedFiles.length < 1" @click="moveToModal" color="#ffffff" style="background-color:#234FA3" text>{{$t('folder.move')}}</v-btn>
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
                            <tr v-for="file in project_files" :key="file.id">
                                <td>
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
                                <td class="p-0">
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
                    <form @submit.prevent="uploadInFolder()" @keydown="form.onKeydown($event)">
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
                        <h5 class="modal-title" id="copyToModal">{{$t('folder.copyFile')}}</h5>
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
                                :label="$t('folder.name')"
                                track-by="id"
                            ></multiselect>
                        </div>
                        <div class="modal-footer">
<!--                            <button-->
<!--                                type="submit"-->
<!--                                class="btn btn-success"-->
<!--                            >{{$t('folder.copy')}}</button>-->
                            <v-btn type="submit" color="#ffffff" style="background-color:#234FA3 " text>{{$t('folder.copy')}}</v-btn>

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
                        <h5 class="modal-title" id="moveToModal">{{$t('folder.moveFile')}}</h5>
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
                                label="$t('folder.name')"
                                track-by="id"
                            ></multiselect>
                        </div>
                        <div class="modal-footer">
<!--                            <button-->
<!--                                type="submit"-->
<!--                                class="btn btn-success"-->
<!--                            >{{$t('folder.move')}}</button>-->
                            <v-btn type="submit" color="#ffffff" style="background-color:#234FA3 " text>{{$t('folder.move')}}</v-btn>

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
    name: "SingleFolderComponent",
    data() {
        return {
            files:[],
            selectedFiles:[],
            copyTo:'',
            moveTo:'',
        };
    },
    methods:{
        getSingleFolder(){
            this.$store
                .dispatch("project_files/getSingleFolder", this.$route.params.folder_id)
                .then()
                .catch(error => {
                    console.log(error);
                });
        },
        uploadFileModal() {
            this.files = [];
            $("#uploadFileModal").modal("show");
        },
        uploadInFolder() {
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
            formData.append('folder_id', this.$route.params.folder_id);
            this.$store
                .dispatch("project_files/uploadInFolder", formData)
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
                    // this.getSingleFolder();
                })
                .catch(error => {
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
                        .dispatch("project_files/deleteFileInFolder", id)
                        .then((response) => {
                            this.$Progress.finish();
                            Toast.fire({
                                type: "success",
                                title: response.data.message,
                            });
                            this.getSingleFolder()
                        })
                        .catch((error) => {
                            this.$Progress.fail();
                        });
                }
            });
        },
        copyToModal() {
            $("#copyToModal").modal("show");
        },
        moveToModal() {
            $("#moveToModal").modal("show");
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
                    this.getSingleFolder()
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
                    this.getSingleFolder()
                })
                .catch(error => {
                    console.log(error);
                });
        },
        getFolders() {
            this.$store
                .dispatch("project_files/getFolders", this.$route.params.id)
                .then()
                .catch(error => {
                    console.log(error);
                });
        },
    },
    mounted() {
        this.getSingleFolder();
        this.getFolders()
    },
    computed: {
        ...mapGetters({
            project_files: "project_files/filesInFolder",
            folders: "project_files/allFolders",
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
tbody tr:nth-of-type(odd) {
    background-color: #dcdcdc;
}
thead tr th {
    color: #2f5298 !important;
    font-size: 18px !important;
}
</style>
