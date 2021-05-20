<template>
  <div class="board row justify-content-center">
    <template>
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body search-bar">
            <div class="filters-dev row">
              <div class="col-sm-4">
                  <multiselect
                    v-model="project_filter"
                    :options="AllProjects"
                    :close-on-select="true"
                    :clear-on-select="false"
                    :preserve-search="true"
                    :preselect-first="false"
                    :placeholder="$t('Kanban.allProject')"
                    style="border-radius: 10px;height: 4rem"
                    label="name"
                    track-by="id"
                  ></multiselect>
              </div>

              <div class="col-sm-4">
                    <multiselect
                    v-model="responsible_filter"
                    :options="responsible"
                    :close-on-select="true"
                    :clear-on-select="false"
                    :preserve-search="true"
                    :preselect-first="false"
                    :placeholder="$t('Kanban.responsible')"
                    style="border-radius: 10px;height: 4rem"
                    label="name"
                    track-by="id"
                  ></multiselect>



              </div>
              <div class="col-sm-4 align-text-bottom">
                <button class="btn btn-primary" @click="filter_tasks">{{$t('Kanban.go')}}</button>
                <router-link :to="{ name: 'tasks.list'}" class="btn btn-default float-right">
                  <i class="fas fa-info fa-fw"></i> {{$t('Kanban.defaultView')}}
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>

    <template v-if="!loading && (!AllCards.columns || AllCards.length <= 0)">
      <div class="col-sm-12">
        <div class="alert alert-secondary">{{$t('Kanban.noTask')}}</div>
      </div>
    </template>

    <div class="col-sm-12" v-if="loading && tasks_button_clicked">
      <div class="alert alert-secondary">{{$t('Kanban.loading')}}</div>
    </div>

    <template v-else>
      <div class="col-sm-12" v-if="!loading && (AllCards.columns && AllCards.columns.length > 0)">
        <div class="card kanban-body">
          <div class="card-body">
            <div class="row">

              <div
                v-for="(column , $columnIndex) of AllCards.columns"
                :key="$columnIndex++"
                @drop="moveTaskOrColumn($event, column.tasks, $columnIndex)"
                @dragover.prevent
                @dragenter.prevent
                class="col-sm-12 col-md-3"
              >
                <div class="card bg-light">
                  <div class="card-header">{{ column.name | capitalized }}</div>
                  <div
                    id="scrollbar-style"
                    class="card-body p-1 overflow-auto"
                    style="min-height: 100px; min-height: 60vh;"
                  >
                  <li class="list-group-item m-1">
                    <input
                        type="text"
                        class="block p-0 col-12 bg-transparent border-0"
                        placeholder="+ Enter new task"
                        @keyup.enter="createTask($event, $columnIndex)"
                      />
                  </li>
                    <div
                      class="bg-white p-1 m-1 border"
                      v-for="(task, $taskIndex) of column.tasks"
                      :key="$taskIndex"
                      draggable
                      @dblclick="editModal(task)"
                      @dragover.prevent
                      @dragenter.prevent
                      @dragstart="pickupTask($event, task.id, $columnIndex, $taskIndex)"
                      @drop.stop="moveTaskOrColumn($event, column.tasks, $columnIndex, $taskIndex)"
                    >
                      <span v-if="!showEdit || editId !== task.id"><router-link :to="'task/'+task.id">{{ task.name }}</router-link></span>
                      <button @click="show_task_panel(task.id,$event)" class="float-right btn-dots">
                        <i class="fas fa-ellipsis-h"></i>
                      </button>

                        <div :ref="'task_menu_'+task.id"
                              v-show="show_panel && panel_id == task.id"
                              :class="
                                        [task.status_id == 4
                                            ? 'task-panel task-panel-left'
                                            : 'task-panel',
                                        is_mobile ? 'is_mobile' : '']
                                    "
                            >
                              <ul class="list-unstyled p-2">
                                <li class="p-1 mb-1" @click="change_responsible_modal(task)">
                                  <i class="fas fa-user-edit"></i>&nbsp;Change responsible
                                </li>
                                <li class="p-1 mb-1" @click="changeStatusModal(task)">
                                  <i class="fas fa-arrows-alt"></i>&nbsp;Move
                                </li>
                                <li class="p-1 mb-1" @click="show_dueDateModel(task)">
                                  <i class="far fa-clock"></i>&nbsp;Change Due Date
                                </li>
                                <li class="p-1 mb-1" @click="editModal(task)">
                                  <i class="fas fa-edit"></i>&nbsp;Edit
                                </li>
                                <li class="p-1 mb-1" @click="deleteTask(task.id)">
                                  <i class="fas fa-trash" ></i>&nbsp;Delete
                                </li>
                                <li class="p-1 mb-1" @click="hide_task_panel">
                                  <i class="fas fa-times"></i>&nbsp;Close
                                </li>
                              </ul>
                            </div>


                      <br />

                      <button @click="editTitle(task)" class="float-right btn-dots">
                        <i class="fas fa-edit"></i>
                      </button>

                      <div v-if="showEdit && editId == task.id">
                        <textarea @paste="paste_function" style="background-color:#ccc;width:100%;heigh:100%;" type="text" v-model="taskName">
                        </textarea>
                        <button @click="updateTitle(task.id)" class="btn btn-success float-right">Save</button>

                      </div>
                      
                      <br />
                      {{ task.responsible ? task.responsible.name : '' }}
                      <br />
                      {{ task.project ? task.project.name : '' }}
                    </div>


                  </div>
                  <div class="card-footer p-2">
                    <input
                      type="text"
                      class="block p-0 col-12 bg-transparent border-0"
                      placeholder="+ Enter new task"
                      @keyup.enter="createTask($event, $columnIndex)"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
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
                <h5 class="modal-title" id="newTaskLabel">{{$t('Kanban.editTask')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form @submit.prevent="editTask(form)" @keydown="form.onKeydown($event)">
                <div class="modal-body">
                  <div class="form-group">
                    <label for="name">{{$t('Kanban.taskName')}}</label>
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
                    <label for="description">{{$t('Kanban.taskDesc')}}</label>
                    <quill-editor
                      id="comments-editor"
                      v-model="form.description"
                      ref="myQuillEditor"
                      :options="editorOption"
                    ></quill-editor>
                    <has-error :form="form" field="description"></has-error>
                  </div>
                  <div class="form-group">
                    <label for="name">{{$t('Kanban.responsible')}}</label>
                    <multiselect
                      v-model="form.responsible"
                      :options="responsible"
                      :close-on-select="true"
                      :clear-on-select="false"
                      :preserve-search="true"
                      :placeholder="$t('Kanban.selectOne')"
                      label="name"
                      :preselect-first="true"
                      @input="opt => form.responsible_id = opt.id"
                    ></multiselect>
                    <has-error :form="form" field="responsible_id"></has-error>
                  </div>
                  <div class="form-group" v-if="form.priority">
                    <label for="priority">{{$t('Kanban.priorty')}}</label>
                    <multiselect
                      class="clearfix"
                      v-model="form.priority"
                      :options="priorityList"
                      :close-on-select="true"
                      :allow-empty="false"
                      :show-labels="false"
                      :placeholder="$t('Kanban.selectOne')"
                    ></multiselect>
                    <has-error :form="form" field="priority"></has-error>
                  </div>
                  <div class="form-group">
                    <label for="deadline">{{$t('Kanban.deadline')}}</label>
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
                  <button type="submit" class="btn btn-success">{{$t('Kanban.update')}}</button>
                </div>
              </form>
            </div>
          </div>
        </div><!-- modal -->



        <!-- Modal -->
        <div class="modal fade" id="responsibleModal" tabindex="-1" role="dialog" aria-labelledby="responsibleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="responsibleModalLabel">{{$t('Kanban.responsible')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                  <div class="form-group">
                    <multiselect
                      v-model="form.responsible"
                      :options="responsible"
                      :close-on-select="true"
                      :clear-on-select="false"
                      :preserve-search="true"
                      :placeholder="$t('Kanban.selectOne')"
                      label="name"
                      track-by="id"
                      :preselect-first="true"
                    ></multiselect>
                    <has-error :form="form" field="responsible_id"></has-error>
                  </div>

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" @click="change_responsible">Save changes</button>
              </div>
            </div>
          </div>
        </div>
        <!-- modal -->



      <!-- Modal -->
        <div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">{{$t('Kanban.status')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                  <div class="form-group">
                    <multiselect
                      v-model="status_id"
                      :options="status"
                      :close-on-select="true"
                      :clear-on-select="false"
                      :preserve-search="true"
                      :placeholder="$t('Kanban.selectOne')"
                      label="name"
                      :preselect-first="true"
                    ></multiselect>
                    <has-error :form="form" field="status_id"></has-error>
                  </div>

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" @click="change_status">Save changes</button>
              </div>
            </div>
          </div>
        </div>
        <!-- modal -->



<!-- Modal -->
        <div class="modal fade" id="dueDateModel" tabindex="-1" role="dialog" aria-labelledby="dueDateModelLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="dueDateModelLabel">{{$t('Kanban.status')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                  <div class="form-group">
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" @click="change_due_date">Save changes</button>
              </div>
            </div>
          </div>
        </div>
        <!-- modal -->
      </div>
    </template>


                        <div class="img-wrapper" v-for="(image, index) in images" :key="index">
                        <img style="max-width:230px; max-height: 230px;" thumbnail center rounded  class="max-image-upload" :src="image" :alt="`Image Uplaoder ${index}`">
                    </div>


  </div>
</template>

<script>
import { quillEditor,Quill } from "vue-quill-editor";
import DatePicker from "vue2-datepicker";
import { mapGetters, mapState } from "vuex";
// require styles
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";
import { ImageDrop } from 'quill-image-drop-module'

import { Container, Draggable, smoothDnD } from 'vue-smooth-dnd'
// import { applyDrag, generateItems } from '../utils'


export default {
  data() {
    return {
      loading: false,
      show_panel: false,
      panel_id: null,
      is_mobile: true,
      projectTitle: this.$route.params.pagetitle || "AllCards",
      status_id:null,
      old_status_id: null,
      task_id:null,
      images:[],
      files:[],
      status:[
            {name:'Open',id:1},
            {name:'Pending',id:2},
            {name:'In-progress',id:3},
            {name:'Done',id:4}
      ],
      form: new Form({
        id: "",
        name: "",
        description: "",
        status: {},
        project: {},
        project_id: "",
        ticket: [],
        ticket_id: "",
        responsible: {},
        responsible_id: "",
        priority: "",
        deadline: ""
      }),
      showEdit:false,
      editId:null,
      responsible_filter: "",
      project_filter: "",
      tasks_button_clicked: false,
      taskName: '',
      priorityList: ["normal", "high", "low"],
      editorOption: {
        modules: {
          toolbar: [
            ["bold", "italic", "underline", "strike"],
            ["blockquote", "code-block"],
            [{ list: "ordered" }, { list: "bullet" }]
          ],
          imageDrop: true
        }
      }
    };
  },
  methods: {
    paste_function(pasteEvent, callback) {
      if(pasteEvent.clipboardData == false){
          if(typeof(callback) == "function"){
              console.log('Undefined ')
              callback(undefined);
          }
      };

      var items = pasteEvent.clipboardData.items;

              if(items == undefined){
                  if(typeof(callback) == "function"){
                      callback(undefined);
                      console.log('Undefined 2')
                  }
              };
              for (var i = 0; i < items.length; i++) {
                  
                  if (items[i].type.indexOf("image") == -1) continue;
                  var blob = items[i].getAsFile();
                  this.addImage(blob)
              }
    
          },          
          
          addImage(file){
            if (!file.type.match('image.*')) {
                return new Promise((reject) => {
                    const error = {
                        message: 'Solo puede arrastrar imágenes.',
                        response: {
                            status: 200
                        }
                    }
                    this.$obtenerError(error)
                    reject()
                    return;
                })
            }

            this.files.push(file);
            const img = new Image(),
                reader = new FileReader();

            reader.onload = (e) => this.images.push(e.target.result);
            const str = JSON.stringify(file);

            reader.readAsDataURL(file);
           },


    change_responsible_modal(task){
      this.form.responsible = task.responsible ? this.responsible.find(res => res.id == task.responsible.id) : [];
      this.form.id = task.id;
      $('#responsibleModal').modal('show');
    },
    change_responsible(){
      this.$Progress.start();
      this.$store
        .dispatch("task/editkanbanTask", {id:this.form.id,responsible_id: this.form.responsible.id})
        .then(response => {
          this.$Progress.finish();
          $('#responsibleModal').modal('hide');
          Toast.fire({
            type: "success",
            title: response.data.message
          });
          this.showEdit = false;
        })
        .catch(error => {
          this.$Progress.fail();
          if (error.response) {
            this.form.errors.errors = error.response.data.errors;
          }
        });

},

    changeStatusModal(task){
      this.status_id = this.status.find(stat => stat.id == task.status_id);
      this.old_status_id = task.status_id;
      this.task_id = task.id;
      $('#statusModal').modal('show');
    },
    change_status(){
      this.$Progress.start();
      this.$store
        .dispatch("task/editkanbanTask", {id:this.task_id,status_id: this.status_id.id,old_status_id:this.old_status_id})
        .then(response => {
          this.$Progress.finish();
          $('#statusModal').modal('hide');
          Toast.fire({
            type: "success",
            title: response.data.message
          });
          this.showEdit = false;
        })
        .catch(error => {
          console.log(error);
          this.$Progress.fail();
          if (error.response) {
            this.form.errors.errors = error.response.data.errors;
          }
        });
    },
    show_dueDateModel(task){
      this.form.id = task.id,
      this.form.deadline = task.deadline;
      $('#dueDateModel').modal("show");
    },
    change_due_date(){
      this.$Progress.start();
      this.$store
        .dispatch("task/editkanbanTask", {id:this.form.id,deadline:this.form.deadline})
        .then(response => {
          this.$Progress.finish();
          $('#dueDateModel').modal('hide');
          Toast.fire({
            type: "success",
            title: response.data.message
          });
          this.showEdit = false;
        })
        .catch(error => {
          console.log(error);
          this.$Progress.fail();
          if (error.response) {
            this.form.errors.errors = error.response.data.errors;
          }
        });
    },
    hide_task_panel(){
      this.show_panel = false;
    },
    show_task_panel(task_id,event){
      this.panel_id = task_id,
      this.show_panel = !this.show_panel;
      let $ref = this.$refs['task_menu_'+task_id][0];
      $ref.style.top = (event.target.offsetTop - 20)+'px'; 
    },
    cancelEditTitle(){
       this.showEdit = false;
    },
    deleteTask(id){
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
        .dispatch("task/deleteTask", id)
        .then(response => {
          this.$Progress.finish();
          Toast.fire({
            type: "success",
            title: response.data.message
          });
          this.showEdit = false;
        })
        .catch(error => {
          console.log(error);
          this.$Progress.fail();
          if (error.response) {
            this.form.errors.errors = error.response.data.errors;
          }
        });





        }});
    },
    editTitle(task){
      if(this.editId == task.id){
       this.showEdit = !this.showEdit;
      }else {
        this.showEdit = true;
      }
      this.editId    = task.id;
      this.taskName = task.name;
    },
    updateTitle(id){

      this.$Progress.start();
      this.$store
        .dispatch("task/editkanbanTask", {id:id,name: this.taskName})
        .then(response => {
          this.$Progress.finish();
          Toast.fire({
            type: "success",
            title: response.data.message
          });
          this.showEdit = false;
        })
        .catch(error => {
          this.$Progress.fail();
          if (error.response) {
            this.form.errors.errors = error.response.data.errors;
          }
        });


    },
    getAllTasksForBoard($filter_params) {
      $filter_params.project = $filter_params.project ? $filter_params.project.id : null;
      $filter_params.responsible = $filter_params.responsible ? $filter_params.responsible.id : null;
      this.$Progress.start();
      this.$store
        .dispatch("task/getAllTasksForBoard", $filter_params)
        .then(() => {
          this.$Progress.finish();
          this.loading = false;
          this.tasks_button_clicked = false;
        })
        .catch(error => {
          this.$Progress.fail();
          if (error.response) {
            this.form.errors.errors = error.response.data.errors;
          }
          this.loading = false;
        });
    },
    pickupTask(e, taskId, fromColumnIndex, taskIndex) {
      e.dataTransfer.effectAllowed = "move";
      e.dataTransfer.dropEffect = "move";
      e.dataTransfer.setData("task-id", taskId);
      e.dataTransfer.setData("from-task-index", taskIndex);
      e.dataTransfer.setData("from-column-index", fromColumnIndex);
    },
    moveTaskOrColumn(e, toTasks, toColumnIndex, toTaskIndex) {
      this.moveTask(
        e,
        toTasks,
        toColumnIndex,
        toTaskIndex !== undefined ? toTaskIndex : 0
      );
    },
    moveTask(e, toTasks, toColumnIndex, toTaskIndex) {
      const fromColumnIndex = e.dataTransfer.getData("from-column-index");
      const taskId = e.dataTransfer.getData("task-id");
      const fromTaskIndex = parseInt(e.dataTransfer.getData("from-task-index"));


      if (toTaskIndex !== undefined) {
        this.$store.commit("task/MOVE_TASK", {
          fromColumnIndex,
          toColumnIndex,
          taskId,
          fromTaskIndex,
          toTaskIndex
        });
        let previousTaskId =
          toTaskIndex == 0 ? 0 : toTasks[toTaskIndex - 1].id;
        this.$store.dispatch("task/moveTask", {
          fromColumnIndex,
          toColumnIndex,
          taskId,
          fromTaskIndex,
          toTaskIndex
        });
      }
    },
    createTask(e, columnIndex) {
      // const projectId = this.$route.params.projectId;
      // const userId = this.$route.params.projectId;

      const projectId =  this.project_filter ? this.project_filter.id : null;
      const userId = this.responsible_filter ? this.responsible_filter.id : null;

      this.$store.dispatch("task/createkanbanTask", {
        columnIndex,
        projectId,
        userId,
        title: e.target.value
        });
      // clear the input
      e.target.value = "";
    },
    editModal(task) {
      this.form.reset();
      this.form.clear();
      this.form.fill(task);
      $("#newTask").modal("show");
    },
    editTask(data) {
      this.$Progress.start();
      this.$store
        .dispatch("task/editkanbanTask", data)
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
    getResponsibles() {
      this.$store
        .dispatch("regularUser/getRegularUser",{dropdown:true})
        .then()
        .catch(error => {
          console.log(error);
        });
    },
    getAllProjects() {
      this.$store
        .dispatch("task/getAllProjects")
        .then()
        .catch(error => {
          console.log(error);
        });
    },
    filter_tasks() {
      this.$store.commit("task/update_responsible_id", this.responsible_filter);
      this.loading = true;
      this.tasks_button_clicked = true;
      var $filter = {
        responsible: this.responsible_filter,
        project: this.project_filter,
        index: true
      };
      localStorage.setItem("tasksKanbanFilter",JSON.stringify($filter));
      this.getAllTasksForBoard($filter);
    },
    
  },
  computed: {
    // ...mapState(["AllCards"]),
    ...mapGetters({
      responsible: "regularUser/activeRegularUser",
      AllCards: "task/AllCards",
      AllProjects: "task/AllProjects"
    })
  },
  components: {
    quillEditor,
    DatePicker
  },
  mounted() {

 
    if (window.screen.availWidth <= 425) {
      this.is_mobile = true;
    } else {
      this.is_mobile = false;
    }


      var $filter = JSON.parse(localStorage.getItem("tasksKanbanFilter"));

      if(!$filter) {
        $filter = {};
      } else {
        this.responsible_filter = $filter.responsible;
        this.project_filter = $filter.project;
      }
      
      this.getAllTasksForBoard($filter);






    this.loading = true;
    this.tasks_button_clicked = false;
    this.getResponsibles();
    this.getAllProjects();
    let $this = this;
    $('body').click(function(event){
      if (event.target.className != "fas fa-ellipsis-h") {
          $this.show_panel = false;
      }
    })
  },
  filters: {
    capitalized: function(value) {
      return value.charAt(0).toUpperCase() + value.slice(1);
    }
  }
};
</script>

<style scoped>

.card-body.search-bar{
  padding: 0 1.25rem !important;
  margin-bottom: -20px;
}

.card.kanban-body{
  margin-top: -35px;
}


#scrollbar-style::-webkit-scrollbar-track {
  -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
  background-color: #f5f5f5;
}
#scrollbar-style::-webkit-scrollbar {
  width: 6px;
  background-color: #f5f5f5;
}
#scrollbar-style::-webkit-scrollbar-thumb {
  background-color: #3b3b3b;
}
.mx-datepicker {
  display: block;
  width: unset;
}




.btn-dots {
  border: none;
  background: none;
  outline: none;
}

.task-cart {
  position: relative;
}

.task-panel {
  position: absolute;
  top: -12px;
  right: -190px;
  z-index: 100;
  background: transparent;
  padding: 3px;
  width: 200px;
  color: #ffffff;
}
.is_mobile {
  top: 0px !important;
  right: 0px !important;
}
.task-panel-left {
  left: -190px;
}

.task-panel li {
  /* background: #1d68a7; */
  background-color: #333333;
  opacity: 75%;
  transition: all 0.5s;
}

.task-panel li:hover {
  cursor: pointer;
  opacity: 100%;
  padding-left: 20px;
}
</style>
