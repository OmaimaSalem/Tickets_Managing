<template >
  <!-- Edit Task Modal -->
  <div
    class="modal fade"
    id="ModalTask"
    tabindex="-1"
    role="dialog"
    aria-labelledby="newModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="newModalLabel">{{task.name}}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

          <div class="modal-body">
            <div class="form-group">
              <input type="text" name="name" :value="task.name" @input="updateTaskName" class="form-control">
            </div>
            <div class="form-group">
              <textarea name="description" :value="task.description" @input="updateTaskDescription" class="form-control"></textarea>
            </div>
            <!--                     v-model="form.deadline"
 -->
            <date-picker
                    :value="task.deadline"
                    @input="updateTaskDeadLine" 
                    lang="en"
                    type="datetime"
                    format="DD-MM-YYYY HH:mm"
                    :minute-step="15"
                    value-type="format"
                    input-class="form-control"
            ></date-picker>
            
            <label for="assigned_to">Assigned To:</label>
            <div v-for="user in task.assigned_to">
              {{user.name}}
            </div>
            <label for="assigned_to">Assigning To:</label>
            <select
              @input="updateTaskAssignedTo"
              class = "form-control"
              :value = "task.assigned_to"
              placeholder = "Pick one or more ..."
              multiple="multiple"
              >
              <option  v-for="user in responsible" :key="user.id" :value="user.id" >{{user.name}}</option>
              </select>
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-primary" @click ="updateTask()">Save</button>
          </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import LabelEdit from './LabelEdit';
import TextAreaEdit from './TextAreaEdit';
import DatePicker from "vue2-datepicker";

export default {
  name: "TaskModal",
  data() {
      return {
          form: new Form({
              id: '',
              name: '',
              description: '',
              card_id: '',
              status: '',
              deadline:'',
              assigned_to :[]
          }),
      }
  },
  methods: {
    updateTaskName (e) {
      this.form.name = e.target.value;
    },
    updateTaskDeadLine (e) {
       this.form.deadline = e;
    },
    updateTaskDescription (e) {
      this.form.description = e.target.value;
    },
    updateTaskAssignedTo (e) {
      this.form.assigned_to.push(e.target.value);
    },
    updateTask(task) {
      if(this.form.name == '')
      {
        this.form.name = this.task.name;
      }
      if(this.form.description == '')
      {
        this.form.description = this.task.description;
      }
      if(this.form.deadline == '')
      {
        this.form.deadline = this.task.deadline;
      }
      if(this.form.assigned_to == '')
      {
        this.form.assigned_to = this.task.assigned_to;
      }
      this.form.id = this.task.id;
      this.form.card_id = this.task.card_id;
      this.form.status = this.task.status;
      this.$Progress.start();
      this.$store
        .dispatch("todo/updateTask", this.form)
        .then(response => {
          $("#ModalTask").modal("hide");
          this.$Progress.finish();
          Toast.fire({
            type: "success",
            title: response.data.message
          });
        })
        .then(()=> {
          this.form.reset();
          this.form.clear();
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
          .dispatch("regularUser/getRegularUser")
          .then()
          .catch(error => {
              console.log(error);
          });
    } 

  },

  components: {
    DatePicker
  },
    computed: {
      ...mapGetters({
        task: "todo/updatingTask",
        responsible: "regularUser/activeRegularUser"

    }),
},
  mounted() {
      this.getResponsibles();
      }
}
</script>
