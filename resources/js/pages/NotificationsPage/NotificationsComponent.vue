<template>
<div>
<h3>Notifications</h3>
<h6 class="float-right" style="cursor:pointer" @click="ReadAllNotifications"><i class="fa fa-eye"></i> Read all</h6>


<div class="clearfix" v-if="AllNotifications.data && AllNotifications.data.length > 0" style="border-radius: 15px;background-color: #f9f9f9;">
           <div @click="markasread(notification)" v-for="(notification,index) in AllNotifications.data" :key="notification.data.object_id+'-'+index" style="text-align: left  ;display: flex;flex-direction: column;" :class="notification.read_at ? 'readed' : ''">
              <router-link v-if="notification.data.not_id" :to="{name: notification.data.not_type, params: {id: notification.data.not_id}}" class="dropdown-item notification_item" :title="notification.data.notification+' '+moment(notification.created_at).fromNow()">
                {{ notification.data.notification }} --- {{moment(notification.created_at).fromNow()}}
              </router-link>

              <router-link v-else-if="notification.data.not_type == 'calendar'" :to="{name: 'calendar'}" class="dropdown-item notification_item" :title="notification.data.notification+' '+moment(notification.created_at).fromNow()">
                {{ notification.data.notification }} --- {{moment(notification.created_at).fromNow()}}
              </router-link>

              <div v-else  class="dropdown-item notification_item" :title="notification.data.notification+' '+moment(notification.created_at).fromNow()">
                    {{ notification.data.notification }} --- {{moment(notification.created_at).fromNow()}}
              </div>
<!--               <hr>-->
              <div class="dropdown-divider"></div>
           </div>
</div>

      <pagination
          :data="AllNotifications"
          :limit="parseInt(2)"
          size="small"
          @pagination-change-page="getAllNotifications"
      >
          <span slot="prev-nav">&lt; Previous</span>
          <span slot="next-nav">Next &gt;</span>
      </pagination>



</div>
  
</template>

<script>
import { mapGetters } from "vuex";
import moment from "moment";

export default {
    name: "notification",
    computed: {
        ...mapGetters({
            AllNotifications: "notification/getAllNotifications",
        }),
    },
    methods: {
      markasread(notification){
          this.$store.dispatch('notification/readNotification',{id:notification.id})
      },

      ReadAllNotifications(notification){
          this.$store.dispatch('notification/readAllNotifications').then(function(notification){

          });

      },

       getAllNotifications(page=1) {
            this.$store.dispatch('notification/getAllNotifications',{page:page});
        },

    },
    mounted() {
        this.getAllNotifications();
    },


}
</script>

<style scoped>
.notification_item{
    cursor: pointer;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    width: 100%;
    text-align: left;
    background-color: #3490dc;

}
.readed .notification_item{
  background-color: white;
  color: #3490dc;
}
</style>
