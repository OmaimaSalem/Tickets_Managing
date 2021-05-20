<template>
  <div class="container col-sm-12">
    <draggable
    :list="cardsByBoardId.data"
    :element="'div'"
    v-bind="cardDragOptions"
    @start="drag=true"
    @end="drag=false">

      <div  v-for="card in cardsByBoardId" :key="card.id" class="card m-3" id="card">
        <div class="card-header bg-secondary" id="cardHeader">
          <div >
            <a href="#" @click="deleteCard(card.id)" class="btn btn-s float-right text-white">
              <i  class="fas fa-trash-alt" ></i>
            </a>
            <a href="#" @click="newModal(card.id)" class="btn btn-s float-right text-white">
              <i class="fa fa-plus" ></i>
            </a>
          </div>
          <label-edit :text="card.name" :id="card.id"  v-on:text-updated-enter="updateCardName" class="h4"></label-edit>
        </div>


          <div id="scrollbar-style" class="cardBody card-body p-1 overflow-auto">
              <draggable
              :list="card.tasks"
              :element="'div'"
              v-bind="taskDragOptions"
              @start="drag=true"
              @end="drag=false"
              :move="onMove"
              :id="card.id"
              >
                <ul :id="task.id" v-for="task in card.tasks" :key="task.id" class="todo-list ui-sortable" data-widget="todo-list">
                  <li class="bg-white p-2 m-2 border" id="task" > 
                    <draggable 
                      :options="{group:'people', disabled: false }">
                      <TaskModal :task="form"></TaskModal>
                    </draggable>
                    <div>
                       <label>
                          <input type="checkbox" id="checkState" class= "d-inline ml-2 strikethrough" v-model="task.status" @click="updateCheck(task)">
                          <span class="lead">{{ task.name }}</span>
                       </label>
                       <div class="tools">
                         <a href="#" @click="TaskModal(task)">
                           <i class="fas fa-edit" style="color: black;"></i>
                         </a>
                         <a href="#" @click="deleteTask(task)">
                           <i class="fas fa-trash-alt" style="color: black;"></i>
                         </a>
                       </div>
                    </div>
                  </li>
                </ul>
              </draggable>
         </div>
    </div>
  </draggable>

<!-- Modal Create Task -->
  <div
    class="modal fade"
    id="ModalCreateTask"
    tabindex="-1"
    role="dialog"
    aria-labelledby="newModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="newModalLabel">Task</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form
        @submit.prevent="createTask(form)"
          @keydown="form.onKeydown($event)"
        >
          <div class="modal-body">
            <div class="form-group">
              <label for="name">{{$t('AttributesEmails.Name')}}</label>
              <input
                v-model="form.name"
                type="text"
                name="name"
                class="form-control"
                :class="{ 'is-invalid': form.errors.has('name') }"
              />
              <label for="description">{{$t('AttributesEmails.description')}} </label>
              <input
                v-model="form.description"
                type="text"
                name="description"
                class="form-control"
                :class="{ 'is-invalid': form.errors.has('description') }"
              />
              <has-error :form="form" field="description"></has-error>
              <label for="assigned_to">{{$t('AttributesEmails.AssignTo')}}</label>
              <select
              class = "form-control tags-selector"
              v-model = "form.assigned_to"
              placeholder = "Pick one or more ..."
              multiple
              >
              <option v-for="user in responsible" :key="user.id" :value="user.id">{{user.name}}</option>
              </select>
              <label for="deadline">DeadLine</label>
              <date-picker
                  v-model="form.deadline"
                  lang="en"
                  type="datetime"
                  format="DD-MM-YYYY HH:mm"
                  :minute-step="15"
                  value-type="format"
                  input-class="form-control"
              ></date-picker>
              <has-error
                  :form="form"
                  field="deadline"
              ></has-error>




              <label for="freq">Frequency</label>
              <select class="form-control" v-model="form.freq">
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
                <option value="yearly">Yearly</option>
              </select>
              <has-error
                  :form="form"
                  field="freq"
              ></has-error>






            </div>
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>


</div>
</template>
<script>
import Multiselect from 'vue-multiselect'
import { mapGetters } from "vuex";
import draggable from 'vuedraggable';
import LabelEdit from './LabelEdit';
import TaskModal from './TaskModal';
import DatePicker from "vue2-datepicker";

export default {
  name: "CardComponent",
  data() {
      return {
          cardId: '',
          board_id: this.$route.params.id,
          editMode: false,
          form: new Form({
              id: '',
              name: '',
              description: '',
              card_id: '',
              status: '',
              deadline:'',
              assigned_to :[],
              freq:'',
            
          }),
      }
  },
  methods: {
    onMove(event){
      let oldParent =  event.from.id;
      let newParent =  event.to.id;
      if(oldParent != newParent)
      {
        let task = event.dragged._underlying_vm_;
        let myTask = {
          name: task.name,
          description: task.description,
          card_id: newParent,
          id: task.id,
          status: task.status,
        }
        this.$Progress.start();
        this.$store
        .dispatch("todo/updateTaskCard", myTask)
        .then(response => {
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
      }
      else if (event.related.id != event.dragged.id) {
          let ids = {
            id1: event.related.id,
            id2: event.dragged.id,
          }
          this.$Progress.start();
          this.$store
          .dispatch("todo/sortTasks", ids)
          .then(response => {
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
      }
   } ,
    updateCheck(task){
      let newStatus;
      if(task.status == 0){
        newStatus = 1;
      }
      else{
        newStatus = 0;
      }
      let myTask = {
        name: task.name,
        description: task.description,
        card_id: task.card_id,
        id: task.id,
        status: newStatus
      }
      this.$Progress.start();
      this.$store
        .dispatch("todo/editTaskStatus", myTask)
        .then(response => {
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
    updateCardName(text , id ) {
     this.form.name = text;
     this.form.id = id;
      this.$Progress.start();
      this.$store
        .dispatch("todo/updateCardName", this.form)
        .then(response => {
          this.$Progress.finish();
          Toast.fire({
            type: "success",
            title: response.data.message
          });
        })
        .then( ()=> {
          this.form.name = '';
          this.form.id = '';
        })
        .catch(error => {
          this.$Progress.fail();
        });
    },
    newModal(id) {
      this.editMode = false;
      this.form.reset();
      this.form.clear();
      $("#ModalCreateTask").modal("show");
      this.form.card_id = id;
    },
    editModal(item) {
      this.editMode = true;
      this.form.reset();
      this.form.clear();
      $("#ModalCreateTask").modal("show");
      this.form.fill(item);
      this.form.selected = _.map(this.form.project_assign, function(
        value,
        key
      ) {
        return value.name;
      });
    },
    TaskModal(task) {
      this.editMode = true;
      this.form.reset();
      this.form.clear();
      this.form.fill(task);
      this.form.selected = _.map(this.form.project_assign, function(
        value,
        key
      ) {
        return value.name;
      });
      this.$store.commit('todo/updatingTask', task)
      $("#ModalTask").modal("show");
    },
    getCardsByBoardId() {
        this.$store
            .dispatch("todo/getCardsByBoardId", this.board_id )
            .then(() => {
                this.$Progress.finish();
                this.isLoading = false;
            })
            .catch(error => {
                this.$Progress.fail();
                this.isLoading = false;
            });
    },
    deleteCard(id) {
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
                  .dispatch("todo/deleteCard", id)
                  .then(response => {
                    this.$Progress.finish();
                    Toast.fire({
                      type: "success",
                      title: response.data.message
                    });
                  })
                    .catch(error => {
                        this.$Progress.fail();
                        Toast.fire({
                            type: "error",
                            title: error.response.data.message
                        });
                    });
            }
        });
    },
    createTask(data) {
      this.$Progress.start();
      this.$store
        .dispatch("todo/createTask", data)
        .then(response => {
          $("#ModalCreateTask").modal("hide");
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
    deleteTask(data) {
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
                  .dispatch("todo/deleteTask", data)
                  .then(response => {
                    this.$Progress.finish();
                    Toast.fire({
                      type: "success",
                      title: response.data.message
                    });
                  })
                    .catch(error => {
                      console.log(error);
                        this.$Progress.fail();
                        Toast.fire({
                            type: "error",
                            title: error.response.data.message
                        });
                    });
            }
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
    addTag (newTag) {
      console.log(newTag);
//       const tag = {
//         name: newTag,
//         code: newTag.substring(0, 2) + Math.floor((Math.random() * 10000000))
//       }
//       this.form.responsible_id.push(tag)
//       this.form.responsible_id.push(tag) 
 }
  },
  components: {
      draggable,
      LabelEdit,
      TaskModal,
      DatePicker
      },
  computed: {
      ...mapGetters({
          cardsByBoardId: "todo/cardsByBoardId",
          responsible: "regularUser/activeRegularUser"
      }),
      taskDragOptions() {
      return {
        animation: 200,
        group: "task-list",
        dragClass: "status-drag"
      };
    },
      cardDragOptions() {
      return {
        animation: 300,
        dragClass: "status-drag"
      };
    }
  },
  mounted() {
      this.getCardsByBoardId();
      this.getResponsibles();
      }
}



</script>
<style scoped>
  #card{
    width:20%;
    display: inline-flex;
    border-radius: 6px;
    box-shadow: 0px 0px 0px rgba(0, 0, 0, 0.25);
  }
  #cardHeader{
    border-radius: 6px 6px 0px 0px;
  }
  .cardBody{
    background-color: #F6F6F6;
  }
  #task{
    box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.10);
    border-radius: 6px;
  }
</style>
