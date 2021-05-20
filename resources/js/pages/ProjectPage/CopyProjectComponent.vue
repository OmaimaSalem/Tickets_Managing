<template>

    <v-container >
                <h3>{{$t('Project.copyProject')}}</h3>
                <div class="line"></div>
            <v-row>
                <v-col cols="7" class="removeTopBottomPad" style="margin-top: 1rem">
                    <form @submit.prevent="" @keydown="form.onKeydown($event)">
                        <v-row class="ml-2">
                            <v-col cols="12" class="removeTopBottomPad">
                                <v-row>
                                    <v-col cols="4">
                                        <label class="fontSize">{{$t('Project.name')}}</label>
                                    </v-col>
                                    <v-col cols="8">
                                        <input
                                            v-model="form.name"
                                            type="text"
                                            name="name"
                                            class="form-control floatingArea"
                                            :placeholder="$t('Project.name')"
                                            :class="{'is-invalid': form.errors.has('name')}"
                                        />
                                        <has-error
                                            :form="form"
                                            field="name"
                                        ></has-error>
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col cols="4">
                                        <label class="fontSize">{{$t('Project.desc')}}</label>
                                    </v-col>
                                    <v-col cols="8">
                                        <quill-editor
                                            id="comments-editor"
                                            v-model="form.description"
                                            ref="myQuillEditor"
                                            :options="editorOption"
                                            class="removeSpace floatingArea"
                                            style="background-color: #FFFFFF"
                                        ></quill-editor>
                                        <has-error :form="form" field="description"></has-error>
                                    </v-col>
                                </v-row>
                                <!-- <v-row>
                                    <v-col cols="4">
                                        <label class="fontSize">{{$t('Project.taskRate')}}</label>
                                    </v-col>
                                    <v-col cols="8">
                                        <input
                                            v-model="form.task_rate"
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            name="task_rate"
                                            class="form-control floatingArea"
                                            :class="{'is-invalid': form.errors.has('task_rate')}"
                                        />
                                        <has-error
                                            :form="form"
                                            field="task_rate"
                                        ></has-error>
                                    </v-col>
                                </v-row> -->
                                <v-row>
                                    <v-col cols="4">
                                        <label class="fontSize">{{$t('Project.budgetHours')}}</label>
                                    </v-col>
                                    <v-col cols="8">
                                        <input
                                            v-model="form.budget_hours"
                                            type="number"
                                            name="budget_hours"
                                            class="form-control floatingArea"
                                            :class="{'is-invalid': form.errors.has('budget_hours')}"
                                        />
                                        <has-error
                                            :form="form"
                                            field="budget_hours"
                                        ></has-error>
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col cols="4">
                                        <label class="fontSize">{{$t('Project.estimatedTime')}}</label>
                                    </v-col>
                                    <v-col cols="8">
                                        <input
                                            v-model="form.estimated_time"
                                            type="number"
                                            name="estimated_time"
                                            class="form-control floatingArea"
                                            :class="{'is-invalid': form.errors.has('estimated_time')}"
                                        />
                                        <has-error
                                            :form="form"
                                            field="estimated_time"
                                        ></has-error>
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col cols="4">
                                        <label class="fontSize">Status</label>
                                    </v-col>
                                    <v-col cols="8">
                                        <multiselect
                                            v-model="form.status"
                                            :options="status"
                                            :placeholder="$t('Task.selectOne')"
                                            label="name"
                                            track-by="name"
                                            class="removeSpace floatingArea rounded"
                                            @input="opt => (form.status_id = opt.id)"
                                        ></multiselect>
                                        <has-error
                                            :form="form"
                                            field="status_id"
                                        ></has-error>
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col cols="4">
                                        <label class="fontSize">{{$t('Project.clients')}}</label>
                                    </v-col>
                                    <v-col cols="8">
                                        <multiselect
                                            v-model="form.owner"
                                            :multiple="true"
                                            :options="owners"
                                            :searchable="true"
                                            :close-on-select="true"
                                            :clear-on-select="false"
                                            :preserve-search="true"
                                            :placeholder="$t('Project.selectOne')"
                                            label="name"
                                            track-by="id"
                                            :preselect-first="true"
                                            @input="opt => form.owner_id = opt.id"
                                            class="removeSpace floatingArea rounded"
                                        ></multiselect>
                                        <has-error :form="form" field="owner_id"></has-error>
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col cols="4">
                                        <label class="fontSize">{{$t('Project.assignedUsers')}}</label>
                                    </v-col>
                                    <v-col cols="8">
                                        <multiselect
                                            v-model="form.project_assign"
                                            :options="responsible"
                                            :multiple="true"
                                            :close-on-select="false"
                                            :clear-on-select="false"
                                            :preserve-search="true"
                                            :placeholder="$t('Project.pickSome')"
                                            label="name"
                                            track-by="id"
                                            :preselect-first="true"
                                            class="removeSpace floatingArea rounded"
                                        >
                                        </multiselect>
                                        <has-error :form="form" field="project_assign"></has-error>
                                    </v-col>
                                </v-row>
                            </v-col>
                        </v-row>
                    </form>
                    <v-btn @click="copyProject" class="float-right" color="#234FA3"> <span class="text-bold text-white" >{{$t('Project.copy')}}</span> </v-btn>
                </v-col>
                <v-col cols="5">
                    <v-row>
                        <v-col cols="12" class="removeTopBottomPad">
                            <v-card>
                                <v-card-title style="padding: 0.3rem; justify-content: center" class="gredient">
                                    <label>{{$t('Project.advanced')}}</label>
                                </v-card-title>
                            </v-card>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col>
                            <div class="card">
                                <v-row>
                                    <v-col>
                                        <v-card color="#DCDCDC" >
                                            <v-card-title style="padding: 0.3rem;" class="ml-2">
                                                <input style="margin-left: 0.5rem" type="checkbox" @click="selectall">
                                                <span style="color: black; margin-left: 1.5rem">{{$t('Project.selectAll')}}</span>
                                            </v-card-title>
                                        </v-card>
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col>
                                        <v-container>
                                            <v-card color="#CCCBCB">
                                                <v-card-title style="padding: 0.1rem;" class="ml-2">
                                                    <input  type="checkbox" class="mb-2" @click="selectallTickets">
                                                    <label class="ml-4" style="color: black">{{$t('Project.tickets')}}</label>
                                                </v-card-title>
                                                <v-card color="#DCDCDC" flat>
                                                    <div v-for="ticket in tickets" :key="ticket.id">
                                                        <input v-model="selectedTicketsIDs" :value="ticket.id" type="checkbox" class="mb-2 ml-2">
                                                        <span class="ml-4">{{ticket.name}}</span>
                                                    </div>
                                                </v-card>
                                                <v-card-title style="padding: 0.1rem;" class="ml-2">
                                                    <input type="checkbox" class="mb-2" @click="selectallTasks">
                                                    <label class="ml-4" style="color: black">{{$t('Project.tasks')}}</label>
                                                </v-card-title>
                                               <v-card color="#DCDCDC" flat>
                                                   <div v-for="task in tasks" :key="task.id">
                                                       <input v-model="selectedTasksIDs" :value="task.id" type="checkbox" class="mb-2 ml-2">
                                                       <span class="ml-4">{{task.name}}</span>
                                                   </div>
                                               </v-card>
                                            </v-card>
                                        </v-container>
                                    </v-col>
                                </v-row>
                            </div>
                        </v-col>
                    </v-row>
                </v-col>
            </v-row>
    </v-container>

</template>

<script>
import { mapActions, mapGetters } from "vuex";
import { quillEditor } from "vue-quill-editor";
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";
import _ from "lodash";

export default {
    components: {
        quillEditor
    },
    data() {
        return {
            solo: true,
            ticketstab: 'ticketstable',
            loading: true,
            projectId: this.$route.params.id,
            form: new Form({
                id: "",
                name: "",
                owner: [],
                owners:[],
                description: "",
                task_rate: "",
                budget_hours: "",
                project_assign: [],
                assigns: [],
                status: {},
                status_id: '',
                selectedTickets: [],
                selectedTasks: [],
                estimated_time: 0,
            }),
            owner_id: "",
            selected: null,
            editorOption: {
                modules: {
                    toolbar: [
                        ["bold", "italic", "underline", "strike"],
                        ["blockquote", "code-block"],
                        [{ list: "ordered" }, { list: "bullet" }]
                    ]
                }
            },
            selectedTicketsIDs: [],
            selectedTasksIDs: [],
            allTickets: false,
            allTasks: false,
            all: false
        };
    },
    methods: {
        ...mapActions("project", ["getProjectById"]),
        selectallTickets(){
            this.allTickets = !this.allTickets;
            this.selectedTicketsIDs = []
            if(this.allTickets === true){
                Object.keys(this.tickets).forEach(key => {
                        this.selectedTicketsIDs.push(this.tickets[key].id)
                })
            }
            else{
                this.selectedTicketsIDs = []
            }
        },
        selectallTasks(){
            this.allTasks = !this.allTasks
            this.selectedTasksIDs = []
            if(this.allTasks === true){
                Object.keys(this.tasks).forEach(key => {
                    this.selectedTasksIDs.push(this.tasks[key].id)
                })
            }
            else{
                this.selectedTasksIDs = []
            }
        },
        selectall(){
            this.all = !this.all;
            this.allTickets = !this.all;
            this.allTasks = !this.all;
            if(this.all === true){
                Object.keys(this.tickets).forEach(key => {
                    this.selectedTicketsIDs.push(this.tickets[key].id)
                })
                Object.keys(this.tasks).forEach(key => {
                    this.selectedTasksIDs.push(this.tasks[key].id)
                })
            }
            else{
                this.selectedTicketsIDs = []
                this.selectedTasksIDs = []
            }
        },
        getOwners() {
            this.$Progress.start();
            this.$store
                .dispatch("owner/getOwners")
                .then(() => {
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },
        getResponsibles() {
            this.$store
                .dispatch("regularUser/getRegularUser")
                .then()
                .catch(error => {
                    console.log(error);
                });
        },
        async loadEditData() {
            this.$Progress.start();
            let response = await this.getProjectById(this.projectId)
                .then(() => {
                    this.$Progress.finish();
                    this.form.fill(this.singleProject);
                    this.form.name = ''
                    this.form.selected = _.map(this.form.project_assign, function(
                        value,
                        key
                    ) {
                        return value.name;
                    });
                    this.form.owner = this.singleProject.owners;
                    this.loading = false;
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },
        getTasksByProjectId(page = 1) {
            this.$Progress.start();
            this.$store
                .dispatch("task/getTasksByProjectId", { id: this.projectId })
                .then((response) => {
                    this.showTasks = true;
                    this.$Progress.finish();
                })
                .catch((error) => {
                    this.$Progress.fail();
                });
        },
        getTicketsByProjectId(page = 1) {
            this.$Progress.start();
            this.$store
                .dispatch("ticket/getTicketsByProjectId", {
                    id: this.projectId,
                })
                .then(response => {
                    this.showTickets = true;
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },
        copyProject(){
            this.form.assigns = [];
            this.form.project_assign.forEach(element => {
                // this.form.project_assign = this.form.project_assign.filter(function(
                //     obj
                // ) {
                //     return obj.id !== element.id;
                // });
                this.form.assigns.push(element.id);
            });
            this.form.owners =  [];
            this.form.owner.forEach(element => {
                // this.form.owner = this.form.owner.filter(function(
                //     obj
                // ) {
                //     return obj.id !== element.id;
                // });
                this.form.owners.push(element.id);
            });
            this.form.status_id = this.form.status.id
            this.form.selectedTickets = this.selectedTicketsIDs;
            this.form.selectedTasks = this.selectedTasksIDs;
            this.$Progress.start();
            this.$store
                .dispatch("project/copyProject", this.form)
                .then(response => {
                    this.$Progress.finish();
                    Toast.fire({
                        type: "success",
                        title: response.data.message
                    });
                    this.$router.push({ name: "projects.list" });
                })
                .catch(error => {
                    this.$Progress.fail();
                    this.form.errors.errors = error.response.data.errors;    
                });
        },
        getStatus() {
            this.$store
                .dispatch("ticket/getStatus")
                .then(response => {})
                .catch(error => {
                });
        },
    },
    mounted() {
        this.getOwners();
        this.getResponsibles();
        this.loadEditData();
        this.getTicketsByProjectId();
        this.getTasksByProjectId();
        this.getStatus();
    },
    computed: {
        ...mapGetters({
            singleProject: "project/activeSingleProject",
            owners: "owner/activeOwners",
            responsible: "regularUser/activeRegularUser",
            tickets: "ticket/activeTickets",
            tasks: "task/activeTasks",
            status: "ticket/activeStatus",
        })
    }
};
</script>

<style scoped>

.removeSpace{
    /*margin: 0;*/
    /*padding: 0;*/
    /*margin-bottom: -1rem;*/
    /*margin-top: -1rem;*/
}

.floatingArea{
    box-shadow: 0 3px 1px -2px rgba(0,0,0,.2),0 2px 2px 0 rgba(0,0,0,.14),0 1px 5px 0 rgba(0,0,0,.12);
}

.removeTopBottomPad{
    padding-top: 0;
    padding-bottom: 0;
}
.fontSize{
    font-size: large;
}
.line{
    background-color: #234FA3;
    width: 5.3rem;
    height: .2rem;
    border-radius: 5px;
}
.col{
    padding-bottom: 5px;
    padding-top: 0;
}
.multiselect__tag {
    background: #f0f0f0 ;
    border: 1px solid rgba(60, 60, 60, 0.26);
    color: black !important;
    margin-bottom: 0px !important;
    margin-right: 5px !important;
}
.gredient{
    background-image: linear-gradient(180deg, white, dimgrey);
}
</style>
