<template>
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/admin" class="brand-link">
      <img
        src="/assets/img/ALFerp.png"
        alt="ALFerp Logo"
        class="brand-image img-circle elevation-3"
        style="opacity: 0.8"
      />
      <span class="brand-text font-weight-light">ALFerp</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img
            src="/assets/img/profile.png"
            class="img-circle elevation-2"
            alt="User Image"
          />
        </div>
        <div class="info">
            <router-link class="d-block" :to="'/admin/profile/' + user.id">{{
                user.name
            }}</router-link>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul
          class="nav nav-pills nav-sidebar flex-column nav-flat nav-compact"
          data-widget="treeview"
          role="menu"
          data-accordion="false"
        >
          <!-- Add icons to the links using the .nav-icon class
          with font-awesome or any other icon font library-->
          <li class="nav-item">
            <router-link to="/admin/dashboard" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>{{ $t("Layout.dashboard") }}</p>
            </router-link>
          </li>
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-project-diagram"></i>
              <p>
                {{ $t("Layout.projectsManagment") }}
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li v-if="$can('project-list')" class="nav-item">
                <router-link to="/admin/projects" class="nav-link">
                  <i class="nav-icon fas fa-briefcase"></i>
                  <p>{{ $t("Layout.projects") }}</p>
                </router-link>
              </li>

              <li
                v-if="$can('ticket-list') || $can('ticket-management')"
                class="nav-item"
              >
                <router-link
                  :to="{
                    name: 'tickets.list',
                    params: { filter: 'all' },
                  }"
                  class="nav-link"
                >
                  <i class="nav-icon fas fa-ticket-alt"></i>
                  <p>{{ $t("Layout.tickets") }}</p>
                </router-link>
              </li>

              <li
                v-if="$can('ticket-list') || $can('ticket-management')"
                class="nav-item"
              >
                <router-link
                  :to="{
                    name: 'tickets.filtered',
                    params: { filter: 'all' },
                  }"
                  class="nav-link"
                >
                  <i class="nav-icon fas fa-filter"></i>
                  <p>Filtered tickets</p>
                </router-link>
              </li>

              <li class="nav-item">
                <router-link
                  :to="{
                    name: 'tickets.contracts',
                  }"
                  class="nav-link"
                >
                  <i class="nav-icon fas fa-file-contract"></i>
                  <p>{{ $t("Layout.ticketsContracts") }}</p>
                </router-link>
              </li>


              <li v-if="$can('task-list')" class="nav-item">
                <router-link to="/admin/tasks" class="nav-link">
                  <i class="nav-icon fas fa-tasks"></i>
                  <p>{{ $t("Layout.tasks") }}</p>
                </router-link>
              </li>
              <li v-if="$can('receipt-list')" class="nav-item">
                <router-link to="/admin/receipts" class="nav-link">
                  <i class="nav-icon fas fa-receipt"></i>
                  <p>{{ $t("Layout.receipts") }}</p>
                </router-link>
              </li>
              <li v-if="user.type != 'client'" class="nav-item">
                <router-link to="/admin/clients" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>{{ $t("Layout.clients") }}</p>
                </router-link>
              </li>
            </ul>
          </li>




          <!-- <li v-if="$can('tracking_task-list')" class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>
                {{ $t("Layout.reports") }}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview" style="display: none">
              <li class="nav-item">
                <router-link to="/admin/time-report" class="nav-link">
                  <i class="far fa-dot-circle nav-icon"></i>
                  <p>{{ $t("Layout.timeReports") }}</p>
                </router-link>
              </li>
            </ul>
          </li>

 -->



 <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="far fa-clipboard nav-icon"></i>
              <p>
                {{ $t("Layout.reports") }}
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <router-link
                  to="/admin/reports/project-employees"
                  class="nav-link"
                >
                  <i class="nav-icon fas fa-report"></i>
                  <p>
                    {{ $t("Layout.ProjectsMembers") }}
                  </p>
                </router-link>
              </li>

              <li class="nav-item">
                <router-link to="/admin/reports/time-tracking" class="nav-link">
                  <i class="nav-icon fas fa-report"></i>
                  <p>
                    {{ $t("Layout.TimeTracking") }}
                  </p>
                </router-link>
              </li>



                <li class="nav-item">
                <router-link to="/admin/reports/financial" class="nav-link">
                  <i class="nav-icon fas fa-report"></i>
                  <p>
                    {{ $t("Layout.Financial") }}
                  </p>
                </router-link>
              </li>


                <li class="nav-item">
                <router-link to="/admin/reports/vacations" class="nav-link">
                  <i class="nav-icon fas fa-report"></i>
                  <p>
                    {{ $t("Layout.Vacations") }}
                  </p>
                </router-link>
              </li>

                <li class="nav-item">
                <router-link to="/admin/reports/project-status" class="nav-link">
                  <i class="nav-icon fas fa-report"></i>
                  <p>
                    {{ $t("Layout.project_status") }}
                  </p>
                </router-link>
              </li>


                <li class="nav-item">
                <router-link to="/admin/reports/project-employee-tasks" class="nav-link">
                  <i class="nav-icon fas fa-report"></i>
                  <p>
                    {{ $t("Layout.Employee_project") }}
                  </p>
                </router-link>
              </li>


              <li class="nav-item">
                <router-link to="/admin/reports/employees-attendees" class="nav-link">
                  <i class="nav-icon fas fa-report"></i>
                  <p>
                    {{ $t("Layout.Employees_attendees") }}
                  </p>
                </router-link>
              </li>


            </ul>
          </li>



          <li v-if="$can('offer-list')" class="nav-item has-treeview">
          <a href="#" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>
              {{$t('Layout.services')}}
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
        </li>


          <li v-if="$can('offer-list')" class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="far fa-clipboard nav-icon"></i>
              <p>
                {{ $t("Layout.sales") }}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview" style="display: none">
              <li class="nav-item">
                <router-link to="/admin/offers" class="nav-link">
                  <i class="nav-icon fas fa-print"></i>
                  <p>{{ $t("Layout.offers") }}</p>
                </router-link>
              </li>
              <li v-if="$can('contract-list')" class="nav-item">
                <router-link to="/admin/contracts" class="nav-link">
                  <i class="nav-icon fas fa-file"></i>
                  <p>{{ $t("Layout.contracts") }}</p>
                </router-link>
              </li>
            </ul>
          </li>

          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-briefcase"></i>
              <p>
                {{ $t("Layout.attend") }}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item ml-2">
                <router-link to="/admin/attends" class="nav-link">
                  <i class="nav-icon fas fa-users"></i>
                  <p>{{ $t("Layout.attends") }}</p>
                </router-link>
              </li>
              <li class="nav-item ml-2">
                <router-link to="/admin/vacations" class="nav-link">
                  <i class="nav-icon fas fa-umbrella"></i>
                  {{ $t("Layout.vacations") }}
                </router-link>
              </li>
            </ul>
          </li>
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-book"></i>
              <p>
                {{ $t("Layout.wiki") }}
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">

              <li class="nav-item">
                <router-link to="/admin/categories" class="nav-link">
                  <i class="nav-icon fas fa-book"></i>
                  <p>{{ $t("Layout.categories") }}</p>
                </router-link>

                <router-link to="/admin/sub-category" class="nav-link">
                  <i class="nav-icon fas fa-book"></i>
                  <p>{{ $t("Layout.subcategories") }}</p>
                </router-link>

                <router-link to="/admin/topics" class="nav-link">
                  <i class="nav-icon fas fa-bookmark"></i>
                  <p>{{ $t("Layout.topics") }}</p>
                </router-link>
              
              </li>
            </ul>
          </li>

          <li class="nav-item">
            <router-link to="/admin/calendar" class="nav-link">
              <i class="nav-icon fas fa-calendar"></i>
              <p>{{ $t("Layout.calender") }}</p>&nbsp;&nbsp;&nbsp;<span v-if="layoutnotification > 0" class="badge badge-danger">{{ layoutnotification }}</span>
            </router-link>
          </li>

          <li v-if="$can('attribute-emails-list')" class="nav-item">
            <router-link to="/admin/attributes-emails-list" class="nav-link">
              <i class="nav-icon fas fa-envelope-open-text"></i>
              <p>{{ $t("Layout.AttributesEmails") }}</p>
            </router-link>
          </li>

          <li class="nav-item">
            <router-link to="/admin/todo/boards" class="nav-link">
              <i class="nav-icon fas fa-list-ul"></i>
              <p>Todo</p>
            </router-link>
          </li>


          <li class="nav-item">
            <router-link to="/admin/versions" class="nav-link">
              <i class="nav-icon fas fa-history"></i>
              <p>Versions</p>
            </router-link>
          </li>






        </ul>
      </nav>
    </div>
  </aside>
</template>
<script>
import { mapGetters } from "vuex";
export default {
  name: "SideBar",
  props:['layoutnotification'],
  methods: {
    getUserById(id) {
      this.$store
        .dispatch("user/getUserById", id)
        .then(() => {})
        .catch(() => {});
    },
  },
  computed: {
    ...mapGetters({
      user: "user/activeSingleUser",
    }),
  },
  mounted() {
    this.getUserById(this.$userId);
  },
};
</script>
