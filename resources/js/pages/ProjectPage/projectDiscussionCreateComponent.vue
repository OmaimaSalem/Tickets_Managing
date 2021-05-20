<template>
    <v-row justify="center">
        <v-btn
            color="#234FA3"
            dark
            top
            right
            fab
            class="newModalBtn"
            style=" float:right;z-index: 100;margin-top: -2rem;width: 2.5rem;height: 2.5rem;text-decoration: none;margin-left: 95%;"
            @click.stop="NewModal">
            <v-icon style="font-size: 1rem">fas fa-plus</v-icon>
        </v-btn>
        <div data-app="true">
            <v-dialog v-model="dialog" persistent max-width="700px">
                <v-card>
                    <v-card-title >
                        <h3>{{$t('disc.create')}}</h3>
                        <div style="margin-top: 3rem;margin-left: -42%;" class="line"></div>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row justify="center">
                                <v-col cols="12">
                                    <v-text-field :label="$t('disc.title')" v-model="form.title" required></v-text-field>
                                    <has-error :form="form" field="title"></has-error>

                                </v-col>
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
                                    <has-error :form="form" field="responsilbes"></has-error>
                                </v-col>
                                <v-col cols="12">
                                    <quill-editor
                                        v-model="form.content"
                                        ref="myQuillEditor"
                                        :options="editorOption"
                                    ></quill-editor>
                                    <has-error :form="form" field="content"></has-error>

                                </v-col>
                                <v-col cols="12">
                                    <v-file-input v-model="form.files" show-size counter multiple :label="$t('disc.up')"></v-file-input>
                                </v-col>

                            </v-row>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" text @click="dialog = false">{{$t('disc.close')}}</v-btn>
                        <v-btn  color="#ffffff" style="background-color:#234FA3 " text @click="CreateDiscussion">{{$t('disc.save')}}</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </div>
    </v-row>
</template>


<script>
import {mapGetters} from "vuex";
import {quillEditor} from "vue-quill-editor";
// require styles
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";
export default {
    data: () => ({
        clientsArr: [],
        employeesArr: [],
        dialog: false,
        editMode: false,
        responsilbes: [],
        form: new Form({
            id: "",
            title: "",
            content: "",
            responsilbes : [],
            files : [],
        }),
        editorOption: {
            placeholder: 'Email..',
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
    }),
    props : ['project_id'],
    methods: {
        NewModal() {
            this.dialog = true;
            this.setClientsArr();
        },

        CreateDiscussion() {
            this.$Progress.start();
            let formData = new FormData();
            if(this.form.files){
                for(var i = 0; i < this.form.files.length; i++ ){
                    let file = this.form.files[i];

                    formData.append('files[' + i + ']', file);
                }
            }else{
                formData.append('files',[]);
            }
            for( var i = 0; i < this.form.responsilbes.length; i++ ){
                let responsilbe = this.form.responsilbes[i];
                formData.append('responsilbes[' + i + ']', responsilbe);
            }
            formData.append('title', this.form.title);
            formData.append('content', this.form.content);
            // formData.append('responsilbes', this.form.responsilbes);
            axios.post( '/v-api/project-discussion/'+this.project_id,
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
                        title: "discussion created successfully"
                    });
                this.form.reset();
                this.form.clear();
                this.getDiscussions();
                })
                .catch(error => {
                    this.$Progress.fail();
                    if (error.response) {
                        this.form.errors.errors = error.response.data.errors;
                    }
                });
        },
        setClientsArr(){
            this.project.owners.forEach(owner => {
                this.clientsArr.push(owner.name);
            })
        },
        getProjectData(){
            this.$store
                .dispatch('project/getProjectById', this.project_id)
                .then(() => {
                })
                .catch(() => {
                })
        },
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
        getDiscussions(){
            this.$store
                .dispatch("project/getAllDiscussion", {id : this.project_id})
                .then()
                .catch(error => {
                    console.log(error);
                });
        },
    },
    components: {
        quillEditor,
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
            owners: "project/ownerOfProject",
            user: "user/activeSingleUser",
            project : "project/activeSingleProject",
            discussions : "project/getProjectDiscussions"
        })
    },
    mounted() {
        this.getProjectData();
        this.getDiscussions();
    }

}
</script>
<style scoped>


.quill{
    border-radius: 15px;
    margin-left: 5rem;
    width: 90%;
    background-color: #ffffff;
    border:none;
    margin-top: -2.5rem;
}
.line{
    background-color: #234FA3;
    width: 5.3rem;
    height: .2rem;
    border-radius: 5px;
}
.newModalBtn{

}

</style>
