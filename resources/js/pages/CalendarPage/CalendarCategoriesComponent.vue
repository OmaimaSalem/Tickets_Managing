<template>
<div>

    <div @click="addCategory">
        Add New Category
    </div>
<v-simple-table dense fixed-header>
                                <template v-slot:default>
                                <thead>
                                    <tr>
                                    <th class="text-left">#</th>
                                    <th class="text-left">Title</th>
                                    <th class="text-left">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="category in categories" :key="category.id">
                                        <td>{{category.id}}</td>
                                        <td> {{category.title}} </td>

                                        <td>
                                            <i
                                            @click="editCategory(category)"
                                            data-widget="collapse"
                                            data-toggle="tooltip"
                                            title="Edit"
                                            class="text-success icon fas fa-edit"
                                            style=":hover {color: #ffffff}"
                                            ></i>
                                            <i
                                            @click="deleteCategory(category.id)"
                                            data-widget="collapse"
                                            data-toggle="tooltip"
                                            title="Edit"
                                            class="text-danger icon pl-1 fas fa-trash"
                                            ></i>
                                        </td>
                                    </tr>
                                </tbody>
                                </template>
                            </v-simple-table>






<div class="modal fade" id="category" tabindex="-1" role="dialog" aria-labelledby="categoryLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="categoryLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
            <div class="form-group">
                <span for="name">Title</span>
                <input
                    v-model="form.title"
                    type="text"
                    name="title"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('title') }"
                />
                <has-error :form="form" field="title"></has-error>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button v-if="form.id"  @click="updateCategory" type="button" class="btn btn-primary">update</button>
        <button v-if="!form.id" @click="saveCategory" type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>







                            </div>




</template>

<script>

import { mapGetters } from "vuex";

export default {
 data() {
     return {
         form: new Form({
            id : null,
            title: null
         }),
     }
 },

methods : {
        getCategories() {
        this.$Progress.start();
        this.$store
            .dispatch("calendar/getCategories")
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
            addCategory(){
                this.form.clear();
                this.form.reset();
                $('#category').modal('show');
            },
            editCategory(category){
                this.form.reset();
                this.form.fill(category);
                $('#category').modal('show');
            },
            deleteCategory(id){
                this.$Progress.start();
                this.$store
                    .dispatch("calendar/deleteCategory",{id})
                        .then(response => {
                            this.$Progress.finish();
                        })
                        .catch(error => {
                            this.$Progress.fail();
                            console.log(error);
                        });


            },
            saveCategory() {
                this.$Progress.start();
                this.$store
                    .dispatch("calendar/addCategory",this.form)
                        .then(response => {
                            this.$Progress.finish();
                            $('#category').modal('hide');

                        })
                        .catch(error => {
                            this.$Progress.fail();
                            if (error.response) {
                                this.form.errors.errors = error.response.data.errors;
                            }
                        });
            },
            updateCategory(){
                this.$Progress.start();
                this.$store
                    .dispatch("calendar/editCategory",this.form)
                        .then(response => {
                            $('#category').modal('hide');
                            this.$Progress.finish();
                        })
                        .catch(error => {
                            this.$Progress.fail();
                            console.log(error);
                            if (error.response) {
                                this.form.errors.errors = error.response.data.errors;
                            }
                        });

            }
    },
    mounted() {
        this.getCategories();
    },
    computed: {
        ...mapGetters({
            categories: "calendar/Categories",
        })
    }
}
</script>

<style>

</style>
