<template>
  <div @dblclick="show_edit_panel" :data-id="ticket.id" @drop.prevent="paste_in_description" @dragover.prevent class="bg-white shadow rounded px-3 pt-3 pb-5 border border-white ticket-cart">
    <div class="cart-handle" :data-id="ticket.id">
      <i class="fas fa-arrows-alt"></i>
      <button @click="edit_ticket(ticket, col_index, row_index)" class="float-right btn-dots">
        <i class="fas fa-ellipsis-h"></i>
      </button>
    </div>

      <button @click="edit_title(ticket)" class="float-right btn-dots">
        <i class="fas fa-edit"></i>
      </button>

    <div :data-id="ticket.id" class="flex justify-between">
      <p :data-id="ticket.id" class="text-gray-700 font-semibold font-sans tracking-wide text-sm">
        <router-link :title="ticket.name" v-if="!show_edit" :to="`/admin/ticket/${ticket.id}`">
          {{ ticket.name | subStrname }}- {{ ticket.number }} (
          {{ ticket.status.name }})
        </router-link>
        <span v-if="show_edit">
          <textarea @paste="paste_in_description"  style="background-color:#ccc;width:100%;heigh:100%;" type="text" v-model="ticket_name">
          </textarea>
            <button @click="save_title(ticket.id)" class="btn btn-success float-right">Save</button>
        </span> 

      </p>

      <p :data-id="ticket.id"
        v-if="ticket.project && ticket.project.name"
        class="text-gray-700 font-semibold font-sans tracking-wide text-sm"
      >Project : {{ ticket.project.name }}</p>
      <p :data-id="ticket.id"
        v-if="ticket && ticket.owner && ticket.owner.length > 0"
        class="text-gray-700 font-semibold font-sans tracking-wide text-sm"
      >
        Client:
        <span :data-id="ticket.id" v-for="(owner,index) of ticket.owner"  :key="index" class="badge badge-danger ml-2">
          {{ owner.name }}
        </span>
      </p>

      <ul :data-id="ticket.id" class="list-inline" v-if="ticket.assigns && ticket.assigns.length > 0">
        <li class="list-inline-item" v-for="(assign, index) of ticket.assigns" :key="index">
          <span class="badge badge-primary">{{ assign.name }}</span>
        </li>
      </ul>

      <p :data-id="ticket.id"
        class="text-gray-700 font-semibold font-sans tracking-wide text-sm"
      >{{ moment(ticket.created_at).format("MMMM Do YYYY") }}</p>
    </div>

    <div :data-id="ticket.id" class="flex mt-4 justify-between items-center">
      <span class="text-sm text-gray-600">{{ ticket.date }}</span>
    </div>

    <div :data-id="ticket.id" v-if="ticket.due_date" class="flex mt-4 justify-between items-center">
      <span
        :class="
                    Date.parse(ticket.due_date) > Date.parse(new Date())
                        ? 'badge badge-success'
                        : 'badge badge-danger'
                "
      >
        <i class="far fa-clock"></i>
        &nbsp;{{ ticket.due_date }}
      </span>
    </div>



    <div
      v-if="show_panel"
      :class="
                [ticket.status.id == 4
                    ? 'ticket-panel ticket-panel-left'
                    : 'ticket-panel',
                is_mobile ? 'is_mobile' : '']
            "
    >
      <ul class="list-unstyled p-2">
        <li class="p-1 mb-1" @click="change_assigns_panel">
          <i class="fas fa-user-edit"></i>&nbsp;{{ $t("Ticket.ChangeAssigns") }}
        </li>
        <li class="p-1 mb-1" @click="show_move_panel">
          <i class="fas fa-arrows-alt"></i>&nbsp;{{ $t("Ticket.Move") }}
        </li>
        <li class="p-1 mb-1" @click="copy_ticket(ticket.id)">
          <i class="fas fa-copy"></i>&nbsp;{{ $t("Ticket.Copy") }}
        </li>
        <li class="p-1 mb-1" @click="show_due_date_panel">
          <i class="far fa-clock"></i>&nbsp;{{ $t("Ticket.ChangeDueDate") }}
        </li>
        <li class="p-1 mb-1" @click="show_edit_panel">
          <i class="fas fa-edit"></i>&nbsp;{{ $t("Ticket.Edit") }}
        </li>
        <li class="p-1 mb-1" @click="delete_ticket">
          <i class="fas fa-trash"></i>&nbsp;{{ $t("Ticket.Delete") }}
        </li>
        <li @click="hide_panel" class="p-1 mb-1">
          <i class="fas fa-times"></i>&nbsp;{{ $t("Ticket.close") }}
        </li>
      </ul>
    </div>




        <div :data-id="ticket.id" class="img-wrapper" v-for="(image, index) in images" :key="index">
            <img style="max-width:230px; max-height: 230px;" thumbnail center rounded  class="max-image-upload" :src="image" :alt="`Image Uplaoder ${index}`">
        </div>


  </div>




</template>
<script>
import Badge from "./Badge.vue";
import moment from "moment";
import { bus } from "./main";

export default {
  data() {
    return {
      ticket_name: null,
      show_edit: false,
      show_panel: false,
      tracking: false,
      track_time: 0,
      counter: { track_time: 0, seconds: 0 },
      formated_time: null,
      is_mobile: true,
      edited_ticket: {},
      files:[],
      images:[]
    };
  },
  components: {
    Badge
  },
  props: ["ticket", "col_index", "row_index"],
  computed: {
    badgeColor() {
      const mappings = {
        Design: "purple",
        "Feature Request": "teal",
        Backend: "blue",
        QA: "green",
        default: "teal"
      };
      return mappings[this.ticket.type] || mappings.default;
    }
  },
  methods: {
    paste_in_description(pasteEvent, callback){
      if(pasteEvent.clipboardData == false){
          if(typeof(callback) == "function"){
                  console.log('Undefined')
                  callback(undefined);
              }
          };

          var items = pasteEvent.clipboardData ? pasteEvent.clipboardData.items : undefined;

          if(items == undefined){
              if(typeof(callback) == "function"){
                  callback(undefined);
                  console.log('Undefined 2')
              }
          }else {
            for (var i = 0; i < items.length; i++) {
              if (items[i].type.indexOf("image") == -1) continue;
              var blob = items[i].getAsFile();
              this.addImage(blob)
            }
            return true;
          }


      let droppedFiles = pasteEvent.dataTransfer.files;
      let target_id = parseInt(pasteEvent.target.dataset.id);
      console.log(target_id);
      if(!droppedFiles) return;
        ([...droppedFiles]).forEach(f => {
          if (f.type.indexOf("image") > -1) {
              this.addImage(f)
          }
        });
    },
    addImage(file){
        if (!file.type.match('image.*')) {
            return new Promise((reject) => {
                const error = {
                    message: 'You can only drag images.',
                    response: {
                        status: 200
                    }
                }
                this.$obtenerError(error)
                reject()
                return;
            })
        }

        this.files.push(file);
        const img = new Image(),
            reader = new FileReader();
        reader.onload = (e) => this.images.push(e.target.result);
        const str = JSON.stringify(file);
        reader.readAsDataURL(file);
    },

    edit_title(ticket){
      this.ticket_name = ticket.name;
      this.show_edit = !this.show_edit;
    },
    save_title(id){
      this.edited_ticket.kanban = true;
      this.edited_ticket.name = this.ticket_name;
      this.edited_ticket.id      = id;
      this.$Progress.start();
      this.$store
        .dispatch("ticket/editKanbanTicket", this.edited_ticket)
        .then(response => {
          this.show_edit = false;
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
    edit_ticket(ticket, index) {
      bus.$emit("hideAll");
      this.show_panel = !this.show_panel;
    },
    hide_panel(ticket, index) {
      this.show_panel = false;
    },
    change_assigns_panel() {
      this.$emit("change_assigns_panel");
    },
    show_move_panel() {
      this.$emit("move_panel");
    },
    show_edit_panel() {
      this.$emit("edit_ticket_event");
    },
    show_due_date_panel() {
      this.$emit("change_due_date");
    },
    delete_ticket() {
      this.$emit("delete_ticket");
    },
    copy_ticket() {
      this.$Progress.start();
      this.$store
        .dispatch("ticket/copyTicket", {
          ticket_id: this.ticket.id
        })
        .then(response => {
          //this.$emit("reload_tickets");
        })
        .catch(error => {
          this.$Progress.fail();
          Toast.fire({
            type: "error",
            title: error.response.data.message
          });
        });
    },
    start_track() {
      this.tracking = true;
      let $this = this;
      this.$Progress.start();
      this.$store
        .dispatch("ticket/startTime", {
          ticket_id: this.ticket.id
        })
        .then(response => {
          $this.counter.timer = setInterval(() => {
            this.formated_time = $this.humanReadableFromSecounds(
              parseInt(response.data) + parseInt(++this.track_time)
            );
          }, 1000);
        })
        .catch(error => {
          this.$Progress.fail();
          Toast.fire({
            type: "error",
            title: error.response.data.message
          });
        });
    },

    end_track() {
      this.tracking = false;

      let $this = this;
      this.$Progress.start();
      this.$store
        .dispatch("ticket/endTime", {
          ticket_id: this.ticket.id
        })
        .then(response => {
          clearInterval($this.counter.timer);
        })
        .catch(error => {
          this.$Progress.fail();
          Toast.fire({
            type: "error",
            title: error.response.data.message
          });
        });
    },
    getDueDate() {},
    humanReadableFromSecounds(seconds) {
      let duration = this._readableTimeFromSeconds(seconds);
      return `${duration.hours}:${duration.minutes}:${duration.seconds}`;
    },
    _readableTimeFromSeconds: function(seconds) {
      const hours = 3600 > seconds ? 0 : parseInt(seconds / 3600, 10);
      return {
        hours: this._padNumber(hours),
        seconds: this._padNumber(seconds % 60),
        minutes: this._padNumber(parseInt(seconds / 60, 10) % 60)
      };
    },
    _padNumber: number =>
      number > 9 ? number : number === 0 ? "00" : "0" + number
  },
  mounted() {
    // if (this.ticket.tracking.length > 0) {
    //   this.start_track();
    // }
    let $this = this;
    bus.$on("hideAll", function() {
      $this.show_panel = false;
    });

    if (window.screen.availWidth <= 425) {
      this.is_mobile = true;
    } else {
      this.is_mobile = false;
    }
  },
  filters: {
    strippedContent: function(string) {
      return string.replace(/<\/?[^>]+>/gi, " ");
    },
    subStrname: value => {
          if (value.length > 40) {
              return value.substring(0, 40) + "..";
          }
          return value;
      },
  }
};
</script>

<style>
.btn-dots {
  border: none;
  background: none;
  outline: none;
}

.ticket-cart {
  position: relative;
}

.ticket-panel {
  position: absolute;
  top: -12px;
  right: -190px;
  z-index: 100;
  background: transparent;
  padding: 3px;
  width: 200px;
  color: #ffffff;
}
.is_mobile {
  top: 0px !important;
  right: 0px !important;
}
.ticket-panel-left {
  left: -190px;
}

.ticket-panel li {
  /* background: #1d68a7; */
  background-color: #333333;
  opacity: 75%;
  transition: all 0.5s;
}

.ticket-panel li:hover {
  cursor: pointer;
  opacity: 100%;
  padding-left: 20px;
}
</style>
