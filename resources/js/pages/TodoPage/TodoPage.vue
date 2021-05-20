<template>

<div class="container">
  <div class="card">
      <div class="card-header">
          <div class="card-title">{{$t('AttributesEmails.Boards')}}</div>
      </div>
      <div class="card-body">
          <div class="row">
            <div
              v-for="board in boards.data"
              :key="board.id"
              v-show="board"
              class="col-lg-3 col-6"
            >
              <!-- small card -->
              <div class="small-box bg-blue">
                <div class="inner">
                  <h3>
                    <router-link
                      :to="'/admin/todo/boards/' + board.id"
                      class="text-white"
                    >{{ board.name }}</router-link>
                  </h3>

                  <a href="#" @click="editModal(board)" class="btn btn-light btn-xs">
                    <i class="fas fa-edit fa-fw"></i>
                  </a>
                  <a href="#" @click="deleteBoard(board.id)" class="btn btn-xs btn-light">
                    <i class="fas fa-trash fa-fw"></i>
                  </a>
                </div>
                <div class="icon">
                  <i class="fas fa-list-ul "></i>
                </div>
                <router-link :to="'/admin/todo/boards/' + board.id" class="small-box-footer">
                  More info
                  <i class="fas fa-arrow-circle-right"></i>
                </router-link>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <button
                @click="newModal"
                class="btn btn-dark btn-block"
                style="display: block; height:90%;"
              >
                <i class="fas fa-plus fa-2x"></i>
                <p>{{$t('AttributesEmails.Createboard')}}</p>
              </button>
            </div>
          </div>

      </div>
  </div>
  <div
    class="modal fade"
    id="Modal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="newModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 v-show="!editMode" class="modal-title" id="newModalLabel">{{$t('AttributesEmails.Createboard')}}</h5>
          <h5 v-show="editMode" class="modal-title" id="newModalLabel">{{$t('AttributesEmails.Editboard')}}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form
          @submit.prevent="editMode ? editBoard(form.id) : createBoard()"
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
import {
  mapGetters , mapActions
} from "vuex";
import todoApi from "../../api/todo";

export default {
  name: "TodoPage",
  data() {
      return {
          isLoading: false,
          form: new Form({
              id: '',
              name: '',
          }),
          total_rows: 0,
          editMode: false,

      }
  },
  methods: {
    getBoards() {
        this.$store
            .dispatch("todo/getBoards")
            .then(() => {
                this.$Progress.finish();
                this.isLoading = false;
                this.total_rows = response.data.data.total;
            })
            .catch(error => {
                this.$Progress.fail();
                this.isLoading = false;
            });
    },
    newModal() {
      this.editMode = false;
      this.form.reset();
      this.form.clear();
      $("#Modal").modal("show");
    },
    editModal(item) {
      this.editMode = true;
      this.form.reset();
      this.form.clear();
      $("#Modal").modal("show");
      this.form.fill(item);
      this.form.selected = _.map(this.form.project_assign, function(
        value,
        key
      ) {
        return value.name;
      });
    },
    createBoard() {
      this.$Progress.start();
      this.$store
        .dispatch("todo/createBoard", this.form)
        .then(response => {
          console.log("test1");
            $("#Modal").modal("hide");
            this.$Progress.finish();
            Toast.fire({
                type: "success",
                title: response.data.message
            });
        })
        .catch(error => {
          console.log("test2");
          this.$Progress.fail();
          if (error.response) {
            this.form.errors.errors = error.response.data.errors;
          }
        });
    },
    editBoard(id) {
      this.$Progress.start();
      this.$store
        .dispatch("todo/editBoard", this.form)
        .then(response => {
          $("#Modal").modal("hide");
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
    deleteBoard(id) {
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
                  .dispatch("todo/deleteBoard", id)
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
    }
  },
  computed: {
      ...mapGetters({
          boards: "todo/allBoards"
      }),
  },
  mounted() {
      this.getBoards();
  }
}

</script>
