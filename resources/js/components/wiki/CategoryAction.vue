<template>
  <div class="card-tools pull-right text-right">
    <div class="btn-group">
      <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
        <i class="fas fa-bars"></i>
      </button>
      <div class="dropdown-menu dropdown-menu-right" role="menu">
        <router-link :to="editCategoryLink" class="dropdown-item">{{$t('Wiki.edit')}}</router-link>
        <div @click="deleteCategory(category.id)" class="dropdown-item">{{$t('Wiki.delete')}}</div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  data() {
    return {
      category_id: this.category.id
    }
  },
  props: {
    category: { type: Object, required: true }
  },
  methods: {
    deleteCategory(id) {
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
          this.$store
            .dispatch("wiki/DeleteCategory", id)
            .then(response => {
              this.$router.push({ name: "categories.list", params: {page: this.$route.params.page}});
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
  computed: {
    editCategoryLink() {
      return { name: "category-edit", params: { id: this.category.id } };
    }
  }
};
</script>

<style>
</style>
