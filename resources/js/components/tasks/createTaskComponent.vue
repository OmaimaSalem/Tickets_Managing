<template>
    <v-row justify="center">
        <v-btn
            color="#A0466F"
            dark
            absolute
            bottom
            right
            fab
            @click.stop="activeDialog"
        >
            <v-icon>fas fa-plus</v-icon>
        </v-btn>
        <div data-app="true">
            <v-dialog v-model="dialog" persistent max-width="700px">
                <v-card>
                    <v-card-title>
                        <span class="headline">Create New Task</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row justify="center">
                                <v-col cols="12">
                                    <v-text-field label="Task Name" v-model="form.name" required></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <quill-editor
                                        v-model="form.description"
                                        ref="myQuillEditor"
                                        :options="editorOption"
                                    ></quill-editor>
                                </v-col>
                                <v-col cols="6">
                                    <v-select
                                        @change="onStatusChange($event)"
                                        :items="statusArr"
                                        label="Status"
                                        chips
                                        clearable
                                        v-model="statusName"
                                    ></v-select>
                                </v-col>
                                <v-col cols="6">
                                    <v-select
                                        @change="onProjectChange($event)"
                                        :items="projectsArr"
                                        label="Projects"
                                        chips
                                        clearable
                                        v-model="projectName"
                                    ></v-select>
                                </v-col>
                                <v-col cols="6">
                                    <v-select
                                        @change="onTicketsChange($event)"
                                        :items="ticketsArr"
                                        label="Ticket"
                                        chips
                                        clearable
                                        v-model="ticketName"
                                    ></v-select>
                                </v-col>
                                <v-col cols="6">
                                    <v-select
                                        @change="onResponsibleChange($event)"
                                        :items="responsilbeArr"
                                        label="Assign To"
                                        chips
                                        clearable
                                        v-model="assignedName"
                                    ></v-select>
                                </v-col>

                                <v-row>
                                    <v-col cols="6">
                                        <v-date-picker color="#A0466F" @change="handleDate" v-model="date"></v-date-picker>
                                    </v-col>
                                    <v-col cols="6">

                                        <v-time-picker color="#A0466F" format="24hr" @change="handleDate" v-model="time"></v-time-picker>
                                    </v-col>
                                </v-row>

                            </v-row>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" text @click="closeTask">Close</v-btn>
                        <v-btn color="blue darken-1" text @click="createTask">Save</v-btn>
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
        statusName: "",
        ticketName :"",
        projectName :"",
        assignedName :"",
        dialog: false,
        menu: false,
        date: new Date().toISOString().substr(0, 10),
        time: new Date().toISOString().substr(12, 7),
        statusArr: [],
        projectsArr: [],
        ticketsArr : [],
        responsilbeArr : [],

        form: new Form({
            id: "",
            name: "",
            description: null,
            status_id: '',
            project_id: "",
            ticket_id : "",
            due_date: '',
            responsible_id : "",
        }),
        editorOption: {
            placeholder: 'Task Description',
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
    methods: {
        handleDate() {
            const date = new Date(this.date);
            if(typeof this.time === 'string') {
                let hours = this.time.match(/^(\d+)/)[1]
                const minutes = this.time.match(/:(\d+)/)[1]
                date.setHours(hours)
                date.setMinutes(minutes)
                let formatted_date = date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate() + " " + date.getHours() + ":" + date.getMinutes()
                this.form.due_date = formatted_date;
            } else {
                this.form.due_date = date;
            }

        },
        onStatusChange(event) {
            this.status.forEach(stat => {
                if(stat.name == event) {
                    return this.form.status_id = stat.id;
                }
            })
            this.statusName = event;
        },
        onProjectChange(event) {
            this.projects[0].forEach(project => {
                if(project.name == event) {
                    return this.form.project_id = project.id;
                }
            })
            this.projectName = event;
        },
        onTicketsChange(event) {
            this.tickets.data.forEach(ticket => {
                if(ticket.name == event) {
                    return this.form.ticket_id = ticket.id;
                }
            })
            this.ticketName = event;
        },
        onResponsibleChange(event) {
            this.responsible.forEach(responsible => {
                if(responsible.name == event) {
                    return this.form.responsible_id = responsible.id;
                }
            })
            this.assignedName = event
        },
        activeDialog() {
            this.dialog = true;
            this.setStatusArr(this.status)
            this.setProjectsArr(this.projects[0])
            this.setTicketsArr(this.tickets.data)
            this.setResponsibleArr(this.responsible)
        },
        createTask() {
            if (!this.form.responsible_id) {
                Toast.fire({
                    type: "error",
                    title: "Responsible Missing"
                });
                return;
            }
            this.$Progress.start();
            this.$store
                .dispatch("task/createTask", this.form)
                .then(response => {
                    this.statusName =""
                    this.ticketName =""
                    this.projectName =""
                    this.assignedName =""
                    this.dialog = false;
                    this.form.reset();
                    this.$Progress.finish();
                    this.$emit('TaskCreated');
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
        closeTask(){
            this.dialog = false;
            this.form.reset();
        },
        getProjects() {
            let queryParams = {
                filters: [],
                global_search: "",
                per_page: 15,
                page: 1
            }
            this.$store
                .dispatch("project/getProjects", queryParams)
                .then(() => {
                })
                .catch(() => {
                });
        },
        getTickets() {
            let queryParams = {
                filters: [],
                global_search: "",
                per_page: 15,
                page: 1
            }
            this.$store
                .dispatch("ticket/getTickets", queryParams)
                .then(() => {
                })
                .catch(() => {
                });
        },
        getStatus() {
            let queryParams = {
                filters: [],
                global_search: "",
                per_page: 15,
                page: 1
            }
            this.$store
                .dispatch("ticket/getStatus", queryParams)
                .then(() => {
                })
                .catch(() => {
                });
        },
        getResponsible() {
            let queryParams = {
                filters: [],
                global_search: "",
                per_page: 15,
                page: 1
            }
            this.$store
                .dispatch("regularUser/getRegularUser", queryParams)
                .then(() => {
                })
                .catch(() => {
                });
        },

        setStatusArr(status) {
            status.forEach(stat => {
                this.statusArr.push(stat.name);
            })
        },
        setProjectsArr(projects) {
            projects.forEach(project => {
                this.projectsArr.push(project.name);
            })
        },
        setTicketsArr(tickets) {
            tickets.forEach(ticket => {
                this.ticketsArr.push(ticket.name);
            })
        },
        setResponsibleArr(responsible) {
            responsible.forEach(responsibl => {
                this.responsilbeArr.push(responsibl.name);
            })
        },


    },
    components: {
        quillEditor,
    },
    computed: {
        ...mapGetters({
            projects: "project/projectByOwners",
            status: "ticket/activeStatus",
            user: "user/activeSingleUser",
            tickets: "ticket/activeTickets",
            responsible: "regularUser/activeRegularUser",
        })
    },

    mounted() {
        this.getProjects();
        this.getTickets();
        this.getStatus();

    }
}
</script>
