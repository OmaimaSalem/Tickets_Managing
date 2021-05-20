<template>
  <div>
    <ul class="list-inline list-group list-group-horizontal">
      <li class="btn btn-secondary float-left ml-1" v-for="(page,index) in  pages" :key="index">
        <router-link class="text-white" :to="page.route">{{ page.title }}</router-link>
        <a class="text-white" @click="pop(index)" href="javascript:;">&times;</a>
      </li>
    </ul>
    <div class="float-left" v-if="pages.length > 0">
      <a href="javascript:;" @click="clearNavbar()">{{$t('Navbar.clear')}}</a>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  name: "NavRoutes",
  data() {
    return {};
  },
  methods: {
    clearNavbar() {
      this.$store.commit("global/EmptyRecentPages");
    },
    pop(index) {
      this.$store.commit("global/RemoveRecentPage", index);
    }
  },
  computed: {
    ...mapGetters({
      pages: "global/getRecentPages"
    })
  },
  mounted() {
    this.$store.commit("global/setRecentPages", {
      title: this.$route.meta.title,
      route: this.$router.currentRoute
    });
    this.$router.beforeEach((to, from, next) => {
      this.$store.commit("global/setRecentPages", {
        title: to.meta.title,
        route: to.path
      });
      next();
    });
  }
};
</script>

<style>
</style>
