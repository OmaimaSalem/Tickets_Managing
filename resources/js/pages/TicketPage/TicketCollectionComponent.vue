<template>
    <div>
        Collections
                            <v-btn
                              color="#A0466F"
                              dark
                              fixed
                              bottom
                              right
                              fab
                              @click.stop="newCollectionModal"
                            >
                              <v-icon>fas fa-plus</v-icon>
                            </v-btn>

                                <vue-bootstrap4-table
                                    :rows="ticketCollections"
                                    :columns="columns"
                                >

                                <template slot="action-buttons" slot-scope="props">
                                    <a href="#" @click="editCollectionModal(props.row)" class="btn btn-primary btn-xs">
                                    <i class="fas fa-edit fa-fw"></i>
                                    </a>
                                    <a href="#" @click="deleteCollectionTicket(props.row.id)" class="btn btn-danger btn-xs">
                                    <i class="fas fa-trash fa-fw"></i>
                                    </a>
                                </template>

                                </vue-bootstrap4-table>


<!-- Modal -->
<div class="modal fade" id="newCollectionModal" tabindex="-1" role="dialog" aria-labelledby="newCollectionModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="newCollectionModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
            <div class="form-group">
                <label for="email">Email</label>
                <input v-model="form.email" type="email" class="form-control" id="email"  placeholder="Enter email">
                <has-error :form="form" field="email"></has-error>
            </div>
            <div class="form-group">
                <label for="collection">Collection</label>
                <input v-model="form.collection"  type="text" class="form-control" id="collection" placeholder="Enter collection">
                <has-error :form="form" field="collection"></has-error>
            </div>

        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" v-if="!editMode" @click="createCollection">Save changes</button>
        <button type="button" class="btn btn-primary" v-if="editMode" @click="updateCollection">update changes</button>
      </div>
    </div>
  </div>
</div>
    </div>
</template>




<script>
import VueBootstrap4Table from "vue-bootstrap4-table";
import {mapGetters} from "vuex";

export default {

  data() {
    return {
            editMode: false,
            id: null,
            form: new Form({
                email: null,
                collection: null
            }),
            columns: [
            {
              label: "Email",
              name: "email",
              filter: {
                type: "simple",
                placeholder: ""
              },
              sort: true,
              row_text_alignment: "text-left"
            },
            {
              label: "Collection",
              name: "collection",
              filter: {
                type: "simple",
                placeholder: ""
              },
              sort: true,
              row_text_alignment: "text-left"
            },
            {
              label: "Actions",
              name: "action-buttons"
            }
        ]
    }
  },
    methods: {
        newCollectionModal(){
            this.form.clear();
            this.form.reset();
            $("#newCollectionModal").modal("show");
        },
        editCollectionModal(collection) {
            this.form.clear();
            this.form.reset();
            this.editMode = true;
            this.form.fill(collection);
            this.form.id = collection.id;
            $("#newCollectionModal").modal("show");
        },
        getTicketCollection() {
            this.$Progress.start();
            this.$store
                .dispatch("ticket/getTicketCollection",this.form)
                .then(response => {
                    this.$Progress.finish();
                })
                .catch(error => {
                this.$Progress.fail();
                    if (error.response) {
                        this.form.errors.errors = error.response.data.errors;
                    }
                });
        },
        createCollection() {
            this.$Progress.start();
            this.$store
                .dispatch("ticket/createTicketCollection",this.form)
                .then(response => {
                    this.$Progress.finish();
                    $("#newCollectionModal").modal("hide");
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
        updateCollection() {
            this.$Progress.start();
            this.$store
                .dispatch("ticket/updateTicketCollection",this.form)
                .then(response => {
                    this.$Progress.finish();
                    $("#newCollectionModal").modal("hide");
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

        deleteCollectionTicket(id) {
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
                        .dispatch("ticket/deleteTicketCollection", {id})
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
    components: {
        VueBootstrap4Table,
    },
    computed: {
        ...mapGetters({
            ticketCollections: "ticket/getTicketCollections",
        }),
    },
    mounted() {
       this.getTicketCollection();
    }

}
</script>

<style>

</style>
