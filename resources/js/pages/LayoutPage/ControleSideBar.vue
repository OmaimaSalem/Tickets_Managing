<template>
  <aside class="control-sidebar control-sidebar-dark text-white">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
      <h5>{{$t('Layout.settings')}}</h5>
      <ul
        class="nav nav-pills nav-sidebar flex-column nav-flat nav-compact"
        data-widget="treeview"
        role="menu"
        data-accordion="false"
      >
        <!-- <li v-if="$can('offer-list')" class="nav-item has-treeview">
          <a href="#" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>
              {{$t('Layout.offers')}} - {{$t('Layout.contracts')}}
              <i
                class="right fas fa-angle-left"
              ></i>
            </p>
          </a>
          <ul class="nav nav-treeview" style="display: none;">
            <li v-if="$can('item-list')" class="nav-item">
              <router-link to="/admin/items/category" class="nav-link">
                <i class="nav-icon fas fa-tags"></i>
                <p>{{$t('Layout.itemsCategories')}}</p>
              </router-link>
            </li>
            <li v-if="$can('item-list')" class="nav-item">
              <router-link to="/admin/items" class="nav-link">
                <i class="nav-icon fas fa-bars"></i>
                <p>{{$t('Layout.items')}}</p>
              </router-link>
            </li>
          </ul>
        </li> -->


        <li v-if="$can('hr') || $can('hr-assistant')" class="nav-item has-treeview">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-briefcase"></i>
            <p>
              {{$t('Layout.humanRe')}}
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item ml-2">
              <router-link to="/admin/manage-attends" class="nav-link">
                <i class="nav-icon fas fa-users"></i>
                <p>{{$t('Layout.manageAttends')}}</p>
              </router-link>
            </li>

            <li class="nav-item ml-2">
              <router-link to="/admin/manage-vacations" class="nav-link">
                <i class="nav-icon fas fa-umbrella"></i>
                <p>{{$t('Layout.manageVacations')}}</p>
              </router-link>
            </li>
            <li class="nav-item ml-2">
              <router-link to="/admin/hr/manage-users" class="nav-link">
                <i class="nav-icon fas fa-users"></i>
                <p>{{$t('Layout.manageUsers')}}</p>
              </router-link>
            </li>
          </ul>
        </li>



        <li v-if="$is('Full Administrator') || $is('Manger')" class="nav-item has-treeview">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-envelope"></i>
            <p>
              {{$t('Layout.mail_templates')}}
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <router-link to="/admin/mail-template-categories" class="nav-link">
                <i class="nav-icon fas fa-cogs"></i>
                <p>{{$t('Layout.mail_template_categories')}}</p>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/admin/mail-templates" class="nav-link">
                <i class="nav-icon fas fa-envelope-square"></i>
                <p>{{$t('Layout.templates')}}</p>
              </router-link>
            </li>
          </ul>
        </li>
        <li v-if="$is('Full Administrator')" class="nav-item has-treeview">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-cog"></i>
            <p>
              System
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <!-- <li class="nav-item">
                      <router-link to="/admin/permissions" class="nav-link">
                          <i class="nav-icon fas fa-cogs"></i>
                          <p>
                              Permissions
                          </p>
                      </router-link>
            </li>-->
            <li class="nav-item">
              <router-link to="/admin/roles" class="nav-link">
                <i class="nav-icon fas fa-cogs"></i>
                <p>Role Management</p>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/admin/users" class="nav-link">
                <i class="nav-icon fas fa-list"></i>
                <p>Employees</p>
              </router-link>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </aside>
</template>
<script>
export default {
  name: "ControleSideBar",
};
</script>
<style scoped>
p,
i {
  color: #ffffff;
}
</style>
