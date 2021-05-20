<template>
  <div class="justify-content-center" v-if="!loading">
    <div class="row">

      <!-- card box -->
      <!-- <card-box cardBg="bg-info">
        <template v-slot:count>{{ projectCount }}</template>
        <template v-slot:title>{{$t('User.projects')}}</template>
        <template v-slot:icon>
          <i class="fas fa-briefcase"></i>
        </template>
      </card-box>
      <card-box cardBg="bg-success">
        <template v-slot:count>{{ ticketCount }}</template>
        <template v-slot:title>{{$t('User.tickets')}}</template>
        <template v-slot:icon>
          <i class="fas fa-chart-pie"></i>
        </template>
      </card-box>
      <card-box cardBg="bg-warning">
        <template v-slot:count>{{ openTaskCount }}</template>
        <template v-slot:title>{{$t('User.openTasks')}}</template>
        <template v-slot:icon>
          <i class="fas fa-tasks"></i>
        </template>
      </card-box>
      <card-box cardBg="bg-danger">
        <template v-slot:count>{{ closedTaskCount }}</template>
        <template v-slot:title>{{$t('User.closedTasks')}}</template>
        <template v-slot:icon>
          <i class="fas fa-list-ul"></i>
        </template>
      </card-box> -->
    </div>
    <!-- <div class="row"> -->
      <!-- profile card -->
        <!-- <profile-card  :user="user"></profile-card> -->
      <!-- /profile card -->
      <!-- <div class="col-sm">

            <meta-data></meta-data> -->
        <!-- activity list -->
        <!-- <tab-panel></tab-panel> -->
        <!-- /activity list -->
      <!-- </div>
    </div> -->

    <v-row>
      <v-col md="3" sm="12">
        <profile-card  :user="user"></profile-card>
    <br />
        <div class="card card-primary">
          <div class="card-header">
            <div class="stat-container"><label class="float-left ml-2">Client attributes</label></div>
          </div>
        <!-- /.card-header -->
          <div class="card-body">
            <h5 class="text-muted text-center" v-for="attribute in user.attributes" :key="attribute.id"> 
              <span style="margin-left: 0.5rem">
                {{attribute.name}}:
              </span>
              <span> 
                {{attribute.value}} 
              </span>
              <hr>
            </h5>
          </div>
        <!-- /.card-body -->
      </div>
    <meta-data :user="user"></meta-data>
      </v-col>
      <v-col md="9" sm="12">
        <tab-panel></tab-panel>
      </v-col>
    </v-row>
  </div>
  <div class="card" v-else>
    <div class="card-body justify-content-center">{{$t('User.loading')}}</div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
  data() {
    return {
      userId: this.$route.params.id,
      loading: true
    };
  },
  methods: {
    getUserProfile(id) {
      this.$Progress.start();
      this.$store
        .dispatch("user/getUserProfile", id)
        .then(response => {
          this.$Progress.finish();
        })
        .catch(error => {
          this.$Progress.fail();
        });
    },
    getProjectCountPerClient(id) {
      this.$Progress.start();
      this.$store
        .dispatch("project/getProjectCountPerClient", id)
        .then(response => {
          this.$Progress.finish();
        })
        .catch(error => {
          this.$Progress.fail();
        });
    },
    getTicketCountPerClient(id) {
      this.$Progress.start();
      this.$store
        .dispatch("ticket/getTicketCountPerClient", id)
        .then(response => {
          this.$Progress.finish();
        })
        .catch(error => {
          this.$Progress.fail();
        });
    },
    getTaskCountPerClient(id) {
      this.$Progress.start();
      this.$store
        .dispatch("task/getTaskCountPerClient", id)
        .then(response => {
          this.$Progress.finish();
          this.loading = false;
        })
        .catch(error => {
          this.$Progress.fail();
        });
    }
  },
  mounted() {
    this.getUserProfile(this.userId);
    this.getProjectCountPerClient(this.userId);
    this.getTicketCountPerClient(this.userId);
    this.getTaskCountPerClient(this.userId);
  },
  computed: {
    ...mapGetters({
      user: "user/userProfile",
      projectCount: "user/ProjectCountPerClient",
      ticketCount: "user/TicketCountPerClient",
      openTaskCount: "user/OpenTaskCountPerClient",
      closedTaskCount: "user/ClosedTaskCountPerClient"
    })
  }
};
</script>
