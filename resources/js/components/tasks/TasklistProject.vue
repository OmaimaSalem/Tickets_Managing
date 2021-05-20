<template>
    <div class>
        <h3 style="font-weight: bold;text-align: center;margin-bottom:1rem;margin-top:2rem">Project Tasks</h3>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{$t('Task.tasksTable')}}</h3>

                <div class="card-tools">
                    <button type="submit" class="btn btn-success btn-sm" @click="newModel">
                        <i class="fas fa-plus fa-fw"></i>
                        <span class="d-none d-lg-inline">{{$t('Task.newTask')}}</span>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-sm">
                    <thead>
                    <tr>
                        <th width="2%">ID</th>
                        <th width="20%">{{$t('Task.name')}}</th>
                        <th width="10%">{{$t('Task.status')}}</th>
                        <th width="12%">{{$t('Task.createdAt')}}</th>
                        <th width="10%">{{$t('Task.priorty')}}</th>
                        <th width="10%">{{$t('Task.deadline')}}</th>
                        <th width="10%">{{$t('Task.project')}}</th>
                        <th width="10%">{{$t('Task.responsible')}}</th>
                        <th width="10%">{{$t('Task.action')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="task in tasks.data" :key="task.id">
                        <td>{{ task.id }}</td>
                        <td>
                            <router-link :to="'/admin/task/' + task.id">{{ task.name }}</router-link>
                        </td>
                        <td>
                            <div class="badge bg-primary">{{ task.status.name }}</div>
                        </td>
                        <td>
                            <div class="small">{{ task.created_at | DateWithTime }}</div>
                        </td>
                        <td>
                            <div class="small">{{ task.priority }}</div>
                        </td>
                        <td>
                            <div class="small">{{ task.deadline | DateOnly}}</div>
                        </td>
                        <td>
                <span v-if="task.project">
                  <router-link :to="'/admin/project/' + task.project.id">{{ task.project.name }}</router-link>
                </span>
                        </td>
                        <td>
                <span v-if="task.responsible">
                  <router-link
                      :to="'/admin/profile/' + task.responsible.id"
                  >{{ task.responsible.name }}</router-link>
                </span>
                        </td>
                        <td>
                            <a href="#" @click="editModel(task)" class="btn btn-primary btn-xs">
                                <i class="fas fa-edit fa-fw"></i>
                            </a>
                            <a href="#" @click="deleteTask(task.id)" class="btn btn-danger btn-xs">
                                <i class="fas fa-trash fa-fw"></i>
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->

        <!-- Modal -->
        <div
            class="modal fade"
            id="newTask"
            tabindex="-1"
            role="dialog"
            aria-labelledby="newTaskLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5
                            v-show="!editMode"
                            class="modal-title"
                            id="newTaskLabel"
                        >{{$t('Task.createNewTask')}}</h5>
                        <h5 v-show="editMode" class="modal-title" id="newTaskLabel">{{$t('Task.editTask')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form
                        @submit.prevent="editMode ? editTask(form) : createTask(form)"
                        @keydown="form.onKeydown($event)"
                    >
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name">{{$t('Task.taskName')}}</label>
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
                                <label for="description">{{$t('Task.taskDesc')}}</label>
                                <quill-editor
                                    id="comments-editor"
                                    v-model="form.description"
                                    ref="myQuillEditor"
                                    :options="editorOption"
                                ></quill-editor>
                                <has-error :form="form" field="description"></has-error>
                            </div>
                            <div class="form-group">
                                <label for="name">{{$t('Task.status')}}</label>
                                <multiselect
                                    v-model="form.status"
                                    :options="status"
                                    :placeholder="$t('Task.selectOne')"
                                    label="name"
                                    track-by="name"
                                    @input="opt => form.status_id = opt.id"
                                ></multiselect>

                                <has-error :form="form" field="status_id"></has-error>
                            </div>
                            <div class="form-group">
                                <label for="name">Ticket</label>
<!--                                <select-->
<!--                                    v-model="form.ticket"-->
<!--                                    :options="ticket.data"-->
<!--                                    label="name"-->
<!--                                    track-by="name"-->
<!--                                    :placeholder="$t('Task.selectOne')"-->
<!--                                    @input="opt => form.ticket_id = opt.id"-->
<!--                                ></select>-->

                                <multiselect
                                    v-model="form.ticket"
                                    :options="tickets.data"
                                    label="name"
                                    track-by="name"
                                    :placeholder="$t('Task.selectOne')"
                                    @input="opt => form.ticket_id = opt.id"
                                ></multiselect>

                                <has-error :form="form" field="status_id"></has-error>
                            </div>
                            <div class="form-group">
                                <label for="name">{{$t('Task.responsible')}}</label>
                                <multiselect
                                    v-model="form.responsible"
                                    :options="responsible"
                                    :close-on-select="true"
                                    :clear-on-select="false"
                                    :preserve-search="true"
                                    :placeholder="$t('Task.selectOne')"
                                    label="name"
                                    :preselect-first="true"
                                    @input="opt => form.responsible_id = opt.id"
                                >
                                    <template slot="selection" slot-scope="{ values, search, isOpen }">
                    <span
                        class="multiselect__single"
                        v-if="values.length &amp;&amp; !isOpen"
                    >{{ values.length }} {{$t('Task.options')}}</span>
                                    </template>
                                </multiselect>
                                <has-error :form="form" field="responsible_id"></has-error>
                            </div>
                            <div class="form-group" v-if="form.priority">
                                <label for="priority">{{$t('Task.priorty')}}</label>
                                <multiselect
                                    class="clearfix"
                                    v-model="form.priority"
                                    :options="priorityList"
                                    :close-on-select="true"
                                    :allow-empty="false"
                                    :show-labels="false"
                                    :placeholder="$t('Task.selectOne')"
                                ></multiselect>
                                <has-error :form="form" field="priority"></has-error>
                            </div>
                            <div class="form-group">
                                <label for="deadline">{{$t('Task.deadline')}}</label>
                                <date-picker
                                    v-model="form.deadline"
                                    lang="en"
                                    type="datetime"
                                    format="DD-MM-YYYY HH:mm"
                                    :minute-step="15"
                                    value-type="format"
                                    input-class="form-control"
                                ></date-picker>
                                <has-error :form="form" field="deadline"></has-error>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button
                                v-show="!editMode"
                                type="submit"
                                class="btn btn-primary"
                            >{{$t('Task.save')}}</button>
                            <button
                                v-show="editMode"
                                type="submit"
                                class="btn btn-success"
                            >{{$t('Task.update')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import { quillEditor } from "vue-quill-editor";
import DatePicker from "vue2-datepicker";
import moment from "moment";

// require styles
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";

export default {
    data() {
        return {
            editMode: false,
            isDisabled: false,

            form: new Form({
                id: "",
                name: "",
                description: "",
                status: {},
                project_id: this.$route.params.id,
                owner: {},
                project: {
                    id: "",
                    name: ""
                },
                ticket :{},
                ticket_id: "",
                responsible: {},
                responsible_id: "",
                priority: "",
                deadline: ""
            }),
            priorityList: ["normal", "high", "low"],
            editorOption: {
                modules: {
                    toolbar: [
                        ["bold", "italic", "underline", "strike"],
                        ["blockquote", "code-block"],
                        [{ list: "ordered" }, { list: "bullet" }]
                    ]
                }
            }
        };
    },
    methods: {
        newModel() {
            this.editMode = false;
            this.form.reset();
            this.form.clear();
            $("#newTask").modal("show");
            this.form.priority = "normal";
            this.form.deadline = moment()
                .add(1, "day")
                .format("YYYY-MM-DD HH:mm:ss");
        },
        editModel(task) {
            this.editMode = true;
            this.form.reset();
            this.form.clear();
            if (this.singlePage) {
                if(this.ticket.project) {
                    this.form.project_id = this.ticket.project.id;
                }
                this.form.ticket_id = this.ticket.id;
                this.isDisabled = true;
            }
            $("#newTask").modal("show");
            this.form.fill(task);
            this.form.owner = task.ticket.owner
            this.getProjects(task.ticket.owner.id);
        },
        getStatus() {
            this.$store
                .dispatch("task/getStatus")
                .then(response => {})
                .catch(error => {
                    console.log(error);
                });
        },
        getOwners() {
            this.$store
                .dispatch("owner/getOwners")
                .then(response => {})
                .catch(error => {
                    console.log(error);
                });
        },
        getTickets(){
            this.$store
                .dispatch("ticket/getTickets")
                .then(response => {})
                .catch(error => {
                    console.log(error);
                });
        },
        getProjects(owner_id) {
            this.$store
                .dispatch("project/getProjectsByOwner", owner_id)
                .then()
                .catch(error => {
                    console.log(error);
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
        createTask(data) {
            this.$Progress.start();
            this.$store
                .dispatch("task/createTask", data)
                .then(response => {
                    $("#newTask").modal("hide");
                    this.$Progress.finish();
                    Toast.fire({
                        type: "success",
                        title: response.data.message
                    });
                })
                .catch(error => {
                    this.$Progress.fail();
                    if (error.response) {
                        this.form.errors.errors = error.response.data.errors;
                    }
                });
        },
        editTask(data) {
            this.$Progress.start();
            this.$store
                .dispatch("task/editTask", data)
                .then(response => {
                    $("#newTask").modal("hide");
                    this.$Progress.finish();
                    Toast.fire({
                        type: "success",
                        title: response.data.message
                    });
                })
                .catch(error => {
                    this.$Progress.fail();
                    if (error.response) {
                        this.form.errors.errors = error.response.data.errors;
                    }
                });
        },
        deleteTask(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then(result => {
                if (result.value) {
                    this.$Progress.start();
                    this.$store
                        .dispatch("task/deleteTask", id)
                        .then(response => {
                            this.$Progress.finish();
                            Toast.fire({
                                type: "success",
                                title: response.data.message
                            });
                        })
                        .catch(error => {
                            this.$Progress.fail();
                            console.log(error);
                            Toast.fire({
                                type: "error",
                                title: error.response.data.message
                            });
                        });
                }
            });
        }
    },
    mounted() {
        this.getStatus();
        this.getOwners();
        this.getResponsibles();
        this.getTickets();

    },
    computed: {
        ...mapGetters({
            status: "task/activeStatus",
            owners: "owner/activeOwners",
            projects: "project/projectByOwners",
            responsible: "regularUser/activeRegularUser",
            // ticket: "ticket/activeTicket",
            tickets: "ticket/activeTickets",
            project : "project/activeSingleProject"
        })
    },
    props: {
        tasks: {
            type: Object,
            required: true
        },
        singlePage: false,
    },
    directives: {
        trim: {
            inserted: function(el, maxWords = 4) {
                var str = el.innerHTML;
                var resultString =
                    str
                        .split(" ")
                        .slice(0, maxWords.value)
                        .join(" ") + "...";
                el.innerHTML = resultString;
            }
        }
    },
    components: {
        quillEditor,
        DatePicker
    }
};
</script>

<style scoped>
.mx-datepicker {
    display: block;
    width: unset;
}
</style>
