<template>

  <div class="col-lg-3 col-6">
  <div> Welcome </div>
    <!-- small box -->
    <div class="small-box bg-info">
      <div class="inner">
        <h3>{{category.topics_count}}</h3>
        <p>
          <router-link :to="categoryLink" class="text-dark">{{category.name}}</router-link>
        </p>
        <category-action :category="category"></category-action>
      </div>
      <div class="icon">
        <i class="fas fa-book"></i>
      </div>
      <router-link :to="categoryLink" class="small-box-footer">
        {{$t('Wiki.moreInfo')}}
        <i class="fas fa-arrow-circle-right"></i>
      </router-link>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    category: { type: Object, required: true }
  },
  computed: {
    categoryLink() {
      return {
        name: "category",
        params: {
          id: this.category.id
        }
      };
    }
  }
};
</script>

<style>
</style>
