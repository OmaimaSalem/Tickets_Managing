<template>
    <v-container >
        <h3>{{$t("disc.projectDis")}}</h3>
        <div class="line"></div>
        <ProjectDiscussionCreateComponent :project_id = projectId></ProjectDiscussionCreateComponent>
        <div class="card" style="margin-top:1rem;">
            <!-- /.card-header -->
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-sm">
                    <thead>
                    <tr>
                        <th width="2%"><input @click.prevent="selectAll" type="checkbox"></th>
                        <th width="20%">{{$t("disc.title")}}</th>
                        <th width="10%">{{$t("disc.postedBy")}}</th>
                        <th width="12%">{{$t("disc.date")}}</th>
                        <th width="10%">{{$t("disc.comments")}}</th>
                        <th width="10%">{{$t("disc.actions")}}<a href="#" ><i style="color:#B91E1E;margin-left: .5rem" v-if="selectedRows.length > 0" @click.prevent="deleteSelected" class="fas fa-trash fa-fw "></i></a></th>
                    </tr>
                    </thead>
                    <tbody >
                    <tr v-for="discussion in discussions" :key="discussion.id" style="border-radius: 25px;margin:1rem 0">

                        <td><input v-model="selectedRows" :value="discussion.id" type="checkbox"></td>

                        <td>
                            <router-link
                                :to="'/admin/project/' + projectId +'/discussion/'+discussion.id"
                                style="color: #000000"
                            >{{ discussion.title | title }}</router-link>
                        </td>
                        <td>
                            <router-link
                                :to="'/admin/profile/'+discussion.created_by.id"
                                style="color: #484848;margin-left:.5rem;margin-top:.4rem"
                            >{{ discussion.created_by.name }}</router-link>
                        </td>
                        <td>
                            <div class="small">{{ discussion.created_at | DateWithTime }}</div>
                        </td>
                        <td>
                            <div class="small">{{ discussion.comments_num }}</div>
                        </td>

                        <td>
                            <a href="#" v-if="discussion.seen">
                                <i style="color:#7EB38B" class="fas fa-reply fa-fw"></i>
                            </a>
                            <a href="#" v-if="!discussion.seen">
                                <i style="color:#B91E1E" class="fas fa-reply fa-fw"></i>
                            </a>

                            <a href="#" >
                                <i style="color:#B91E1E" @click.prevent="deleteDis(discussion.id)" class="fas fa-trash fa-fw "></i>
                            </a>
                        </td>

                    </tr>
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
    </v-container>


</template>

<script>
import { mapActions, mapGetters } from "vuex";
import { quillEditor } from "vue-quill-editor";
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";
import _ from "lodash";
import ProjectDiscussionCreateComponent from "./projectDiscussionCreateComponent";

export default {
    components: {
        ProjectDiscussionCreateComponent,
        quillEditor
    },
    data() {
        return {
            selectedRows : [],
            allDiscussions : false,
            solo: true,
            loading: true,
            projectId: this.$route.params.id,
        };
    },
    methods: {
        selectAll(){
            this.allDiscussions = !this.allDiscussions;
            this.selectedRows = []
            if(this.allDiscussions === true){
                this.discussions.forEach(discussion => {
                    this.selectedRows.push(discussion.id)
                })
            }
            else{
                this.selectedRows = []
            }

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
                            this.getDiscussions();
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
        deleteSelected(){
            if(this.selectedRows.length === 0){
                Toast.fire({
                    type: "error",
                    title: "You haven't selected any discussions to delete !"
                });
            }else{
                Swal.fire({
                    title: "Are you sure you want to delete the selected discussions?",
                    showCancelButton: true,
                    confirmButtonColor: "#B91E1E",
                    cancelButtonColor: "#234FA3",
                    confirmButtonText: "Yes",
                    cancelButtonText: "No",
                }).then(result => {
                    if (result.value) {
                        this.$Progress.start();
                        this.$store
                            .dispatch("project/deleteDiscussion", this.selectedRows)
                            .then(response => {
                                this.$Progress.finish();
                                Toast.fire({
                                    type: "success",
                                    title: "Discussions deleted successfully"
                                });
                                this.getDiscussions();
                            })
                            .catch(error => {
                                this.$Progress.fail();
                                Toast.fire({
                                    type: "error",
                                    title: "items not deleted"
                                });
                            });
                    }
                });
            }

        },
        getDiscussions(){
            this.$store
                .dispatch("project/getAllDiscussion", {id : this.projectId})
                .then()
                .catch(error => {
                    console.log(error);
                    Toast.fire({
                        type: "error",
                        title: 'Only the responsilbes of this project can see this discussion !'
                    });
                });
        },

    },
    mounted() {
        this.getDiscussions();

    },
    computed: {
        ...mapGetters({
            singleProject: "project/activeSingleProject",
            discussions : "project/getProjectDiscussions"
        })
    },
    filters : {
        title(title) {
            let str = title;
            let n = str.lastIndexOf("#");
            return str.substring(n + 1);
        },
    }
};
</script>

<style scoped>
.line{
    background-color: #234FA3;
    width: 5.3rem;
    height: .2rem;
    border-radius: 5px;
}
tr th{
    font-weight: bold;
    color:#234FA3 ;
}
</style>
