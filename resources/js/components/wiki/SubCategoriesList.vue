<template>
  <div v-if="isLoading" class="sub-category-preview">{{$t('Wiki.loadingCategories')}}</div>
  <div v-else>
    <CategoriesHeader />
    <div v-if="categories.data.length === 0" class="sub-category-preview">{{$t('Wiki.noCategory')}}</div>
    <div class="card" v-else>
      <div class="card-body">
        <div class="row">
          <category-preview
            v-for="(category, index) in categories.data"
            :category="category"
            :key="category.name + index"
          />
        </div>
        <pagination
          align="right"
          size="small"
          :show-disabled="true"
          :data="categories"
          :limit="3"
          @pagination-change-page="onPaginate"
        ></pagination>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  data() {
    return {
      isLoading: true,
      query: ''
    };
  },
  beforeRouteUpdate(to, from, next) {
    this.fetchCategories(to.params.page, this.$route.query.q);
    next();
  },
  mounted() {
    this.fetchCategories(
      this.$route.params.page ? this.$route.params.page : 1,
      this.$route.query.q
    );
  },
  methods: {
    onPaginate(page) {
      this.$router.push({
        name: "sub-category.list",
        params: { page },
        query: { q: this.$route.query.q }
      });
    },
    fetchCategories(page = 1, query = '') {
      this.isLoading = false;
      this.$Progress.start();
      this.$store
        .dispatch("wiki/FetchSubCategories", { page: page, query: query })
        .then(response => {
          console.log(response.data);
          this.$Progress.finish();
        });
    }
  },
  computed: {
    ...mapGetters({
      categories: "wiki/categories"
    })
  }
};
</script>

<style>
</style>
