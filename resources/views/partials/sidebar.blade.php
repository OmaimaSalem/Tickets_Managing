<!-- Brand Logo -->
<a href="/admin" class="brand-link">
    <img src="{{ asset('assets/img/ALFerp.png') }}" alt="ALFerp Logo" class="brand-image img-circle elevation-3"
        style="opacity: .8">
    <span class="brand-text font-weight-light">{{ config('app.name', 'ALFerp') }}</span>
</a>

<!-- Sidebar -->
<div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
            <img src="{{ asset('assets/img/profile.png') }}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
            <a href="#" class="d-block">
                @auth
                {{ Auth::user()->name }}
                @endauth
            </a>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
            <li class="nav-item">
                <router-link to="/admin/dashboard" class="nav-link">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>
                        Dashboard
                    </p>
                </router-link>
            </li>
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-project-diagram"></i>
                    <p>
                        Projects Management
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    @can('project-list')
                        <li class="nav-item">
                            <router-link to="/admin/projects" class="nav-link">
                                <i class="nav-icon fas fa-briefcase"></i>
                                <p>
                                    Projects
                                </p>
                            </router-link>
                        </li>
                    @endcan
                    @can('ticket-list')
                        <li class="nav-item">
                            <router-link to="/admin/tickets" class="nav-link">
                                <i class="nav-icon fas fa-ticket-alt"></i>
                                <p>
                                    Tickets
                                </p>
                            </router-link>
                        </li>
                    @endcan
                    @can('task-list')
                        <li class="nav-item">
                            <router-link to="/admin/tasks" class="nav-link">
                                <i class="nav-icon fas fa-tasks"></i>
                                <p>
                                    Tasks
                                </p>
                            </router-link>
                        </li>
                    @endcan
                    @can('receipt-list')
                        <li class="nav-item">
                            <router-link to="/admin/receipts" class="nav-link">
                                <i class="nav-icon fas fa-receipt"></i>
                                <p>
                                    Receipts
                                </p>
                            </router-link>
                        </li>
                    @endcan
                    @can('user-list')
                        @if(auth()->user()->type != 'client')
                            <li class="nav-item">
                                <router-link to="/admin/clients" class="nav-link">
                                    <i class="nav-icon fas fa-list"></i>
                                    <p>
                                        Clients
                                    </p>
                                </router-link>
                            </li>
                        @endif
                    @endcan
                    @can('report-list')
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    Reports
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" style="display: none;">
                                <li class="nav-item">
                                <router-link to="/admin/time-report" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Time Report</p>
                                  </router-link>
                                </li>
                            </ul>
                        </li>
                    @endcan
                </ul>
            </li>

            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>
                        Offers & Contracts
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview" style="display: none;">
                  <li class="nav-item">
                      <router-link to="/admin/items/category" class="nav-link">
                          <i class="nav-icon fas fa-tags"></i>
                          <p>
                              Items Categories
                          </p>
                      </router-link>
                  </li>
                  <li class="nav-item">
                      <router-link to="/admin/items" class="nav-link">
                          <i class="nav-icon fas fa-bars"></i>
                          <p>
                              Items
                          </p>
                      </router-link>
                  </li>
                  @can('offer-list')
                      <li class="nav-item">
                          <router-link to="/admin/offers" class="nav-link">
                              <i class="nav-icon fas fa-print"></i>
                              <p>
                                  Offers
                              </p>
                          </router-link>
                      </li>
                  @endcan
                  <li class="nav-item">
                      <router-link to="/admin/contracts" class="nav-link">
                          <i class="nav-icon fas fa-file"></i>
                          <p>
                              Contracts
                          </p>
                      </router-link>
                  </li>
                </ul>
            </li>
            @can('mail-template')
              <li class="nav-item has-treeview">
                  <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>
                          Mail Templates
                          <i class="right fas fa-angle-left"></i>
                      </p>
                  </a>
                  <ul class="nav nav-treeview" style="display: none;">
                    <li class="nav-item">
                        <router-link to="/admin/mail-template-categories" class="nav-link">
                            <i class="nav-icon fas fa-tags"></i>
                            <p>
                                Template Categories
                            </p>
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/admin/mail-templates" class="nav-link">
                            <i class="nav-icon fas fa-bars"></i>
                            <p>
                                Templates
                            </p>
                        </router-link>
                    </li>
                  </ul>
              </li>
            @endcan


            @if(!auth()->user()->hasAnyPermission('hr','hr-assistant'))
            <li class="nav-item has-treeview @if(Request::is('admin/yearly-vacations') || Request::is('admin/yearly-holidays') || Request::is('admin/weekly-vacations')) menu-open @endif">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-briefcase"></i>
                    <p>
                        Attend
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview" style="@if(Request::is('admin/yearly-vacations') || Request::is('admin/yearly-holidays') || Request::is('admin/weekly-vacations')) display: block; @else display: none; @endif">


                    <li class="nav-item ml-2">
                        <router-link to="/admin/attends" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Attends
                            </p>
                        </router-link>
                    </li>
                    <li class="nav-item ml-2">
                        <router-link to="/admin/vacations" class="nav-link">
                            <i class="nav-icon fas fa-umbrella"></i>
                            <p>
                                Vactions
                            </p>
                        </router-link>
                    </li>
                </ul>
            <li>
            @endif


            @canany(['hr','hr-assistant'])

            <li class="nav-item has-treeview @if(Request::is('admin/yearly-vacations') || Request::is('admin/yearly-holidays') || Request::is('admin/weekly-vacations')) menu-open @endif">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-briefcase"></i>
                    <p>
                        Human Resources
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview" style="@if(Request::is('admin/yearly-vacations') || Request::is('admin/yearly-holidays') || Request::is('admin/weekly-vacations')) display: block; @else display: none; @endif">


                    <li class="nav-item ml-2">
                        <router-link to="/admin/attends" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Attends
                            </p>
                        </router-link>
                    </li>
                    <li class="nav-item ml-2">
                        <router-link to="/admin/vacations" class="nav-link">
                            <i class="nav-icon fas fa-umbrella"></i>
                            <p>
                                Vactions
                            </p>
                        </router-link>
                    </li>




                    <li class="nav-item ml-2">
                        <router-link to="/admin/manage-attends" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Manage Attends
                            </p>
                        </router-link>
                    </li>

                    <li class="nav-item ml-2">
                        <router-link to="/admin/manage-vacations" class="nav-link">
                            <i class="nav-icon fas fa-umbrella"></i>
                            <p>
                                Manage Vactions
                            </p>
                        </router-link>
                    </li>


                    {{-- <li class="nav-item">
                      <router-link to="/admin/yearly-vacations" class="nav-link">
                        <i class="fas fa-caravan nav-icon"></i>
                        <p>Yearly Vacations</p>
                      </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/admin/yearly-holidays" class="nav-link">
                            <i class="fas fa-caravan nav-icon"></i>
                            <p>Yearly Holidays</p>
                          </router-link>

                    </li> --}}


                    <li class="nav-item ml-2">
                        <router-link to="/admin/hr/manage-users" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Manage Users
                            </p>
                        </router-link>
                    </li>
                </ul>
            </li>
          @endcanany


            @can('wiki-list')
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-book"></i>
                    <p>
                        Wiki
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <router-link to="/admin/categories" class="nav-link">
                            <i class="nav-icon fas fa-book"></i>
                            <p>
                                Categories
                            </p>
                        </router-link>
                        <router-link to="/admin/topics" class="nav-link">
                            <i class="nav-icon fas fa-bookmark"></i>
                            <p>
                                Topics
                            </p>
                        </router-link>
                    </li>
                </ul>
            </li>
            @endcan

            @can('permission-list')

            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-cog"></i>
                    <p>
                        System
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <router-link to="/admin/permissions" class="nav-link">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>
                                Permissions
                            </p>
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/admin/roles" class="nav-link">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>
                                Role Management
                            </p>
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/admin/users" class="nav-link">
                            <i class="nav-icon fas fa-list"></i>
                            <p>
                                Employees
                            </p>
                        </router-link>
                    </li>
                </ul>
            </li>
            @endcan
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>
<!-- /.sidebar -->
