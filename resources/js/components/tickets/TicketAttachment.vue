<template>
  <span>
    <!-- start Modal -->
    <span v-if="isModal">
      <span class="text-muted mx-2 btn" data-toggle="modal" data-target="#exampleModal">
        <i class="fas fa-paperclip"></i> {{$t('Ticket.attachments')}} <span class="badge badge-secondary">{{attachmentLength}}</span>
      </span>
      <!-- Modal -->
      <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-paperclip"></i> {{$t('Ticket.ticket_attachment')}} <span class="badge badge-secondary">{{attachmentLength}}</span></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-6" v-if="ticketDialogFiles  && ticketDialogFiles.length > 0" v-for="dialogFile in ticketDialogFiles" :key="dialogFile.id">
                  <div class="mailbox-attachment-info" style="border-radius: 10px;border: 1px solid #8e8e8e;">
                    <a
                        target="_plank"
                        :title="dialogFile.file_path"
                        :href=" dialogFile.file_path | filePath"
                        class="mailbox-attachment-name"
                    >
                        <i class="fas fa-paperclip"></i>
                        {{ dialogFile.file_path | fileName }}
                    </a>
                  </div>
                </div>
                <div class="col-6" v-if="files  && files.length > 0" v-for="file in files" :key="file.id">
                  <div class="mailbox-attachment-info" style="border-radius: 10px;border: 1px solid #8e8e8e;">
                    <a
                        target="_plank"
                        :href=" file.attachment_path | filePath"
                        :title="file.attachment_path"

                        class="mailbox-attachment-name"
                    >
                        <i class="fas fa-paperclip"></i>
                        {{ file.attachment_path | TicketfileName }}
                      </a>
                  </div>
                </div>
                <div class="col-12" v-if="ticketDialogFiles  && ticketDialogFiles.length < 1 && files  && files.length < 1">
                  <span>{{$t('Ticket.no_attachment')}}</span>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <span class="btn btn-blue" data-dismiss="modal">{{$t('Ticket.colse')}}</span>
            </div>
          </div>
        </div>
      </div>
    </span>
    <!-- end modal -->

    <!-- attachments list start -->
    <span v-else class="ml-1">
      <div class="row">
        <div class="font-weight-bold col-12 px-0 py-0">Attachments: </div>
        <div class="col-md-2 col-xs-12 py-1 px-1" v-if="ticketDialogFiles  && ticketDialogFiles.length > 0" v-for="dialogFile in ticketDialogFiles" :key="dialogFile.id">
          <div class="mailbox-attachment-info" style="border-radius: 10px;border: 1px solid #8e8e8e;">
            <a
                target="_plank"
                :title="dialogFile.file_path"
                :href=" dialogFile.file_path | filePath"
                class="mailbox-attachment-name"
            >
                <i class="fas fa-paperclip"></i>
                {{ dialogFile.file_path | fileName }}
            </a>
          </div>
        </div>
        <div class="col-md-2 col-xs-12 py-1 px-1" v-if="files  && files.length > 0" v-for="file in files" :key="file.id">
          <div class="mailbox-attachment-info" style="border-radius: 10px;border: 1px solid #8e8e8e;">
            <a
                target="_plank"
                :href=" file.attachment_path | filePath"
                :title="file.attachment_path"

                class="mailbox-attachment-name"
            >
                <i class="fas fa-paperclip"></i>
                {{ file.attachment_path | TicketfileName }}
              </a>
          </div>
        </div>
        <div class="col-12 py-1 px-1" v-if="ticketDialogFiles  && ticketDialogFiles.length < 1 && files  && files.length < 1">
          <span>{{$t('Ticket.no_attachment')}}</span>
        </div>
      </div>
    </span>
    <!-- attachments list end -->
  </span>
</template>
<script>
  export default {
    name: 'TicketAttachmentComponent',
    props: {
      isModal: {
        type: Boolean,
        default: true,
      },
      ticketDialogFiles: Array,
      files: Array
    },
    data() {
      return {

      }
    },
    computed: {
      attachmentLength() {
        let total = 0;
        if(this.files && this.files.length > 0) {
          total = this.files.length;
        } 
        if(this.ticketDialogFiles && this.ticketDialogFiles.length > 0) {
          total += this.ticketDialogFiles.length;
        }
        return total;
      }
    },
    filters: {
      filePath(path) {
          let str = path;
          let n = str.indexOf("public");
          return "/storage" + str.substring(n + 6);
      },
      fileName(path) {
          let str = path;
          let n = str.lastIndexOf("-");
          let name =  str.substring(n + 1);
          if(name.length > 13) {
              return name.substring(0, 13) + '...'
          } else {
              return name;
          }
      },
      TicketfileName(path) {
          let str = path;
          let n = str.lastIndexOf("/");
          let name =  str.substring(n + 1);
          if(name.length > 11) {
              return name.substring(0, 11) + '..'
          } else {
              return name;
          }
      },
    }
  }
</script>