<template>
    <div class="active tab-pane" id="activity">
    <div v-if="activities.data && activities.data.length !== 0">
        <v-simple-table dense fixed-header v-if="activities.data && activities.data.length !== 0">
            <template v-slot:default>
            <thead>
                <tr>
                <th class="text-left">#</th>
                <th class="text-left">{{ $t('User.Created at') }} </th>
                <th class="text-left">{{ $t('User.User') }}</th>
                <th class="text-left">{{ $t('User.Subject') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="activity in activities.data" :key="activity.id">
                    <td>{{activity.id}}</td>
                    <td>{{ activity.created_at | DateWithTime }}</td>
                    <td v-if="activity.user">
                        <router-link :to="'/admin/profile/' + activity.user.id">
                            {{activity.user.name}}
                        </router-link>
                    </td>
                    <td v-else>
                        <label> No User </label>
                    </td>
                    <td>{{ activity.subject }}</td>
                </tr>
            </tbody>
            </template>
        </v-simple-table>
       <pagination
          :data="activities"
          :limit="parseInt(2)"
          size="small"
          @pagination-change-page="changePage"
        >
          <span slot="prev-nav">&lt; Previous</span>
          <span slot="next-nav">Next &gt;</span>
        </pagination>
    </div>
    <div v-if="activities.data && activities.data.length == 0">
        {{$t('User.noActivity')}}
    </div>
    </div>
    <!-- /.tab-pane -->
</template>

<script>
import { mapGetters } from "vuex";

export default {
    props: {
        userId: {
            type: Number,
            required: true
        }
    },
    data() {
        return {
        Page: 1,
        }
    },
    methods: {
        getlogActivityListsByClientId() {
            console.log(this.page)
            this.$store
                .dispatch("activity/getlogActivityListByClientId", {
                     id: this.userId,
                     page: this.Page ,
                     perPage: 15
                     })
                .then(response => {
                    this.myActivities = response.data.data;
                })
                .catch(error => {
                });
        },
        changePage(p){
            console.log(p)
            this.Page = p;
            this.getlogActivityListsByClientId();
      },
    },
    created() {
        this.getlogActivityListsByClientId(this.userId);
    },
    computed: {
        ...mapGetters({
            activities: "activity/activityList"
        })
    }
};
</script>

<style scoped>
tbody tr:nth-of-type(odd) {
  background-color: #dcdcdc;
}

thead tr th {
  color: #2f5298 !important;
  font-size: 18px !important;
}
</style>
