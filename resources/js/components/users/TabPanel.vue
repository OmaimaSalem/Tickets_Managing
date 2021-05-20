<template>
  <div class="card">
    <div class="card-header p-2">
      <ul class="nav nav-pills">
        <li class="nav-item row" v-for="tab in tabs" :key="tab.component" >
          <div class="nav-link col-md-12 col-xs-4 text-md-center text-xs-center" :class="{ active: selected === tab.component }" @click="selected = tab.component;">{{ tab.title }}</div>
        </li>
      </ul>
    </div>
    <div class="card-body p-0">
      <div class="tab-content">
        <component :is="selected" v-bind="{ userId: this.user.id }"></component>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
  data() {
    return {
      tabs: [
            {component: "UserComment",  title: "comments"},
            {component: "ActivityList", title: "activity"},
            {component: "ProjectsListPerUser",  title: "projects"},
            {component: "TicketsListPerUser",  title: "tickets"},
            {component: "TasksListPerUser",  title: "tasks"}
            
      ],
      selected: "UserComment"
    };
  },
  computed: {
    ...mapGetters({
      user: "user/userProfile"
    })
  }
};
</script>

<style scoped>
.nav-link {
    cursor: pointer;
}
</style>
