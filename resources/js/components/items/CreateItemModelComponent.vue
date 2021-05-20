<template>
<div class="modal fade" id="CreateItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{$t('Category.createItem')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form @submit.prevent="createItem" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">{{$t('Category.name')}}</label>
                        <input type="text" name="name" class="form-control" v-model="form.name">
                    </div>
                    <div class="form-group">
                        <label for="description">{{$t('Category.description')}}</label>
                        <input type="text" name="description" class="form-control" v-model="form.description">
                    </div>
                    <div class="form-group">
                        <label for="price">{{$t('Category.price')}}</label>
                        <input type="text" name="price" class="form-control" v-model="form.price">
                    </div>
                    <div class="form-group">
                        <label for="category">{{$t('Category.category')}}</label>
                        <select class="form-control" name="category" v-model="form.item_category_id">
                            <option value=""></option>
                            <option v-for="category in categories.data" :key="category.id" :value="category.id">{{category.name}}</option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{$t('Category.create')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
</template>
<script>
import {
    mapGetters
} from "vuex";
export default {
    props: ['form'],
    methods: {
        createItem() {
            this.$Progress.start();
            this.$store
                .dispatch("item/createItem", this.form)
                .then(response => {
                    $("#CreateItem").modal("hide");
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
        getCategories() {
            this.$store
                .dispatch("item/getCategories")
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
    },
    computed: {
        ...mapGetters({
            categories: "item/allCategories",
            user: "user/activeSingleUser",
        })
    },
    mounted() {
        this.getCategories();
    }
}
</script>
