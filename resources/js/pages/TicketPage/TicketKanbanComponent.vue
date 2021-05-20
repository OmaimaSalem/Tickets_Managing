<template>
  <div class="row justify-content-center">
    <div class="col-12">
      <div class="row p-2">
        <div class="col-12">
          <h3 class="card-title">{{ $t("Ticket.ticketsTable") }}</h3>

          <div class="card-tools float-right">
            <router-link :to="{ name: 'tickets.list' }" class="btn btn-info btn-sm">
              <i class="fas fa-list fa-fw"></i>
              {{ $t("ticket.list") }}
            </router-link>
          </div>
        </div>

        <div class="container">
            <form @submit.prevent="SearchTasks" class="row">
            <div class="col-md-1 mr-1">
             <label>
                {{ $t("Ticket.Number") }}
              </label>
                <input
                  v-model="ticketSearchForm.number"
                  type="text"
                  name="ticket_id"
                  class="form-control"
                />
            </div>

              <div class="mr-1 col-md-2">
              <label>
                {{ $t("Ticket.title") }}
                <br />
              </label>
                <input
                  class="form-control"
                  v-model="ticketSearchForm.title"
                  type="text"
                  name="ticket_title"
                />
              </div>



            <div class="mr-1 col-md-2">
              <label>
                Status
                <br />
              </label>

                <select                     
                @input="getKanbanTickets($event)"
                key="" class="form-control" v-model="ticketSearchForm.status">
                  <option></option>
                  <option value="1">{{ $t("Ticket.Open") }}</option>
                  <option value="3">{{ $t("Ticket.In-Progress") }}</option>
                  <option value="1,3">{{ $t("Ticket.OpenANDIn-progress") }}</option>
                  <option value="4">{{ $t("Ticket.done") }}</option>
                  <option value="2">{{ $t("Ticket.pending") }}</option>
                </select>
            </div>


            <div class="mr-1 col-md-2">
              <label>
               {{ $t("Ticket.client") }} 
                <br />
              </label>

                 <multiselect
                    v-model="ticketSearchForm.client"
                    :options="clients"
                    :close-on-select="true"
                    :clear-on-select="false"
                    :preserve-search="true"
                    :preselect-first="false"
                    placeholder="Clients"
                    style="border-radius: 10px;height: 4rem"
                    label="name"
                    track-by="id"
                    @input="getKanbanTickets($event)"
                  ></multiselect>
              </div>

          <div class="mr-1 col-md-2">
              <label>
                {{ $t("Ticket.project") }}
                <br />
              </label>

                 <multiselect
                    v-model="ticketSearchForm.project"
                    :options="projects"
                    :close-on-select="true"
                    :clear-on-select="false"
                    :preserve-search="true"
                    :preselect-first="false"
                    placeholder="Projects"
                    style="border-radius: 10px;height: 4rem"
                    label="name"
                    track-by="id"
                    @input="getKanbanTickets($event)"
                  ></multiselect>
              </div>


              <div class="mr-1 col-md-2">
               <label>
               {{ $t("Time.employee") }}
                <br />
              </label>
                <multiselect
                    v-model="ticketSearchForm.user"
                    :options="users"
                    :close-on-select="true"
                    :clear-on-select="false"
                    :preserve-search="true"
                    :preselect-first="false"
                    placeholder="Employees"
                    style="border-radius: 10px;height: 4rem"
                    label="name"
                    track-by="id"
                    @input="getKanbanTickets($event)"
                  ></multiselect>
              </div>
                <!-- <select class="form-control" v-model="ticketSearchForm.user">
                  <option></option>

                  <option v-for="user of users" :key="user.id" :value="user.id">{{ user.name }}</option>
                </select> -->

              <label>
                <input type="submit" value="search" class="btn btn-sm btn-primary" />
              </label>


            </form>

        </div>



        <!-- /.card-header -->
        <div class="card-body table-responsive p-0">
          <div id="app">
            <div class="flex justify-center">
              <div class="row" v-if="kanban.columns">
                <div
                  v-for="(column, cindex) in kanban.columns"
                  :key="column.title"
                  class="kanban-column box box-solid col-md-3 col-12"
                >
                  <div class="wrapper">
                    <div class="box-header with-border clearfix">
                      <!-- <i class="fas fa-spinner float-left mr-1"></i> -->
                      <h3 class="box-title float-left">{{ column.title }}</h3>
                    </div>
                    <div class="card-header p-2">
                    <input
                      type="text"
                      class="text-white block p-0 col-12 bg-transparent border-0"
                      placeholder="+ Enter new ticket"
                      @keyup.enter="createKanbanTicket($event, cindex)"
                    />
                  </div>

                    <draggable
                      :list="column.tickets"
                      :animation="200"
                      ghost-class="box-body"
                      group="tasks"
                      handle=".cart-handle"
                      @change="MoveCart($event, cindex)"
                    >
                      <ticket-card
                        v-for="(ticket,
                                            rindex) in column.tickets"
                        :key="ticket.id"
                        :ticket="ticket"
                        :col_index="cindex"
                        :row_index="rindex"
                        class="mt-3 cursor-move"
                        @edit_ticket_event="
                                                show_modal(
                                                    ticket,
                                                    cindex,
                                                    rindex
                                                )
                                            "
                        @change_assigns_panel="
                                                show_assign_modal(ticket)
                                            "
                        @move_panel="
                                                show_move_modal(ticket)
                                            "
                        @reload_tickets="reload_tickets"
                        @change_due_date="
                                                change_due_date(ticket)
                                            "
                        @delete_ticket="
                                                delete_ticket(ticket.id)
                                            "
                      ></ticket-card>
                    </draggable>
                  </div>
                  <!-- .wrapper -->
                </div>
              </div>
            </div>

            <div
              class="modal edit-modal fade bd-example-modal-lg"
              tabindex="-1"
              role="dialog"
              aria-labelledby="myLargeModalLabel"
              aria-hidden="true"
            >
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <form method="post" v-if="edited_ticket">
                    <div class="form-group col-12">
                      <label for="ticket-name">Name</label>
                      <input
                        class="form-control"
                        type="text"
                        name="name"
                        v-model="edited_ticket.name"
                        id="ticket-name"
                      />
                    </div>

                    <div class="form-group col-12">
                      <label for="description">{{ $t("Ticket.ticketDesc") }}</label>
                      <quill-editor
                        v-model="
                                                    edited_ticket.description
                                                "
                        ref="myQuillEditor"
                        :options="editorOption"
                      ></quill-editor>
                    </div>

                    <div class="form-group col-12">
                      <label for="ticket-name">Status</label>
                      <select
                        class="form-control"
                        v-model="edited_ticket.status_id"
                      >
                        <option value="1">{{ $t("Ticket.Open") }}</option>
                        <option value="2">{{ $t("Ticket.pending") }}</option>
                        <option value="3">{{ $t("Ticket.In-Progress") }}</option>
                        <option value="4">{{ $t("Ticket.done") }}</option>

                      </select>
                    </div>

                    <div class="form-group col-12">
                      <label for="ticket-name">  {{ $t("Ticket.client") }}</label>
                      <select multiple="multiple" class="form-control" v-model="edited_ticket.client">
                        <option
                          v-for="(client,index) of clients"
                          :key="index"
                          :value="client.id"
                        >{{ client.name }}</option>
                      </select>
                    </div>

                    <div class="form-group col-12">
                      <label for="ticket-name">  {{ $t("Ticket.project") }}</label>
                      <select class="form-control" v-model="edited_ticket.project_id">
                        <option
                          v-for="(project,
                                                    index) of projects"
                          :key="index"
                          :value="project.id"
                        >{{ project.name }}</option>
                      </select>
                    </div>

                    <div class="form-group col-12">
                      <label for="ticket-name">  {{ $t("Ticket.assigns") }}</label>

                      <select class="form-control select2" v-model="edited_ticket.assigns" multiple>
                        <option
                          v-for="(user,
                                                    index) of users"
                          :key="index"
                          :value="user.id"
                        >{{ user.name }}</option>
                      </select>
                    </div>
                  </form>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" @click="save_ticket">{{ $t("Vacation.saveChanges") }} </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ $t("Vacation.close") }} </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- start-of-assigns-modal -->
            <div
              class="assigns-modal modal fade bd-example-modal-sm"
              tabindex="-1"
              role="dialog"
              aria-labelledby="mySmallModalLabel"
              aria-hidden="true"
            >
              <div class="modal-dialog modal-sm">
                <div class="modal-content">
                  <div class="form-group col-12">
                    <label for="ticket-name">{{ $t("Ticket.project") }}</label>

                    <select class="form-control select2" multiple v-model="assigns">
                      <option
                        v-for="(user, index) of users"
                        :key="index"
                        :value="user.id"
                      >{{ user.name }}</option>
                    </select>

                    <button class="btn btn-primary float-right" @click="save_assign_modal">{{ $t("Project.save") }} </button>
                  </div>
                </div>
              </div>
            </div>
            <!-- end-of-assigns-modal -->

            <!-- start-of-move-modal -->
            <div
              class="move-modal modal fade bd-example-modal-sm"
              tabindex="-1"
              role="dialog"
              aria-labelledby="mySmallModalLabel"
              aria-hidden="true"
            >
              <div class="modal-dialog modal-sm">
                <div class="modal-content">
                  <div class="form-group col-12">
                    <label for="ticket-name">{{ $t("Ticket.move") }}</label>
                    <select class="form-control" v-model="status">
                        <option value="1">{{ $t("Ticket.Open") }}</option>
                        <option value="2">{{ $t("Ticket.pending") }}</option>
                        <option value="3">{{ $t("Ticket.In-Progress") }}</option>
                        <option value="4">{{ $t("Ticket.done") }}</option>
                    </select>

                    <button class="btn btn-primary float-right" @click="save_move_modal">{{$t('Ticket.save')}}</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- end-of-move-modal -->

            <!-- start-of-due-date-modal -->
            <div
              class="due-date-modal modal fade bd-example-modal-sm"
              tabindex="-1"
              role="dialog"
              aria-labelledby="mySmallModalLabel"
              aria-hidden="true"
            >
              <div class="modal-dialog modal-sm">
                <div class="modal-content">
                  <div class="form-group col-12">
                    <label for="ticket-name">{{$t('Ticket.ChangeDueDate')}}</label>
                    <div class="row">
                      <div class="col-md-6">
                        <label>{{ $t('Wiki.date')}}</label>
                        <date-picker
                          v-model="ticket_date"
                          lang="en"
                          type="date"
                          format="DD-MM-YYYY HH:mm"
                          value-type="format"
                          input-class="form-control"
                        ></date-picker>
                      </div>
                      <div class="col-md-6">
                        <label>{{ $t('Client.time')}}</label>
                        <date-picker
                          v-model="ticket_time"
                          lang="en"
                          type="time"
                          format="HH:mm"
                          value-type="format"
                          input-class="form-control"
                          :minute-step="minuteStep"
                        ></date-picker>
                      </div>
                    </div>
                    <br />
                    <button class="btn btn-primary float-right" @click="save_due_date">{{$t('Ticket.save')}}</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- end-of-due-date-modal -->
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import draggable from "vuedraggable";
import { mapGetters } from "vuex";
import DatePicker from "vue2-datepicker";
import moment from "moment";
import { quillEditor,Quill } from "vue-quill-editor";
// require styles
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";

import TicketCard from "../../components/ticketsKanban/TicketCart";

import { ImageDrop } from 'quill-image-drop-module'

Quill.register('modules/imageDrop', ImageDrop)

export default {
  components: {
    TicketCard,
    draggable,
    DatePicker,
    quillEditor
  },
  data() {
    return {
      editorOption: {
        modules: {
          toolbar: [
            ["bold", "italic", "underline", "strike"],
            ["blockquote", "code-block"],
            [{ list: "ordered" }, { list: "bullet" }]
          ]
          ,        
          imageDrop: true
        },
      },
      edited_ticket: null,
      ticket: null,
      col_index: null,
      row_index: null,
      ticket_date: null,
      ticket_time: null,
      ticketSearchForm: {},
      page: 1,
      assigns: [],
      status: null,
      minuteStep: 1,
      queryParams: {
        sort: [],
        filters: [],
        global_search: "",
        per_page: 15,
        page: 1
      }
    };
  },
  methods: {
    createKanbanTicket(event,$columnindex){
      this.$Progress.start();
        this.$store
          .dispatch("ticket/createKanbanTicket", {
            name:event.target.value,
            status_id:$columnindex+1,
            kanban:true
          })
          .then(response => {
            this.$Progress.finish();
            Toast.fire({
              type: "success",
              title: response.data.message
            });
            event.target.value = '';
          })
          .catch(error => {
            this.$Progress.fail();
            if (error.response) {
              this.form.errors.errors = error.response.data.errors;
            }
          });
    },
    MoveCart(evt, index) {
      let status = [
        { id: 1, name: "open" },
        { id: 2, name: "pending" },
        { id: 3, name: "in-progress" },
        { id: 4, name: "done" }
      ];
      if (evt.added) {
        evt.added.element.order = true;

        let direction = evt.added.oldIndex > evt.added.newIndex ? "up" : "down";
        let newIndex =
          evt.added.newIndex == 0 ? evt.added.newIndex : evt.added.newIndex - 1;
        evt.added.element.status_id = status[index].id;
        evt.added.element.direction = direction;
        evt.added.element.target = this.kanban.columns[index].tickets[
          newIndex
        ].id;

        this.$Progress.start();
        this.$store
          .dispatch("ticket/moveKanbanTicket", evt.added.element)
          .then(response => {
            this.$Progress.finish();
            evt.added.element.status = status[index];
            evt.added.element.kanban = true;

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
      } else if (evt.moved) {
        evt.moved.element.order = true;

        let direction = evt.moved.oldIndex > evt.moved.newIndex ? "up" : "down";
        let newIndex =
          evt.moved.newIndex == 0 ? evt.moved.newIndex : evt.moved.newIndex - 1;
        evt.moved.element.status_id = status[index].id;
        evt.moved.element.direction = direction;
        evt.moved.element.target = this.kanban.columns[index].tickets[
          newIndex
        ].id;
        evt.added.element.kanban = true;


        this.$Progress.start();
        this.$store
          .dispatch("ticket/editTicket", evt.moved.element)
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
      }
    },
    show_modal(ticket, col_index, row_index) {
      this.$Progress.start();

      this.col_index = col_index;
      this.row_index = row_index;
      this.ticket = ticket;

      this.$store
        .dispatch("ticket/getTicketDescription", ticket.id)
        .then(response => {
          this.$Progress.finish();
          this.edited_ticket = {
            id: ticket.id,
            name: ticket.name,
            status_id: ticket.status.id,
            client: ticket.owner.map(own => own.id),
            project_id: ticket.project ? ticket.project.id : null,
            assigns: ticket.assigns.map(assign => assign.id),
            description: response.data.data.description
          };
          $(".edit-modal").modal("show");
        })
        .catch(error => {});
    },
    delete_ticket(id) {
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
            .dispatch("ticket/deleteKanbanTicket", id)
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

    show_assign_modal(ticket) {
      this.assigns = ticket.assigns.map(assign => assign.id);
      this.edited_ticket = {
        id: ticket.id,
        assigns: this.assigns
      };
      $(".assigns-modal").modal("show");
    },
    show_move_modal(ticket) {
      this.status = ticket.status.id;
      this.edited_ticket = {
        id: ticket.id,
        status_id: ticket.status.id,
        assigns: ticket.assigns,
        direction: true,
      };
      $(".move-modal").modal("show");
    },
    change_due_date(ticket) {
      this.ticket = ticket;

      let datetime = new Date(ticket.due_date);
      let date =
        datetime.getFullYear() +
        "-" +
        this.padNumber(datetime.getMonth() + 1) +
        "-" +
        this.padNumber(datetime.getDate());

      let time =
        this.padNumber(datetime.getHours()) +
        ":" +
        this.padNumber(datetime.getMinutes());
      this.ticket_date = date;
      this.ticket_time = time;
      $(".due-date-modal").modal("show");
    },
    padNumber(number) {
      return number > 9 ? number : number === 0 ? "00" : "0" + number;
    },
    save_due_date() {
      this.$Progress.start();
      this.$store
        .dispatch("ticket/editTicketDueDate", {
          ticket_id: this.ticket.id,
          ticket_date: this.ticket_date,
          ticket_time: this.ticket_time,
          kanban:true
        })
        .then(response => {
          $(".modal").modal("hide");
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
    save_move_modal() {
      this.edited_ticket.status_id = this.status;
      this.save_ticket();
      $(".move-modal").modal("hide");
    },
    save_assign_modal() {
      this.edited_ticket.assigns = this.assigns;
      this.save_ticket();
      $(".assigns-modal").modal("hide");
    },

    save_ticket() {
      this.edited_ticket.kanban = true;
      this.$Progress.start();
      this.$store
        .dispatch("ticket/editKanbanTicket", this.edited_ticket)
        .then(response => {
          $(".modal").modal("hide");
          this.$Progress.finish();
          Toast.fire({
            type: "success",
            title: response.data.message
          });
        })
        .catch(error => {
          console.log(error);
          this.$Progress.fail();
          if (error.response) {
            this.form.errors.errors = error.response.data.errors;
          }
        });
    },
    getKanbanTickets(e) {

      if(e){
        this.page = 1;
      }




      this.$Progress.start();


      let $this = this;
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

      // if(this.ticketSearchForm.client)
      // {
      //   this.ticketSearchForm.client = this.ticketSearchForm.client.id;
      // }

      // if(this.ticketSearchForm.project)
      // {
      //   this.ticketSearchForm.project = this.ticketSearchForm.project.id;
      // }


      // if(this.ticketSearchForm.user)
      // {
      //   this.ticketSearchForm.user = this.ticketSearchForm.user.id;
      // }




        this.$store
        .dispatch("ticket/getkanbanTickets", {
          queryParams: this.queryParams,
          page: $this.page,
          index: true,
          kanban: true,
          params: this.ticketSearchForm,
        })
        .then(response => {
          this.total_rows = response.data.total;
          this.$Progress.finish();
        })
        .catch(error => {
          this.$Progress.fail();
        });
    },
    getUsers() {
      this.$store
        .dispatch("ticket/getUsers")
        .then()
        .catch(error => {
          console.log(error);
        });
    },
    getClients() {
      this.$store
        .dispatch("ticket/getClients")
        .then()
        .catch(error => {
          console.log(error);
        });
    },
    getProjects() {
      this.$store
        .dispatch("ticket/getProjects")
        .then()
        .catch(error => {
          console.log(error);
        });
    },
    reload_tickets() {
      this.getKanbanTickets();
    },
    scroll() {
      window.onscroll = () => {
        let bottomOfWindow =
          Math.max(
            window.pageYOffset,
            document.documentElement.scrollTop,
            document.body.scrollTop
          ) +
            window.innerHeight ===
          document.documentElement.offsetHeight;

        if (bottomOfWindow) {
          if (parseInt(this.kanban.current_page) < this.kanban.pages) {
            this.page++;
            this.getKanbanTickets();
          }
        }
      };
    },
    SearchTasks() {
      this.page = 1;
      this.getKanbanTickets();
    }
  },
  mounted() {
    this.getKanbanTickets();
    this.getUsers();
    this.getClients();
    this.getProjects();
    this.scroll();
  },
  watch:{
    ticketSearchForm(value){
      this.page = 1;
    }
  }
  ,
  computed: {
    ...mapGetters({
      kanban: "ticket/getkanbanTickets",
      users: "ticket/getUsers",
      clients: "ticket/getClients",
      projects: "ticket/getProjects"
    })
  }
};
</script>

<style scoped>
.text-white::placeholder{
  color:#fff;
}
.column-width {
}
/* Unfortunately @apply cannot be setup in codesandbox,
but you'd use "@apply border opacity-50 border-blue-500 bg-gray-200" here */
.ghost-card {
  opacity: 0.5;
  background: #f7fafc;
  border: 1px solid #4299e1;
}

#app .kanban-column {
  margin-top: 20px;
}

#app .kanban-column .wrapper {
  background-color: #4299e1;
  padding: 20px !important;
}

#app .kanban-column .box-header .box-title {
  color: #ffffff;
}

.card-body {
  overflow: hidden;
}
</style>
