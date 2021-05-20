<template>
  <v-container fluid class="grey">
    <v-row>
      <v-col cols="12">
        <v-row class="mb-2">
          <v-col cols="10">
            <v-row class="mp-0">
              <v-col cols="2">
                <v-card
                  class="dropdown dropdown-toggle sort-btn"
                  data-toggle="dropdown"
                >
                  <span style="font-size: 1.1rem" class="btn"
                    >{{ $t('Ticket.sortTickets') }} </span
                  >
                  <div class="dropdown-menu">
                    <v-btn class="dropdown-item" @click="sortTickets('number')"
                      >{{ $t('Ticket.Number') }}</v-btn
                    >
                    <v-btn class="dropdown-item" @click="sortTickets('name')"
                      >{{ $t('Ticket.Name') }}</v-btn
                    >
                    <v-btn
                      class="dropdown-item"
                      @click="sortTickets('due_date')"
                      >{{ $t('Ticket.Duedate') }}</v-btn
                    >
                    <v-btn
                      class="dropdown-item"
                      @click="sortTickets('created_at')"
                      >{{ $t('Ticket.createdAt') }}</v-btn
                    >
                  </div>
                </v-card>
              </v-col>
              <v-col cols="2">
                <v-text-field
                  hide-details
                  solo
                  clear-icon="fas fa-clear"
                  clearable
                  label="Number"
                  type="text"
                  v-model="search.number"
                  @change="multiSearch('number')"
                ></v-text-field>
              </v-col>
              <v-col cols="3">
                <v-text-field
                  hide-details
                  solo
                  clear-icon="fas fa-clear"
                  clearable
                  label="Name"
                  type="text"
                  v-model="search.name"
                  @change="multiSearch('name')"
                ></v-text-field>
              </v-col>
              <v-col cols="3">
                <v-text-field
                  hide-details
                  solo
                  clear-icon="fas fa-clear"
                  clearable
                  label="Client"
                  type="text"
                  v-model="search.owner"
                  @change="multiSearch('owner')"
                ></v-text-field>
              </v-col>
              <v-col cols="1">
                <v-btn block @click="clearQuery">Reset Query</v-btn>
              </v-col>
            </v-row>
          </v-col>
          <v-col cols="2" class="text-left">
            <router-link
              :to="{ name: 'tickets.kanban' }"
              class="ml-1 text-muted float-right"
            >
              <i class="fab fa-trello fa-fw"></i>
            </router-link>
            <router-link to="/admin/calendar" class="nav-link text-secondary">
              <i class="fas fa-calendar-alt"></i> {{ today_date }}
            </router-link>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="10">
            <div v-if="!tickets.data">
              <v-sheet color="grey darken-2" class="px-3 pt-3 pb-3">
                <v-skeleton-loader class="mx-auto" max-width="300" type="card">
                </v-skeleton-loader>
              </v-sheet>
            </div>
            <template v-if="tickets.data">
              <v-tabs v-model="ticketstab">
                <v-tabs-slider></v-tabs-slider>
                <v-tab href="#ticketscards"> Card view </v-tab>
                <v-tab href="#ticketstable"> Table view </v-tab>
              </v-tabs>

              <br />
              Collections
              <br />
              <multiselect
                v-model="collection"
                :options="collections"
                :placeholder="$t('Ticket.selectOne')"
                label="collection"
                track-by="collection"
                @input="getTickets"
              ></multiselect>
              <router-link to="/admin/tickets/collections" class="nav-link">
                <i class="nav-icon fas fa-plus"></i>
                {{ $t("Ticket.newcollection") }}
              </router-link>

              <br />
              <v-tabs-items v-model="ticketstab">
                <v-tab-item value="ticketscards">
                  <v-card
                    elevation="1"
                    class="mb-1"
                    v-for="ticket in tickets.data"
                    :key="ticket.id"
                  >
                    <v-card-text>
                      <v-row>
                        <v-col cols="11">
                          <v-row>
                            <v-col cols="2" class="no-rl-col">
                              <v-row>
                                <v-col cols="12">
                                  <p class="mb-1">#{{ ticket.number }}</p>
                                </v-col>
                                <v-col
                                  class="col stat"
                                  :id="ticket.status.name"
                                >
                                  <span class="stat-name">{{
                                    ticket.status.name
                                  }}</span>
                                </v-col>
                              </v-row>
                            </v-col>
                            <v-col cols="3" class="no-rl-col">
                              <v-row>
                                <v-col cols="12">
                                  <span class="font-weight-black h6 ml-2">
                                    <router-link
                                      style="color: black"
                                      :to="{
                                        name: 'ticket',
                                        params: {
                                          id: ticket.id,
                                        },
                                      }"
                                      >{{ ticket.name }}</router-link
                                    >
                                  </span>
                                  <span class="ml-1">
                                    ({{ ticket.conversations - 1 }})
                                  </span>
                                  <div class="ml-1">
                                    <span
                                      v-if="ticket.read"
                                      style="color: #05579b"
                                    >
                                      Read -
                                    </span>
                                    <span v-else style="color: #981351">
                                      Unread -
                                    </span>
                                    <span
                                      v-if="ticket.reply"
                                      class="ml-1"
                                      style="color: #05579b"
                                    >
                                      replied
                                    </span>
                                    <span v-else style="color: #981351">
                                      Unreplied
                                    </span>
                                  </div>
                                </v-col>
                                <v-col class="col mt-3">
                                  <span class="ml-2 text-muted"
                                    ><span class="text-small">created :</span
                                    >{{
                                      ticket.created_at | ticketDueDate
                                    }}</span
                                  >
                                </v-col>
                              </v-row>
                            </v-col>
                            <v-col cols="2">
                              <p class="mb-3">Due date</p>
                              {{ ticket.due_date | ticketDueDate }}
                            </v-col>
                            <v-col cols="2">
                              <p class="ml-2 mb-1">Requester</p>
                              <div
                                style="display: inline-flex"
                                v-if="ticket.owner"
                              >
                                <avatar
                                  color="#90b0fa"
                                  :fullname="ticket.owner.name"
                                  :size="40"
                                ></avatar>
                                <router-link
                                  :to="'/admin/profile/' + ticket.owner.id"
                                  class="mt-2 ml-1 text-muted"
                                  >{{ ticket.owner.name }}</router-link
                                >
                              </div>
                              <div style="display: inline-flex" v-else>
                                No Owner
                              </div>
                            </v-col>
                            <v-col cols="2">
                              <p class="ml-2 mb-1">Ticket Manager</p>
                              <div
                                v-if="ticket.manager[0]"
                                style="
                                  display: inline-flex;
                                  width: max-content;
                                  position: relative;
                                "
                              >
                                <avatar
                                  color="#90b0fa"
                                  :fullname="ticket.manager[0].name"
                                  :size="40"
                                ></avatar>
                                <router-link
                                  :to="'/admin/profile/' + ticket.manager[0].id"
                                  class="mt-2 ml-1 small"
                                  style="color: #484848; max-width: 3rem"
                                  >{{ ticket.manager[0].name }}</router-link
                                >
                                <v-btn icon @click="showAssigned(ticket.id)">
                                  <i class="fa fa-ellipsis-v"></i>
                                </v-btn>
                                <div
                                  v-if="
                                    showAssignedMangers &&
                                    ticketID === ticket.id
                                  "
                                  class="toggle-menu-card"
                                >
                                  <div
                                    class="content"
                                    style="padding: 0 0.4rem"
                                  >
                                    <ul
                                      style="
                                        list-style: none;
                                        text-align: center;
                                        margin-top: 0.5rem;
                                      "
                                      class="toggle-div"
                                      v-for="manager in ticket.manager"
                                      :key="manager.id"
                                    >
                                      <li
                                        style="
                                          display: inline-flex;
                                          width: max-content;
                                        "
                                      >
                                        <avatar
                                          color="#90b0fa"
                                          :fullname="manager.name"
                                          :size="30"
                                        ></avatar>
                                        <router-link
                                          :to="'/admin/profile/' + manager.id"
                                          style="margin-left: 0.5rem"
                                          >{{ manager.name }}</router-link
                                        >
                                      </li>
                                    </ul>
                                  </div>
                                </div>
                              </div>
                              <div v-else>No Manager</div>
                            </v-col>
                            <v-col cols="1">
                              <p>Project</p>
                              <div
                                v-if="!ticket.project"
                                style="margin-top: 0.8rem"
                              >
                                -------
                              </div>
                              <div
                                v-if="ticket.project"
                                style="margin-top: 0.8rem"
                              >
                                <router-link
                                  :to="'/admin/project/' + ticket.project.id"
                                  class="mt-2 text-muted"
                                  style="
                                    transform: translateX(-25%);
                                    margin-top: 1.2rem;
                                  "
                                  >{{ ticket.project.name }}</router-link
                                >
                              </div>
                            </v-col>
                          </v-row>
                        </v-col>
                        <v-col cols="1">
                          <div
                            class="actions-btn-cont"
                            style="margin-left: 3rem; height: 100%"
                          >
                            <div class="row action">
                              <div class="edit actionIcon">
                                <a href="#" @click="editModal(ticket)">
                                  <i
                                    style="color: #eea24c"
                                    class="fa fa-edit"
                                  ></i>
                                </a>
                              </div>
                            </div>
                            <div class="line"></div>
                            <div class="row action">
                              <div class="trash actionIcon">
                                <a href="#" @click="deleteTicket(ticket.id)">
                                  <i
                                    style="color: #985070"
                                    class="fa fa-trash"
                                  ></i>
                                </a>
                              </div>
                            </div>
                          </div>
                        </v-col>
                      </v-row>
                    </v-card-text>
                  </v-card>
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
                  <vue-bootstrap4-table
                    :rows="tickets.data"
                    :columns="columns"
                    :config="config"
                    @on-change-query="onChangeQuery"
                    :total-rows="total_rows"
                    :classes="classes"
                    @on-download="onChangeQuery"
                  >
                    <template slot="sort-asc-icon">
                      <i class="fas fa-sort-up"></i>
                    </template>
                    <template slot="sort-desc-icon">
                      <i class="fas fa-sort-down"></i>
                    </template>
                    <template slot="no-sort-icon">
                      <i class="fas fa-sort"></i>
                    </template>

                    <template slot="name" slot-scope="props">
                      <router-link
                        :to="{
                          name: 'ticket',
                          params: { id: props.row.id },
                        }"
                        >{{ props.cell_value }}</router-link
                      >
                    </template>
                    <template slot="status.name" slot-scope="props">
                      {{ props.cell_value }}
                    </template>

                    <template slot="owner.name" slot-scope="props">
                      {{ props.row.owner ? props.row.owner.name : "" }}
                    </template>

                    <template slot="created_at" slot-scope="props">{{
                      props.cell_value | DateOnly
                    }}</template>
                    <template slot="due_date" slot-scope="props">{{
                      props.cell_value | ticketDueDate
                    }}</template>
                    <template slot="read" slot-scope="props">
                      <i
                        v-if="!props.cell_value"
                        data-widget="collapse"
                        data-toggle="tooltip"
                        title="Not Reed"
                        class="text-danger icon fas fa-exclamation-triangle"
                      ></i>
                      <i
                        v-else
                        data-widget="collapse"
                        data-toggle="tooltip"
                        title="Reed"
                        class="text-success icon fas fa-check"
                      ></i>
                    </template>
                    <template slot="reply" slot-scope="props">
                      <i
                        data-widget="collapse"
                        data-toggle="tooltip"
                        title="Not Replyed"
                        v-if="!props.cell_value"
                        class="text-danger icon fas fa-reply"
                      ></i>
                      <i
                        v-else
                        data-widget="collapse"
                        data-toggle="tooltip"
                        title="Replyed"
                        class="text-success icon fas fa-reply"
                      ></i>
                    </template>
                    <template slot="action-buttons" slot-scope="props">
                      <a
                        href="#"
                        @click="editModal(props.row)"
                        class="btn btn-primary btn-xs"
                      >
                        <i class="fas fa-edit fa-fw"></i>
                      </a>
                      <a
                        href="#"
                        @click="deleteTicket(props.row.id)"
                        class="btn btn-danger btn-xs"
                      >
                        <i class="fas fa-trash fa-fw"></i>
                      </a>
                    </template>
                  </vue-bootstrap4-table>
                </v-tab-item>
              </v-tabs-items>
            </template>
          </v-col>
          <v-col cols="2">
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="queryParams.global_search"
                  append-icon="fas fa-search"
                  outlined
                  shaped
                  dense
                  hide-details
                  label="Search"
                  type="text"
                  @click:append="getTickets"
                  v-on:keyup.enter="getTickets"
                ></v-text-field>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12">
                <v-card class="text-muted">
                  <v-card-title v-if="tickets.total">
                    {{ tickets.total }} Tickets
                  </v-card-title>
                  <v-card-title v-else> 0 Tickets </v-card-title>
                </v-card>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12">
                <v-card>
                  <v-tabs v-model="tab" grow>
                    <v-tab href="#filter" grow right>
                      <v-icon>fas fa-filter</v-icon>
                    </v-tab>
                  </v-tabs>
                  <v-tabs-items v-model="tab">
                    <v-tab-item value="filter">
                      <v-card flat>
                        <v-card-text>
                          <v-btn @click="filterTickets('myTickets')" block text
                            >{{ $t('Ticket.MyTickets') }}</v-btn
                          >
                          <v-btn @click="filterTickets('all')" block text
                            >{{ $t('Ticket.All') }}</v-btn
                          >
                          <v-btn @click="filterTickets('open')" block text
                            >{{ $t('Ticket.open') }}</v-btn
                          >
                          <v-btn @click="filterTickets('pending')" block text
                            >{{ $t('Ticket.pending') }}</v-btn
                          >
                          <v-btn
                            @click="filterTickets('in-progress')"
                            block
                            text
                            >{{ $t('Ticket.In-Progress') }}</v-btn
                          >
                          <v-btn @click="filterTickets('done')" block text
                            >{{ $t('Ticket.done') }}</v-btn
                          >
                          <v-btn @click="filterTickets('read')" block text
                            >{{ $t('Ticket.Read') }}</v-btn
                          >
                          <v-btn @click="filterTickets('unread')" block text
                            >{{ $t('Ticket.NotRead') }}</v-btn
                          >
                          <v-btn @click="filterTickets('reply')" block text
                            >{{ $t('Ticket.Replyed') }}</v-btn
                          >
                          <v-btn @click="filterTickets('unreplied')" block text
                            >{{ $t('Ticket.NotReplyed') }}</v-btn
                          >
                          <v-btn @click="filterTickets('unproject')" block text
                            >{{ $t('Ticket.NoProject') }}</v-btn
                          >
                          <v-btn @click="filterTickets('withTask')" block text
                            >{{ $t('Ticket.TicketsWithTasks') }}</v-btn
                          >
                          <v-btn
                            @click="filterTickets('withoutTask')"
                            block
                            text
                            >{{ $t('Ticket.TicketsWithoutTasks') }}</v-btn
                          >
                        </v-card-text>
                      </v-card>
                    </v-tab-item>
                  </v-tabs-items>
                </v-card>
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
              {{ $t("Ticket.createNew") }}
              {{ $t("Ticket.ticket") }}
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
                </label>
                <input
                  v-model="form.name"
                  type="text"
                  name="name"
                  class="form-control"
                  :class="{
                    'is-invalid': form.errors.has('name'),
                  }"
                />
                <has-error :form="form" field="name"></has-error>
              </div>
              <div v-if="!editMode" class="form-group">
                <label for="description">
                  {{ $t("Ticket.ticketDesc") }}
                </label>
                <quill-editor
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
                  :placeholder="$t('Ticket.selectOne')"
                  label="name"
                  track-by="name"
                ></multiselect>

                <has-error :form="form" field="status_id"></has-error>
              </div>
              <div class="form-group" v-if="user.type == 'regular-user'">
                <label for="name">
                  {{ $t("Ticket.client") }}
                </label>
                <multiselect
                  v-model="form.owner"
                  :options="owners"
                  @input="getProjectsByOwner(form.owner.id)"
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
              </div>
              <div class="form-group" v-if="form.owner">
                <label for="name">
                  {{ $t("Ticket.project") }}
                </label>
                <multiselect
                  v-model="form.project"
                  :options="projects"
                  :close-on-select="false"
                  :clear-on-select="true"
                  :preserve-search="true"
                  :placeholder="$t('Ticket.selectOne')"
                  label="name"
                ></multiselect>
                <has-error :form="form" field="project_id"></has-error>
              </div>
              <div class="form-group">
                <label for="name"> Responsible (Manager) </label>
                <multiselect
                  v-model="form.manager"
                  :multiple="true"
                  :options="employees.data"
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
            </form>
          </div>

          <div class="modal-footer">
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
      showAssignedMangers: false,
      ticketID: "",
      tab: "filter",
      collection: null,
      collections: [],
      ticketstab: "ticketstable",
      inject: ["theme"],
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
        owner: "",
        project: {
          id: "",
          name: "",
        },
        status: {},
        manager: null,
        manager_id: "",
        project_id: "",
        owner_id: "",
        status_id: "",
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
          name: "owner.name",
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
          label: this.$t("Ticket.collection") + "#",
          name: "collection",
          sort: true,
          row_text_alignment: "text-left",
        },
        {
          label: this.$t("Ticket.createdAt"),
          name: "created_at",
          sort: true,
        },
        {
          label: this.$t('Ticket.Duedate') ,
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
    };
  },
  methods: {
    onChangeQuery(queryParams) {
      this.queryParams = queryParams;

      if (this.filter != "" && this.filter != "all") {
        this.queryParams.global_search = this.filter;
      } else {
        this.queryParams.global_search = "";
      }

      this.getTickets();
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
              item.text = this.search.number;
              found = true;
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
              item.text = this.search.name;
              found = true;
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
              item.text = this.search.owner;
              found = true;
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
        }
      }
      this.getTickets();
      found = false;
      typeSimple = false;
    },
    editModal(ticket) {
      this.editMode = true;
      this.form.reset();
      this.form.clear();
      if (ticket.owner) {
        this.getProjectsByOwner(ticket.owner.id);
      }
      $("#Modal").modal("show");
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
      this.filter = filter;
      this.queryParams.global_search = "";
      this.queryParams.page = 1;
      let typeSelect = false;
      this.queryParams.filters.forEach((item) => {
        if (item.type === "select") {
          typeSelect = true;
        }
      });
      if (typeSelect) {
        this.queryParams.filters.forEach((item) => {
          if (item.type === "select") {
            item.selected_options = [filter];
          }
        });
      } else {
        this.queryParams.filters.push({
          type: "select",
          name: "status.state",
          selected_options: [filter],
        });
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
      this.$store
        .dispatch("ticket/getTickets", {
          queryParams: this.queryParams,
          page: this.queryParams.page,
          index: true,
          roles: true,
          tasks: true,
          filtered: true,
          collection: this.collection ? this.collection.collection : null,
        })
        .then((response) => {
          this.total_rows = response.data.data.total;
          this.mytickets = response.data.data;
          this.$Progress.finish();
          this.$route.params.filter = "";
        })
        .catch((error) => {
          console.log(error);
          this.$Progress.fail();
        });
    },
    getProjectsByOwner(ownerId) {
      this.form.owner_id = ownerId;
      this.form.project = [];
      if (ownerId !== null && ownerId !== "") {
        this.$store
          .dispatch("project/getProjectsByOwner", ownerId)
          .then(() => {
            this.$Progress.finish();
          })
          .catch((error) => {
            this.$Progress.fail();
          });
      }
    },
    createTicket(data) {
      this.$Progress.start();
      data.manager_id = data.manager ? data.manager.map((man) => man.id) : null;
      data.owner_id = data.owner ? data.owner.map((own) => own.id) : null;

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
        .dispatch("user/getEmployees", {
          roles: true,
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
      this.getTickets();
    },
    showAssigned(id) {
      this.ticketID = id;
      this.showAssignedMangers = !this.showAssignedMangers;
    },
    getTicketCollection() {
      this.$Progress.start();
      this.$store
        .dispatch("ticket/getTicketCollection", this.form)
        .then((response) => {
          this.collections = response.data.data.data;
          this.collections.unshift([]);
          this.$Progress.finish();
        })
        .catch((error) => {
          this.$Progress.fail();
          if (error.response) {
            this.form.errors.errors = error.response.data.errors;
          }
        });
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
    this.getTicketCollection();
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
      status: "ticket/activeStatus",
      user: "user/activeSingleUser",
      employees: "user/getEmployees",
    }),
    TicketsCount() {
      if (this.tickets.data) {
        return this.tickets.data.length;
      }
    },
    newManager() {
      return this.form.manager.id;
    },
    newStatus() {
      return this.form.status.id;
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
    subStr: (value) => {
      return string.substring(0, 25) + "...";
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
</style>
