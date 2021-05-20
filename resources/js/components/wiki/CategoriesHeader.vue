<template>
  <section class="mt-2 mb-2">
    <div class="row mb-2">
      <div class="col-sm-6">
        <form class="form-inline" @submit.prevent="onSearch(query)">
          <div class="input-group input-group-sm">
            <input
              class="form-control form-control-navbar"
              type="search"
              :placeholder="$t('Wiki.search')"
              aria-label="Search"
              v-model="query"
            />
            <div class="input-group-append">
              <button class="btn btn-default" type="submit">
                <i class="fas fa-search"></i>
              </button>
            </div>
          </div>
        </form>
      </div>
      <div class="col-sm-6">
        <div class="text-right">
          <router-link :to="{name:'category-edit'}" class="btn btn-sm btn-success">
            <i class="fas fa-plus"></i>{{$t('Wiki.newCategory')}}
          </router-link>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  data() {
    return {
      query: this.$route.query.q
    };
  },
  methods: {
    onSearch(query) {
      this.$router.push({
        name: "categories.list",
        page: 1,
        query: { q: query }
      });
      this.fetchTopics(1, query);
    },
    fetchTopics(page = 1, query = {}) {
      this.isLoading = false;
      this.$Progress.start();
      this.$store
        .dispatch("wiki/FetchCategories", { page: page, query: query })
        .then(response => {
          this.$Progress.finish();
        });
    }
  }
};
</script>

<style>
</style>
