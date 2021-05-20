<template>
    <span>
        <v-container fluid>
            <v-row class="mb-2">
                  <v-col cols="10">
                      <v-row class="mb-2">
                            <v-col cols="4">
                              <v-text-field
                                      append-icon="fas fa-search"
                                      hide-details
                                      solo
                                      clear-icon="fas fa-clear"
                                      clearable
                                      :label="$t('Search.general')"
                                      type="text"
                                      style="border-radius: 10px;"
                                      v-model="queryParams.global_search"
                                      v-on:keyup.enter="getProjects"
                              ></v-text-field>
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
                                      style="border-radius: 10px;"
                                      v-model="search.number"
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
                                      style="border-radius: 10px;"
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
                                      style="border-radius: 10px;"
                                      v-model="search.owner"
                                      @change="multiSearch('owner')"
                              ></v-text-field>
                          </v-col>
                          <v-col cols="4">
<!--                              <v-text-field-->
                              <!--                                  append-icon="fas fa-search"-->
                              <!--                                  hide-details-->
                              <!--                                  solo-->
                              <!--                                  clear-icon="fas fa-clear"-->
                              <!--                                  clearable-->
                              <!--                                  :label="$t('Search.assignedUsers')"-->
                              <!--                                  type="text"-->
                              <!--                                  style="border-radius: 10px;"-->
                              <!--                                  v-model="search.assigned"-->
                              <!--                                  @change="multiSearch('assigned')"-->
                              <!--                              ></v-text-field>-->

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
                                      append-icon="fas fa-calendar-alt"
                                      hide-details
                                      solo
                                      clear-icon="fas fa-clear"
                                      clearable
                                      :label="$t('Search.createdAt')"
                                      type="date"
                                      v-model="search.created_at"
                                      @change="multiSearch('created_at')"
                                      style="background-color: white;border-radius: 10px;"
                              ></v-text-field>
                          </v-col>
                      </v-row>
                  </v-col>
                  <v-col cols="2" style="display: flex;flex-direction: column">
                      <v-card
                              class="dropdown dropdown-toggle sort-btn"
                              data-toggle="dropdown"
                              style="border-radius: 10px"
                      >
                          <span style="font-size: 1.1rem" class="btn">{{$t('Project.sortProjects')}}</span>
                          <div class="dropdown-menu">
                              <span class="dropdown-item sort-item"
                                    @click="sortProjects('id')">{{$t('Project.Number')}}</span>
                              <span class="dropdown-item sort-item"
                                    @click="sortProjects('name')">{{$t('Project.name')}}</span>
                              <span class="dropdown-item sort-item" @click="sortProjects('created_at')">{{$t('Project.createdAt')}}</span>
                          </div>
                      </v-card>
                      <v-btn bottom id="reset-btn" block @click="clearQuery">{{$t('Project.resetQuery')}}</v-btn>
                  </v-col>
              </v-row>
            <v-row>
                <v-col cols="10" class="projects-container">
                <v-row style="margin-top: 1rem">
                <v-col cols="4" v-for="project in projects.data" :key="project.id">
                    <v-card style="border-radius: 2rem; margin-bottom: 0.8rem;">
                        <v-container fluid>
                            <v-row>
                                <v-col cols="1">
                                    <div class="id_circle">
                                       <span class="id_number">{{ project.id }} &nbsp;</span>
                                    </div>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="8">
                                     <router-link
                                             :to="'/admin/project/' + project.id"
                                             class="project-name"
                                             data-toggle="tooltip" data-placement="top" :title="project.name">
                                         {{ project.name | subStr}}
                                     </router-link>
                                </v-col>
                                <v-col cols="2" style="margin-left: -2rem">
                                     <div :id="project.status.name" class="status_container">
                                         <span class="stat-name">{{project.status.name | returnDoing}}</span>
                                    </div>
                                </v-col>
                                <v-col cols="2" style="margin-left: 2rem">
                                    <i v-if="project.fav" @click.prevent="removeFav(project.id)" class="fas fa-star fav"
                                       data-toggle="tooltip" data-placement="top" title="Remove From Favorite"></i>
                                    <i v-else @click.prevent="addToFav(project.id)" class="far fa-star fav"
                                       data-toggle="tooltip" data-placement="top" title="Add To Favorite"></i>
                                </v-col>
                            </v-row>
                            <v-row v-if="project.description">
                                <v-col cols="12" style="margin-top: .3rem">
                                   <p v-html="$options.filters.projectDescription(project.description)"
                                      class="description">
                                   </p>
                                </v-col>
                            </v-row>
                            <v-row style="margin-top: -1rem">
                                <v-col cols="8">
                                    <small style="font-weight: bold;font-size: .9rem">{{$t('Project.createdAt')}}:</small>
                                     <small class="text-muted">{{project.created_at| projectDueDate}}</small>
                                </v-col>
                                 <v-col cols="4">
                                     <small style="font-weight: bold;font-size: .9rem">{{$t('Project.budget')}}:</small>
                                    <small class="text-muted">{{project.budget}}</small>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="8">
                                    <small style="font-weight: bold;font-size: .9rem">{{$t('Project.endDate')}}:</small>
                                    <small class="text-muted">{{project.due_date |  projectDueDate}}</small>
                                </v-col>
                                 <v-col cols="4">
                                     <small style="font-weight: bold;font-size: .9rem">EST:</small>
                                    <small class="text-muted">{{project.estimated_time}}</small>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="5" v-if="project.owners[0]" style="display: inline-flex;">
                                    <span style="font-weight: bold;font-size: .9rem">{{$t('Project.clients')}}:</span>
                                     <avatar
                                             data-toggle="tooltip" data-placement="top" :title="project.owners[0].name"
                                             color="#90b0fa"
                                             :fullname="project.owners[0].name"
                                             :size="20"
                                             style="margin-left: .3rem"
                                     ></avatar>
                                        <v-btn icon @click="showAssignedOwners(project.id)"
                                               style="width: 1.5rem;height: 1.5rem;">
                                            <i style="font-size: .6rem" class="fa fa-ellipsis-v"></i>
                                        </v-btn>
                                        <div v-if="showOwners && projectID ==project.id" class="toggle-menu-card">
                                            <div class="content" style="padding: 0 .4rem">
                                                <ul style="list-style: none;text-align: center; margin-top: 0.5rem;"
                                                    class="toggle-div" v-for="(owner,index) in project.owners"
                                                    :key="index">
                                                    <li style="display: inline-flex; width: max-content;">
                                                        <avatar color="#90b0fa" :fullname="owner.name"
                                                                :size="30"></avatar>
                                                        <router-link :to="'/admin/profile/' + owner.id"
                                                                     style="margin-left: 0.5rem">
                                                            {{owner.name| subStr}}
                                                        </router-link>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                </v-col>
                                <v-col cols="7" v-if="project.project_assign[0]" style="display: inline-flex;">
                                     <span style="font-weight: bold;font-size: .9rem">{{$t('Project.employees')}}:</span>
                                     <avatar
                                             data-placement="top" :title="project.project_assign[0].name"
                                             color="#90b0fa"
                                             :fullname="project.project_assign[0].name"
                                             :size="20"
                                             style="margin-left: .3rem"
                                     ></avatar>
                                    <avatar
                                            v-if="project.project_assign[1]"
                                            data-placement="top" :title="project.project_assign[1].name"
                                            color="#90b0fa"
                                            :fullname="project.project_assign[1].name"
                                            :size="20"
                                            style="margin-left: .3rem"
                                    ></avatar>
                                        <v-btn icon @click="showAssigned(project.id)"
                                               style="width: 1.5rem;height: 1.5rem;">
                                            <i style="font-size: .6rem" class="fa fa-ellipsis-v"></i>
                                        </v-btn>
                                        <div v-if="showAssignedUsers && projectID ==project.id"
                                             class="toggle-menu-card">
                                            <div class="content" style="padding: 0 .4rem">
                                                <ul style="list-style: none;text-align: center; margin-top: 0.5rem;"
                                                    class="toggle-div" v-for="user in project.project_assign"
                                                    :key="user.id">
                                                    <li style="display: inline-flex; width: max-content;">
                                                        <avatar color="#90b0fa" :fullname="user.name"
                                                                :size="30"></avatar>
                                                        <router-link :to="'/admin/profile/' + user.id"
                                                                     style="margin-left: 0.5rem">
                                                            {{user.name| subStr}}
                                                        </router-link>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="9">
                                    <router-link :to="{name:'project.discussion',params: {id: project.id}}">
                                        <i class="fas fa-comment-alt action m-1" data-toggle="tooltip"
                                           data-placement="top" title="Discussions"></i>
                                    </router-link>
                                     <router-link :to="{name: 'project.copy',params: {id: project.id}}">
                                        <i class="fas fa-copy action m-1" data-toggle="tooltip" data-placement="top"
                                           title="Copy Project"></i>
                                     </router-link>
                                     <router-link :to="{name:'project.calender',params: {id: project.id}}">
                                        <i class="fas fa-calendar-alt action m-1" data-toggle="tooltip"
                                           data-placement="top" title="Calender"></i>
                                     </router-link>
                                    <router-link :to="{name:'project.folder',params: {id: project.id}}">
                                        <i class="fas fa-folder-open action m-1" data-toggle="tooltip"
                                           data-placement="top" title="Folder"></i>
                                     </router-link>
                                     <a href="javascript:;" @click="mergeProjects(project)">
                                        <i class="fas fa-object-group action m-1" data-toggle="tooltip"
                                           data-placement="top" title="Merge Projects"></i>
                                     </a>
                                </v-col>
                                <v-col cols="3">
                                    <i v-if="project.editable ||project.deletable" @click.prevent="editModal(project)"
                                       style="color:#81B488" class="fa fa-edit important-actions"></i>
                                    <i @click.prevent="deleteProject(project.id)" style="color:#C64848"
                                       class="fa fa-trash ml-1 important-actions"></i>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card>
                </v-col>
            </v-row>
        </v-col>
        <v-col cols="2">
            <v-row>
                <v-col cols="12" style="display:flex;margin-left: -1rem;margin-top:1rem">
                    <div class="actions-container">
                        <i style="color : #ffffff" class="fas fa-calendar-alt"></i>
                    </div>
                    <router-link class="calender" to="/admin/calendar">{{ today_date }}</router-link>
                </v-col>
                <v-col cols="12">
                    <v-text-field
                            append-icon="fas fa-search"
                            v-model="queryParams.global_search"
                            hide-details
                            solo
                            clear-icon="fas fa-clear"
                            clearable
                            :placeholder="$t('Search.search')"
                            type="text"
                            style="border-radius: 10px;margin-top:1rem"
                            @click:append="getProjects"
                            v-on:keyup.enter="getProjects"
                    ></v-text-field>
                </v-col>
            </v-row>
                <v-col cols="12" class="filter-container">
                    <v-container fluid>
                    <v-row class="filter-header">
                        <v-col cols="9" style="display: flex">
                            <i class="fas fa-filter" style="font-size: 1rem;margin-top: .3rem"></i>
                            <strong>Filters</strong>
                        </v-col>
                        <v-col cols="3">
                            <strong v-if="projects.total">{{projects.total}}</strong>
                            <strong v-else>0</strong>
                        </v-col>
                    </v-row>
                    <v-row style="margin-top:.4rem;">
                        <v-col cols="12" class="act-filters-container">
                            <v-container fluid>
                                <v-row>
                                    <v-col cols="6">
                                        <button @click="filterProject('all')" style="border: none"><small
                                                class="filter-text">{{$t('Filter.all')}}</small></button>
                                    </v-col>
                                    <v-col cols="6">
                                        <button @click="filterProject('favorites')" style="border: none"><small
                                                style="margin-left: -.4rem" class="filter-text">{{$t('Filter.favorites')}}</small></button>
                                    </v-col>
                                </v-row>
                                <hr>
                                <v-row>
                                    <strong style="margin-top:-.5rem">Status</strong>
                                </v-row>
                                <v-row>
                                    <v-col cols="6" class="single-filter">
                                        <!-- <input class="filter-check"  type="checkbox"  @click="filterProject('open')"><small class="filter-text">{{$t('Filter.open')}}</small> -->
                                        <button @click="filterProject('open')" style="border: none"><small
                                                class="filter-text">{{$t('Filter.open')}}</small></button>
                                    </v-col>
                                    <v-col cols="6" class="single-filter" style="margin-left: -.5rem">
                                        <!-- <input class="filter-check"  type="checkbox" @click="filterProject('pending')" ><small class="filter-text">{{$t('Filter.pending')}}</small> -->
                                        <button @click="filterProject('pending')" style="border: none"><small
                                                class="filter-text">{{$t('Filter.pending')}}</small></button>
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col cols="6" class="single-filter">
                                        <!-- <input class="filter-check"  type="checkbox"  @click="filterProject('in-progress')"><small class="filter-text" >{{$t('Filter.doing')}}</small> -->
                                        <button @click="filterProject('in-progress')" style="border: none"><small
                                                class="filter-text">{{$t('Filter.doing')}}</small></button>
                                    </v-col>
                                    <v-col cols="6" class="single-filter">
                                        <!-- <input class="filter-check"  type="checkbox" @click="filterProject('done')"><small class="filter-text" >{{$t('Filter.done')}}</small> -->
                                        <button @click="filterProject('done')" style="border: none"><small
                                                class="filter-text">{{$t('Filter.done')}}</small></button>
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
                                @click="newModal"
                        >
                            <v-icon>fas fa-plus</v-icon>
                        </v-btn>
                </v-col>

        </v-col>
            </v-row>
            <v-col col="12">
                <pagination
                        :data="myProjects"
                        :limit="parseInt(2)"
                        size="small"
                        @pagination-change-page="getResults"
                >
                    <span slot="prev-nav">&lt; Previous</span>
                    <span slot="next-nav">Next &gt;</span>
                </pagination>
            </v-col>
        </v-container>

        <!-- Modal -->
        <div
                class="modal fade"
                id="Modal"
                tabindex="-1"
                role="dialog"
                aria-labelledby="newModalLabel"
                aria-hidden="true"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5
                                v-show="!editMode"
                                class="modal-title"
                                id="newModalLabel"
                        >
                            {{ $t("Project.createProject") }}
                        </h5>
                        <h5
                                v-show="editMode"
                                class="modal-title"
                                id="newModalLabel"
                        >
                            {{ $t("Project.editProject") }}
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
                            editMode ? editProject(form.id) : createProject()
                        "
                            @keydown="form.onKeydown($event)"
                    >
                        <div class="modal-body">
                            <div class="form-group">

                                <label for="name">{{
                                    $t("Project.name")
                                }}
                                <span class="asterisk_input"></span>
                                </label>

                                
                                <input
                                        required
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
                                    $t("Project.desc")
                                }}
                                <span class="asterisk_input"></span>
                                </label>

                                <textarea
                                        required
                                        v-model="form.description"
                                        type="text"
                                        name="description"
                                        class="form-control"
                                        :class="{
                                        'is-invalid': form.errors.has(
                                            'description'
                                        )
                                    }"
                                ></textarea>
                                

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
                                        :placeholder="$t('Task.selectOne')"
                                        label="name"
                                        track-by="id"
                                ></multiselect>
                                <has-error
                                        :form="form"
                                        field="status_id"
                                ></has-error>
                            </div>
                            <div
                                    class="form-group"
                                    v-if="user.type != 'client'"
                            >
                                <label for="client">{{
                                    $t("Project.client")
                                }}
                                <span class="asterisk_input"></span>
                                </label>
                                <multiselect
                                        required
                                        v-model="form.owners"
                                        :multiple="true"
                                        :options="owners"
                                        :searchable="true"
                                        :close-on-select="false"
                                        :clear-on-select="false"
                                        :preserve-search="true"
                                        :placeholder="$t('Project.selectOne')"
                                        label="name"
                                        track-by="name"
                                        :preselect-first="true"
                                        @input="opt => (form.owner_id = opt.id)"
                                ></multiselect>
                                <has-error
                                        :form="form"
                                        field="owner"
                                ></has-error>
                            </div>
                            <div class="form-group">
                                <label for="name">{{
                                    $t("Project.assignedUsers")
                                }}</label>
                                <multiselect
                                        v-model="form.project_assigns"
                                        :options="responsible"
                                        :multiple="true"
                                        :close-on-select="false"
                                        :clear-on-select="false"
                                        :preserve-search="true"
                                        :placeholder="$t('Project.pickSome')"
                                        label="name"
                                        track-by="id"
                                        :preselect-first="true"
                                        @input="
                                        opt => (form.responsible_id = opt.id)
                                    "
                                >
                                    <template
                                            slot="selection"
                                            slot-scope="{ values, isOpen }"
                                    >
                                        <span
                                                class="multiselect__single"
                                                v-if="values.length &amp;&amp; !isOpen"
                                        >{{ values.length }}
                                            {{ $t("Project.options") }}</span
                                        >
                                    </template>
                                </multiselect>
                                <has-error
                                        :form="form"
                                        field="project_assign"
                                ></has-error>
                            </div>
                                 <div class="form-group">
                                <label for="estimated_time">{{
                                    $t("Project.estimatedTime")
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
                            <div class="form-group">
                                <label for="budget">{{
                                    $t("Project.budget")
                                }}</label>
                                <input
                                        v-model="form.budget"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        name="budget"
                                        class="form-control"
                                        :class="{
                            'is-invalid': form.errors.has(
                                'budget'
                            )
                        }"
                                />
                                <has-error
                                        :form="form"
                                        field="budget"
                                ></has-error>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <div class="mr-auto w-20 pl-2">All fields with <span style="color:red"> * </span> are mandatory</div>
<!--                            <button type="submit" class="btn btn-primary">-->
                            <!--                                {{ $t("Project.save") }}-->
                            <!--                            </button>-->
                            <v-btn type="submit" color="#ffffff" style="background-color:#234FA3 " text> {{ $t("Project.save") }}</v-btn>

                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Merge Modal -->
        <div
                v-if="project"
                class="modal fade"
                id="MergeModal"
                tabindex="-1"
                role="dialog"
                aria-labelledby="exampleModalLabel"
                aria-hidden="true"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                           {{$t('Project.mergeProject')}} {{ project.name }}
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
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="exampleFormControlSelect2"
                            >{{$t('Project.client')}}</label
                            >
                            <multiselect
                                    v-model="mergeClient"
                                    :options="owners"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :preserve-search="true"
                                    placeholder="select client"
                                    label="name"
                                    track-by="id"
                                    :preselect-first="true"
                                    @input="getClientProjects"
                            >
                            </multiselect>
                        </div>
                        <div
                                class="form-group"
                                v-if="clientProjects && clientProjects.length > 0"
                        >
                            <label for="exampleFormControlSelect2"
                            >{{$t('Project.intoProject')}}</label
                            >
                            <select
                                    v-model="intoProject"
                                    class="form-control"
                                    id="exampleFormControlSelect2"
                            >
                                <option
                                        v-for="(otherproject,
                                    index) in clientProjects.filter(
                                        item => item.id != project.id
                                    )"
                                        :key="index"
                                        :value="otherproject.id"
                                >{{ otherproject.name }}</option
                                >
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        
<!--                        <button-->
                        <!--                            type="button"-->
                        <!--                            class="btn btn-secondary"-->
                        <!--                            data-dismiss="modal"-->
                        <!--                        >-->
                        <!--                            Close-->
                        <!--                        </button>-->
                        <v-btn color="blue darken-1" text>{{ $t("Project.close") }}</v-btn>
                        <!--                        <button-->
                        <!--                            type="button"-->
                        <!--                            class="btn btn-primary"-->
                        <!--                            @click="doMergeProjects(project.id)"-->
                        <!--                        >-->
                        <!--                            Merge-->
                        <!--                        </button>-->
                        <v-btn type="button" @click="doMergeProjects(project.id)" color="#ffffff"
                               style="background-color:#234FA3 " text> {{ $t("Project.merge") }}</v-btn>

                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import {mapGetters, mapState, mapActions} from "vuex";
    import _ from "lodash";
    import pagination from "laravel-vue-pagination";
    import moment from "moment";

    export default {
        data() {
            return {
                showOwners: false,
                tab: "filter",
                showAssignedUsers: false,
                projectID: "",
                intoProject: "",
                mergeClient: null,
                clientProjects: [],
                myProjects: {},
                allProjects: {},
                responsilbeArr: [],
                sortOrder: true,
                editMode: false,
                search: {
                    name: "",
                    number: "",
                    owner: "",
                    assigned: '',
                    created_at: ''
                },
                queryParams: {
                    filters: [],
                    sort: [],
                    global_search: "",
                    per_page: 15,
                    page: 1
                },
                isTyping: false,
                isLoading: false,
                form: new Form({
                    id: "",
                    name: "",
                    owner: [],
                    owners: [],
                    description: "",
                    task_rate: "",
                    budget_hours: "",
                    project_assign: [],
                    project_assigns: [],
                    estimated_time: "",
                    budget: "",
                    status: {
                        id: 1,
                        name: "open"
                    }
                }),
                selected: null,
                project: null
            };
        },
        methods: {
            resultResponsilbes() {
                this.responsilbeArr = []
                this.responsilbeArr.push('All')
                this.responsilbeArr.push('Not Assigned')
                this.responsible.forEach(item => {
                    this.responsilbeArr.push(item.name)
                });
            },
            addToFav(project_id) {
                this.$Progress.start();
                this.$store
                    .dispatch("project/addFavorites", {
                        project_id: project_id,
                        single: false
                    })
                    .then(response => {
                        this.$Progress.finish();
                        Toast.fire({
                            type: "success",
                            title: "project added to favorites!"
                        });
                    })
                    .catch(error => {
                        this.$Progress.fail();
                    });
            },
            removeFav(project_id) {
                this.$Progress.start();
                this.$store
                    .dispatch("project/removeFavorites", {
                        project_id: project_id,
                        single: false
                    })
                    .then(response => {
                        this.$Progress.finish();
                        Toast.fire({
                            type: "success",
                            title: "project removed from favorites!"
                        });
                    })
                    .catch(error => {
                        this.$Progress.fail();
                    });
            },
            mergeProjects(project) {
                this.project = project;
                $("#MergeModal").modal("show");
            },
            doMergeProjects($project_id) {
                this.$store
                    .dispatch("project/mergeProjects", {
                        project: $project_id,
                        into_project: this.intoProject
                    })
                    .then(response => {
                        this.getProjects();
                        $("#MergeModal").modal("hide");
                        this.$Progress.finish();
                    })
                    .catch(error => {
                        this.$Progress.fail();
                    });
            },
            onPaginate(page) {
                this.$router.push({
                    name: "projects.list",
                    params: {page}
                });
            },
            multiSearch(target) {
                let typeSimple = false;
                this.queryParams.filters.forEach(item => {
                    if (item.type === "simple") {
                        typeSimple = true;
                    }
                });
                let found = false;
                if (typeSimple) {
                    if (target == "number") {
                        this.queryParams.filters.forEach(item => {
                            if (item.type === "simple" && item.name === "number") {
                                if (this.search.number === null || this.search.number === "") {
                                    this.queryParams.filters = this.queryParams.filters.filter(items => items.name !== "number")
                                    found = true;
                                } else {
                                    item.text = this.search.number;
                                    found = true;
                                }
                            }
                        });
                        if (!found) {
                            this.queryParams.filters.push({
                                type: "simple",
                                name: "number",
                                text: this.search.number
                            });
                        }
                    } else if (target == "name") {
                        this.queryParams.filters.forEach(item => {
                            if (item.type === "simple" && item.name === "name") {
                                if (this.search.name === null || this.search.name === '') {
                                    this.queryParams.filters = this.queryParams.filters.filter(items => items.name !== "name")
                                    found = true;
                                } else {
                                    item.text = this.search.name;
                                    found = true;
                                }
                            }
                        });
                        if (!found) {
                            this.queryParams.filters.push({
                                type: "simple",
                                name: "name",
                                text: this.search.name
                            });
                        }
                    } else if (target == "owner") {
                        this.queryParams.filters.forEach(item => {
                            if (item.type === "simple" && item.name === "owner.name") {
                                if (this.search.owner === null || this.search.owner === "") {
                                    this.queryParams.filters = this.queryParams.filters.filter(items => items.name !== "owner.name")
                                    found = true;
                                } else {
                                    item.text = this.search.owner;
                                    found = true;
                                }
                            }
                        });
                        if (!found) {
                            this.queryParams.filters.push({
                                type: "simple",
                                name: "owner.name",
                                text: this.search.owner
                            });
                        }
                    } else if (target == "assigned") {
                        this.queryParams.filters.forEach(item => {
                            if (item.type === "simple" && item.name === "assigned.name") {
                                if (this.search.assigned === null || this.search.assigned === "") {
                                    this.queryParams.filters = this.queryParams.filters.filter(items => items.name !== "assigned")
                                    found = true;
                                } else {
                                    item.text = this.search.assigned;
                                    found = true;
                                }
                            }
                        });
                        if (!found) {
                            this.queryParams.filters.push({
                                type: "simple",
                                name: "owner.assigned",
                                text: this.search.assigned
                            });
                        }
                    } else if (target == "created_at") {
                        this.queryParams.filters.forEach(item => {
                            if (
                                item.type === "simple" &&
                                item.name === "created_at"
                            ) {
                                item.text = this.search.created_at;
                                found = true;
                            }
                        });
                        if (!found) {
                            this.queryParams.filters.push({
                                type: "simple",
                                name: "created_at",
                                text: this.search.created_at
                            });
                        }
                    }
                } else {
                    if (target == "number") {
                        this.queryParams.filters.push({
                            type: "simple",
                            name: "number",
                            text: this.search.number
                        });
                    } else if (target == "name") {
                        this.queryParams.filters.push({
                            type: "simple",
                            name: "name",
                            text: this.search.name
                        });
                    } else if (target == "owner") {
                        this.queryParams.filters.push({
                            type: "simple",
                            name: "owner.name",
                            text: this.search.owner
                        });
                    } else if (target == "assigned") {
                        this.queryParams.filters.push({
                            type: "simple",
                            name: "assigned.name",
                            text: this.search.assigned
                        });
                    } else if (target == "created_at") {
                        this.queryParams.filters.push({
                            type: "simple",
                            name: "created_at",
                            text: this.search.created_at
                        });
                    }
                }
                this.getProjects();
                found = false;
                typeSimple = false;
            },
            clearQuery() {
                this.queryParams = {
                    sort: [],
                    filters: [],
                    global_search: ""
                };
                this.search.name = "";
                this.search.number = "";
                this.search.owner = "";
                this.search.assigned = "";
                this.search.created_at = "";
                this.getProjects();
            },
            getProjects(queryParams) {
                this.$Progress.start();
                this.isLoading = true;
                if (this.queryParams.global_search != "") {
                    $cookies.set(
                        "project_search",
                        this.queryParams.global_search,
                        "90d",
                        "/",
                        process.env.VUE_APP_COOKIES_DOMAIN
                    );
                } else {
                    if ($cookies.isKey("project_search")) {
                        $cookies.remove(
                            "project_search",
                            "/",
                            process.env.VUE_APP_COOKIES_DOMAIN
                        );
                    }
                }
                this.$store
                    .dispatch("project/getProjects", this.queryParams)
                    .then(response => {
                        this.$Progress.finish();
                        this.myProjects = response.data.data;
                        this.isLoading = false;
                    })
                    .catch(error => {
                        this.$Progress.fail();
                        this.isLoading = false;
                    });
            },
            // getAllProjects(queryParams) {
            //   this.$Progress.start();
            //   this.$store
            //     .dispatch("project/getAllProjects", this.queryParams)
            //     .then(response => {
            //       this.$Progress.finish();
            //         this.allProjects = response.data.data;
            //       this.isLoading = false;
            //     })
            //     .catch(error => {
            //       this.$Progress.fail();
            //       this.isLoading = false;
            //     });
            // },
            getClientProjects() {
                this.clientProjects = [];
                let $this = this;
                this.$store
                    .dispatch("project/getProjectsByClient", this.mergeClient.id)
                    .then(function (response) {
                        $this.clientProjects = response.data.data;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            getOwners() {
                this.$Progress.start();
                this.$store
                    .dispatch("owner/getOwners")
                    .then(() => {
                        this.$Progress.finish();
                    })
                    .catch(error => {
                        this.$Progress.fail();
                    });
            },
            newModal() {
                this.editMode = false;
                this.form.reset();
                this.form.clear();
                this.form.estimated_time = 0;
                if (this.user.type == "client") {
                    this.form.owner = {name: this.user.name, id: this.user.id};
                    this.form.owner_id = this.user.id;
                }
                $("#Modal").modal("show");
            },
            editModal(item) {
                this.editMode = true;
                this.form.reset();
                this.form.clear();
                $("#Modal").modal("show");
                this.form.fill(item);
                console.log(item);
                if (item.estimated_time === null || item.estimated_time === 0) {
                    this.form.estimated_time = 0;
                } else {
                    this.form.estimated_time = item.estimated_time
                }

                this.form.selected = _.map(this.form.project_assign, function (
                    value,
                    key
                ) {
                    return value.name;
                });
                this.form.project_assigns = item.project_assign;
                this.form.owner = item.owners;
            },
            createProject() {
                this.$Progress.start();
                // get user id only form assigned users
                // this.form.project_assign.forEach(element => {
                //     this.form.project_assign = this.form.project_assign.filter(
                //         function(obj) {
                //             return obj.id !== element.id;
                //         }
                //     );
                //     this.form.project_assign.push(element.id);
                // });


                this.form.owner = [];
                this.form.owners.forEach(element => {
                    this.form.owner.push(element.id);
                });

                this.form.project_assign = [];
                if (this.form.project_assigns.length > 0) {
                    this.form.project_assigns.forEach(element => {
                        this.form.project_assign.push(element.id);
                    });
                }

                this.form.status_id = this.form.status.id;
                this.$store
                    .dispatch("project/createProject", this.form)
                    .then(response => {
                        $("#Modal").modal("hide");
                        this.$Progress.finish();
                        Toast.fire({
                            type: "success",
                            title: response.data.message
                        });
                        this.getProjects();
                    })
                    .catch(error => {
                        this.$Progress.fail();
                        if (error.response) {
                            this.form.errors.errors = error.response.data.errors;
                        }
                    });
            },
            editProject(id) {
                this.$Progress.start();
                // get user id only form assigned users
                this.form.project_assign = [];
                if (this.form.project_assigns.length > 0) {
                    this.form.project_assigns.forEach(element => {
                        this.form.project_assign.push(element.id);
                    });
                }

                this.form.owner = [];
                this.form.owners.forEach(element => {
                    this.form.owner.push(element.id);
                });
                this.form.status_id = this.form.status.id;
                this.$store
                    .dispatch("project/editProject", this.form)
                    .then(response => {
                        $("#Modal").modal("hide");
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
            deleteProject(id) {
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#234FA3",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then(result => {
                    if (result.value) {
                        this.$Progress.start();
                        this.$store
                            .dispatch("project/deleteProject", id)
                            .then(response => {
                                this.$Progress.finish();
                                Toast.fire({
                                    type: "success",
                                    title: response.data.message
                                });
                            })
                            .catch(error => {
                                this.$Progress.fail();
                                Toast.fire({
                                    type: "error",
                                    title: error.response.data.message
                                });
                            });
                    }
                });
            },
            getResponsibles() {
                this.$store
                    .dispatch("regularUser/getRegularUser", {
                        roles: true
                    })
                    .then(() => {
                        this.resultResponsilbes()
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            getResults(page) {
                this.queryParams.page = page;
                this.getProjects(this.queryParams);
            },
            filterProject(filter) {
                this.filter = filter;
                //this.queryParams.global_search = "";
                this.queryParams.page = 1;
                let notFound = false;
                let remove = false
                if (this.queryParams.filters.length > 0) {
                    this.queryParams.filters.forEach((item) => {
                        if (item.selected_options) {
                            if (filter === 'open' || filter === 'in-progress' || filter === 'pending' || filter === 'done') {
                                if (item.selected_options[0] === 'open' || item.selected_options[0] === 'in-progress' || item.selected_options[0] === 'pending' || item.selected_options[0] === 'done') {
                                    this.queryParams.filters.splice(this.queryParams.filters.indexOf(item), 1);
                                }
                            }
                        }
                        if (item.type === "select" && item.selected_options[0] === filter) {
                            remove = true;
                        } else {
                            notFound = true;
                        }
                    });
                } else {
                    this.queryParams.filters.push({
                        type: "select",
                        name: "status.state",
                        selected_options: [filter],
                    });
                }

                if (notFound === true) {
                    this.queryParams.filters.push({
                        type: "select",
                        name: "status.state",
                        selected_options: [filter],
                    });
                }
                if (remove === true) {
                    this.queryParams.filters = this.queryParams.filters.filter(items => items.selected_options[0] !== filter)
                }
                if (filter === 'all') {
                    this.clearQuery();
                }
                this.getProjects();
            },
            sortProjects(sort) {
                this.queryParams.page = 1;
                this.queryParams.sort = [];
                if (this.sortOrder) {
                    this.queryParams.sort.push({
                        name: sort,
                        order: "ascen"
                    });
                } else {
                    this.queryParams.sort.push({
                        name: sort,
                        order: "desc"
                    });
                }
                this.sortOrder = !this.sortOrder;
                this.getProjects(this.queryParams);
            },
            showAssigned(id) {
                this.projectID = id;
                this.showAssignedUsers = !this.showAssignedUsers;
                this.showOwners = false;
            },
            showAssignedOwners(id) {
                this.projectID = id;
                this.showOwners = !this.showOwners;
                this.showAssignedUsers = false;
            },
            getStatus() {
                this.$store
                    .dispatch("ticket/getStatus")
                    .then(response => {
                    })
                    .catch(error => {
                    });
            }
        },

        filters: {
            returnDoing: value => {
                if (value == 'in-progress') {
                    return 'doing';
                } else {
                    return value;
                }
            },
            projectDescription: value => {
                if (value.length > 75) {
                    return value.substring(0, 75) + "...";
                }
                return value;
            },
            projectDueDate: value => {
                return moment(value).format('D MMM,H:mm')
            },
            subStr: value => {
                if (value.length > 16) {
                    return value.substring(0, 16) + "..";
                }
                return value;
            },
            subStrNames: value => {
                if (value.length > 7) {
                    return value.substring(0, 7) + "...";
                }
                return value;
            },
            subStrProject: value => {
                if (value.length > 6) {
                    return value.substring(0, 6) + "...";
                }
                return value;
            }
        },
        created() {
            if ($cookies.isKey("project_search")) {
                this.queryParams.global_search = $cookies.get("project_search");
            }
        },
        mounted() {
            this.queryParams.page = this.$route.params.page || 1;
            this.getProjects(this.queryParams);
            this.getOwners();
            this.getResponsibles();
            this.getStatus();
            // this.getAllProjects();
        },
        beforeRouteUpdate(to, from, next) {
            this.getProjects(to.params.page);
            next();
        },
        computed: {
            ...mapGetters({
                projects: "project/activeProjects",
                owners: "owner/activeOwners",
                responsible: "regularUser/activeRegularUser",
                user: "user/activeSingleUser",
                status: "ticket/activeStatus"
            }),
            global_search() {
                return this.queryParams.global_search;
            },
            today_date() {
                return moment().format("MMMM Do YYYY");
            },
            projectsCount() {
                if (this.tickets) {
                    return this.tickets.total;
                }
            }
        },
        watch: {
            global_search: _.debounce(function () {
                this.isTyping = false;
            }, 1000),
            isTyping: function (value) {
                if (!value) {
                    this.getProjects(this.queryParams);
                }
            }
        },
        components: {
            pagination,
            moment
        }
    };
</script>
<style scoped>
    /*project card new style*/
    .projects-container {
        border-radius: 2rem;
        margin-bottom: 0.8rem;
        background-color: #ffffff;
        margin-top: 1rem;
    }

    .id_circle {
        width: 2rem;
        height: 2rem;
        background-color: #2F5298;
        text-align: center;
        border-radius: 50%;
        margin-top: -1.2rem;
        margin-left: -1.2rem;
    }

    .id_number {
        font-weight: bold;
        color: #ffffff;
        margin-left: 20%;
        transform: translateX(-20%);
        font-size: .8rem;
    }

    .project-name {
        color: #000000;
        font-weight: bold;
        font-size: 1.3rem;
        text-transform: capitalize;
    }

    .fav {
        color: #EEA24C;
        cursor: pointer;
        font-size: 1.2rem;
        margin-top: -.2rem;

    }

    .status_container {
        width: 4rem;
        height: 1.9rem;
        border-bottom-right-radius: 50%;
        border-top-left-radius: 50%;
        text-align: center;
    }

    .stat-name {
        font-weight: bold;
        font-size: .7rem;
        color: #ffffff;
        transform: translateY(2%);
        text-transform: capitalize;
    }

    #open {
        background-color: #a0466f;
    }

    #done {
        background-color: #67a046;
    }

    #in-progress {
        background-color: #3490dc;
    }

    #pending {
        background-color: #eea24c;
    }

    .description {
        text-transform: capitalize;
    }

    .action {
        font-size: 1.2rem;
        color: #9F9D9D;
    }

    .important-actions {
        font-size: 1rem;
        cursor: pointer;
    }

    #reset-btn {
        font-size: .7rem;
        background-color: #A8557A;
        color: #ffffff;
        margin-top: 1rem;
        border-radius: 10px;
    }

    .actions-container {
        width: 1.8rem;
        height: 1.8rem;
        border-radius: 50%;
        background: linear-gradient(180deg, #DDB456 29.5%, #234fa3 100%);
        text-align: center;
        margin-left: 1rem;

    }

    .calender {
        color: #000000;
        text-decoration: none;
        font-weight: bold;
        font-size: 1rem;
        margin-left: .2rem;
    }

    .sort-item {
        background-color: #ffffff;
        color: #000000;
        cursor: pointer;
        transition: ease .4s;
    }

    .sort-item:hover {
        background-color: #e8e8e8;
    }

    /*end of project card new style*/


    .col-lg-3 .small-box h3 {
        font-size: 2rem;
    }

    /*filter style*/
    .filter-card {
        border-radius: 15px;
    }

    .filter-lists {
        list-style: none;
        padding: 4px;
        color: #707070;
    }

    /*.filter-header {*/
    /*    margin-top: 0.7rem;*/
    /*    margin-left: 0.5rem;*/
    /*    margin-right: 0.5rem;*/
    /*    margin-bottom: -0.9rem;*/
    /*    color: #707070;*/
    /*}*/
    .filter-header {
        display: flex;
        background: linear-gradient(180deg, #AEACAC 0%, #979797 0%, #C8C8C8 0%, rgba(151, 151, 151, 0.92) 100%);
        border-radius: 15px;
    }

    .filter-header i {
        float: right;
        font-size: 1.4rem;
    }

    .filter-lists i {
        padding-right: 0.3rem;
        font-size: 0.8rem;
    }

    .col {
        padding-top: 0rem;
        padding-bottom: 0rem;
    }

    .stat {
        color: #ffffff;
        font-weight: bold;
        padding: 0.7rem 0.7rem 0px 0px;
        text-align: center;
        height: 2.8rem;
    }


    .toggle-div li:hover {
        background: #eaeaea;
        transition: 0.4s ease;
        color: #000000;
        text-decoration: none;
    }

    .toggle-div li a {
        color: #000000;
        text-decoration: none;
    }

    .toggle-div li {
        width: 100%;
    }

    .toggle-menu-card {
        z-index: 9;
        background-color: #ffffff;
        position: absolute;
        margin-top: 2.3rem;
        padding: 0;
        width: 10rem;
        border-radius: 5px;
        box-shadow: 0 3px 1px -2px rgba(0, 0, 0, 0.2),
        0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12);
    }

    /*Filter Style*/


    .filter-container {
        border-radius: 2rem;
        margin-bottom: 0.8rem;
        background-color: #ffffff;
        margin-top: 1rem;
    }

    .filter-header {
        display: flex;
        background: linear-gradient(180deg, #AEACAC 0%, #979797 0%, #C8C8C8 0%, rgba(151, 151, 151, 0.92) 100%);
        border-radius: 15px;
    }

    .act-filters-container {
        background-color: #DCDCDC;
        border-radius: 15px;
    }


    .single-filter {
        display: flex;
    }

    .filter-check {
        margin-left: -.8rem;
    }
</style>
