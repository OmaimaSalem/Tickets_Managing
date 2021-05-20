<template>
  <nav class="main-header navbar navbar-expand navbar-white navbar-light text-sm">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-user"></i>
          <!-- <span class="badge badge-danger navbar-badge">3</span> -->
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <router-link :to="{name: 'profile', params: {id: this.$userId}}" class="dropdown-item">
            {{$t('Navbar.profile')}}
          </router-link>
          <div class="dropdown-divider"></div>
          <a @click="logOut" href="#" class="dropdown-item">
            {{$t('Navbar.logout')}}
          </a>
          <div class="dropdown-divider"></div>
        </div>
      </li>

      <notification-menu></notification-menu>

      <li class="nav-item">
        <LangSwitcher></LangSwitcher>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button"><i class="fas fa-cogs"></i></a>
      </li>
    </ul>
    working time : {{ user.working_time }}
  </nav>
</template>
<script>
import NavRoutes from './NavRoutes';
import NotificationMenu from './NotificationMenu';
import LangSwitcher from '../../components/Layouts/LangSwitcher';
import { mapGetters } from "vuex";

  export default {
    name: 'Navbar',
    components: {
      NavRoutes,
      LangSwitcher,
      NotificationMenu
    },
    methods: {
      logOut() {
        axios.post('/logout')
        .then(response => {
          this.$router.push('/');
          if(localStorage.getItem('alferp')) {
            localStorage.removeItem('alferp')
            console.log('user')
          }
          console.log('response success')
        })
        .catch(error => {
          console.log('error in response')
        })
      },
      goToProfile() {
        this.$router.push({name: 'profile'});
      }
    },
    computed: {
    ...mapGetters({
      user: "user/activeSingleUser",
    }),
  },

  }
</script>
