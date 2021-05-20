<template>
  <div class="layout-page">
      <vue-headful
          :title="$route.meta.title"
      />
    <!-- Navbar -->
    <NavBar></NavBar>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <SideBar :layoutnotification="layoutNotifications"></SideBar>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper" style="background-color: #E6EEEE">
        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">
                <router-view :key="$route.path + ($route.query || '')"></router-view>
                <vue-progress-bar></vue-progress-bar>
            </div>
        </div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <ControleSideBar></ControleSideBar>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->
    <Footer></Footer>

    <notifications position="bottom right" />

  </div>
</template>
<script>
import NavBar from './NavbarPage'
import SideBar from './SideBarPage'
import ControleSideBar from './ControleSideBar'
import Footer from './FooterPage'
import { mapGetters } from "vuex";


  export default {
    data(){
        return {
            sound: new Audio(window.location.hostname+'/sound/notification.mp3'),
        }
    },
    name: 'LayoutPage',
    components: {
      NavBar,
      SideBar,
      Footer,
      ControleSideBar,
    },
    methods: {
        getRealtimeData() {
            Echo.private('user-' + this.currentUser())
                .notification((notification) => {
                    this.$store.commit('notification/addNotification',{data :notification})
                    this.getLayoutNotifications();
                    if(notification.not_url){
                        this.$notify({
                            type: "info",
                            title: 'Notification',
                            text: `<span class="notification"><a target="_blank" href="${notification.not_url}" target="_blank">${notification.notification}</a></span>`,
                        });
                    }else {
                        this.$notify({
                            type: "info",
                            title: 'Notification',
                            text: `<span class="notification">${notification.notification}</span>`,
                        });
                    }

                    this.sound.play();

            });
        },
        getMenuNotifications() {
            this.$store.dispatch('notification/getMenuNotifications');
        },
        currentUser() {
            if (localStorage.getItem("alferp")) {
                let user = JSON.parse(localStorage.getItem("alferp")).user
                    .singleUser;
                return parseInt(user.id);
            }
        },
        getLayoutNotifications(){
            let $this = this;
            this.$store.dispatch(
                'notification/getLayoutNotifications',
                'Modules\\Calender\\Notifications\\AddEventNotification'
            ).then(function(layoutNotifications){
            }).catch(error => {
                console.log(error);
            });
        }
    },
    mounted() {
        this.getRealtimeData();
        this.getMenuNotifications();
        this.getLayoutNotifications();
        $('body').click(function(event){
        if (event.target.className != "control-sidebar control-sidebar-dark text-white" && event.target.className != "fas fa-cogs" && event.target.className != "nav-link" && event.target.className != "nav-item has-treeview menu-open" && event.target.className != "nav-item has-treeview menu-open") {
            if($('.control-sidebar.control-sidebar-dark.text-white').is(':visible')){
              $('li a[data-widget="control-sidebar"]').click();
            }
         }
        })
    },
    computed: {
        ...mapGetters({
            layoutNotifications: "notification/getLayoutNotifications"
        })
    },

  }
</script>


<style>
  .notification a{
    color:#fff;
    text-decoration: none;
  }
</style>
