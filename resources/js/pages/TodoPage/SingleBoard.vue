<template>
<div class="container col-sm-12">
  <div class="card ">
      <div class="card-header">
          <h3 class="card-title">{{$t('AttributesEmails.SingleBoard')}}</h3>
          <div class="card-tools">
            <!-- <AssignUsers></AssignUsers> -->
            <button type="submit" class="btn btn-success btn-sm" @click="newModal">
              <i class="fas fa-plus fa-fw"></i>
              <span class="d-none d-lg-inline">{{$t('AttributesEmails.AddCard')}}</span>
            </button>
          </div>
      </div>
    <CardComponent></CardComponent>
   </div>
   <!-- Card Modal -->
   <div
     class="modal fade"
     id="ModalCard"
     tabindex="-1"
     role="dialog"
     aria-labelledby="newModalLabel"
     aria-hidden="true"
   >
     <div class="modal-dialog" role="document">
       <div class="modal-content">
         <div class="modal-header">
           <h5  class="modal-title" id="newModalLabel">{{$t('AttributesEmails.CreateCard')}}</h5>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
             <span aria-hidden="true">&times;</span>
           </button>
         </div>
         <form
           @submit.prevent=" createCard()"
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
               <has-error :form="form" field="name"></has-error>
             </div>
           </div>

           <div class="modal-footer">
             <button type="submit" class="btn btn-primary">{{$t('AttributesEmails.save')}}</button>
           </div>
         </form>
       </div>
     </div>
   </div>

  </div>

</template>


<script>
import { mapGetters } from "vuex";

import VueBootstrap4Table from "vue-bootstrap4-table";
import draggable from 'vuedraggable';
import userApi from "../../api/users";
//import AssignUsers from '../../components/todo/AssignUsersModal'
import CardComponent from '../../components/todo/CardComponent.vue'

export default {
  name: "SingleBoard",
  data() {
      return {
          isLoading: false,
          form: new Form({
              name: '',
              board_id: this.$route.params.id
          }),
          editedCard: null,
          editField : '',
          tempValue: null,
          editing: false,
          users: null
      }
  },
  methods: {
    newModal() {
      this.form.reset();
      this.form.clear();
      $("#ModalCard").modal("show");
    },
    enableEditing: function(name){
      this.tempValue = name;
      this.editing = true;
    },
    disableEditing: function(){
      this.tempValue = null;
      this.editing = false;
    },
    saveEdit: function(){
      // However we want to save it to the database
      this.value = this.tempValue;
      this.disableEditing();
    },
    focusField(name){
      this.editField = name;
    },
    blurField(){
      this.editField = '';
    },
    showField(name){
      return ( this.editField == name)
    },
    createCard() {
      this.$Progress.start();
      this.$store
        .dispatch("todo/createCard", this.form)
        .then(response => {
            $("#ModalCard").modal("hide");
            this.$Progress.finish();
            Toast.fire({
                type: "success",
                title: response.data.message
            });
        })
        .catch(error => {
          console.log(error);
          this.$Progress.fail();
          if (error.response) {
            this.form.errors.errors = error.response.data.errors;
          }
        });
    },

  },


  components: {
      draggable,
      CardComponent,
      //AssignUsers
      },
  computed: {
      ...mapGetters({
          singleBoard: "todo/activeBoard",

      }),
      taskDragOptions() {
      return {
        animation: 200,
        group: "task-list",
        dragClass: "status-drag"
      };
    }
  },

}
</script>
<style media="screen">
    .strikethrough:checked + span{
    text-decoration:line-through
    }
</style>
