<template>
  <div class="card" id="comments">
    <div class="card-header">
      <h5 class="card-title m-0">{{$t('Ticket.comments')}}</h5>
  </div>
    <div class="card-body">
      <comments v-for="comment in comments.data" :key="comment.id" :comment="comment"></comments>
      <hr>
      <ul class="mailbox-attachments d-flex align-items-stretch clearfix">
        <li v-for="category in categories.data"  :key="category.id">
          <div class="mailbox-attachment-info">
            <span
              href="#"
              class="mailbox-attachment-name"
            >
              <i class="fas fa-paperclip"></i>
              {{ category.name }}
            </span>
          </div>
          <div>
          <span class="form-group ml-3">
            <select class="form-control" @change="templateSelected($event)">
              <option value="">Select Template</option>
              <option v-for="template in category.templates.data"  :key="template.id" :value="template.id">{{ template.title }}</option>
            </select>
          </span>
          </div>
        </li>
      </ul>
    </div>

    <div class="p-4">
      <hr />
      <form @submit.prevent="createComment()">
        <quill-editor
          id="comments-editor"
          v-model="form.comment"
          ref="myQuillEditor"
          :options="editorOption"
        ></quill-editor>
        <br /><br />
        <button class="btn btn-primary">
          {{$t('Ticket.send')}}
          <i class="fab fa-telegram-plane fa-fw"></i>
        </button>
      </form>
    </div>
    <!-- Modal -->
    <div
      class="modal fade"
      id="Modal"
      tabindex="-1"
      role="dialog"
      aria-labelledby="newModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="newTicketLabel">{{$t('Ticket.msgReply')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row mb-3">
              <div class="col">
                <div class="input-group">
                    <input type="text" v-model="form.subject" name="subject" class="form-control">
                    <span class="input-group-append">
                      <button type="button" @click="createCommentFromModel" class="btn btn-primary">{{$t('Ticket.send')}}</button>
                    </span>
                  </div>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <wysiwyg v-model="form.mail" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- end model -->
  </div>

</template>

<script>
import { mapGetters } from "vuex";
import { quillEditor } from "vue-quill-editor";
import moment from 'moment'

// require styles
import "vue-wysiwyg/dist/vueWysiwyg.css";
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";

export default {
  data() {
    return {
      ticketId: this.$route.params.id,
      form: new Form({
        ticket_id: this.$route.params.id,
        comment: "",
        mail: "",
        send_mail: false,
        reply: false,
        by_client: false,
        ticket_description: '',
        ticket_mail: '',
        subject: '',
      }),
      category: '',
      editorOption: {
        modules: {
          toolbar: [
            ["bold", "italic", "underline", "strike"],
            ["blockquote", "code-block"],
            [{ list: "ordered" }, { list: "bullet" }]
          ]
        }
      }
    };
  },
  methods: {
    getComments(id) {
      this.$store
        .dispatch("comment/getCommentsPerTicket", id)
        .then()
        .catch();
    },
    getHumanDate(date) {
      return moment(date).format('MMMM Do YYYY');
    },
    createComment() {
      if(this.user.type != 'client') {
        Swal.fire({
        title: 'What you want to do?',
        text: "Choose send customer reply or only comment!",
        showCancelButton: true,
        confirmButtonText: 'Send Reply',
        cancelButtonText: 'Only Comment',
        reverseButtons: true
      }).then((result) => {
        if (result.value) {
          this.form.send_mail = true;
          this.form.reply = true;
          let currentDate = moment(new Date()).format('dddd, MMMM DD, YYYY H:MM A');
          let reply = this.form.ticket_mail;
          let header;
          let message;
          let link;
          let name;


          if(!this.user.metadata) {
            Toast.fire({
              type: 'error',
              title: 'Please add your information to your profile.'
            })
          }

        // if(this.ticket.owner.length == 1) {
          if(!this.ticket.owner[0].metadata) {
            name = this.ticket.owner[0].name;
          } else {
            if(this.ticket.owner[0].metadata.last_name) {
              name = this.ticket.owner[0].metadata.last_name;
            } else if (this.ticket.owner[0].metadata.first_name) {
              name = this.ticket.owner[0].metadata.first_name;
            } else {
              name = this.ticket.owner[0].name;
            }
          }
        // } else {
        //     // handle multi owner
        // }




          if(!this.user.metadata.signature) {
            Toast.fire({
              type: 'error',
              title: 'Please add signature to your profile.'
            })
          }


          this.form.subject = '##' + this.ticket.number + '## ' + this.ticket.name;
          if(this.ticket.owner[0].metadata && this.ticket.owner[0].metadata.language == 'de') {
            link = '<small><a href="' + this.$base_url + '/admin/ticket/' + this.ticket.id +'">' + this.$base_url + '/admin/ticket/' + this.ticket.id + '</a></small>';
            message = '<div><small>Sie können auf dieses Ticket antworten, indem Sie einfach auf diese E-Mail antworten oder über dem Adminbereich unter dem folgenden Link reagieren.</small></div>';
            if(this.ticket.owner[0].metadata.gender == 'male') {
              header = '<div>Sehr geehrter Herr <span class="header">' + name + ',</span></div><br />';
            } else if(this.ticket.owner[0].metadata.gender == 'female') {
              header = '<div>Sehr geehrte Frau <span class="header">' + name + ',</span></div><br />';
            } else {
              header = '<div>Sehr geehrte(r) Herr/Frau <span class="header">' + name + ',</span></div><br />';
            }
          } else if(this.ticket.owner[0].metadata && this.ticket.owner[0].metadata.language == 'en') {
            link = '<small><a href="' + this.$base_url + '/admin/ticket/' + this.ticket.id +'">' + this.$base_url + '/admin/ticket/' + this.ticket.id + '</a></small>';
            message = '<div><small>You can reply to this ticket by simply replying to this email or by responding via the admin area below.</small></div>';
            if(this.ticket.owner[0].metadata.gender == 'male') {
              header = '<div>Dear Mr <span class="header">' + name + ',</span></div>';
            } else if(this.ticket.owner[0].metadata.gender == 'female') {
              header = '<div>Dear Ms <span class="header">' + name + ',</span></div>';
            } else {
              link = 'Ticket anzeigen <a href="#">hier</a> klicken';
              header = '<div>Dear Mr/Ms <span class="header">' + name + ',</span></div>';
            }
          } else {
            link = '<small><a href="' + this.$base_url + '/admin/ticket/' + this.ticket.id +'">' + this.$base_url + '/admin/ticket/' + this.ticket.id + '</a></small>';
            message = '<div><small>Sie können auf dieses Ticket antworten, indem Sie einfach auf diese E-Mail antworten oder über dem Adminbereich unter dem folgenden Link reagieren.</small></div>';
            if(this.ticket.owner[0].metadata && this.ticket.owner[0].metadata.gender == 'male') {
              header = '<div>Sehr geehrter Herr <span class="header">' + name + ',</span></div>';
            } else if(this.ticket.owner[0].metadata && this.ticket.owner[0].metadata.gender == 'female') {
              header = '<div>Sehr geehrte Frau <span class="header">' + name + ',</span></div>';
            } else {
              header = '<div>Sehr geehrte(r) Herr/Frau <span class="header">' + name + ',</span></div>';
            }
          }
          let comment = header + '<div><br />' + this.form.comment + '</div><div>' + message + link + '</div>' + '<br /><br /><div>' + this.user.metadata.signature + '</div><br /><hr />'+ reply;
          let mail = header + '<div><br />' + this.form.comment + '</div><div>' + message + link + '</div>' + '<br /><br /><div>' + this.user.metadata.signature + '</div>';
          this.form.ticket_mail = comment;
          this.form.mail = mail;
          $("#Modal").modal("show");
        } else if (
          /* Read more about handling dismissals below */
          result.dismiss === Swal.DismissReason.cancel
        ) {
            this.form.send_mail = false;
            this.form.reply = false;
            this.form.by_client = false;
            this.$store
              .dispatch("comment/createCommentForTicket", this.form)
              .then(response => {
                this.form.comment = null;
              })
              .catch();
              this.$store.dispatch("ticket/getTicketById", this.ticketId);
            Toast.fire({
              type: 'success',
              title: 'Comment created without reply to customer.'
            })
          }
        })
      } else {
        this.form.send_mail = false;
        this.form.reply = false;
        this.form.by_client = true;
        this.$store
          .dispatch("comment/createCommentForTicket", this.form)
          .then(response => {
            this.form.comment = null;
          })
          .catch();
          this.$store.dispatch("ticket/getTicketById", this.ticketId);
        Toast.fire({
          type: 'success',
          title: 'Comment created without reply to customer.'
        })
      }
    },
  createCommentFromModel() {
    this.form.send_mail = true;
    this.form.reply = true;
    this.$store
      .dispatch("comment/createCommentForTicket", this.form)
      .then(response => {
        this.form.comment = null;
        Toast.fire({
          type: 'success',
          title: 'Reply sent to customer.'
        })
      })
    $("#Modal").modal("hide");
  },
  getUserById(id) {
    this.$Progress.start();
    this.$store
      .dispatch("user/getUserById", id)
      .then(response => {
        this.$Progress.finish();
      })
      .catch(error => {
        this.$Progress.fail();
      });
  },
  getCategories() {
    this.$store
        .dispatch("template/getCategories")
        .then(() => {
            this.$Progress.finish();
            this.isLoading = false;
        })
        .catch(error => {
            this.$Progress.fail();
            this.isLoading = false;
        });
  },
  getTemplates() {
    this.$store
        .dispatch("template/getTemplates")
        .then(() => {
            this.$Progress.finish();
            this.isLoading = false;
        })
        .catch(error => {
            this.$Progress.fail();
            this.isLoading = false;
        });
  },
  templateSelected(event) {
    this.templates.data.filter(template => {
      if(template.id == event.target.value) {
        this.form.comment = template.body;
      }
    });
  },
},
  mounted() {
    // get all comments for the user
    if(this.$can('taskComment-list')){
        this.getComments(this.ticketId);
    }

    this.getUserById(this.$userId)

    if(this.$can('mail-template')){
      this.getCategories();
      this.getTemplates();
    }



  },
  computed: {
    ...mapGetters({
      comments: "comment/activeComments",
      ticket: "ticket/activeTicket",
      user: "user/activeSingleUser",
      categories: "template/allCategories",
      templates: "template/allTemplates",
    })
  },
  components: {
    quillEditor
  }
};
</script>

<style scoped>
#comments-editor {
  height: 100px;
}
</style>
