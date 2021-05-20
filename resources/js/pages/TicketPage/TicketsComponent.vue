<template>
  <v-container fluid class="grey" data-app>
    <v-row>
      <v-col cols="12">
        <v-row class="mb-2">
          <v-col cols="10">
              <v-row>
                  <v-col cols="12">
                      <v-row class="mb-2">
                          <v-col cols="2">
                              <v-text-field
                                  append-icon="fas fa-search"
                                  hide-details
                                  solo
                                  clear-icon="fas fa-clear"
                                  clearable
                                  label="Number"
                                  type="text"
                                  style="border-radius: 10px;"
                                  v-model="search.number"
                                  @change="multiSearch('number')"
                              ></v-text-field>
                          </v-col>
                          <v-col cols="2">
                              <v-text-field
                                  append-icon="fas fa-search"
                                  hide-details
                                  solo
                                  clear-icon="fas fa-clear"
                                  clearable
                                  label="Name"
                                  type="text"
                                  style="border-radius: 10px;"
                                  v-model="search.name"
                                  @change="multiSearch('name')"
                              ></v-text-field>
                          </v-col>
                          <v-col cols="2">
                              <multiselect
                                  v-model="search.assigned_to"
                                  :options="assignedUsers"
                                  :close-on-select="true"
                                  :clear-on-select="true"
                                  :preserve-search="true"
                                  :preselect-first="false"
                                  placeholder="Assigned Users"
                                  @input="multiSearch('assigned_to')"
                                  style="border-radius: 10px;height: 4rem"
                              ></multiselect>
                          </v-col>
                          <v-col cols="2">
                              <v-text-field
                                  append-icon="fas fa-search"
                                  hide-details
                                  solo
                                  clear-icon="fas fa-clear"
                                  clearable
                                  label="Client"
                                  type="text"
                                  style="border-radius: 10px;"
                                  v-model="search.owner"
                                  @change="multiSearch('owner')"
                              ></v-text-field>
                          </v-col>
                          <v-col cols="2">
                              <v-text-field
                                  append-icon="fas fa-search"
                                  hide-details
                                  solo
                                  clear-icon="fas fa-clear"
                                  clearable
                                  label="Email"
                                  type="text"
                                  style="border-radius: 10px;"
                                  v-model="search.email"
                                  @change="multiSearch('email')"
                              ></v-text-field>
                          </v-col>

                          <v-col cols="2">
                              <v-text-field
                                  append-icon="fas fa-search"
                                  hide-details
                                  solo
                                  clear-icon="fas fa-clear"
                                  clearable
                                  label="Project"
                                  type="text"
                                  style="border-radius: 10px;"
                                  v-model="search.project"
                                  @change="multiSearch('project')"
                              ></v-text-field>

                          </v-col>
                          <v-col cols="3">
                              <v-text-field
                                  append-icon="fas fa-calendar-alt"
                                  hide-details
                                  solo
                                  clear-icon="fas fa-clear"
                                  clearable
                                  label="Created_at"
                                  type="date"
                                 style="border-radius: 10px;"
                                  v-model="search.created_at"
                                  @change="multiSearch('created_at')"
                              ></v-text-field>
                          </v-col>
                      </v-row>
                  </v-col>
              </v-row>
          </v-col>
            <v-col cols="2" style="display: flex;flex-direction: column">
                <v-card
                    class="dropdown dropdown-toggle "
                    data-toggle="dropdown"
                    style="border-radius: 10px"
                >
                    <span style="font-size: 1.1rem;border-radius: 10px" class="btn">{{ $t('Ticket.sortTickets')}}</span>
                    <div class="dropdown-menu">
                        <span class="dropdown-item sort-item" @click="sortTickets('number')">{{ $t('Ticket.Number') }}</span>
                        <span class="dropdown-item sort-item" @click="sortTickets('name')">{{ $t('Ticket.Name') }}</span>
                        <span class="dropdown-item sort-item" @click="sortTickets('due_date')">{{ $t('Ticket.Duedate') }}</span>
                        <span class="dropdown-item sort-item" @click="sortTickets('created_at')">{{ $t('Ticket.createdAt') }}</span>
                    </div>
                </v-card>
                <v-btn
                    color="#234FA3"
                    dark
                    fixed
                    bottom
                    right
                    fab
                    @click.stop="newModal"
                >
                    <v-icon>fas fa-plus</v-icon>
                </v-btn>

                <v-btn bottom id="reset-btn"  block @click="clearQuery">{{ $t('Project.resetQuery') }}</v-btn>
            </v-col>

        </v-row>
        <v-row>
          <v-col cols="9">

            <div v-if="!tickets.data">
            </div>
            <template v-if="tickets.data">
              <v-tabs v-model="ticketstab">
                <v-tabs-slider></v-tabs-slider>
                <v-tab :to="{ name: 'tickets.kanban' }">
                    <i id="kanban" class="fab fa-trello fa-fw "></i> {{$t('Ticket.kanban')}}
                </v-tab>
                <v-tab href="#ticketscards"> {{$t('global.cardView')}} </v-tab>
                <v-tab href="#ticketstable"> {{$t('global.tableView')}} </v-tab>
                <v-spacer></v-spacer>
                <v-btn
                  small
                  class="mt-2 mr-2"
                  @click="addSpam"
                  color="#ffffff"
                  style="color: #ffffff"
                  :disabled="selected.length < 1"
                  >Spam</v-btn
                >
                <v-btn
                  v-if="showingArchive === true"
                  small
                  color="#ffffff"
                  class="mt-2 mr-2"
                  style="color: #ffffff"
                  @click="removeArchive"
                  :disabled="selected.length < 1"
                  >Not Archive</v-btn
                >
                <v-btn
                  v-else
                  small
                  color="#ffffff"
                  class="mt-2 mr-2"
                  style="color: #ffffff"
                  @click="addArchive"
                  :disabled="selected.length < 1"
                  >Archive</v-btn
                >
                  <v-btn
                      small
                      class="mt-2 mr-2"
                      color="#ffffff"
                      style="color: #ffffff"
                      @click="deleteMultiTickets"
                      :disabled="selected.length < 1"
                  >Delete</v-btn
                  >
                <v-spacer></v-spacer>
                <v-btn
                  small
                  class="mt-2 mr-2"
                  @click="openMultiTickets"
                  color="#ffffff"
                  style="color: #ffffff"
                  :disabled="selected.length < 1"
                  >open</v-btn
                >
                <v-btn
                  small
                  class="mt-2 mr-2"
                  @click="closeMultiTickets"
                  color="#ffffff"
                  style="color: #ffffff"
                  :disabled="selected.length < 1"
                  >Close</v-btn
                >

              </v-tabs>

              <v-tabs-items v-model="ticketstab">
                <v-tab-item value="ticketscards" >
                    <v-row style="margin-top: 1rem">
                        <v-col cols="6" v-for="ticket in tickets.data" :key="ticket.id">
                            <v-card  style="border-radius: 2rem; margin-bottom: 0.8rem;" >
                                <v-container fluid>
                                    <v-row>
                                        <v-col cols="1">
                                            <input type="checkbox" v-model="selected" :value="ticket.id"/>
                                        </v-col>
                                        <v-col cols="3" style="margin-left: -.3rem">
                                            <small style="color:#959595;">#{{ ticket.number }}</small>
                                        </v-col>
                                        <v-col cols="2" style="margin-left:-1rem">
                                            <i title="open" @click.prevent="OpenStatus(ticket)" v-if="checkStatus(ticket)" style="color:#C64848" class="fas fa-toggle-off changeStatus"></i>
                                            <i title="close" @click.prevent="CloseStatus(ticket)" v-else style="color:#67A046"  class="fas fa-toggle-on changeStatus"></i>
                                        </v-col>
                                    </v-row>
                                    <v-row style="margin-top:-.4rem">
                                        <v-col cols="6">
                                            <router-link
                                                :to="{name:'ticket' , params: { id: ticket.id }}"
                                                class="ticket-name"
                                                data-toggle="tooltip" data-placement="top" :title="ticket.name">
                                                {{ ticket.name | subStrname}}
                                            </router-link>
                                        </v-col>
                                        <v-col v-if="ticket.status" cols="4" style="margin-left:-1rem">
                                            <div :id="ticket.status.name" class="status_container">
                                                <span class="stat-name">{{ticket.status.name}}</span>
                                            </div>
                                        </v-col>
                                        <v-col cols="2" style="margin-left:1rem">
                                            <i @click.prevent="editModal(ticket)" style="color:#81B488" class="fa fa-edit important-actions"></i>
                                            <i @click.prevent="deleteTicket(ticket.id)" style="color:#C64848" class="fa fa-trash ml-1 important-actions"></i>
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="8">
                                            <span style="font-weight: bold;font-size: .9rem">Estimated Time:</span>
                                            <small style="color:#959595;">{{ticket.estimated_time}} hours</small>
                                        </v-col>
                                        <v-col cols="4" >
                                            <small v-if="ticket.read" style="color: #ABC3E3;font-weight: bold">{{ $t('Ticket.Read') }} -</small>
                                            <small v-else style="color: #C85E5E;font-weight: bold">{{ $t('Ticket.NotRead') }} -</small>
                                            <small v-if="ticket.reply" class="ml-1" style="color: #ABC3E3;font-weight: bold">{{ $t('Ticket.Replyed') }}</small>
                                            <small v-else style="color: #C85E5E;font-weight: bold">{{ $t('Ticket.NotReplyed') }}</small>
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="7">
                                            <span style="font-weight: bold;font-size: .9rem">{{ $t('Ticket.lastUpdate') }}:</span>
                                            <small style="color:#959595;">{{ticket.updated_at | ticketDueDate }}</small>
                                        </v-col>
                                        <v-col cols="5">
                                            <span style="font-weight: bold;font-size: .9rem">{{ $t('Ticket.Duedate') }}:</span>
                                            <small style="color:#959595;">{{ticket.due_date | ticketDueDate }}</small>
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="7" v-if="ticket.owner" style="display: inline-flex;">
                                            <span style="font-weight: bold;font-size: .9rem">{{ $t('Ticket.client') }}:</span>
                                            <avatar
                                                color="#90b0fa"
                                                v-if="ticket.owner[0]"
                                                :fullname="ticket.owner[0].name"
                                                data-placement="top" :title="ticket.owner[0].name"
                                                :size="20"
                                                style="margin-left: .3rem">
                                            </avatar>
                                            <avatar
                                                color="#90b0fa"
                                                v-if="ticket.owner[1]"
                                                :fullname="ticket.owner[1].name"
                                                data-placement="top" :title="ticket.owner[1].name"
                                                :size="20"
                                                style="margin-left: .3rem">
                                            </avatar>
                                            <avatar
                                                color="#90b0fa"
                                                v-if="ticket.owner[2]"
                                                :fullname="ticket.owner[2].name"
                                                data-placement="top" :title="ticket.owner[2].name"
                                                :size="20"
                                                style="margin-left: .3rem">
                                            </avatar>
                                            <avatar
                                                color="#90b0fa"
                                                v-if="ticket.owner[3]"
                                                :fullname="ticket.owner[3].name"
                                                data-placement="top" :title="ticket.owner[3].name"
                                                :size="20"
                                                style="margin-left: .3rem">
                                            </avatar>
                                            <v-btn icon @click="showClients(ticket.id)" style="width: 1.5rem;height: 1.5rem;">
                                                <i style="font-size: .6rem" class="fa fa-ellipsis-v"></i>
                                            </v-btn>
                                            <div v-if="showclients && ticketID ==ticket.id"  class="toggle-menu-card">
                                                <div class="content" style="padding: 0 .4rem">
                                                    <ul style="list-style: none;text-align: center; margin-top: 0.5rem;" class="toggle-div" v-for="user in ticket.owner" :key="user.id">
                                                        <li style="display: inline-flex; width: max-content;">
                                                            <avatar color="#90b0fa" :fullname="user.name" :size="30"></avatar>
                                                            <router-link :to="'/admin/profile/' + user.id" style="margin-left: 0.5rem">
                                                                {{user.name | subStrname}}
                                                            </router-link>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </v-col>
                                        <v-col cols="7" v-else>
                                            <span style="font-weight: bold;font-size: .9rem">{{ $t('Ticket.client') }}:</span>
                                            <small>No Client</small>
                                        </v-col>
                                        <v-col cols="5" v-if="ticket.project">
                                            <span style="font-weight: bold;font-size: .9rem">{{ $t('Ticket.project') }}:</span>
                                            <small ><router-link
                                                style="color: #0a0c0d"
                                                :to="'/admin/project/' + ticket.project.id"
                                                data-placement="top" :title="ticket.project.name"
                                                >{{ ticket.project.name | subStrProject}}</router-link></small>
                                        </v-col>
                                        <v-col cols="5" v-else>
                                            <span style="font-weight: bold;font-size: .9rem">{{ $t('Ticket.project') }}:</span>
                                            <small>{{ $t('Ticket.NoProject') }}</small>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-card>
                        </v-col>
                    </v-row>
                  <pagination
                    :data="mytickets"
                    :limit="parseInt(2)"
                    size="small"
                    @pagination-change-page="getResults"
                  >
                    <span slot="prev-nav">&lt; Previous</span>
                    <span slot="next-nav">Next &gt;</span>
                  </pagination>
                </v-tab-item>
                <v-tab-item value="ticketstable">
                  <v-simple-table dense fixed-header>
                    <template v-slot:default>
                      <thead>
                        <tr>
                          <th width="20%" class="text-left"> <input v-model="select_all" type="checkbox" @click="selectAll"> </th>
                          <th class="text-left">{{ $t('Ticket.Number') }}</th>
                          <th width="20%" class="text-left">{{ $t('Ticket.title') }}</th>
                          <th class="text-left">Status</th>
                          <th width="15%" class="text-left">{{ $t('Ticket.client') }}</th>
                          <th width="12%" class="text-left">{{ $t('Ticket.project') }}</th>
                          <th width="22%" class="text-left">{{ $t('Ticket.lastUpdate') }}</th>
                          <th width="3%" class="text-left" data-placement="bottom" title="estimated time to complete">{{ $t('Ticket.ETC') }}</th>
                          <th width="13%" class="text-left">{{ $t('Ticket.action') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="ticket in tickets.data" :key="ticket.id">
                          <td>
                            <div class="fixedLine">
                              <input
                                type="checkbox"
                                v-model="selected"
                                :value="ticket.id"
                              />
                              <i
                                title="Not Replyed"
                                v-if="!ticket.reply"
                                style="font-size: .8rem"
                                class="text-danger icon fas fa-reply"
                              ></i>
                              <i
                                v-else
                                data-widget="collapse"
                                data-toggle="tooltip"
                                title="Replyed"
                                style="font-size: .8rem"
                                class="text-success icon fas fa-reply"
                              ></i>
                              <i
                                v-if="!ticket.read"
                                data-widget="collapsec"
                                data-toggle="tooltip"
                                title="Not Reed"
                                style="font-size: .8rem"
                                class="text-danger icon pl-1 fas fa-exclamation-triangle"
                              ></i>
                              <i
                                v-else
                                data-widget="collapse"
                                data-toggle="tooltip"
                                style="font-size: .8rem"
                                title="Reed"
                                class="text-success icon pl-1 fas fa-check"
                              ></i>
                            </div>
                          </td>
                          <td>
                            <router-link
                              :to="{
                                name: 'ticket',
                                params: { id: ticket.id },
                              }"
                            >
                              {{ ticket.number }}
                            </router-link>
                          </td>
                          <td>
                            <router-link
                                data-placement="top" :title="ticket.name "
                              :to="{
                                name: 'ticket',
                                params: { id: ticket.id },
                              }"
                            >
                              {{ ticket.name | subStrtitle }}
                            </router-link>
                          </td>
                          <td v-if="ticket.status">{{ ticket.status.name | returnDoing}}</td>
                          <td v-else>----</td>
                          <td v-if="ticket.owner">
                            <router-link
                                data-placement="top" :title="ticket.owner[0].name"
                              v-if="ticket.owner[0]"
                              :to="'/admin/profile/' + ticket.owner[0].id"
                              >{{ ticket.owner[0].name | subStrProjectTable }}

                            </router-link
                            >
                          </td>
                          <td v-else>----</td>
                          <td v-if="ticket.project" data-placement="top" :title="ticket.project.name">
                            {{ ticket.project.name | subStrProjectTable }}
                          </td>
                          <td v-else>----</td>
                          <td>{{ ticket.updated_at | ticketUpdatedAt }}</td>
                          <td><span :title="`Due Date: ${$options.filters.ticketDueDate(ticket.due_date) }`">{{ ticket.estimated_time}}</span></td>
                          <td style="display: inline-flex;margin-top: 1.1rem">
                            <i
                              @click="editModal(ticket)"
                              data-widget="collapse"
                              data-toggle="tooltip"
                              title="Edit"
                              class="text-success icon fas fa-edit fa-xs"
                              style="
                                :hover {
                                  color: #ffffff;
                                };
                                font-size: .8rem
                              "
                            ></i>
                            <i
                              @click="deleteTicket(ticket.id)"
                              data-widget="collapse"
                              data-toggle="tooltip"
                              title="Edit"
                              style="font-size: .8rem"
                              class="text-danger icon pl-1 fas fa-trash fa-xs"
                            ></i>
                            <i title="open" @click.prevent="OpenStatus(ticket)" v-if="checkStatus(ticket)" style="color:#C64848" class="fas fa-toggle-off changeStatusTable"></i>
                            <i title="close" @click.prevent="CloseStatus(ticket)" v-else style="color:#67A046"  class="fas fa-toggle-on changeStatusTable"></i>
                          </td>
                        </tr>
                      </tbody>
                    </template>
                  </v-simple-table>
                  <pagination
                    :data="mytickets"
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
          <v-col cols="3">
              <v-row>
                  <div class="actions-container mt-1">
                      <i style="color : #ffffff" class="fas fa-calendar-alt"></i>
                  </div>
                  <router-link class="calender mt-1" to="/admin/calendar">{{ today_date }}</router-link>
              </v-row>
              <v-row>
                  <v-col cols="12">
                      <v-text-field
                          append-icon="fas fa-search"
                          hide-details
                          solo
                          clear-icon="fas fa-clear"
                          clearable
                          label="Search"
                          type="text"
                          style="border-radius: 10px;margin-top:1rem"
                          v-model="queryParams.global_search"
                          @click:append="getTickets"
                          v-on:keyup.enter="getTickets"
                      ></v-text-field>
                  </v-col>
              </v-row>
              <v-row>
                  <v-col cols="12" class="filter-container">
                      <v-container fluid>
                          <v-row class="filter-header">
                              <v-col cols="9" style="display: flex">
                                  <i class="fas fa-filter" style="font-size: 1rem;margin-top: .3rem" ></i>
                                  <strong>Filters</strong>
                              </v-col>
                              <v-col cols="3" v-if="tickets.total" >
                                  <small>{{ tickets.total }}</small>
                              </v-col>
                              <v-col cols="3" v-else >
                                  <small>0</small>
                              </v-col>
                          </v-row>
                          <v-row style="margin-top:.4rem;">
                              <v-col cols="12" class="act-filters-container" >
                                  <v-container fluid>
                                      <v-row>
                                          <v-col cols="6">
                                              <button @click="findTickets('all')" style="border: none"><small class="filter-text">{{ $t('Ticket.AllTickets') }}</small></button>
                                          </v-col>
                                          <v-col cols="6">
                                              <button @click="findTickets('myTickets')" style="border: none"><small class="filter-text">{{ $t('Ticket.MyTickets') }}</small></button>
                                          </v-col>
                                          <v-col cols="6">
                                              <button @click="findTickets('todayTickets')" style="border: none"><small class="filter-text">{{ $t('Ticket.TodayTickets') }}</small></button>
                                          </v-col>
                                          <v-col cols="6">
                                              <button @click="findTickets('yesterdayTickets')" style="border: none;margin-left:-.5rem"><small class="filter-text" >{{ $t('Ticket.YesterdayTickets') }}</small></button>
                                          </v-col>
                                      </v-row>
                                      <hr>
                                      <v-row>
                                          <strong style="margin-top:-.5rem">Status</strong>
                                      </v-row>
                                      <v-row>
                                          <v-col cols="6" class="single-filter">
                                              <!-- <input id="checking1" class="filter-check"  type="checkbox"  @click="filterTickets('open')"><small class="filter-text">{{ $t('Ticket.Open') }}</small> -->
                                              <button @click="filterTickets('open')" style="border: none"><small class="filter-text">{{ $t('Ticket.Open') }}</small></button>
                                          </v-col>
                                          <v-col cols="6" class="single-filter">
                                              <!-- <input id="checking2" class="filter-check"  type="checkbox" @click="filterTickets('pending')"><small class="filter-text" >{{ $t('Ticket.pending') }}</small> -->
                                              <button @click="filterTickets('pending')" style="border: none"><small class="filter-text">{{ $t('Ticket.pending') }}</small></button>
                                          </v-col>
                                      </v-row>
                                      <v-row>
                                          <v-col cols="6" class="single-filter">
                                              <!-- <input id="checking3" class="filter-check"  type="checkbox"  @click="filterTickets('in-progress')"><small class="filter-text" >{{ $t('Ticket.doing') }}</small> -->
                                              <button @click="filterTickets('in-progress')" style="border: none"><small class="filter-text">{{ $t('Ticket.doing') }}</small></button>
                                          </v-col>
                                          <v-col cols="6" class="single-filter">
                                              <!-- <v-btn id="checking4" class="filter-check"  type="checkbox" @click="filterTickets('done')"><small class="filter-text" >{{ $t('Ticket.done') }}</small></v-btn> -->
                                              <button @click="filterTickets('done')" style="border: none"><small class="filter-text">{{ $t('Ticket.done') }}</small></button>
                                          </v-col>
                                      </v-row>
                                      <hr>
                                      <v-row>
                                          <v-col cols="6" class="single-filter">
                                              <input :disabled="unread" class="filter-check"  type="checkbox"  @click="filterTickets('read')"><small class="filter-text" >{{ $t('Ticket.Read') }}</small>
                                          </v-col>
                                          <v-col cols="6" class="single-filter">
                                              <input :disabled="read" class="filter-check"  type="checkbox" @click="filterTickets('unread')"><small class="filter-text" >{{ $t('Ticket.NotRead') }}</small>
                                          </v-col>
                                      </v-row>
                                      <v-row>
                                          <v-col cols="6" class="single-filter">
                                              <input :disabled="unreply" class="filter-check"  type="checkbox"  @click="filterTickets('reply')"><small class="filter-text" >{{ $t('Ticket.reply') }}</small>
                                          </v-col>
                                          <v-col cols="6" class="single-filter">
                                              <input :disabled="reply" class="filter-check"  type="checkbox" @click="filterTickets('unreplied')"><small class="filter-text" >{{ $t('Ticket.NotReplyed') }}</small>
                                          </v-col>
                                      </v-row>
                                      <hr>
                                      <v-row>
                                          <v-col cols="6" class="single-filter">
                                              <router-link :to="{ name: 'tickets.spam' }" style="border: none;color: #0a0c0d"><small class="filter-text">{{ $t('Ticket.Spam') }}</small></router-link>
                                          </v-col>
                                          <v-col cols="6" class="single-filter">
                                              <input class="filter-check"  type="checkbox" @click="filterTickets('archive')"><small class="filter-text" >{{ $t('Ticket.Archive') }}</small>
                                          </v-col>
                                      </v-row>
                                      <v-row>
                                          <v-col cols="12" class="single-filter">
                                              <input class="filter-check"  type="checkbox"  @click="filterTickets('unproject')"><small class="filter-text" >{{ $t('Ticket.NoProject') }}</small>
                                          </v-col>
                                      </v-row>
                                      <hr>
                                      <v-row>
                                          <strong style="margin-top:-.5rem">{{ $t('Project.tasks') }}</strong>
                                      </v-row>
                                      <v-row>
                                          <v-col cols="12" class="single-filter">
                                              <input class="filter-check"  type="checkbox" @click="filterTickets('withTask')"><small class="filter-text" >{{ $t('Ticket.TicketsWithTasks') }}</small>
                                          </v-col>
                                      </v-row>
                                      <v-row>
                                          <v-col cols="12" class="single-filter">
                                              <input class="filter-check"  type="checkbox" @click="filterTickets('withoutTask')"><small class="filter-text" >{{ $t('Ticket.TicketsWithoutTasks') }}</small>
                                          </v-col>
                                      </v-row>

                                  </v-container>
                              </v-col>
                          </v-row>
                      </v-container>
                  </v-col>
              </v-row>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
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
            <h5 v-show="!editMode" class="modal-title" id="newTicketLabel">
              {{ $t("Ticket.createNew") }} {{ $t("Ticket.ticket") }}
            </h5>
            <h5 v-show="editMode" class="modal-title" id="newTicketLabel">
              Edit Ticket
            </h5>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            />
          </div>
          <!-- /.card-header -->
          <div class="modal-body">
            <form
              @submit.prevent="editMode ? editTicket(form) : createTicket(form)"
              @keydown="form.onKeydown($event)"
            >
              <div class="form-group">
                <label for="name">
                  {{ $t("Ticket.ticketName") }}
                <span class="asterisk_input"></span>
                </label>
                <input
                  required
                  v-model="form.name"
                  type="text"
                  name="name"
                  class="form-control"
                  :class="{
                    'is-invalid': form.errors.has('name'),
                  }"
                />
<!--                  We Get all project at entering the name to light te wight of the request-->
                <has-error :form="form" field="name"></has-error>
              </div>
              <div v-if="!editMode" class="form-group">
                <label for="description">
                  {{ $t("Ticket.ticketDesc") }}
                    <span class="asterisk_input"></span>
                </label>
                <quill-editor
                 required
                 v-model="form.description"
                  ref="myQuillEditor"
                  :options="editorOption"
                ></quill-editor>
                <has-error :form="form" field="description"></has-error>
              </div>
              <div class="form-group">
                <label for="name">
                  {{ $t("Ticket.status") }}
                </label>
                <multiselect
                  v-model="form.status"
                  :options="status"
                  label="name"
                  track-by="id"
                ></multiselect>

                <has-error :form="form" field="status_id"></has-error>
              </div>
              <!-- Showing all clients and getting project related to this client -->
              <div class="form-group" v-if="form.project">
                      <label for="name">{{ $t("Task.client") }}
                        <span class="asterisk_input"></span>
                      </label>
                      <multiselect
                          v-model="form.owner"
                          :options="owners"
                          :close-on-select="true"
                          :clear-on-select="true"
                          :preserve-search="true"
                          :multiple="true"
                          :placeholder="$t('Task.selectOne')"
                          label="name"
                          track-by="id"
                          :preselect-first="true"
                          :allow-empty="true"
                          deselect-label="Can't remove this value"
                          @input="getProjectsByOwner(form.owner[0] ? form.owner[0].id : null)"
                        ></multiselect>
                      <has-error
                          :form="form"
                          field="owner_id"
                      ></has-error>
              </div>
              <!-- Showing Clients related to the selected project -->
              <div class="form-group" v-else>
                  <label for="name">{{ $t("Task.client") }}</label>
                    <multiselect
                        v-model="form.owner"
                        :options="owners"
                        :close-on-select="true"
                        :clear-on-select="true"
                        :preserve-search="true"
                        :multiple="true"
                        track-by="id"
                        :placeholder="$t('Task.selectOne')"
                        label="name"
                        :preselect-first="true"
                        :allow-empty="true"
                        deselect-label="Can't remove this value"
                        @input="getProjectsByOwner(form.owner[0] ? form.owner[0].id : null)"
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
                      :loading="LoadOwnerProjects"
                      v-model="form.project"
                      :options="projects"
                      :close-on-select="true"
                      :clear-on-select="false"
                      :preserve-search="true"
                      :placeholder="$t('Task.selectOne')"
                      label="name"
                      track-by="id"
                      :preselect-first="true"
                      :allow-empty="false"
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
                  }}</label>
                  <!-- <multiselect
                      v-model="form.project"
                      :options="projects"
                      :close-on-select="true"
                      :clear-on-select="false"
                      :preserve-search="true"
                      :placeholder="$t('Task.selectOne')"
                      label="name"
                      track-by="id"
                      :preselect-first="true"
                      :allow-empty="false"
                      @input="getClientsPerProject(form.project.id)"
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
              <!-- <div class="form-group" v-if="user.type == 'regular-user'">
                <label for="name">
                  {{ $t("Ticket.client") }}
                </label>
                <multiselect
                  v-model="form.owner"
                  :options="owners"
                  :close-on-select="true"
                  :clear-on-select="false"
                  :preserve-search="true"
                  :allow-empty="false"
                  label="name"
                  track-by="id"
                  :multiple="true"
                  deselect-label="Can't remove this value"
                  :placeholder="$t('Ticket.selectOne')"
                ></multiselect>
                <has-error :form="form" field="client_id"></has-error>
              </div> -->
              <!-- <div class="form-group" >
                <label for="name">
                  {{ $t("Ticket.project") }}
                </label>
                <multiselect
                  v-model="form.project"
                  :options="allProjects"
                  :close-on-select="false"
                  :clear-on-select="true"
                  :preserve-search="true"
                  :placeholder="$t('Ticket.selectOne')"
                  label="name"
                ></multiselect>
                <has-error :form="form" field="project_id"></has-error>
              </div> -->
              <div class="form-group">
                <label for="name"> Responsible (Manager) 
                  <span class="asterisk_input"></span>
                </label>
                <multiselect
                  v-model="form.manager"
                  :multiple="true"
                  :options="employees"
                  :searchable="true"
                  :close-on-select="true"
                  :clear-on-select="false"
                  :preserve-search="true"
                  track-by="id"
                  :preselect-first="true"
                  :placeholder="$t('Ticket.selectOne')"
                  label="name"
                ></multiselect>
                <has-error :form="form" field="manager_id"></has-error>
              </div>
              <div class="form-group">
                <label for="end_at">{{ $t("Task.endAt") }}</label>
                <date-picker
                  v-model="form.due_date"
                  lang="en"
                  type="datetime"
                  format="DD-MM-YYYY HH:mm"
                  :minute-step="15"
                  input-class="form-control"
                  value-type="format"
                ></date-picker>
                <has-error :form="form" field="due_date"></has-error>
              </div>
                <div class="form-group">
                    <label for="estimated_time">Estimated Time</label>
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
            </form>
          </div>

          <div class="row modal-footer">
            <div class="mr-auto w-20 pl-2">All fields with <span style="color:red"> * </span> are mandatory</div>
            <button
              v-show="!editMode"
              type="submit"
              class="btn btn-primary"
              @click="createTicket(form)"
            >
              {{ $t("Ticket.save") }}
            </button>
            <button
              v-show="editMode"
              type="submit"
              class="btn btn-success"
              @click="editTicket(form)"
            >
              {{ $t("Ticket.update") }}
            </button>
          </div>
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
import pagination from "laravel-vue-pagination";
import avatar from "../../components/tickets/Avatar";
import createTicketComponent from "../../components/tickets/createTicketComponent";

// require styles
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";

export default {
  data() {
    return {
      timer: true,
      showclients: false,
      showingArchive: false,
      loading: false,
      projectSync: null,
      LoadOwnerProjects: false,
      showAssignedMangers: false,
      ticketID: "",
      tab: "filter",
      ticketstab: "ticketstable",
      inject: ["theme"],
      selected: [],
      assignedUsers : [],
      sortItem: "",
      editMode: false,
      dialog: false,
      isActive: false,
      sortOrder: true,
      filter: "",
      mytickets: {},
      form: new Form({
        id: "",
        name: "",
        description: null,
        due_date: "",
        owner: null,
        project: {
          id: "",
          name: "",
        },
          status: {
              id: 1,
              name: "open"
          },
        estimated_time: "",
        manager: null,
        manager_id: "",
        project_id: "",
        owner_id: "",
        status_id: "",
        cc: '',

      }),
      editorOption: {
        modules: {
          toolbar: [
            ["bold", "italic", "underline", "strike"],
            ["blockquote", "code-block"],
            [{ list: "ordered" }, { list: "bullet" }],
          ],
        },
      },
      queryParams: {
        sort: [],
        filters: [],
        global_search: "",
        per_page: 15,
        page: 1,
      },
      search: {
          name: "",
          number: "",
          owner: "",
          email:"",
          project: "",
          created_at:'',
          assigned_to:''
      },
      columns: [
        {
          label: this.$t("Ticket.ticket") + "#",
          name: "number",
          filter: {
            type: "simple",
            placeholder: this.$t("Ticket.enterTicketNum"),
          },
          sort: true,
          row_text_alignment: "text-left",
        },
        {
          label: this.$t("Ticket.read"),
          name: "read",
          sort: true,
          row_text_alignment: "text-center",
        },
        {
          label: this.$t("Ticket.title"),
          name: "name",
          filter: {
            type: "simple",
            placeholder: this.$t("Ticket.enterTicketTitle"),
          },
          sort: true,
          row_text_alignment: "text-left",
        },
        {
          label: this.$t("Ticket.reply"),
          name: "reply",
          sort: true,
          row_text_alignment: "text-center",
        },
        {
          label: this.$t("Ticket.status"),
          name: "status.name",
          sort: true,
        },
        {
          label: this.$t("Ticket.client"),
          name: "owner",
          filter: {
            type: "simple",
            placeholder: this.$t("Ticket.enterClient"),
          },
          sort: true,
        },
        {
          label: this.$t("Ticket.project"),
          name: "project.name",
          filter: {
            type: "simple",
            placeholder: this.$t("Ticket.enterProject"),
          },
          sort: true,
        },
        {
          label: this.$t("Ticket.createdAt"),
          name: "created_at",
          sort: true,
        },
        {
          label: this.$t("Ticket.dueDate"),
          name: "due_date",
          sort: true,
        },
        {
          label: this.$t("Ticket.action"),
          name: "action-buttons",
        },
      ],
      config: {
        server_mode: true,
        card_mode: false,
        show_refresh_button: false,
        pagination: true,
        pagination_info: true,
        per_page: 15,
        global_search: {
          placeholder: "Enter custom Search text",
          init: {
            value: "",
          },
        },
      },
      classes: {
        table: {
          "table-sm": true,
        },
      },
      total_rows: 0,
        read: false,
        reply: false,
        unread: false,
        unreply: false,
        select_all:false,
    };
  },
  methods: {
      getClientsPerProject(id){
          this.$store
              .dispatch("owner/getClientsPerProject",{id:id, roles:true})
              .then(() => {})
              .catch(() => {});
      },
      searchAssigned(){
          this.multiSearch('assigned_to');
      },
      checkStatus(ticket){
          return ticket.status.name === 'done';
      },
      OpenStatus(ticket){
          this.form.reset();
          this.form.clear();
          if (ticket.owner) {
              this.getProjectsByOwner(ticket.owner.id);
          }
          this.form.fill(ticket);
          this.form.manager_id = this.form.manager.id;
          this.form.owner_id = this.form.owner.id;
          this.form.status_id = this.form.status.id;
          if (this.singlePage) {
              if (this.form.project) {
                  this.form.project = this.project;
                  this.form.project_id = this.project.id;
              }
          }
          this.form.status.id = 1;
          this.form.status.name = 'open';
          this.form.status_id = 1;

          this.$Progress.start();
          this.form.manager_id = this.form.manager.map((man) => man.id);
          this.form.owner_id = this.form.owner.map((own) => own.id);
          if (this.form.project) {
              this.form.project_id = this.form.project.id;
          }
          this.$store
              .dispatch("ticket/editTicket", this.form)
              .then((response) => {
                  this.getTickets();
                  this.$Progress.finish();
                  Toast.fire({
                      type: "success",
                      title: response.data.message,
                  });
              })
              .catch((error) => {
                  this.$Progress.fail();
                  if (error.response) {
                      this.form.errors.errors = error.response.data.errors;
                  }
              });
      },
      CloseStatus(ticket){
          this.form.reset();
          this.form.clear();
          if (ticket.owner) {
              this.getProjectsByOwner(ticket.owner.id);
          }
          this.form.fill(ticket);
          this.form.manager_id = this.form.manager.id;
          this.form.owner_id = this.form.owner.id;
          this.form.status_id = this.form.status.id;
          if (this.singlePage) {
              if (this.form.project) {
                  this.form.project = this.project;
                  this.form.project_id = this.project.id;
              }
          }

          this.form.status.id = 4;
          this.form.status.name = 'done';
          this.form.status_id = 4;

          this.$Progress.start();
          this.form.manager_id = this.form.manager.map((man) => man.id);
          this.form.owner_id = this.form.owner.map((own) => own.id);
          if (this.form.project) {
              this.form.project_id = this.form.project.id;
          }
          this.$store
              .dispatch("ticket/editTicket", this.form)
              .then((response) => {
                  this.getTickets();
                  this.$Progress.finish();
                  Toast.fire({
                      type: "success",
                      title: response.data.message,
                  });
              })
              .catch((error) => {
                  this.$Progress.fail();
                  if (error.response) {
                      this.form.errors.errors = error.response.data.errors;
                  }
              });

      },
      selectAll(){
        this.select_all = !this.select_all;
        if(this.select_all === true){
            this.select_all = []
            this.tickets.data.forEach(ticket=>{
                this.selected.push(ticket.id)
            })
        }
        else{
            this.selected = []
        }
      },
      showClients(id) {
          this.ticketID = id;
          this.showclients = !this.showclients;
      },
    onChangeQuery(queryParams) {
      this.queryParams = queryParams;

      if (this.filter != "" && this.filter != "all") {
        this.queryParams.global_search = this.filter;
      } else {
        this.queryParams.global_search = "";
      }

      this.getTickets();
    },
    addSpam() {
      this.$store
        .dispatch("ticket/addTicketToSpam", { selected: this.selected })
        .then((res) => {
          Toast.fire({
            type: "success",
            title: res.data.message,
          });
          this.getTickets();
          this.selected = [];
        })
        .catch((error) => {
          console.log(error);
        });
    },
    addArchive() {
      this.$store
        .dispatch("ticket/addTicketToArchive", { selected: this.selected })
        .then((res) => {
          Toast.fire({
            type: "success",
            title: res.data.message,
          });
          this.getTickets();
          this.selected = [];
        })
        .catch((error) => {
          console.log(error);
        });
    },
    removeArchive() {
      this.$store
        .dispatch("ticket/removeTicketToArchive", { selected: this.selected })
        .then((res) => {
          Toast.fire({
            type: "success",
            title: res.data.message,
          });
          this.getTickets();
          this.selected = [];
        })
        .catch((error) => {
          console.log(error);
        });
    },
      deleteMultiTickets() {
          Swal.fire({
              title: "Are you sure?",
              text: "You won't be able to revert this!",
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#3085d6",
              cancelButtonColor: "#d33",
              confirmButtonText: "Yes, delete it!",
          }).then((result) => {
              if (result.value) {
                  this.$Progress.start();
                  this.$store
                      .dispatch("ticket/deleteMultiTickets",  this.selected )
                      .then((response) => {
                          this.$Progress.finish();
                          Toast.fire({
                              type: "success",
                              title: response.data.message,
                          });
                          this.getTickets();
                          this.selected = [];
                          this.select_all = false;
                      })
                      .catch((error) => {
                          console.log(error);
                          this.$Progress.fail();
                          Toast.fire({
                              type: "error",
                              title: error.response.data.message,
                          });
                      });
              }
          });

    },

    openMultiTickets() {
          Swal.fire({
              title: "Are you sure?",
              text: "You won't be able to revert this!",
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#3085d6",
              cancelButtonColor: "#d33",
              confirmButtonText: "Yes, open it!",
          }).then((result) => {
              if (result.value) {
                  this.$Progress.start();
                  this.$store
                      .dispatch("ticket/openMultiTickets",  this.selected )
                      .then((response) => {
                          this.$Progress.finish();
                          Toast.fire({
                              type: "success",
                              title: response.data.message,
                          });
                          this.getTickets();
                          this.selected = [];
                          this.select_all = false;
                      })
                      .catch((error) => {
                          console.log(error);
                          this.$Progress.fail();
                          Toast.fire({
                              type: "error",
                              title: error.response.data.message,
                          });
                      });
              }
          });

    },

    closeMultiTickets() {
          Swal.fire({
              title: "Are you sure?",
              text: "You won't be able to revert this!",
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#3085d6",
              cancelButtonColor: "#d33",
              confirmButtonText: "Yes, close it!",
          }).then((result) => {
              if (result.value) {
                  this.$Progress.start();
                  this.$store
                      .dispatch("ticket/closeMultiTickets",  this.selected )
                      .then((response) => {
                          this.$Progress.finish();
                          Toast.fire({
                              type: "success",
                              title: response.data.message,
                          });
                          this.getTickets();
                          this.selected = [];
                          this.select_all = false;
                      })
                      .catch((error) => {
                          console.log(error);
                          this.$Progress.fail();
                          Toast.fire({
                              type: "error",
                              title: error.response.data.message,
                          });
                      });
              }
          });

    },
    multiSearch(target) {
      let typeSimple = false;
      this.queryParams.filters.forEach((item) => {
        if (item.type === "simple") {
          typeSimple = true;
        }
      });
      let found = false;
      if (typeSimple) {
        if (target == "number") {
          this.queryParams.filters.forEach((item) => {
            if (item.type === "simple" && item.name === "number") {
              if(this.search.number === null || this.search.number === "" ){
                 this.queryParams.filters = this.queryParams.filters.filter(items => items.name !== "number")
                 found = true;
              }
              else{
                item.text = this.search.number;
                found = true;
              }
            }
          });
          if (!found) {
            this.queryParams.filters.push({
              type: "simple",
              name: "number",
              text: this.search.number,
            });
          }
        } else if (target == "name") {
          this.queryParams.filters.forEach((item) => {
            if (item.type === "simple" && item.name === "name") {
              if(this.search.name === null || this.search.name === '' ){
                this.queryParams.filters = this.queryParams.filters.filter(items => items.name !== "name")
                found = true;
              }
              else{
                item.text = this.search.name;
                found = true;
              }
            }
          });
          if (!found) {
            this.queryParams.filters.push({
              type: "simple",
              name: "name",
              text: this.search.name,
            });
          }
        } else if (target == "owner") {
          this.queryParams.filters.forEach((item) => {
            if (item.type === "simple" && item.name === "owner.name") {
              if(this.search.owner === null || this.search.owner === "" ){
                this.queryParams.filters = this.queryParams.filters.filter(items => items.name !== "owner.name")
                found = true;
              }
              else{
                item.text = this.search.owner;
                found = true;
              }
            }
          });
          if (!found) {
            this.queryParams.filters.push({
              type: "simple",
              name: "owner.name",
              text: this.search.owner,
            });
          }
        }

        else if (target == "email") {
          this.queryParams.filters.forEach((item) => {
            if (item.type === "simple" && item.name === "owner.email") {
              if(this.search.owner === null || this.search.owner === "" ){
                this.queryParams.filters = this.queryParams.filters.filter(items => items.name !== "owner.email")
                found = true;
              }
              else{
                item.text = this.search.owner;
                found = true;
              }
            }
          });
          if (!found) {
            this.queryParams.filters.push({
              type: "simple",
              name: "owner.email",
              text: this.search.owner,
            });
          }
        }


        else if (target == "project") {
            this.queryParams.filters.forEach((item) => {
                if (item.type === "simple" && item.name === "project.name") {
                    if(this.search.project === null || this.search.project === "" ){
                      this.queryParams.filters = this.queryParams.filters.filter(items => items.name !== "project.name")
                      found = true;
                    }
                    else{
                        item.text = this.search.project;
                        found = true;
                    }
                }
            });
            if (!found) {
                this.queryParams.filters.push({
                    type: "simple",
                    name: "project.name",
                    text: this.search.project,
                });
            }
        }else if (target == "created_at") {
            this.queryParams.filters.forEach((item) => {
                if (item.type === "simple" && item.name === "created_at") {
                    if(this.search.created_at === null || this.search.created_at === "" ){
                      this.queryParams.filters = this.queryParams.filters.filter(items => items.name !== "created_at")
                      found = true;
                    }
                    else{
                      item.text = this.search.created_at;
                      found = true;
                    }
                }
            });
            if (!found) {
                this.queryParams.filters.push({
                    type: "simple",
                    name: "created_at",
                    text: this.search.created_at,
                });
            }
        }else if (target == "assigned_to") {
            this.queryParams.filters.forEach((item) => {
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
            });
            if (!found) {
                this.queryParams.filters.push({
                    type: "simple",
                    name: "assigned_to",
                    text: this.search.assigned_to,
                });
            }
        }
      } else {
        if (target == "number") {
          this.queryParams.filters.push({
            type: "simple",
            name: "number",
            text: this.search.number,
          });
        } else if (target == "name") {
          this.queryParams.filters.push({
            type: "simple",
            name: "name",
            text: this.search.name,
          });
        } else if (target == "owner") {
          this.queryParams.filters.push({
            type: "simple",
            name: "owner.name",
            text: this.search.owner,
          });
        } else if (target == "email") {
          this.queryParams.filters.push({
            type: "simple",
            name: "owner.email",
            text: this.search.email,
          });
        }else if (target == "project") {
            this.queryParams.filters.push({
                type: "simple",
                name: "project.name",
                text: this.search.project,
            });
        }else if (target == "created_at") {
            this.queryParams.filters.push({
                type: "simple",
                name: "created_at",
                text: this.search.created_at,
            });
        }else if (target == "assigned_to") {
            this.queryParams.filters.push({
                type: "simple",
                name: "assigned_to",
                text: this.search.assigned_to,
            });
        }
      }
      this.getTickets();
      found = false;
      typeSimple = false;
    },
    newModal() {
      this.editMode = false;
      this.form.reset();
      this.form.clear();
      if (this.singlePage) {
        if (this.form.project) {
          this.form.project = this.project;
          this.form.project_id = this.project.id;
        }
      }
        this.form.status_id = this.form.status.id;
        if (this.user.type == "client") {
        this.form.owner = { name: this.user.name, id: this.user.id };
        this.getProjectsByOwner(this.form.owner.id);
      }
      this.form.estimated_time = 0;
      $("#Modal").modal("show");
    },
    editModal(ticket) {
      this.editMode = true;
      this.form.reset();
      this.form.clear();
      if (ticket.owner[0]) {
        this.getProjectsByOwner(ticket.owner[0].id);
      }
      $("#Modal").modal("show");
      this.form.fill(ticket);
      if(ticket.estimated_time === null || ticket.estimated_time === 0){
        this.form.estimated_time = 0;
      }
      else{
        this.form.estimated_time = ticket.estimated_time
      }
      this.form.manager_id = this.form.manager.id;
      this.form.owner_id = this.form.owner.id;
      this.form.status_id = this.form.status.id;
      if (this.singlePage) {
        if (this.form.project) {
          this.form.project = this.project;
          this.form.project_id = this.project.id;
        }
      }
    },
    onPaginate(page) {
      this.$router.push({
        name: "tickets.list",
        params: {
          page: page,
        },
      });
    },
    getResults(page) {
      this.queryParams.page = page;
      this.getTickets();
    },
    filterTickets(filter) {
        if(filter === 'read'){
            this.read = !this.read;
            this.unread = false
        }
        if(filter === 'unread'){
            this.read = false;
            this.unread = !this.unread
        }
        if(filter === 'reply'){
            this.unreply = false;
            this.reply = !this.reply
        }
        if(filter === 'unreplied'){
            this.reply = false;
            this.unreply = !this.unreply
        }
      if (filter === "archive") {
          this.showingArchive = true;
      } else {
          this.showingArchive = false;
      }
        this.filter = filter;
        this.queryParams.global_search = "";
        this.queryParams.page = 1;
        let notFound = false;
        let remove = false;
        if( this.queryParams.filters.length > 0 ){
            this.queryParams.filters.forEach((item) => {
                if(item.selected_options){
                  if(filter ==='open' || filter ==='in-progress' || filter ==='pending' || filter ==='done' ){
                    if(item.selected_options[0] === 'open' || item.selected_options[0] === 'in-progress' ||item.selected_options[0] === 'pending' ||item.selected_options[0] === 'done'){
                      this.queryParams.filters.splice(this.queryParams.filters.indexOf(item), 1);
                    }
                }
                }
                if (item.type === "select" && item.selected_options[0] === filter) {
                    remove = true;
                }
                else{
                    notFound = true;
                }
            });
        }
        else{
            this.queryParams.filters.push({
                type: "select",
                name: "status.state",
                selected_options: [filter],
            });
        }

        if(notFound === true){
            this.queryParams.filters.push({
                type: "select",
                name: "status.state",
                selected_options: [filter],
            });
        }
        if(remove === true){
            this.queryParams.filters = this.queryParams.filters.filter( items => items.selected_options[0] !== filter)
      }
      this.getTickets();
    },
    findTickets(filter) {
        // document.getElementById("checking1").checked = false;
        // document.getElementById("checking2").checked = false;
        // document.getElementById("checking3").checked = false;
        // document.getElementById("checking4").checked = false;
        this.filter = filter;
        this.queryParams.global_search = "";
        this.queryParams.page = 1;
        this.queryParams.filters = [];
        this.queryParams.filters.push({
            type: "search",
            name: "status.state",
            selected_options: [filter],
        });
        if(filter === 'all'){
          this.clearQuery();
        }
        this.getTickets();
    },
    getTickets() {
      this.$Progress.start();
      if (this.queryParams.global_search != "") {
        $cookies.set(
          "ticket_search",
          this.queryParams.global_search,
          "90d",
          "/",
          process.env.VUE_APP_COOKIES_DOMAIN
        );
      } else {
        if ($cookies.isKey("ticket_search")) {
          $cookies.remove(
            "ticket_search",
            "/",
            process.env.VUE_APP_COOKIES_DOMAIN
          );
        }
      }
      if (this.$route.params.filter) {
        if (
          this.$route.params.filter == "all" ||
          this.$route.params.filter == "read" ||
          this.$route.params.filter == "unread" ||
          this.$route.params.filter == "reply" ||
          this.$route.params.filter == "unreplied" ||
          this.$route.params.filter == "due_today"
        ) {
          // this.queryParams.filters = [];
          this.queryParams.filters.push({
            type: "select",
            name: "status.state",
            selected_options: [this.$route.params.filter],
          });
        } else {
          // this.queryParams.filters = [];
          this.queryParams.filters.push({
            type: "select",
            name: "status.name",
            selected_options: [this.$route.params.filter],
          });
        }
      }
      this.tickets.data = null;
      this.$store
        .dispatch("ticket/getTickets", {
          queryParams: this.queryParams,
          page: this.queryParams.page,
          index: true,
          roles: true,
          tasks: true,
          dropdown: true,
        })
        .then((response) => {
          this.total_rows = response.data.data.total;
          this.mytickets = response.data.data;
          this.$Progress.finish();
          this.$route.params.filter = "";
          this.resultEmployees()
        })
        .catch((error) => {
          this.$Progress.fail();
        });
    },
    getProjectsByOwner(ownerId) {

      if(!ownerId){
        this.$store.commit('project/setProjectByOwners', []);
        this.$store.commit('project/setAllProjects', []);
        return false;
      }



      this.form.owner_id = ownerId;
      this.LoadOwnerProjects = true;
      // this.form.project = [];
      if (ownerId !== null && ownerId !== "") {
        this.$store
          .dispatch("project/getProjectsByOwner", ownerId)
          .then(() => {
            console.log(this.form);
            this.LoadOwnerProjects = false;
            this.$Progress.finish();
          })
          .catch((error) => {
                  console.log(error)
            this.$Progress.fail();
          });
      }
    },
      getAllProjects(project) {
        this.loading = true;

        this.$store
          .dispatch("project/getAllProjects",{dropdown:true,project})
          .then(() => {
            this.$Progress.finish();
            this.loading = false;
          })
          .catch((error) => {
            this.$Progress.fail();
          });
    },
    createTicket(data) {
      this.$Progress.start();
      data.manager_id = data.manager ? data.manager.map((man) => man.id) : null;
      data.owner_id = data.owner ? data.owner.map((own) => own.id) : null;
      // data.owner_id = data.owner.id;
      this.$store
        .dispatch("ticket/createTicket", data)
        .then((response) => {
          $("#Modal").modal("hide");
          this.$Progress.finish();
          this.getTickets();
          Toast.fire({
            type: "success",
            title: response.data.message,
          });
          this.tickets.total++;
        })
        .catch((error) => {
          this.$Progress.fail();
          if (error.response) {
            this.form.errors.errors = error.response.data.errors;
          }
        });
    },
    editTicket(data) {
      this.$Progress.start();
      this.form.manager_id = this.form.manager.map((man) => man.id);
      this.form.owner_id = this.form.owner.map((own) => own.id);
      if (this.form.project) {
        this.form.project_id = this.form.project.id;
      }
      this.$store
        .dispatch("ticket/editTicket", this.form)
        .then((response) => {
          $("#Modal").modal("hide");
          this.getTickets();
          this.$Progress.finish();
          Toast.fire({
            type: "success",
            title: response.data.message,
          });
        })
        .catch((error) => {
          this.$Progress.fail();
          if (error.response) {
            this.form.errors.errors = error.response.data.errors;
          }
        });
    },
    deleteTicket(id) {
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
      }).then((result) => {
        if (result.value) {
          this.$Progress.start();
          this.$store
            .dispatch("ticket/deleteTicket", id)
            .then((response) => {
              this.$Progress.finish();
              Toast.fire({
                type: "success",
                title: response.data.message,
              });
              this.tickets.total--;
            })
            .catch((error) => {
              console.log(error);
              this.$Progress.fail();
              Toast.fire({
                type: "error",
                title: error.response.data.message,
              });
            });
        }
      });
    },
    getTicketFiltered() {
      let data = {
        filter: "pending",
      };
      this.$store
        .dispatch("ticket/getTicketFiltered", data)
        .then((response) => {})
        .catch((error) => {
          console.log(error);
        });
    },
    sortTickets(sortType) {
      this.queryParams.page = 1;
      this.sortItem = sortType;
      this.queryParams.sort = [];
      if (this.sortOrder) {
        this.queryParams.sort.push({
          name: sortType,
          order: "asc",
        });
      } else {
        this.queryParams.sort.push({
          name: sortType,
          order: "desc",
        });
      }
      this.sortOrder = !this.sortOrder;
      this.getTickets();
    },
    getOwners() {
      this.$store
        .dispatch("owner/getOwners")
        .then(() => {})
        .catch((error) => {
          console.log(error);
        });
    },
    getStatus() {
      this.$store
        .dispatch("ticket/getStatus")
        .then((response) => {})
        .catch((error) => {
          console.log(error);
        });
    },
    getEmployees() {
      this.$store
        .dispatch("user/getRegularUsers", {
          roles: true,
          dropdown:true
        })
        .then(() => {})
        .catch(() => {});
    },
    statusColor(value) {
      if (value == "open") {
        return "#A0466F";
      }
      if (value == "pending") {
        return "#EEA24C";
      }
      if (value == "in-progress") {
        return "#3490DC";
      }
      if (value == "done") {
        return "#67A046";
      }
    },
    clearQuery() {
      this.queryParams = {
        sort: [],
        filters: [],
        global_search: "",
      };
      this.search.name = "";
      this.search.number = "";
      this.search.owner = "";
      this.search.email = "";
      this.search.created_at = "";
      this.search.project = "";
        this.search.assigned_to = "";
      this.getTickets();
    },
    showAssigned(id) {
      this.ticketID = id;
      this.showAssignedMangers = !this.showAssignedMangers;
    },
      resultEmployees() {
          this.assignedUsers= []
          this.assignedUsers.push('All')
          this.assignedUsers.push('Not Assigned')
          this.employees.forEach(employee => {
              this.assignedUsers.push(employee.name)
          });
      },
  },
  watch: {
    newManager(newValue) {
      this.form.manager_id = newValue;
    },
    newStatus(newValue) {
      this.form.status_id = newValue;
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
    if ($cookies.isKey("ticket_search")) {
      this.queryParams.global_search = $cookies.get("ticket_search");
    }
    this.getOwners();
    this.getEmployees();
  },
  mounted() {
    this.getTickets(this.$route.params.page || 1);
    this.getStatus();
    // this.getAllProjects();
  },
  beforeRouteUpdate(to, from, next) {
    this.getTickets(to.params.page);
    next();
  },
  computed: {
    ...mapGetters({
      tickets: "ticket/activeTickets",
      owners: "owner/activeOwners",
      projects: "project/projectByOwners",
      allProjects: "project/allProjects",
      status: "ticket/activeStatus",
      user: "user/activeSingleUser",
      employees: "user/getRegularUsers",
    }),
    TicketsCount() {
      if (this.tickets.data) {
        return this.tickets.data.length;
      }
    },
    newManager() {
      return this.form.manager ? this.form.manager.id : null;
    },
    newStatus() {
      return this.form.status ? this.form.status.id : null;
    },
    resultTickets() {
      return this.tickets.data;
    },
    today_date() {
      return moment().format("ddd MMM D YYYY");
    },
  },
  filters: {
    ticketDueDate: (value) => {
      return moment(value).fromNow();
    },
    ticketUpdatedAt: (value) => {
      return moment(value).format('DD.MM.YYYY hh:mm')
    },
    subStr: (value) => {
      return string.substring(0, 25) + "...";
    },
      subStrtitle: value => {
          if (value.length > 75) {
              return value.substring(0, 75) + "..";
          }
          return value;
      },
      subStrname: value => {
          if (value.length > 10) {
              return value.substring(0, 10) + "..";
          }
          return value;
      },
      subStrProject: value => {
          if (value.length > 11) {
              return value.substring(0, 11) + "...";
          }
          return value;
      },
      subStrProjectTable : value => {
        if (value.length > 8) {
            return value.substring(0, 8) + "..";
        }
        return value;
      },
      returnDoing : value => {
        if(value == 'in-progress'){
            return 'doing';
        }else {
            return value;
        }
      },
  },
  components: {
    VueBootstrap4Table,
    quillEditor,
    DatePicker,
    pagination,
    avatar,
    moment,
    createTicketComponent,
  },

};
</script>

<style scoped>
.col {
  padding-top: 0px;
  padding-bottom: 0px;
}
.stat {
  color: #ffffff;
  font-weight: bold;
  padding: 0.7rem 0.7rem 0px 0px;
  text-align: center;
  height: 2.8rem;
}
.stat-name {
  position: relative;
  margin-top: -1rem;
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
.v-slide-group {
  align-items: center;
}
.font-weight-black {
  color: black;
  font-weight: bold;
}
.no-rl-col {
  margin-left: 0px;
  margin-right: 0px;
}
.v-card__text {
  padding-left: 12px;
}

/*actions-btn-styling*/
.actions-btn-cont {
  height: 50%;
  border-radius: 15px;
  border: 1px solid #b8b8b8;
  background-color: #ffffff;
  padding: 0 1.2rem;
}

.actions-btn-cont .action {
  height: 50%;
  width: 100%;
}

.action .actionIcon i {
  margin-top: 25%;
  transform: translateY(50%);
  margin-left: 0.3rem;
}

.line {
  background-color: #b8b8b8;
  height: 0.06rem;
  width: 30%;
  color: #919191;
  position: absolute;
  margin-right: 50%;
  transform: translateX(-50%);
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
    box-shadow: 0 3px 1px -2px rgba(0, 0, 0, 0.2), 0 2px 2px 0 rgba(0, 0, 0, 0.14),
    0 1px 5px 0 rgba(0, 0, 0, 0.12);
}
/*end actions-btn-styling*/

/*ticket new styles*/

.ticket-name{
color: #000000;
font-weight: bold;
font-size: 1.3rem;
text-transform: capitalize;
}

.status_container{
    width: 7rem;
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

#reset-btn{
    font-size: 1rem;
    background-color: #A8557A;
    color: #ffffff;
    margin-top:1rem;
    border-radius:10px;
}

.actions-container{
    width: 1.8rem;
    height: 1.8rem;
    border-radius: 50%;
    background: linear-gradient(180deg, #DDB456 29.5%, #234fa3 100%);
    text-align: center;
    margin-left: 1rem;

}

.calender{
    color: #000000;
    text-decoration: none;
    font-weight: bold;
    font-size: 1.3rem;
    margin-left: .2rem;
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

.changeStatus{
    font-size: 1.3rem;
    margin-top:.2rem;
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
    font-size:.8rem;
    margin-left: .2rem;

}

/*end of filters new styles*/

</style>
