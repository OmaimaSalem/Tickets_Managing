<template>
    <v-container fluid data-app>
        <v-row class="mb-2">
            <v-col cols="10" >
                <v-row class="mb-2">
                            <v-col cols="4">
                                <multiselect
                                    v-model="search.assigned"
                                    :options="responsilbeArr"
                                    :close-on-select="true"
                                    :clear-on-select="true"
                                    :preserve-search="true"
                                    :preselect-first="false"
                                    placeholder="Assigned Users"
                                    @input="multiSearch('assigned')"
                                    style="border-radius: 10px;height: 4rem"
                                ></multiselect>
                            </v-col>
                            <v-col cols="4">
                                <v-text-field
                                    append-icon="fas fa-search"
                                    hide-details
                                    solo
                                    clear-icon="fas fa-clear"
                                    clearable
                                    :label="$t('Search.Number')"
                                    type="text"
                                    style="border-radius: 10px"
                                    v-model="search.number"
                                    v-on:keyup.enter="multiSearch('number')"
                                    @change="multiSearch('number')"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="4">
                                <v-text-field
                                    append-icon="fas fa-search"
                                    hide-details
                                    solo
                                    clear-icon="fas fa-clear"
                                    clearable
                                    :label="$t('Search.name')"
                                    type="text"
                                    style="border-radius: 10px"
                                    v-model="search.name"
                                    @change="multiSearch('name')"
                                ></v-text-field>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="4">
                                <v-text-field
                                    append-icon="fas fa-search"
                                    hide-details
                                    solo
                                    clear-icon="fas fa-clear"
                                    clearable
                                    :label="$t('Search.client')"
                                    type="text"
                                    style="border-radius: 10px"
                                    v-model="search.owner"
                                    v-on:keyup.enter="multiSearch('owner')"
                                    @change="multiSearch('owner')"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="4">
                                <v-text-field
                                    append-icon="fas fa-search"
                                    hide-details
                                    solo
                                    clear-icon="fas fa-clear"
                                    clearable
                                    :label="$t('Search.project')"
                                    type="text"
                                    style="border-radius: 10px"
                                    v-model="search.project"
                                    v-on:keyup.enter="multiSearch('project')"
                                    @change="multiSearch('project')"
                                ></v-text-field>

                            </v-col>
                            <v-col cols="4">
                                <v-text-field
                                    append-icon="fas fa-search"
                                    hide-details
                                    solo
                                    clear-icon="fas fa-clear"
                                    clearable
                                    :label="$t('Search.ticket')"
                                    type="text"
                                    style="border-radius: 10px"
                                    v-model="search.ticket"
                                    v-on:keyup.enter="multiSearch('ticket')"
                                    @change="multiSearch('ticket')"
                                ></v-text-field>
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="2" style="display: flex;flex-direction: column">
                        <v-text-field
                            append-icon="fas fa-calendar-alt"
                            hide-details
                            solo
                            clear-icon="fas fa-clear"
                            clearable
                            :label="$t('Search.deadline')"
                            type="date"
                            v-model="search.deadline"
                            v-on:keyup.enter="multiSearch('deadline')"
                            @change="multiSearch('deadline')"
                            style="background-color: white;border-radius: 10px"
                        ></v-text-field>
                        <v-card
                            class="dropdown dropdown-toggle sort-btn"
                            data-toggle="dropdown"
                            style="border-radius: 10px;margin-top:1rem">
                            <span style="font-size: 1rem" class="btn">{{$t('Task.sortTasks')}}</span>
                            <div class="dropdown-menu">
                                <span style="" class="dropdown-item sort-item" @click="sortTasks('number')">{{$t('Search.Number')}}</span>
                                <span  class="dropdown-item sort-item" @click="sortTasks('name')">{{$t('Search.name')}}</span>
                                <span  class="dropdown-item sort-item" @click="sortTasks('due_date')">{{$t('Search.deadline')}}</span>
                                <span  class="dropdown-item sort-item" @click="sortTasks('created_at')">{{$t('Search.createdAt')}}</span>

                            </div>
                        </v-card>
                    </v-col>
                </v-row>

                  <!-- <router-link :to="{ name: 'tasks.canaban' }" class=" float-right">
                      <i id="kanban" class="fab fa-trello fa-fw "></i>
                  </router-link> -->




        <v-row class="mt-2">
            <v-col cols="10 ">
                <template v-if="tasks.data">
                    <v-tabs
                        v-model="ticketstab"
                    >
                        <v-tabs-slider></v-tabs-slider>
                       
                        <v-tab :to="{ name: 'tasks.canaban' }">
                            <i id="kanban" class="fab fa-trello fa-fw "></i> {{$t('Task.kanban')}}
                        </v-tab>
                        <v-tab href="#ticketscards">
                            {{$t('global.cardView')}}
                        </v-tab>
                        <v-tab href="#ticketstable">
                            {{$t('global.tableView')}}
                        </v-tab>
                        <v-spacer></v-spacer>
                    </v-tabs>

                    <v-tabs-items v-model="ticketstab" style="background-color: transparent; margin-top: 2px">
                        <v-tab-item value="ticketscards">
                            <v-row>
                                <v-col cols="6" v-for="task in tasks.data" :key="task.id">
                                    <v-card style="border-radius: 2rem; margin-bottom: 0.8rem;margin-top: .5rem" >
                                        <v-container fluid>
                                            <v-row>
                                                <v-col cols="2">
                                                    <h6 style="color:#959595">#{{ task.id }}</h6>
                                                </v-col>
                                                <v-col cols="2" style="margin-left:-2rem">
                                                    <i @click.prevent="OpenStatus(task)" v-if="checkStatus(task)" style="color:#C64848" class="fas fa-toggle-off changeStatus"></i>
                                                    <i @click.prevent="CloseStatus(task)" v-else style="color:#67A046"  class="fas fa-toggle-on changeStatus"></i>
                                                </v-col>
                                            </v-row>
                                            <v-row >
                                                <v-col cols="6">
                                                    <router-link
                                                        :to="'/admin/task/'+task.id"
                                                        class="h5" style="color: #000000; font-weight: bold;"
                                                        data-toggle="tooltip" data-placement="top" :title="task.name">
                                                        {{task.name| subStrCardTitle}}
                                                    </router-link>
                                                </v-col>
                                                <v-col cols="4" style="margin-left:-1rem">
                                                    <div :id="task.status.name" class="status_container">
                                                        <span class="stat-name">{{task.status.name}}</span>
                                                    </div>
                                                </v-col>
                                                <v-col cols="2" style="margin-left:1rem">
                                                    <i @click.prevent="editModel(task)" style="color:#81B488" class="fa fa-edit important-actions"></i>
                                                    <i @click.prevent="deleteTask(task.id)" style="color:#C64848" class="fa fa-trash ml-1 important-actions"></i>
                                                </v-col>
                                            </v-row>
                                            <v-row v-if="task.description">
                                                <v-col cols="12" style="margin-top: .3rem">
                                                    <p v-html="$options.filters.taskDescription(task.description)" class="description">

                                                    </p>
                                                </v-col>
                                            </v-row>
                                            <v-row v-else>
                                                <v-col cols="12" style="margin-top: .3rem">
                                                    <p class="description">
                                                       -----
                                                    </p>
                                                </v-col>
                                            </v-row>
                                            <v-row style="margin-top:-1rem">
                                                <v-col cols="7">
                                                    <span style="font-weight: bold;font-size: .9rem">{{$t('Task.estimatedTime')}}:</span>
                                                    <small style="color:#959595;">{{task.estimated_time}} hours</small>
                                                </v-col>
                                            </v-row>
                                            <v-row>
                                                <v-col cols="7">
                                                    <span style="font-weight: bold;font-size: .9rem">{{$t('Task.createdAt')}}:</span>
                                                    <small style="color:#959595;">{{task.created_at | tasksDateNow }}</small>
                                                </v-col>
                                                <v-col cols="5">
                                                    <span style="font-weight: bold;font-size: .9rem">{{$t('Task.endDate')}}:</span>
                                                    <small style="color:#959595;">{{task.deadline | tasksDateNow }}</small>
                                                </v-col>
                                            </v-row>
                                            <v-row style="margin-top: .3rem">
                                                <v-col cols="7" v-if="task.ticket.owner  && task.ticket.owner.length > 0" style="display: inline-flex;">
                                                    <span style="font-weight: bold;font-size: .9rem">{{$t('Task.owners')}}:</span>
                                                    <router-link
                                                        v-if="task.ticket.owner[0]"
                                                        :to="'/admin/profile/'+task.ticket.owner[0].id"
                                                    >
                                                        <avatar
                                                            color="#90b0fa"
                                                            :fullname="task.ticket.owner[0].name"
                                                            data-placement="top" :title="task.ticket.owner[0].name"
                                                            :size="20"
                                                            style="margin-left: .3rem">
                                                        </avatar>
                                                    </router-link>
                                                    <router-link
                                                        v-if="task.ticket.owner[1]"
                                                        :to="'/admin/profile/'+task.ticket.owner[1].id"
                                                    >
                                                        <avatar
                                                            color="#90b0fa"
                                                            :fullname="task.ticket.owner[1].name"
                                                            data-placement="top" :title="task.ticket.owner[1].name"
                                                            :size="20"
                                                            style="margin-left: .3rem">
                                                        </avatar>
                                                    </router-link>
                                                    <router-link
                                                        v-if="task.ticket.owner[2]"
                                                        :to="'/admin/profile/'+task.ticket.owner[2].id"
                                                    >
                                                        <avatar
                                                            color="#90b0fa"
                                                            :fullname="task.ticket.owner[2].name"
                                                            data-placement="top" :title="task.ticket.owner[2].name"
                                                            :size="20"
                                                            style="margin-left: .3rem">
                                                        </avatar>
                                                    </router-link>
                                                    <router-link
                                                        v-if="task.ticket.owner[3]"
                                                        :to="'/admin/profile/'+task.ticket.owner[3].id"
                                                    >
                                                        <avatar
                                                            color="#90b0fa"
                                                            :fullname="task.ticket.owner[3].name"
                                                            data-placement="top" :title="task.ticket.owner[3].name"
                                                            :size="20"
                                                            style="margin-left: .3rem">
                                                        </avatar>
                                                    </router-link>
                                                </v-col>
                                                <v-col cols="7" v-else-if="task.project.owners && task.project.owners.length > 0" style="display: inline-flex;">
                                                    <span style="font-weight: bold;font-size: .9rem">{{$t('Task.owners')}}:</span>
                                                    <router-link
                                                        v-if="task.project.owners[0]"
                                                        :to="'/admin/profile/'+task.project.owners[0].id"
                                                    >
                                                        <avatar
                                                            color="#90b0fa"
                                                            :fullname="task.project.owners[0].name"
                                                            data-placement="top" :title="task.project.owners[0].name"
                                                            :size="20"
                                                            style="margin-left: .3rem">
                                                        </avatar>
                                                    </router-link>
                                                    <router-link
                                                        v-if="task.project.owners[1]"
                                                        :to="'/admin/profile/'+task.project.owners[1].id"
                                                    >
                                                        <avatar
                                                            color="#90b0fa"
                                                            :fullname="task.project.owners[1].name"
                                                            data-placement="top" :title="task.project.owners[1].name"
                                                            :size="20"
                                                            style="margin-left: .3rem">
                                                        </avatar>
                                                    </router-link>
                                                    <router-link
                                                        v-if="task.project.owners[2]"
                                                        :to="'/admin/profile/'+task.project.owners[2].id"
                                                    >
                                                        <avatar
                                                            color="#90b0fa"
                                                            :fullname="task.project.owners[2].name"
                                                            data-placement="top" :title="task.project.owners[2].name"
                                                            :size="20"
                                                            style="margin-left: .3rem">
                                                        </avatar>
                                                    </router-link>
                                                    <router-link
                                                        v-if="task.project.owners[3]"
                                                        :to="'/admin/profile/'+task.project.owners[3].id"
                                                    >
                                                        <avatar
                                                            color="#90b0fa"
                                                            :fullname="task.project.owners[3].name"
                                                            data-placement="top" :title="task.project.owners[3].name"
                                                            :size="20"
                                                            style="margin-left: .3rem">
                                                        </avatar>
                                                    </router-link>
                                                </v-col>
                                                <v-col cols="7" v-else>
                                                    <span style="font-weight: bold;font-size: .9rem">{{$t('Task.owners')}}:</span>
                                                    <small>{{$t('Task.noOwners')}}</small>
                                                </v-col>
                                                <v-col cols="4">
                                                    <span style="font-weight: bold;font-size: .9rem">{{$t('Task.priorty')}}:</span>
                                                    <small style="color:#959595;">{{task.priority}}</small>
                                                </v-col>
                                            </v-row>
                                            <v-row>
                                                <v-col cols="7">
                                                    <span style="font-weight: bold;font-size: .9rem">{{$t('Task.project')}}:</span>
                                                   <router-link
                                                        v-if="task.project"
                                                        :title="task.project.name"
                                                        style="color:#959595"
                                                        :to="'/admin/project/'+task.project.id"
                                                    >{{task.project.name | subStrPrTi}}</router-link>
                                                    <small v-else style="color:#959595;">{{$t('Task.noProject')}}</small>
                                                </v-col>
                                                <v-col cols="5">
                                                    <span style="font-weight: bold;font-size: .9rem">{{$t('Task.ticket')}}: </span><router-link
                                                        v-if="task.ticket && task.ticket !== [] && task.ticket !== null"
                                                        :title="task.ticket.name"
                                                        style="color:#959595"
                                                        :to="'/admin/project/'+task.project.id"
                                                    >{{task.ticket.name | subStrPrTi}}</router-link>
                                                    <small v-else style="color:#959595;">{{$t('Task.noTicket')}}</small>
                                                </v-col>
                                            </v-row>
                                        </v-container>
                                    </v-card>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col>
                                    <pagination :data="myTasks" :limit="parseInt(2)" size="small" @pagination-change-page="getResults">
                                        <span slot="prev-nav">&lt; Previous</span> <span slot="next-nav">Next &gt;</span>
                                    </pagination>
                                </v-col>
                            </v-row>
                        </v-tab-item>

                        <v-tab-item value="ticketstable">
                            <v-simple-table dense fixed-header>
                                <template v-slot:default>
                                <thead>
                                    <tr>
                                    <th class="text-left">#</th>
                                    <th class="text-left">{{$t('Task.title')}}</th>
                                    <th class="text-left">Status</th>
                                    <th class="text-left">{{$t('Task.priorty')}}</th>
                                    <th class="text-left">{{$t('Task.project')}}</th>
                                    <!-- <th class="text-left">Created At</th> -->
                                    <th class="text-left">{{$t('Task.deadline')}}</th>
                                    <th class="text-left">{{$t('Task.client')}}</th>
                                    <th class="text-left">{{$t('Task.ticket')}}</th>
                                    <th width="3%" class="text-left">{{$t('Task.ETC')}}</th>
                                    <th class="text-left">{{$t('Task.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="task in tasks.data" :key="task.id">
                                        <td>{{task.id}}</td>
                                        <td>
                                            <router-link :title="task.name" :to="{ name: 'task', params: { id: task.id } }">
                                            {{ task.name | subStr}}
                                            </router-link>
                                        </td>
                                        <td>{{task.status.name}}</td>
                                        <td> {{task.priority}} </td>
                                        <td v-if="task.project">
                                            <router-link
                                                v-if="task.project"
                                                :title="task.project.name"
                                                :to="'/admin/project/'+task.project.id"
                                            >{{task.project.name | subStr}}</router-link>
                                            <span v-else>------</span>
                                        </td>

                                        <!-- <td> {{task.created_at | tasksDateNow}}</td> -->
                                        <td>{{ task.deadline | tasksDateNow }}</td>
                                        <td>
                                            <router-link
                                                :title="task.ticket.owner[0].name"
                                                v-if="task.ticket.owner && task.ticket.owner.length > 0"
                                                :to="'/admin/profile/'+task.ticket.owner[0].id"
                                            >{{task.ticket.owner[0].name | subStr}}</router-link>
                                            <router-link
                                                :title="task.project.owners[0].name"
                                                v-else-if="task.project.owners && task.project.owners.length > 0"
                                                :to="'/admin/profile/'+task.project.owners[0].id"
                                            >{{task.project.owners[0].name | subStr}}</router-link>
                                            <span v-else>------</span>
                                        </td>
                                        <td>
                                            <router-link
                                                v-if="task.ticket"
                                                :title="task.ticket.name"
                                                :to="'/admin/ticket/'+task.ticket.id"
                                            >{{task.ticket.name | subStr}}</router-link>
                                            <span v-else>------</span>
                                        </td>
                                        <td>{{task.estimated_time}}</td>
                                        <td>
                                            <i
                                            @click="editModel(task)"
                                            data-widget="collapse"
                                            data-toggle="tooltip"
                                            title="Edit"
                                            class="text-success icon fas fa-edit"
                                            style=":hover {color: #ffffff}"
                                            ></i>
                                            <i
                                            @click="deleteTask(task.id)"
                                            data-widget="collapse"
                                            data-toggle="tooltip"
                                            title="Edit"
                                            class="text-danger icon pl-1 fas fa-trash"
                                            ></i>
                                            <i @click.prevent="OpenStatus(task)" v-if="checkStatus(task)" style="color:#C64848;" class="fas fa-toggle-off changeStatusTable "></i>
                                            <i @click.prevent="CloseStatus(task)" v-else style="color:#67A046;"  class="fas fa-toggle-on changeStatusTable "></i>
                                        </td>
                                    </tr>
                                </tbody>
                                </template>
                            </v-simple-table>
                            <pagination
                                :data="myTasks"
                                :limit="parseInt(2)"
                                size="small"
                                @pagination-change-page="getResults"
                            >
                                <span slot="prev-nav">&lt; Previous</span>
                                <span slot="next-nav">Next &gt;</span>
                            </pagination>
                            </v-tab-item>
                        </v-tabs-items>
                </template>
            </v-col>
            <v-col cols="2">
                <v-btn bottom id="reset-btn" block @click="clearQuery">{{$t('global.resetQuery')}}</v-btn>
                <v-row>
                    <v-col cols="12" class="filter-container">
                        <v-container fluid>
                            <v-row class="filter-header">
                                <v-col cols="9" style="display: flex">
                                    <i class="fas fa-filter" style="font-size: 1rem;margin-top: .3rem" ></i>
                                    <strong>Filters</strong>
                                </v-col>
                            </v-row>
                            <v-row style="margin-top:.4rem;">
                                <v-col cols="12" class="act-filters-container" >
                                    <v-container fluid>
                                        <v-row>
                                            <v-col cols="6">
                                                <button @click="filterTasks('all')" style="border: none"><small class="filter-text">{{$t('Filter.all')}}</small></button>
                                            </v-col>
                                        </v-row>
                                        <hr>
                                        <v-row>
                                            <strong style="margin-top:-.5rem">Status</strong>
                                        </v-row>
                                        <v-row>
                                            <v-col cols="6" class="single-filter">
                                                <!-- <input class="filter-check"  type="checkbox"  @click="filterTasks('open')"><small class="filter-text">{{$t('Filter.open')}}</small> -->
                                                <button @click="filterTasks('open')" style="border: none"><small class="filter-text">{{$t('Filter.open')}}</small></button>
                                            </v-col>
                                            <v-col cols="6" class="single-filter" style="margin-left:-.5rem">
                                                <!-- <input class="filter-check"  type="checkbox" @click="filterTasks('pending')"><small class="filter-text" >{{$t('Filter.pending')}}</small> -->
                                                <button @click="filterTasks('pending')" style="border: none"><small class="filter-text">{{$t('Filter.pending')}}</small></button>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col cols="6" class="single-filter">
                                                <!-- <input class="filter-check"  type="checkbox"  @click="filterTasks('in-progress')"><small class="filter-text" >{{$t('Filter.doing')}}</small> -->
                                                <button @click="filterTasks('in-progress')" style="border: none"><small class="filter-text">{{$t('Filter.doing')}}</small></button>
                                            </v-col>
                                            <v-col cols="6" class="single-filter">
                                                <!-- <input class="filter-check"  type="checkbox" @click="filterTasks('done')"><small class="filter-text" >{{$t('Filter.done')}}</small> -->
                                                <button @click="filterTasks('done')" style="border: none"><small class="filter-text">{{$t('Filter.done')}}</small></button>
                                            </v-col>
                                        </v-row>

                                    </v-container>
                                </v-col>
                            </v-row>
                        </v-container>
                        <v-btn
                            color="#234FA3"
                            dark
                            fixed
                            bottom
                            right
                            fab
                            @click.stop="newModel"
                        >
                            <v-icon>fas fa-plus</v-icon>
                        </v-btn>
                    </v-col>
                </v-row>

            </v-col>
            <v-col cols="2" style="margin-top: -4rem">

<!--                <v-card style="border-radius:15px ">-->
<!--                    <v-tabs-->
<!--                        v-model="tab"-->
<!--                    >-->
<!--                        <v-tabs-slider></v-tabs-slider>-->
<!--                        <v-tab href="#filter" >-->
<!--                            <v-icon>fas fa-filter</v-icon>-->
<!--                        </v-tab>-->
<!--                        <v-tab href="#search" >-->
<!--                            <v-icon>fas fa-search</v-icon>-->
<!--                        </v-tab>-->
<!--                    </v-tabs>-->
<!--                    <v-tabs-items v-model="tab">-->
<!--                        <v-tab-item value="filter">-->
<!--                            <v-card flat>-->
<!--                                <v-card-text>-->
<!--                                    <v-btn @click="filterTasks('all')" block text>All</v-btn>-->
<!--                                    <v-btn @click="filterTasks('open')" block text>Open</v-btn>-->
<!--                                    <v-btn @click="filterTasks('pending')" block text>Pending</v-btn>-->
<!--                                    <v-btn @click="filterTasks('in-progress')" block text>In-Progress</v-btn>-->
<!--                                    <v-btn @click="filterTasks('done')" block text>Done</v-btn>-->
<!--                                </v-card-text>-->
<!--                            </v-card>-->
<!--                        </v-tab-item>-->
<!--                        <v-tab-item value="search">-->
<!--                            <v-col >-->
<!--                                <v-card flat >-->
<!--                                    <v-card-text>-->
<!--                                        <v-row>-->
<!--                                            <v-col cols="12">-->

<!--                                            </v-col>-->
<!--                                            <v-col cols="12">-->
<!--                                                <v-btn color="#A0466F" @click="multiSearch" block dark>Search</v-btn>-->
<!--                                            </v-col>-->
<!--                                        </v-row>-->
<!--                                    </v-card-text>-->
<!--                                </v-card>-->
<!--                            </v-col>-->

<!--                        </v-tab-item>-->
<!--                    </v-tabs-items>-->

                    <!-- <createTaskComponent @TaskCreated="getTasks"></createTaskComponent> -->
<!--                </v-card>-->

            </v-col>

        </v-row>


        <!-- <v-dialog v-model="editMode" persistent max-width="700px">
            <v-card>
                <v-card-title>
                    <span class="headline">Edit Task</span>
                </v-card-title>
                <v-card-text>
                    <v-container>
                        <v-row justify="center">
                            <v-col cols="12">
                                <v-text-field  label="Task Name" v-model="form.name" required></v-text-field>
                            </v-col>
                            <v-col cols="6">
                                <v-select
                                    @change="onStatusChange($event)"
                                    :items="statusArr"
                                    label="Status"
                                    chips
                                    clearable
                                    :value="form.status.name"
                                ></v-select>
                            </v-col>
                            <v-col cols="6" v-if="form.project">
                                <v-select
                                    @change="onProjectChange($event)"
                                    :items="projectsArr"
                                    label="Projects"
                                    chips
                                    clearable
                                    :value="form.project.name"
                                ></v-select>
                            </v-col>
                            <v-col cols="6" v-else>

                            </v-col>
                            <v-col cols="6">
                                <v-select
                                    @change="onTicketsChange($event)"
                                    :items="ticketsArr"
                                    label="Ticket"
                                    chips
                                    clearable
                                    :value="form.ticket.name"
                                ></v-select>
                            </v-col>
                            <v-col cols="6">
                                <v-select
                                    @change="onResponsibleChange($event)"
                                    :items="responsilbeArr"
                                    label="Assign To"
                                    chips
                                    clearable
                                    :value="form.responsible.name"
                                ></v-select>
                            </v-col>
                            <v-row>
                                <v-col cols="6">
                                    <v-date-picker color="#A0466F" @change="handleDate" v-model="date"></v-date-picker>
                                </v-col>
                                <v-col cols="6">
                                    <v-time-picker color="#A0466F" format="24hr" @change="handleDate" v-model="time"></v-time-picker>
                                </v-col>
                            </v-row>
                        </v-row>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="closeTask">Close</v-btn>
                    <v-btn color="blue darken-1" text @click="editTask">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog> -->

         <div
            class="modal fade"
            id="newTask"
            tabindex="-1"
            role="dialog"
            aria-labelledby="newTaskLabel"
            aria-hidden="true"
        >

            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5
                            v-show="!editMode"
                            class="modal-title"
                            id="newTaskLabel"
                        >
                            {{ $t("Task.createNewTask") }}
                        </h5>
                        <h5
                            v-show="editMode"
                            class="modal-title"
                            id="newTaskLabel"
                        >
                            {{ $t("Task.editTask") }}
                        </h5>
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form
                        @submit.prevent="
                            editMode ? editTask(form) : createTask(form)
                        "
                        @keydown="form.onKeydown($event)"
                    >
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name">{{
                                    $t("Task.taskName")
                                }}
                                    <span class="asterisk_input"></span>
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.has('name')
                                    }"
                                />
                                <has-error
                                    :form="form"
                                    field="name"
                                ></has-error>
                            </div>
                            <div class="form-group">
                                <label for="description">{{
                                    $t("Task.taskDesc")
                                }}</label>
                                <quill-editor
                                    id="comments-editor"
                                    v-model="form.description"
                                    ref="myQuillEditor"
                                    :options="editorOption"
                                ></quill-editor>
                                <has-error
                                    :form="form"
                                    field="description"
                                ></has-error>
                            </div>
                            <div class="form-group">
                                <label for="name">{{
                                    $t("Task.status")
                                }}</label>
                                <multiselect
                                    v-model="form.status"
                                    :options="status"
                                    label="name"
                                    track-by="name"
                                    @input="opt => (form.status_id = opt.id)"
                                ></multiselect>

                                <has-error
                                    :form="form"
                                    field="status_id"
                                ></has-error>
                            </div>





                            <!-- Showing all clients and getting project related to this client -->
                            <div class="form-group" v-if="form.project">
                                    <label for="name">{{
                                        $t("Task.client")
                                    }}</label>
                                    <multiselect
                                        v-model="form.owner"
                                        :options="owners"
                                        @input="getProjects(form.owner.id)"
                                        :close-on-select="true"
                                        :clear-on-select="true"
                                        :preserve-search="true"
                                        :placeholder="$t('Task.selectOne')"
                                        label="name"
                                        :preselect-first="true"
                                        :allow-empty="false"
                                        deselect-label="Can't remove this value"
                                        :disabled="isDisabled"
                                    ></multiselect>
                                    <has-error
                                        :form="form"
                                        field="client_id"
                                    ></has-error>
                            </div>
                            <!-- Showing Clients related to the selected project -->
                            <div class="form-group" v-else>
                                <label for="name">{{
                                        $t("Task.client")
                                    }}</label>
                                    <multiselect
                                        v-model="form.owner"
                                        :options="owners"
                                        @input="getProjects(form.owner.id)"
                                        :close-on-select="true"
                                        :clear-on-select="true"
                                        :preserve-search="true"
                                        :placeholder="$t('Task.selectOne')"
                                        label="name"
                                        :preselect-first="true"
                                        :allow-empty="false"
                                        deselect-label="Can't remove this value"
                                        :disabled="isDisabled"
                                    ></multiselect>
                                    <has-error
                                        :form="form"
                                        field="client_id"
                                    ></has-error>
                            </div>



                                                    <!-- Showing all projects and getting clients related to this project -->
                            <div class="form-group" v-if="form.owner && form.owner.length > 0">
                                <label for="name">{{
                                    $t("Task.project")
                                }}</label>
                                <multiselect
                                    v-model="form.project"
                                    :options="projects"
                                    :close-on-select="true"
                                    :clear-on-select="false"
                                    :preserve-search="true"
                                    :placeholder="$t('Task.selectOne')"
                                    label="name"
                                    :preselect-first="true"
                                    :allow-empty="false"
                                     @input="getClientsPerProject(form.project.id)"
                                ></multiselect>
                                <has-error
                                    :form="form"
                                    field="project_id"
                                ></has-error>
                            </div>


                            <!-- Showing Projects related to the selected project -->
                            <div class="form-group" v-else>
                                <label for="name">{{
                                    $t("Task.project")
                                }}<span class="asterisk_input"></span>
                                </label>


                                <!-- <multiselect
                                    v-model="form.project"
                                    :options="projects"
                                    :close-on-select="true"
                                    :clear-on-select="false"
                                    :preserve-search="true"
                                    :placeholder="$t('Task.selectOne')"
                                    label="name"
                                    :preselect-first="true"
                                    :allow-empty="false"
                                ></multiselect> -->

                    <v-autocomplete
                        v-model="form.project"
                        :items="projects"
                        :search-input.sync="projectSync"
                        color="white"
                        hide-no-data
                        hide-selected
                        item-text="name"
                        item-value="id"
                        :placeholder="$t('Task.selectOne')"
                        prepend-icon="mdi-database-search"
                        return-object
                    ></v-autocomplete>


                                <has-error
                                    :form="form"
                                    field="project_id"
                                ></has-error>
                            </div>


                            <!-- <div class="form-group">
                                <label for="name">{{
                                    $t("Task.client")
                                }}</label>
                                <multiselect
                                    v-model="form.owner"
                                    :options="owners"
                                    @input="getProjects(form.owner.id)"
                                    :close-on-select="true"
                                    :clear-on-select="false"
                                    :preserve-search="true"
                                    :placeholder="$t('Task.selectOne')"
                                    label="name"
                                    :preselect-first="true"
                                    :allow-empty="false"
                                    deselect-label="Can't remove this value"
                                    :disabled="isDisabled"
                                ></multiselect>
                                <has-error
                                    :form="form"
                                    field="client_id"
                                ></has-error>
                            </div> -->



                            <!-- <div class="form-group">
                                <label for="name">{{
                                    $t("Task.project")
                                }}</label>
                                <multiselect
                                    v-model="form.project"
                                    :options="projects"
                                    :close-on-select="true"
                                    :clear-on-select="false"
                                    :preserve-search="true"
                                    :placeholder="$t('Task.selectOne')"
                                    label="name"
                                    :preselect-first="true"
                                    :allow-empty="false"
                                ></multiselect>
                                <has-error
                                    :form="form"
                                    field="project_id"
                                ></has-error>
                            </div> -->



                            <div class="form-group" v-if="form.project.tickets">
                                <label for="name">{{
                                    $t("Ticket.ticket")
                                }}</label>
                                <multiselect
                                    v-model="form.ticket"
                                    :options="form.project.tickets"
                                    :placeholder="$t('Task.selectOne')"
                                    label="name"
                                ></multiselect>
                                <has-error
                                    :form="form"
                                    field="ticket_id"
                                ></has-error>
                            </div>
                            <div class="form-group">
                                <label for="name">{{
                                    $t("Task.responsible")
                                }}</label>
                                <multiselect
                                    v-model="form.responsible"
                                    :options="responsible"
                                    :close-on-select="true"
                                    :clear-on-select="false"
                                    :preserve-search="true"
                                    :placeholder="$t('Task.selectOne')"
                                    label="name"
                                    :preselect-first="true"
                                    @input="
                                        opt => (form.responsible_id = opt.id)
                                    "
                                >
                                    <template
                                        slot="selection"
                                        slot-scope="{ values, search, isOpen }"
                                    >
                                        <span
                                            class="multiselect__single"
                                            v-if="values.length && !isOpen"
                                            >{{ values.length }}
                                            {{ $t("Task.options") }}</span
                                        >
                                    </template>
                                </multiselect>
                                <has-error
                                    :form="form"
                                    field="responsible_id"
                                ></has-error>
                            </div>
                            <div class="form-group" v-if="form.priority">
                                <label for="priority">{{
                                    $t("Task.priorty")
                                }}</label>
                                <multiselect
                                    class="clearfix"
                                    v-model="form.priority"
                                    :options="priorityList"
                                    :close-on-select="true"
                                    :allow-empty="false"
                                    :show-labels="false"
                                    :placeholder="$t('Task.selectOne')"
                                ></multiselect>
                                <has-error
                                    :form="form"
                                    field="priority"
                                ></has-error>
                            </div>
                            <div class="form-group">
                                <label for="deadline">{{
                                    $t("Task.deadline")
                                }}</label>
                                <date-picker
                                    v-model="form.deadline"
                                    lang="en"
                                    type="datetime"
                                    format="DD-MM-YYYY HH:mm"
                                    :minute-step="15"
                                    value-type="format"
                                    input-class="form-control"
                                ></date-picker>
                                <has-error
                                    :form="form"
                                    field="deadline"
                                ></has-error>
                            </div>
                            <div class="form-group">
                                <label for="estimated_time">{{
                                    $t("Task.estimatedTime")
                                    }}</label>
                                <input
                                    v-model="form.estimated_time"
                                    type="number"
                                    min="0"
                                    name="estimated_time"
                                    class="form-control"
                                    :class="{
                            'is-invalid': form.errors.has(
                                'estimated_time'
                            )
                        }"
                                />
                                <has-error
                                    :form="form"
                                    field="estimated_time"
                                ></has-error>
                            </div>
                        </div>

                        <div class="modal-footer">
<!--                            <button-->
<!--                                v-show="!editMode"-->
<!--                                type="submit"-->
<!--                                class="btn btn-primary"-->
<!--                                :disabled="form.project_id == ''"-->
<!--                            >-->
<!--                                {{ $t("Task.save") }}-->
<!--                            </button>-->
                            <v-btn :disabled="form.project_id == '' || isLoading"  v-show="!editMode" type="submit" color="#ffffff" style="background-color:#234FA3 " text>  {{ $t("Task.save") }}</v-btn>
<!--                            <button-->
<!--                                v-show="editMode"-->
<!--                                type="submit"-->
<!--                                class="btn btn-success"-->
<!--                                :disabled="form.project_id == ''"-->
<!--                            >-->
<!--                                {{ $t("Task.update") }}-->
<!--                            </button>-->
                            <v-btn :disabled="form.project_id == '' || isLoading"  v-show="editMode" type="submit" color="#ffffff" style="background-color:#234FA3 " text>  {{ $t("Task.update") }}</v-btn>

                        </div>
                    </form>
                </div>
            </div>
        </div>
        


    </v-container>
</template>

<script>
import { mapGetters } from "vuex";
import VueBootstrap4Table from "vue-bootstrap4-table";
import moment from "moment";
import { quillEditor } from "vue-quill-editor";
import DatePicker from "vue2-datepicker";
import pagination from 'laravel-vue-pagination';

// require styles
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";
import _ from "lodash";


export default {
    data() {
        return {
            ticketstab: 'ticketstable',
            editMode: false,
            isDisabled: false,
            isLoading: false,
            date: new Date().toISOString().substr(0, 10),
            time: new Date().toISOString().substr(12, 7),
            timer: null,
            projectSync: null,
            tab: 'filter',
            editableTask : '',
            myTasks: {},
            statusArr: [],
            projectsArr: [],
            ticketsArr : [],
            responsilbeArr : [],
            form: new Form({
                id: "",
                name: "",
                description: "",
                status: {
                    id: 1,
                    name: "open"
                },
                estimated_time : "",
                project: {},
                project_id: '',
                ticket: [],
                ticket_id: '',
                owner: {},
                responsible: {},
                priority: "",
                deadline: ""
            }),
            priorityList: ["normal", "high", "low"],
            editorOption: {
                modules: {
                    toolbar: [
                        ["bold", "italic", "underline", "strike"],
                        ["blockquote", "code-block"],
                        [{ list: "ordered" }, { list: "bullet" }]
                    ]
                }
            },
            config: {
                server_mode: true,
                card_mode: false,
                show_refresh_button: false,
                pagination: true,
                pagination_info: true,
                per_page: 15,
                global_search: {
                    placeholder: this.$t("Task.enterCustom"),
                    init: {
                        value: ""
                    }
                }
            },
            classes: {
                table: {
                    "table-sm": true
                }
            },
            queryParams: {
                sort: [],
                filters: [],
                global_search: "",
                per_page: 15,
                page: 1
            },
            total_rows: 0,
            search: {
                name: '',
                number: '',
                owner: '',
                project: '',
                deadline: '',
                ticket: '',
                assigned: '',
            },
        };
    },


    methods: {
        resultResponsilbes() {
            this.responsilbeArr= []
            this.responsilbeArr.push('All')
            this.responsilbeArr.push('Not Assigned')
            this.employees.forEach(employee => {
                this.responsilbeArr.push(employee.name)
            });
        },
        checkStatus(task){
            return task.status.name === 'done';
        },
        onChangeQuery(queryParams) {
            this.queryParams = queryParams;
            this.getTasks();
        },
        getResults(page) {
            this.queryParams.page = page;
            this.getTasks();
        },
        filterTasks(filter) {
            this.queryParams.global_search = '';
            this.queryParams.page = 1
            let typeSelect = false;
            this.queryParams.filters.forEach( item => {
                if(item.type === 'select'){
                    typeSelect =true;
                }
            })
            if(typeSelect){
                this.queryParams.filters.forEach( item => {
                    if(item.type === 'select'){
                        item.selected_options = [filter]
                    }
                })
            }
            else{
                this.queryParams.filters.push({
                    "type": "select",
                    "name": "status.state",
                    "selected_options": [filter]
                });
            }
            if(filter === 'all'){
                this.clearQuery();
            }
            this.getTasks();
        },
        clearQuery(){
            this.queryParams= {
                sort: [],
                filters: [],
                global_search: "",
            }
            this.search.name =''
            this.search.number =''
            this.search.owner =''
            this.search.deadline =''
            this.search.project =''
            this.search.ticket =''
            this.search.assigned =''
            this.getTasks()
        },
        multiSearch(target) {
            let typeSimple = false;
            this.queryParams.filters.forEach( item => {
                if(item.type === 'simple'){
                    typeSimple =true;
                }
            })
            let found = false
            if(typeSimple){
                if(target === 'number'){
                    this.queryParams.filters.forEach( item => {
                        if(item.type === 'simple' && item.name === 'number'){
                            if(this.search.number === null || this.search.number === "" ){
                                this.queryParams.filters = this.queryParams.filters.filter(items => items.name !== "number")
                                found = true;
                            }
                            else{
                                item.text = this.search.number;
                                found = true;
                            }
                        }
                    })
                    if(!found){
                        this.queryParams.filters.push({ 
                            type:"simple", 
                            name:"number", 
                            text:this.search.number 
                            })
                    }
                }
                else if(target === 'name'){
                    this.queryParams.filters.forEach( item => {
                        if(item.type === 'simple' && item.name === 'name'){
                            if(this.search.name === null || this.search.name === "" ){
                                this.queryParams.filters = this.queryParams.filters.filter(items => items.name !== "name")
                                found = true;
                            }
                            else{
                                item.text = this.search.name;
                                found = true;
                            }
                        }
                    })
                    if(!found){
                        this.queryParams.filters.push({ 
                            type:"simple", 
                            name:"name", 
                            text:this.search.name 
                            })
                    }
                }
                else if(target === 'owner'){
                    this.queryParams.filters.forEach( item => {
                        if(item.type === 'simple' && item.name === 'owner.name'){
                            if(this.search.owner === null || this.search.owner === "" ){
                                this.queryParams.filters = this.queryParams.filters.filter(items => items.name !== "owner.name")
                                found = true;
                            }
                            else{
                                item.text = this.search.owner;
                                found = true;
                            }
                        }
                    })
                    if(!found){
                        this.queryParams.filters.push({ 
                            type:"simple", 
                            name:"owner.name", 
                            text:this.search.owner 
                            })
                    }
                }
                else if(target === 'project'){
                    this.queryParams.filters.forEach( item => {
                        if(item.type === 'simple' && item.name === 'project.name'){
                            if(this.search.project === null || this.search.project === "" ){
                            this.queryParams.filters = this.queryParams.filters.filter(items => items.name !== "project.name")
                            found = true;
                            }
                            else{
                                item.text = this.search.project;
                                found = true;
                            }
                        }
                    })
                    if(!found){
                        this.queryParams.filters.push({ 
                            type:"simple", 
                            name:"project.name", 
                            text:this.search.project 
                            })
                    }
                }
                else if(target === 'ticket'){
                    this.queryParams.filters.forEach( item => {
                        if(item.type === 'simple' && item.name === 'ticket.name'){
                            if(this.search.ticket === null || this.search.ticket === "" ){
                                this.queryParams.filters = this.queryParams.filters.filter(items => items.name !== "ticket.name")
                                found = true;
                            }
                            else{
                                item.text = this.search.ticket
                                found = true;
                            }
                        }
                    })
                    if(!found){
                        this.queryParams.filters.push({ 
                            type:"simple", 
                            name:"ticket.name", 
                            text:this.search.ticket 
                            })
                    }
                }
                else if(target === 'deadline'){
                    this.queryParams.filters.forEach( item => {
                        if(item.type === 'simple' && item.name === 'deadline'){
                            if(this.search.deadline === null || this.search.deadline === "" ){
                                this.queryParams.filters = this.queryParams.filters.filter(items => items.name !== "deadline")
                                found = true;
                            }
                            else{
                                item.text = this.search.deadline
                                found = true;
                            }
                        }
                    })
                    if(!found){
                        this.queryParams.filters.push({ 
                            type:"simple", 
                            name:"deadline", 
                            text:this.search.deadline 
                            })
                    }
                }
                else if(target === 'assigned'){
                    this.queryParams.filters.forEach( item => {
                        if(item.type === 'simple' && item.name === 'assigned.name'){
                            if (item.type === "simple" && item.name === "assigned_to") {
                                if(this.search.assigned_to === null || this.search.assigned_to === "" ){
                                this.queryParams.filters = this.queryParams.filters.filter(items => items.name !== "assigned_to")
                                found = true;
                                }
                                else{
                                    item.text = this.search.assigned_to;
                                    found = true;
                                }
                            }
                        }
                    })
                    if(!found){
                        this.queryParams.filters.push({ 
                            type:"simple", 
                            name:"assigned.name", 
                            text:this.search.assigned 
                            })
                    }
                }
            }
            else{
                if(target === 'number'){
                     this.queryParams.filters.push({ type:"simple", name:"number", text:this.search.number })
                }
                else if(target === 'name'){
                    this.queryParams.filters.push({ type:"simple", name:"name", text:this.search.name })
                }
                else if(target === 'owner'){
                    this.queryParams.filters.push({ type:"simple", name:"owner.name", text:this.search.owner })
                }
                else if(target === 'project'){
                    this.queryParams.filters.push({ type:"simple", name:"project.name", text:this.search.project })
                }else if(target === 'ticket'){
                    this.queryParams.filters.push({ type:"simple", name:"ticket.name", text:this.search.ticket })
                }else if(target ==='deadline'){
                    this.queryParams.filters.push({ type:"simple", name:"deadline", text:this.search.deadline })
                }else if(target ==='assigned'){
                    this.queryParams.filters.push({ type:"simple", name:"assigned.name", text:this.search.assigned })
                }
            }
            this.getTasks();
            found = false;
            typeSimple = false;
        },
        //sorting
        sortTasks(sortType) {
            this.queryParams.page = 1
            this.queryParams.sort = [];
            if(this.sortOrder)
            {
                this.queryParams.sort.push({
                    "name": sortType,
                    "order": "asc"
                });
            }
            else{
                this.queryParams.sort.push({
                    "name": sortType,
                    "order": "desc"
                });
            }
            this.sortOrder = !this.sortOrder
            this.getTasks();
        },
        //getters
        getTasks() {
            this.$Progress.start();
            if (this.queryParams.global_search != "") {
                $cookies.set(
                    "task_search",
                    this.queryParams.global_search,
                    "90d",
                    "/",
                    process.env.VUE_APP_COOKIES_DOMAIN
                );
            } else {
                if ($cookies.isKey("task_search")) {
                    $cookies.remove(
                        "task_search",
                        "/",
                        process.env.VUE_APP_COOKIES_DOMAIN
                    );
                }
            }
            this.$store
                .dispatch("task/getTasks", {
                    queryParams: this.queryParams,
                    page: this.queryParams.page,
                    index: true,
                    dropdown:true
                })
                .then(response => {
                    this.total_rows = response.data.data.total;
                    this.myTasks = response.data.data;
                    this.$Progress.finish();
                    this.resultResponsilbes();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },
        newModel() {
          this.editMode = false;
          this.form.reset();
          this.form.clear();
          if (this.singlePage) {
            this.form.fill(this.ticket);
            this.form.ticket_id = this.ticket.id;
            if(this.ticket.project) {
              this.form.project_id = this.ticket.project.id;
            }
            this.isDisabled = true;
          }
          $("#newTask").modal("show");
          this.form.priority = "normal";
          this.form.deadline = moment()
            .add(1, "day")
            .format("YYYY-MM-DD HH:mm:ss");
            //this.getAllProjects();
            this.getOwners();
        },
        editModel(task) {
          this.editMode = true;
          this.form.reset();
          this.form.clear();
          if (this.singlePage) {
            if(this.ticket.project) {
              this.form.project_id = this.ticket.project.id;
            }
            this.form.ticket_id = this.ticket.id;
            this.isDisabled = true;
          }
          $("#newTask").modal("show");
          this.form.fill(task);
          if(task.ticket.owner != null || task.ticket.owner != '') {
            this.form.owner = task.ticket.owner
          }
          if(task.project.owner != null || task.project.owner != '') {
            this.form.owner = task.project.owner
          }
          this.getProjects(this.form.owner.id);
        },
        getStatus() {
          this.$store
            .dispatch("task/getStatus")
            .then(response => {})
            .catch(error => {
              console.log(error);
            });
        },
        getOwners() {
          this.$store
            .dispatch("owner/getOwners")
            .then(response => {})
            .catch(error => {
              console.log(error);
            });
        },
        getProjects(owner_id) {
          this.form.project = [];
          this.$store
            .dispatch("project/getProjectsByOwner", owner_id)
            .then()
            .catch(error => {
              console.log(error);
            });
        },
        getResponsibles() {
          this.$store
            .dispatch("regularUser/getRegularUser",{dropdown:true})
            .then()
            .catch(error => {
              console.log(error);
            });
        },
        createTask(data) {
          this.isLoading = true;
          this.$Progress.start();
          this.$store
            .dispatch("task/createTask", data)
            .then(response => {
              $("#newTask").modal("hide");
              this.$Progress.finish();
              this.isLoading = false;

              Toast.fire({
                type: "success",
                title: response.data.message
              });
            })
            .catch(error => {
              this.$Progress.fail();
              if (error.response) {
                this.isLoading = false;
                this.form.errors.errors = error.response.data.errors;
              }

            });
        },
        editTask(data) {
          this.isLoading = true;
          this.$Progress.start();
          this.$store
            .dispatch("task/editTask", data)
            .then(response => {
               this.isLoading = false;
              $("#newTask").modal("hide");
              this.$Progress.finish();
              Toast.fire({
                type: "success",
                title: response.data.message
              });
            })
            .catch(error => {
              this.$Progress.fail();
              if (error.response) {
                this.isLoading = true;
                this.form.errors.errors = error.response.data.errors;
              }
            });
        },
        OpenStatus(task){
            this.form.reset();
            this.form.clear();
            if (this.singlePage) {
                if(this.ticket.project) {
                    this.form.project_id = this.ticket.project.id;
                }
                this.form.ticket_id = this.ticket.id;
                this.isDisabled = true;
            }
            this.form.fill(task);
            if(task.ticket.owner != null || task.ticket.owner != '') {
                this.form.owner = task.ticket.owner
            }
            if(task.project.owner != null || task.project.owner != '') {
                this.form.owner = task.project.owner
            }

            this.form.status.id = 1;
            this.form.status.name = 'open';
            this.form.status_id = 1;
            console.log("no error")
            this.$Progress.start();
            this.$store
                .dispatch("task/editTask", this.form)
                .then(response => {
                    this.$Progress.finish();
                    Toast.fire({
                        type: "success",
                        title: response.data.message
                    });
                })
                .catch(error => {
                    this.$Progress.fail();
                    if (error.response) {
                        this.form.errors.errors = error.response.data.errors;
                    }
                });
        },
        CloseStatus(task){
            this.form.reset();
            this.form.clear();
            if (this.singlePage) {
                if(this.ticket.project) {
                    this.form.project_id = this.ticket.project.id;
                }
                this.form.ticket_id = this.ticket.id;
                this.isDisabled = true;
            }
            this.form.fill(task);
            if(task.ticket.owner != null || task.ticket.owner != '') {
                this.form.owner = task.ticket.owner
            }
            if(task.project.owner != null || task.project.owner != '') {
                this.form.owner = task.project.owner
            }

            this.form.status.id = 4;
            this.form.status.name = 'done';
            this.form.status_id = 4;

            this.$Progress.start();
            this.$store
                .dispatch("task/editTask", this.form)
                .then(response => {
                    this.$Progress.finish();
                    Toast.fire({
                        type: "success",
                        title: response.data.message
                    });
                })
                .catch(error => {
                    this.$Progress.fail();
                    if (error.response) {
                        this.form.errors.errors = error.response.data.errors;
                    }
                });
        },
        deleteTask(id) {
          Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
          }).then(result => {
            if (result.value) {
              this.$Progress.start();
              this.$store
                .dispatch("task/deleteTask", id)
                .then(response => {
                  this.$Progress.finish();
                  Toast.fire({
                    type: "success",
                    title: response.data.message
                  });
                })
                .catch(error => {
                  this.$Progress.fail();
                  console.log(error);
                  Toast.fire({
                    type: "error",
                    title: error.response.data.message
                  });
                });
            }
          });
        },
        getEmployees() {
            this.$store
                .dispatch("user/getRegularUsers",{rules:false,dropdown:true})
                .then(() => {})
                .catch(() => {});
        },
        getClientsPerProject(id){
            this.$store
                .dispatch("owner/getClientsPerProject",id)
                .then(() => {})
                .catch(() => {});
        },
        getAllProjects(project){
            this.$store
                .dispatch("project/getAllProjects",{dropdown:true,project})
                .then(() => {})
                .catch(() => {});
        }
    },
    filters:{
        subStr: (value) => {
            if(value && value.length > 75) {
                return value.substring(0, 75) + '...';
            }
            return value;
        },
        subStrPrTi: (value) => {
            if(value && value.length > 8) {
                return value.substring(0, 8) + '...';
            }
            return value;
        },
        subStrCardTitle: (value) => {
            if(value && value.length > 13) {
                return value.substring(0, 13) + '...';
            }
            return value;
        },
        taskDescription: value => {
            if (value.length > 130) {
                return value.substring(0, 130) + "...";
            }
            return value;
        },
        tasksDateNow: (value) => {
            return moment(value).fromNow()
        },
    },
    components: {
        VueBootstrap4Table,
        quillEditor,
        DatePicker,
        pagination
    },
    global_search() {
        return this.queryParams.global_search;
    },
    watch: {
        global_search: _.debounce(function() {
            this.isTyping = false;
        }, 1000),
        isTyping: function(value) {
            if (!value) {
                this.getProjects(this.queryParams);
            }
        },
        selectedTicketId(newValue) {
          this.form.ticket_id = parseInt(newValue);
        },
        selectedProjectId(newValue) {
          this.form.project_id = parseInt(newValue);
        },

        projectSync (val) {
            if(!val) return false;
            if (this.timer) {
                clearTimeout(this.timer);
                this.timer = null;
            }
            this.timer = setTimeout(() => {
                 this.getAllProjects(val);
        }, 800);

    },

    },
    created() {
        if ($cookies.isKey("task_search")) {
            this.queryParams.global_search = $cookies.get("task_search");
            this.config.global_search.init.value = $cookies.get("task_search");
        }
    },
    mounted() {
        this.getTasks(this.$route.params.page || 1);
        this.getStatus();
        this.getOwners();
        this.getResponsibles();
        // this.getEmployees();
        //this.getAllProjects();
    },
    beforeRouteUpdate(to, from, next) {
        this.getTasks(to.params.page);
        next();
    },

    computed: {
        ...mapGetters({
            tasks: "task/activeTasks",
            status: "task/activeStatus",
            owners: "owner/activeOwners",
            projects: "project/projectByOwners",
            responsible: "regularUser/activeRegularUser",
            tickets: "ticket/activeTickets",
            employees: "user/getRegularUsers",
        }),
        resultTasks() {
            return this.tasks.data;
        },
        selectedProjectId() {
            return this.form.project ? this.form.project.id : null;
        },
        selectedTicketId() {
            return this.form.ticket ? this.form.ticket.id : null;
        },
    }
};
</script>

<style scoped>

/*sort btn styling*/

.sort-btn-row sort-btn{
    height: 3rem
}
.sort-btn-row span{
    font-size: 1.2rem;
}

/* end sort btn styling*/


/*task card styling*/

.task-card .card-cont{
    margin-top:1rem;
    margin-bottom:1rem
}

.task-card .f-task-col{
    display:flex;
    flex-direction: column;
}

.task-card .task-status{
    width:70%;
    text-align: center;
    color: #ffffff;
    font-weight: bold;

}

.task-card .task-created_by{
    margin-top: .4rem;
}

.task-card .task-created_by .task-created_by-col{
    display: flex;
    flex-direction: column;
}

.task-created_by-col small{
    color: #484848;
    max-width:5rem;
    padding-top:.7rem;
    padding-left: .3rem;
}

.sort-item{
    background-color: #ffffff;
    color: #000000;
    cursor: pointer;
    transition: ease .4s;
}
.sort-item:hover{
    background-color: #e8e8e8;
}


#reset-btn{
    font-size: .7rem;
    background-color: #A8557A;
    color: #ffffff;
    margin-top: 1rem;
    border-radius:10px;
}
/*end task card styling*/


/*actions-btn-styling*/
.actions-btn-cont{
    height: 50%;
    border-radius: 15px;
    border:1px solid #b8b8b8;
    background-color: #ffffff;
    padding:0 1.2rem;
    margin-top:45%;
    transform: translateY(45%);
}

.actions-btn-cont .action{
    height: 50%;
    width: 100%;
}

.action .actionIcon i{
    margin-top: 25%;
    transform: translateY(50%);
    margin-left: .3rem;
}

.line{
    background-color: #b8b8b8;
    height: .06rem;
    width: 80%;
    color:#919191;
    position: absolute;
    margin-right:50%;
    transform:translateX(-50%);

}

/*end actions-btn-styling*/



/*colors*/
#open{
    background-color: #A0466F;
}
#done{
    background-color: #67A046;
}
#in-progress{
    background-color: #3490dc;
}
#pending{
    background-color: #EEA24C;
}
/*end colors*/



.col {
    padding-top: 0;
    padding-bottom: 0;
}

/*new style*/

.description{
    text-transform: capitalize;
}


.status_container{
    width: 9rem;
    height: 1.9rem;
    border-bottom-right-radius: 50%;
    border-top-left-radius: 50%;
    text-align: center;
}

.stat-name {
    font-weight: bold;
    font-size: .9rem;
    color: #ffffff;
    transform: translateY(2%);
    text-transform: capitalize;
}
.important-actions{
    font-size: 1rem;
    cursor: pointer;
}

/*new style*/


.filter-container{
    border-radius: 2rem;
    margin-bottom: 0.8rem;
    background-color: #ffffff;
    margin-top: 1rem;
}

.filter-header{
    display: flex;
    background:linear-gradient(180deg, #AEACAC 0%, #979797 0%, #C8C8C8 0%, rgba(151, 151, 151, 0.92) 100%);
    border-radius: 15px;
}
.act-filters-container{
    background-color:#DCDCDC;
    border-radius: 15px;
}

.filter-text{
    margin-left: .2rem;
}
.single-filter{
    display: flex;
}
.filter-check{
    margin-left: -.8rem;
}

.changeStatus{
    font-size: 1.3rem;
    font-weight: bold;
    cursor: pointer;
    transition: ease .4s;
}
.changeStatus:hover{
    opacity: 80%;
}
.changeStatusTable{
    font-weight: bold;
    cursor: pointer;
    transition: ease .4s;
    font-size:1rem;
    margin-left:.5rem;
    margin-top:.1rem;
}

/*end of task new styles*/

</style>
