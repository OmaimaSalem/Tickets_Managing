
<template>
      <li class="nav-item dropdown" style="box-shadow: 0 3px 1px -2px rgba(0, 0, 0, 0.2),
        0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12)">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
            <span v-if="NavbarNotificationsCount > 0" class="badge badge-danger navbar-badge">{{ NavbarNotificationsCount }}</span>
        </a>



        <div v-if="notifications.length > 0" class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="border-radius: 15px;background-color: #f9f9f9;">
            <h5 style="font-weight: bold;margin: .6rem">Notifications</h5>
            <hr>
           <div @click="markasread(notification)" v-for="(notification,index) in notifications" :key="notification.data.object_id+'-'+index" style="text-align: left  ;display: flex;flex-direction: column;" :class="notification.read_at ? 'readed' : ''">
              
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

            <div class="dropdown-divider"></div>
            <div class="dropdown-item">
              <router-link to="/admin/notifications" >
                  All Notifications <i class="far fa-bell"></i>
              </router-link>

            </div>


        </div>

      </li>
</template>

<script>
import { mapGetters } from "vuex";
import moment from "moment";

export default {
    name: "notification",
    computed: {
        ...mapGetters({
            notifications: "notification/getNotifications",
            LayoutNotificationsCount: "notification/getLayoutNotifications",
            NavbarNotificationsCount: "notification/getNavbarNotifications"
        }),
    },
    methods: {
      markasread(notification){
          this.$store.dispatch('notification/readNotification',{id:notification.id}).then(function(notification){
          }).catch(error => {
                console.log(error);
          });

      }
    }
}
</script>

<style>
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
