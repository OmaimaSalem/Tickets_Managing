<template>
<span>
	<div v-if="ticketReplies && ticketReplies.length > 0 || ticketComments && ticketComments.length > 0" class="row">
      <div class="col-md-12 px-0 py-0">
        <div class="card">
          <div class="card-header px-0 py-0">
            <div class="card-title">
              <ul class="nav nav-tabs" id="custom-content-below-tab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="reply-tab" data-toggle="pill" @click="tab = 'replies'" href="#reply" role="tab" aria-controls="reply" aria-selected="true">{{$t('Ticket.replies')}}</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="comment-tab" data-toggle="pill" @click="tab = 'comments'" href="#comment" role="tab" aria-controls="comment" aria-selected="false">{{$t('Ticket.comments')}}</a>
                </li>
              </ul>
            </div>
            <div class="card-tools">
              <div v-if = "tab === 'replies'" class="btn btn-sm btn-blue mt-2 mr-3" @click="addReply"><i class="text-yellow fas fa-reply"></i>&nbsp;&nbsp;{{$t('Ticket.reply')}}</div>
              <div v-if = "tab === 'comments'" class="btn btn-sm btn-blue mt-2 mr-3" @click="addReply('comment')"><i class="text-yellow fas fa-comment-alt"></i>&nbsp;&nbsp;{{$t('Ticket.comments')}}</div>

            </div>
          </div>
          <div class="card-body">
            <div class="tab-content" id="replyContent">
              <div class="tab-pane fade active show" id="reply" role="tabpanel" aria-labelledby="reply-tab">
                 <div class="row">
                   <div class="col-12 py-1" v-if="ticketReplies" v-for="reply in ticketReplies" :key="reply.id">
                    <!-- Reply header -->
                     <div class="row">
                       <div class="col-md-10 col-xs-12 py-0" v-if="reply.created_by">
                        <div class="row">
                          <div class="col-1 pr-0 py-0">
                            <avatar
                              color="#234FA3"
                              :fullname="reply.created_by.name"
                              :size="30"
                            ></avatar>
                          </div>
                          <div class="col-11 pl-0 py-0">
                            <span class="ml-xs-3">{{reply.created_by.name}}</span>
                            <small class="text-muted">&lt;{{reply.created_by.email}}&gt;</small>
                          </div>
                        </div>
                       </div>
                       <div class="col-md-2 col-xs-12 py-0">
                        <small style="margin-left: -1rem;">{{$t('Ticket.posted')}} : {{reply.created_at | dateFormat}}</small>
                        <div class="btn btn-sm" title="reply" v-if="!showreply" @click="showReply(reply.id, reply.subject, reply.cc)"><i class="text-blue fas fa-reply"></i></div>
                        <div class="btn btn-sm" title="reply" v-else @click="showreply = false"><i class="text-red fas fa-reply"></i></div>
                       </div>
                     </div>
                     <!-- SubReply Form -->
                     <div class="row mb-1" v-if="showreply && subreplyID == reply.id">
                       <div class="col-1">
                          <avatar
                            color="#234FA3"
                            :fullname="user.name"
                            :size="30"
                          ></avatar>
                       </div>
                       <div class="col-11 subReply">
                         <div class="row">
                          <div class="col-12 pt-0 pb-0">
                            <Template @selectedTemplate="templateSelectedReplyForm"></Template>
                          </div>
                          <div class="col-12 pt-0 pb-0">
                            <div class="form-group row my-0">
                              <div class="col-1 pl-4 pt-2 text-muted" for="name">To:</div>
                              <div class="col-11 px-0 py-1">
                                <span v-for="owner in ticket.owner" :key="owner.id"><small class="text-muted">&lt;{{owner.email}}&gt;,</small></span>
                              </div>
                            </div>
                          </div>
                          <div class="col-12 pt-0 pb-0">
                            <div class="form-group row my-0">
                              <div class="col-1 pl-4 pt-2 text-muted">CC:</div>
                              <div class="col-11 px-0 py-0">
                                <multiselect
                                  v-model="subReplyCC"
                                  :options="owners"
                                  placeholder="CC"
                                  label="email"
                                  track-by="id"
                                  :multiple="true"
                                  @input="(opt) => (pushReplyCC(opt))"
                                ></multiselect>
                                <has-error :form="form" field="name"></has-error>
                              </div>
                            </div>
                          </div>
                          <div class="col-12 pt-0 pb-0">
                            <div class="form-group row my-0">
                              <div class="col-1 pl-4 pt-2 text-muted">BCC:</div>
                              <div class="col-11 px-0 py-0">
                                <multiselect
                                  v-model="subReplyBCC"
                                  :options="employees"
                                  placeholder="BCC"
                                  label="email"
                                  track-by="id"
                                  :multiple="true"
                                  @input="(opt) => (pushReplyBCC(opt))"
                                ></multiselect>
                                <has-error :form="form" field="name"></has-error>
                              </div>
                            </div>
                          </div>
                          <div class="col-12 pt-0 pb-0">
                            <div class="form-group row my-0">
                              <div class="col-md-1 col-xs-2 pl-4 pt-2 text-muted"><small>{{$t('Ticket.subject')}}:</small></div>
                              <div class="col-md-8 col-xs-10  px-1 py-1 pl-3">
                                <input type="text" class="form-control" v-model="replyForm.subject">
                              </div>
                              <div class="col-md-3 col-xs-12 py-0">
                                <v-file-input v-model="replyForm.files" show-size counter multiple :label="$t('Ticket.upload')"></v-file-input>
                              </div>
                            </div>
                          </div>
                          <div class="col-12 pt-0 pb-0">
                            <div class="form-group row my-0">
                              <div class="col-12  px-1 py-1">
                                <quill-editor
                                  v-model="replyForm.content"
                                  ref="myQuillEditor"
                                  :options="editorOption"
                                ></quill-editor>
                              </div>
                            </div>
                          </div>
                          <div class="col-12 pt-0 pb-0 text-right">
                            <div class="btn btn-red" @click="showreply = false"><i class="fa fa-times-circle"></i>{{$t("Ticket.colse")}}</div>
                            <div class="btn btn-blue" @click="createSubReply"><i class="text-yellow fa fa-reply"></i>{{$t("Ticket.reply")}}</div>
                          </div>
                        </div>
                       </div>
                     </div>
                     <!-- Reply body -->
                     <div class="row">
                       <div class="col-12 py-0">
                         <h3>{{reply.subject}}</h3>
                       </div>
                       <div class="col-12 py-0">
                         <p v-html="reply.content"></p>
                       </div>
                     </div>
                     <!-- SubReply -->
                     <span v-if="reply.subReplies && reply.subReplies.length > 0">
                      <div class="text-center">
                        <div class="btn btn-blue" v-if="!showsubreply" @click="showSubReply(reply.id)">{{$t('Ticket.show_replies')}} <span class="badge bg-yellow">{{reply.subReplies.length}}</span></div>
                        <div class="btn btn-blue mb-2" v-else @click="showsubreply = false">{{$t('Ticket.hide_replies')}} <span class="badge bg-yellow">{{reply.subReplies.length}}</span></div>
                      </div>
                       <span v-if="showsubreply && replyId == reply.id">
                         <div class="row" v-for="subReply in reply.subReplies" :key="subReply.id">
                          <div class="col-12 py-0 px-0 mb-1 subReply">
                            <div class="row pl-4">
                              <div class="col-12 py-0">
                                <div class="row">
                                  <div class="col-md-10 col-xs-12">
                                    <p class="text-muted">
                                      <avatar
                                        color="#234FA3"
                                        :fullname="subReply.created_by.name"
                                        :size="30"
                                      ></avatar>
                                      <span>{{subReply.created_by.name}}</span>
                                      <small class="text-muted">&lt;{{subReply.created_by.email}}&gt;</small>
                                    </p>
                                  </div>
                                  <div class="col-md-2 col-xs-12 py-1">
                                    <small class="text-muted">{{$t('Ticket.posted')}} : {{subReply.created_at | dateFormat}}</small>
                                  </div>
                                </div>
                              </div>
                              <div class="col-12 py-0" v-if="reply.cc.length > 0">
                                <small class="text-muted pl-3">CC: 
                                  <span v-for="mail in reply.cc" :key="mail.id">&lt;{{mail.email}}&gt;</span>
                                </small>
                              </div>
                              <div class="col-12 py-0" v-if="ticket.owner.length > 0">
                                <small class="text-muted pl-3">To: 
                                  <span v-for="mail in ticket.owner" :key="mail.id">&lt;{{mail.email}}&gt;</span>
                                </small>
                              </div>
                              <div class="col-12 py-1">
                                <p v-html="subReply.content" class="pl-3"></p>
                              </div>
                            </div>
                          </div>
                        </div>
                       </span>
                       <div class="text-center">
                         <div class="btn btn-blue mb-2" v-if="showsubreply" @click="showsubreply = false"><i class="fa fa-arrow-up text-yellow"></i> {{$t('Ticket.hide_above_replies')}}</div>
                       </div>
                     </span>
                     <hr style="width: 70%;margin-left: 50%;transform: translateX(-50%); background-color: #000">
                   </div>
                 </div>
              </div>
              <!-- Commnet Tab -->
              <div class="tab-pane fade" id="comment" role="tabpanel" aria-labelledby="comment-tab">
                <div class="row">
                   <div class="col-12 py-1" v-if="ticketComments" v-for="comment in ticketComments" :key="comment.id">
                    <!-- Comment header -->
                     <div class="row">
                       <div class="col-md-10 col-xs-12 py-0" v-if="comment.created_by">
                        <div class="row">
                          <div class="col-1 pr-0 py-0">
                            <avatar
                              color="#234FA3"
                              :fullname="comment.created_by.name"
                              :size="30"
                            ></avatar>
                          </div>
                          <div class="col-11 pl-0 py-0">
                            <span class="ml-xs-3">{{comment.created_by.name}}</span>
                            <small class="text-muted">&lt;{{comment.created_by.email}}&gt;</small>
                          </div>
                        </div>
                       </div>
                       <div class="col-md-2 col-xs-12 py-0">
                        <small style="margin-left: -1rem;">{{$t('Ticket.posted')}} : {{comment.created_at | dateFormat}}</small>
                        <div class="btn btn-sm" title="reply" @click="showComment(comment.id)"><i class="text-blue fas fa-reply"></i></div>
                       </div>
                     </div>
                     <!-- subComment Form -->
                     <div class="row mb-1" v-if="showcomment && commentId == comment.id">
                       <div class="col-1">
                          <avatar
                            color="#234FA3"
                            :fullname="user.name"
                            :size="30"
                          ></avatar>
                       </div>
                       <div class="col-11 subReply">
                         <div class="row">
                          <div class="col-12 pt-0 pb-0">
                            <Template @selectedTemplate="templateSelectedCommentForm"></Template>
                          </div>
                          <div class="col-12 pt-0 pb-0">
                            <div class="form-group row my-0">
                              <div class="col-md-1 col-xs-2 pl-4 pt-2" for="name">To:</div>
                              <div class="col-md-8 col-xs-10 px-0 py-1">
                                <span v-for="manager in ticket.manager" :key="manager.id"><small class="text-muted">&lt;{{manager.email}}&gt;,</small></span>
                              </div>
                              <div class="col-md-3 col-xs-12 py-0">
                                <v-file-input v-model="commentForm.files" show-size counter multiple :label="$t('Ticket.upload')"></v-file-input>
                              </div>
                            </div>
                          </div>
                          <div class="col-12 pt-0 pb-0">
                            <div class="form-group row my-0">
                              <div class="col-12  px-1 py-1">
                                <quill-editor
                                  v-model="commentForm.content"
                                  ref="myQuillEditor"
                                  :options="editorOption"
                                ></quill-editor>
                              </div>
                            </div>
                          </div>
                          <div class="col-12 pt-0 pb-0 text-right">
                            <div class="btn btn-red" @click="showcomment = false"><i class="fa fa-times-circle"></i>{{$t("Ticket.colse")}}</div>
                            <div class="btn btn-blue" @click="createSubComment"><i class="text-yellow fa fa-reply"></i>{{$t("Ticket.comment")}}</div>
                          </div>
                        </div>
                       </div>
                     </div>
                     <!-- Comment body -->
                     <div class="row">
                       <div class="col-12 py-0">
                         <h3>{{comment.subject}}</h3>
                       </div>
                       <div class="col-12 py-0">
                         <p v-html="comment.content"></p>
                       </div>
                     </div>
                     <!-- SubComment -->
                     <span v-if="comment.subComments && comment.subComments.length > 0">
                      <div class="text-center">
                        <div class="btn btn-blue" v-if="!showsubcomment" @click="showSubComments(comment.id)">{{$t('Ticket.show_comments')}} <span class="badge bg-yellow">{{comment.subComments.length}}</span></div>
                        <div class="btn btn-blue mb-2" v-if="showsubcomment && commentId == comment.id" @click="showsubcomment = false">{{$t('Ticket.hide_comments')}} <span class="badge bg-yellow">{{comment.subComments.length}}</span></div>
                      </div>
                       <span v-if="showsubcomment && commentId == comment.id">
                         <div class="row" v-for="subComment in comment.subComments" :key="subComment.id">
                          <div class="col-12 py-0 px-0 mb-1 subReply">
                            <div class="row pl-4">
                              <div class="col-12 py-0">
                                <div class="row">
                                  <div class="col-md-10 col-xs-12">
                                    <p class="text-muted">
                                      <avatar
                                        color="#234FA3"
                                        :fullname="subComment.created_by.name"
                                        :size="30"
                                      ></avatar>
                                      <span>{{subComment.created_by.name}}</span>
                                      <small class="text-muted">&lt;{{subComment.created_by.email}}&gt;</small>
                                    </p>
                                  </div>
                                  <div class="col-md-2 col-xs-12 py-1">
                                    <small class="text-muted">Posted : {{subComment.created_at | dateFormat}}</small>
                                  </div>
                                </div>
                              </div>
                              <div class="col-12 py-1">
                                <p v-html="subComment.content" class="pl-3"></p>
                              </div>
                            </div>
                          </div>
                        </div>
                       </span>
                       <div class="text-center">
                         <div class="btn btn-blue mb-2" v-if="showsubcomment" @click="showsubcomment = false"><i class="fa fa-arrow-up text-yellow"></i> {{$t('Ticket.hide_above_comments')}}</div>
                      </div>
                     </span>
                     <hr style="width: 70%;margin-left: 50%;transform: translateX(-50%); background-color: #000">
                   </div>
                 </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <span v-else-if="dialogLoaded && this.ticketReplies.length == 0 || this.ticketComments.length == 0">
      <div class="text-center pt-2">
        <p class="h2">{{$t('Ticket.no_replies_or_comments')}}</p>
        <div class="btn btn-sm btn-blue my-2" @click="addReply"><i class="text-yellow fas fa-reply"></i>&nbsp;&nbsp;{{$t('Ticket.reply')}}</div>
        <div class="btn btn-sm btn-blue my-2" @click="addReply('comment')"><i class="text-yellow fas fa-comment-alt"></i>&nbsp;&nbsp;{{$t('Ticket.comments')}}</div>
      </div>
    </span>
    <span v-else>
      <div class="text-center">
        <scale-loader :loading="loader.loading" :color="loader.color" :size="loader.size"></scale-loader>
      </div>
    </span>

    <!-- Modal -->
      <div class="modal fade" id="replyModal" tabindex="-1" role="dialog" aria-labelledby="replyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="replyModalLabel" v-if="tab === 'replies'">{{$t('Ticket.reply')}}</h5>
              <h5 class="modal-title" id="replyModalLabel" v-if="tab === 'comments'">{{$t('Ticket.comment')}}</h5>
              <div aria-label="Close">
                <div class="btn btn-red" :title="$t('Ticket.colse')" data-dismiss="modal"><i class="fas fa-times-circle mr-1"></i></div>
                <div class="btn btn-blue" :title="$t('Ticket.reply')" v-if="tab === 'replies'" @click.prevent="CreateReply"><i class="fas fa-reply text-yellow mr-1"></i></div>
                <div class="btn btn-blue" :title="$t('Ticket.comments')" v-if="tab === 'comments'" @click.prevent="CreateComment"><i class="fas fa-save text-yellow mr-1"></i></div>
                <!-- <span aria-hidden="true">&times;</span> -->
              </div>
            </div>
            <div class="modal-body">
              <div v-if="tab === 'replies'">
                <div class="row">
                  <div class="col-12 pt-0 pb-0">
                    <Template @selectedTemplate="templateSelect"></Template>
                  </div>
                  <div class="col-12 pt-0 pb-0">
                    <div class="form-group row my-0">
                      <div class="col-1 pl-4 pt-2" for="name">CC:</div>
                      <div class="col-11 px-0 py-0">
                        <multiselect
                          v-model="ticketCC"
                          :options="owners"
                          placeholder="CC Mails"
                          label="name"
                          track-by="id"
                          :multiple="true"
                          @input="(opt) => (pushCC(opt))"
                        ></multiselect>
                        <has-error :form="form" field="name"></has-error>
                      </div>
                    </div>
                  </div>
                  <div class="col-12 pt-0 pb-0">
                    <div class="form-group row my-0">
                      <div class="col-1 pl-4 pt-2">BCC:</div>
                      <div class="col-11 px-0 py-0">
                        <multiselect
                          v-model="ticketBcc"
                          :options="employees"
                          placeholder="BCC Mails"
                          label="name"
                          track-by="id"
                          :multiple="true"
                          @input="(opt) => (pushBCC(opt))"
                        ></multiselect>
                        <has-error :form="form" field="name"></has-error>
                      </div>
                    </div>
                  </div>
                  <div class="col-12 pt-0 pb-0">
                    <div class="form-group row my-0">
                      <div class="col-md-9 col-xs-12  px-1 py-1 pl-3">
                        <input type="text" class="form-control" v-model="form.subject">
                      </div>
                      <div class="col-md-3 col-xs-12 py-0">
                        <v-file-input v-model="form.files" show-size counter multiple :label="$t('Ticket.upload')"></v-file-input>
                      </div>
                    </div>
                  </div>
                  <div class="col-12 pt-0 pb-0">
                    <div class="form-group row my-0">
                      <div class="col-12  px-1 py-1">
                        <quill-editor
                          v-model="form.content"
                          ref="myQuillEditor"
                          :options="editorOption"
                        ></quill-editor>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-if="tab === 'comments'">
                <div class="row">
                  <div class="col-12 pt-0 pb-0">
                    <Template @selectedTemplate="templateSelect"></Template>
                  </div>
                  <div class="col-12 pt-0 pb-0">
                    <div class="form-group row my-0">
                      <div class="col-12  px-1 py-1">
                        <quill-editor
                          v-model="form.content"
                          ref="myQuillEditor"
                          :options="editorOption"
                        ></quill-editor>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer" style="display:block">
              <div class="float-left">
                <div class="btn btn-blue" @click="closeTicketWithReply"><i class="fas fa-comments text-yellow mr-1"></i>close with message</div>
                <div class="btn btn-blue" @click="closeTicket"><i class="fas fa-comment-slash text-yellow mr-1"></i>close</div>
                <div class="btn btn-blue" @click="openTicket"><i class="fas fa-door-open text-yellow mr-1"></i>open</div>
                <div class="btn btn-blue" @click="closeTicketCreateInvoice"><i class="fas fa-hand-holding-usd text-yellow mr-1"></i>invoice</div>
                <div class="btn btn-blue" @click="closeTicket"><i class="fas fa-free text-yellow mr-1"></i>free</div>
              </div>
              <div class="float-right">
                <div class="btn btn-red" data-dismiss="modal"><i class="fas fa-times-circle mr-1"></i>{{$t('Ticket.colse')}}</div>
                <div class="btn btn-blue" v-if="tab === 'replies'" @click.prevent="CreateReply"><i class="fas fa-reply text-yellow mr-1"></i>{{$t('Ticket.reply')}}</div>
                <div class="btn btn-blue" v-if="tab === 'comments'" @click.prevent="CreateComment"><i class="fas fa-save text-yellow mr-1"></i>{{$t('Ticket.comments')}}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
  </span>
</template>
<script>
import { mapGetters } from "vuex";
import moment from "moment";
import {quillEditor} from "vue-quill-editor";
import Template from '../../../components/templates/Template.vue';
import ScaleLoader from 'vue-spinner/src/ScaleLoader.vue'
// require styles
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";

	export default {
		data() {
			return {
        loader: {
          loading: true,
          color: '#2959B5',
          size: '70px',
        },
        dialogLoaded: false,
        tab: 'replies',
        user_id : this.$userId,
        showreply: false,
        showcomment: false,
        addcomment: false,
        addreply: false,
        subreplyID: '',
        commentId: '',
        replyId: '',
        showsubreply: false,
        showsubcomment: false,
        employeesMails : [],
        bccEmails:[],
        clientsMails:[],
        ticketReplies: [],
        ticketComments : [],
				replyBtn: false,
				commentBtn: false,
				subReplyBtn: false,
				subCommentBtn: false,
        ticketCC: [],
        ticketBcc: [],
        subReplyCC: [],
        subReplyBCC: [],
        files: null,
				form: new Form({
  				subject: '',
  				content: '',
  				files: null,
          bcc: [],
          cc: [],
				}),
				replyForm: new Form ({
					id: '',
					subject: '',
					content: '',
					files: '',
          cc: [],
          bcc: [],
				}),
        commentForm: new Form ({
          id: '',
          subject: '',
          content: '',
          files: '',
        }),
        mail: {
            template: null,
            content: null,
            header: null,
            footer: null,
        },
				editorOption: {
          placeholder: 'Type here..',
          modules: {
            toolbar: [
              ["bold", "italic", "underline", "strike"],
              ["blockquote", "code-block"],
              [{
              	list: "ordered"
              }, {
                list: "bullet"
              }]
            ]
          }
        },
			}
		},
		methods: {
      // ticket actions;

      closeTicketWithReply(){
        this.CreateReply().then(function(){
          this.closeTicket();
        });
      },
      closeTicketCreateInvoice(){
        this.closeTicket();
        this.$emit('createInvoice');
        $("#replyModal").modal('hide')
      },
      closeTicket(){
            this.$Progress.start();
             this.$store
            .dispatch("ticket/updateStatus", {
              id: this.ticket.id,
              status_id : 4
            })
            .then(response => {
                this.$Progress.finish();
                Toast.fire({
                    type: "success",
                    title: "ticket closed successfully"
                });

            })
            .catch(error => {
                this.$Progress.fail();
            });
          },

          openTicket(){
            this.$Progress.start();
             this.$store
            .dispatch("ticket/updateStatus", {
              id: this.ticket.id,
              status_id : 1
            })
            .then(response => {
                this.$Progress.finish();
                Toast.fire({
                    type: "success",
                    title: "ticket opened successfully"
                });

            })
            .catch(error => {
                this.$Progress.fail();
            });
          },
      getCC() {
        if(this.ticket.cc && this.ticket.cc.length > 0) {
          this.owners.forEach(owner => {
            this.ticket.cc.forEach(mail => {
              if(owner.email == mail.email) {
                this.ticketCC.push(owner);
                this.form.cc.push(owner.email);
              }
            })
          })
        }
      },
      getBCC() {
        this.ticket.manager.forEach(manag => {
          this.ticketBcc.push(manag);
          this.form.bcc.push(manag.email);
        })
      },
      pushBCC(opt) {
        this.form.bcc = [];
        this.ticketBCC = [];
        this.ticketBCC = [ ...new Set(opt) ];
        this.ticketBCC.forEach(user => {
          this.form.bcc.push(user.email)
        })
      },
      pushCC(opt) {
        this.form.cc = [];
        this.ticketCC = [];
        this.ticketCC = [ ...new Set(opt) ];
        opt.forEach(user => {
          this.form.cc.push(user.email)
        })
      },
      pushReplyCC(opt) {
        this.replyForm.cc = [];
        this.subReplyCC = [];
        this.subReplyCC = [ ...new Set(opt) ];
        opt.forEach(user => {
          this.replyForm.cc.push(user.email)
        })
      },
      pushReplyBCC(opt) {
        this.replyForm.bcc = [];
        this.subReplyBCC = [];
        this.subReplyBCC = [ ...new Set(opt) ];
        opt.forEach(user => {
          this.replyForm.bcc.push(user.email)
        })
      },
      templateSelectedCommentForm(e){
        this.commentForm.content = e;
      },
      templateSelectedReplyForm(e){
        this.mail.template = e;
        this.replyForm.content = this.mail.header + '<br/>' + this.mail.template + '<br/>' + this.mail.footer
      },
      templateSelect(e) {
        this.mail.template = e;
        if(this.tab === 'replies') {
          this.form.content = this.mail.header + '<br/>' + this.mail.template + '<br/>' + this.mail.footer;
        } else {
          this.form.content = e;
        }
      },

      addReply(comment) {
        if(comment === 'comment') {
          this.tab = 'comments'
        }
        $('#replyModal').modal('show');
          let header;
          let name;
          if(this.tab === 'replies') {
            this.form.subject = this.ticket.name;
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
            if(this.ticket.owner[0].metadata && this.ticket.owner[0].metadata.language == 'de') {
                if(this.ticket.owner[0].metadata.gender == 'male') {
                    header = '<div>Sehr geehrter Herr <span class="header">' + name + ',</span></div><br />';
                } else if(this.ticket.owner[0].metadata.gender == 'female') {
                    header = '<div>Sehr geehrte Frau <span class="header">' + name + ',</span></div><br />';
                } else {
                    header = '<div>Sehr geehrte(r) Herr/Frau <span class="header">' + name + ',</span></div><br />';
                }
            } else if(this.ticket.owner[0].metadata && this.ticket.owner[0].metadata.language == 'en') {
                if(this.ticket.owner[0].metadata.gender == 'male') {
                    header = '<div>Dear Mr <span class="header">' + name + ',</span></div>';
                } else if(this.ticket.owner[0].metadata.gender == 'female') {
                    header = '<div>Dear Ms <span class="header">' + name + ',</span></div>';
                } else {
                    header = '<div>Dear Mr/Ms <span class="header">' + name + ',</span></div>';
                }
            } else {
                if(this.ticket.owner[0].metadata && this.ticket.owner[0].metadata.gender == 'male') {
                    header = '<div>Sehr geehrter Herr <span class="header">' + name + ',</span></div>';
                } else if(this.ticket.owner[0].metadata && this.ticket.owner[0].metadata.gender == 'female') {
                    header = '<div>Sehr geehrte Frau <span class="header">' + name + ',</span></div>';
                } else {
                    header = '<div>Sehr geehrte(r) Herr/Frau <span class="header">' + name + ',</span></div>';
                }
            }
            this.mail.header = header;
            this.mail.footer = '<br /><div>' + this.user.metadata.signature + '</div>';
            let mail = header + '<br /><div>' + this.user.metadata.signature + '</div>';

            this.form.content = mail;
          }
      },
      showSubComments(id) {
        this.commentId = id;
        this.showsubcomment = true;
      },
      showSubReply(id) {
        this.replyId = id;
        this.showsubreply = true;
      },
      showReply(id, subject, mails) {
        // this.replyForm.cc = this.ticket.cc.map(c => c.email);
        let header;
        let name;
        this.showreply = true;
        this.subreplyID = id;
        this.subReplyCC = [];
        mails.forEach(mail => {
          this.subReplyCC.push(mail)
        })
        $('#subReplyModal').modal('show');
        let newSubject = subject.split('##');
        if(newSubject[2]) {
          this.replyForm.subject = newSubject[2];
        } else {
          this.replyForm.subject = subject;
        }
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
          if(this.ticket.owner[0].metadata && this.ticket.owner[0].metadata.language == 'de') {
              if(this.ticket.owner[0].metadata.gender == 'male') {
                  header = '<div>Sehr geehrter Herr <span class="header">' + name + ',</span></div><br />';
              } else if(this.ticket.owner[0].metadata.gender == 'female') {
                  header = '<div>Sehr geehrte Frau <span class="header">' + name + ',</span></div><br />';
              } else {
                  header = '<div>Sehr geehrte(r) Herr/Frau <span class="header">' + name + ',</span></div><br />';
              }
          } else if(this.ticket.owner[0].metadata && this.ticket.owner[0].metadata.language == 'en') {
              if(this.ticket.owner[0].metadata.gender == 'male') {
                  header = '<div>Dear Mr <span class="header">' + name + ',</span></div>';
              } else if(this.ticket.owner[0].metadata.gender == 'female') {
                  header = '<div>Dear Ms <span class="header">' + name + ',</span></div>';
              } else {
                  header = '<div>Dear Mr/Ms <span class="header">' + name + ',</span></div>';
              }
          } else {
              if(this.ticket.owner[0].metadata && this.ticket.owner[0].metadata.gender == 'male') {
                  header = '<div>Sehr geehrter Herr <span class="header">' + name + ',</span></div>';
              } else if(this.ticket.owner[0].metadata && this.ticket.owner[0].metadata.gender == 'female') {
                  header = '<div>Sehr geehrte Frau <span class="header">' + name + ',</span></div>';
              } else {
                  header = '<div>Sehr geehrte(r) Herr/Frau <span class="header">' + name + ',</span></div>';
              }
          }
          this.mail.header = header;
          this.mail.footer = '<br /><div>' + this.user.metadata.signature + '</div>';
          let mail = header + '<br /><div>' + this.user.metadata.signature + '</div>';
          this.replyForm.content = mail;
      },
      showComment(id) {
        this.commentId = id;
        this.showcomment = true;
      },
			getReplies(){
					 this.$store
            .dispatch("ticket/getTicketReplies", this.$route.params.id)
            .then(response => {
                this.$Progress.finish();
                this.ticketReplies = response.data.data;
                setTimeout(() => {
                  this.isLoading = false;
                }, 1000);
            })
            .catch(error => {
                this.$Progress.fail();
                this.isLoading = false;
            });
			},
      getComments(){
           this.$store
            .dispatch("ticket/getTicketComments", this.$route.params.id)
            .then(response => {

                this.$Progress.finish();
                this.isLoading = false;
                this.ticketComments = response.data.data;

            })
            .catch(error => {
                this.$Progress.fail();
                this.isLoading = false;
            });
      },
      CreateComment() {
      	this.$Progress.start();
      	this.commentBtn = true;
            let formData = new FormData();
            if(this.form.files){
                for(var i = 0; i < this.form.files.length; i++ ){
                    let file = this.form.files[i];

                    formData.append('files[' + i + ']', file);
                }
            }
            formData.append('content', this.form.content);
            formData.append('ticket_id',this.ticket_id);
            formData.append('subject', this.form.subject);
            formData.append('bcc', this.form.bcc);
            axios.post( '/v-api/ticketComment',
                formData,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
            )
            .then(response => {
                $('#replyModal').modal('hide');
                this.$Progress.finish();
                Toast.fire({
                    type: "success",
                    title: "comment created successfully"
                });
                this.form.reset();
                this.form.clear();
                this.commentBtn = false;
                this.sheet = false;
                this.$emit('closeDialog');
                this.getComments();
            })
                .catch(error => {
                    this.$Progress.fail();
                    if (error.response) {
                        this.form.errors.errors = error.response.data.errors;
                    }
                    this.commentBtn = false;
                });
      },
      CreateReply() {
        if(this.form.subject == '') {
          Toast.fire({
              type: "error",
              title: "Please add subject to your reply"
          });
          return;
        }
      	this.$Progress.start();
      	this.replyBtn = true;
            let formData = new FormData();
            if(this.form.files){
                for(var i = 0; i < this.form.files.length; i++ ){
                    let file = this.form.files[i];

                    formData.append('files[' + i + ']', file);
                }
            }
            formData.append('content', this.form.content);
            formData.append('ticket_id',this.ticket_id);
            formData.append('subject', this.form.subject);
            formData.append('bcc', this.form.bcc);
            formData.append('cc', this.form.cc);
            axios.post( '/v-api/ticketReply',
                formData,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
            ).then(response => {
                $('#replyModal').modal('hide');
                this.$Progress.finish();
                this.getReplies();
                Toast.fire({
                    type: "success",
                    title: "Reply created successfully"
                });
                this.replyBtn = false;
                this.sheet = false;
                this.form.reset();
                this.form.clear();
                this.$emit('closeDialog');
            })
                .catch(error => {
                    this.$Progress.fail();
                    if (error.response) {
                        this.form.errors.errors = error.response.data.errors;
                    }
                    this.replyBtn = false;
                });
      },
      createSubReply(id) {
        if(this.replyForm.subject == '') {
          Toast.fire({
              type: "error",
              title: "Please add subject to your reply"
          });
          return;
        }
        if(this.replyForm.cc.length < 1 & this.subReplyCC.length > 0) {
          this.subReplyCC.forEach(mail => {
            this.replyForm.cc.push(mail.email)
          })
        }
      	this.$Progress.start();
      	this.subReplyBtn = true;
            let formData = new FormData();
            if(this.replyForm.files){
              for(var i = 0; i < this.replyForm.files.length; i++ ){
                let file = this.replyForm.files[i];
                formData.append('files[' + i + ']', file);
              }
            }
            formData.append('content', this.replyForm.content);
            formData.append('ticket_id',this.ticket_id);
            formData.append('reply_id',this.subreplyID);
            formData.append('subject', this.replyForm.subject);
            formData.append('bcc', this.replyForm.bcc);
            formData.append('cc', this.replyForm.cc);
            axios.post( '/v-api/ticketSubReply',
                formData,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
            ).then(response => {
                this.dialog = false;
                this.subReplyBtn = false;
                this.showreply = false;
                this.$Progress.finish();
                this.getReplies();
                Toast.fire({
                    type: "success",
                    title: "Reply created successfully"
                });
                this.replyForm.reset();
                this.replyForm.clear();
            })
                .catch(error => {
                	this.subReplyBtn = false;
                  this.showreply = false;
                    this.$Progress.fail();
                    if (error.response) {
                        this.form.errors.errors = error.response.data.errors;
                    }

                });
      },
      createSubComment() {
        this.$Progress.start();
        this.subCommentBtn = true;
            let formData = new FormData();
            if(this.commentForm.files){
              for(var i = 0; i < this.commentForm.files.length; i++ ){
                let file = this.commentForm.files[i];
                formData.append('files[' + i + ']', file);
              }
            }
            formData.append('content', this.commentForm.content);
            formData.append('comment_id',this.commentId);
            formData.append('subject', this.commentForm.subject);
            axios.post( '/v-api/ticketSubComment',
                formData,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
            ).then(response => {
                this.dialog = false;
                this.subCommentBtn = false;
                this.showcomment = false;
                this.$Progress.finish();
                  this.getComments();
                Toast.fire({
                    type: "success",
                    title: "Comment created successfully"
                });
                this.commentForm.reset();
                this.commentForm.clear();
            })
                .catch(error => {
                  this.subCommentBtn = false;
                  this.showcomment = false;
                    this.$Progress.fail();
                    if (error.response) {
                        this.form.errors.errors = error.response.data.errors;
                    }

                });
      },
      openEdit(comment, check){
          this.editMode = true;
          this.commentForm.fill(comment);
          if(check === 'comment'){
              this.CommentEdit = 'comment';
          }else{
              this.CommentEdit = 'subComment';
          }
      },
      editComment(form){
        this.$Progress.start();
        this.$store
            .dispatch("ticket/updateTicketComment", form)
            .then(response => {
                this.$Progress.finish();
                this.CommentEdit = '';
                this.editMode = false;
                this.commentForm.reset();
                this.commentForm.clear();
                this.getComments();
                Toast.fire({
                    type: "success",
                    title: "Comment updated successfully"
                });

            })
            .catch(error => {
                this.subCommentBtn = false;
                this.$Progress.fail();
                if (error.response) {
                    this.form.errors.errors = error.response.data.errors;
                }
            });
    },
      deleteComment(id){
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
                  .dispatch("ticket/deleteTicketComment", id)
                  .then(response => {
                      this.$Progress.finish();
                      this.getComments();
                      Toast.fire({
                          type: "success",
                          title: "Comment deleted successfully"
                      });

                  })
                  .catch(error => {
                      this.subCommentBtn = false;
                      this.showcomment = false;
                      this.$Progress.fail();
                      if (error.response) {
                          this.form.errors.errors = error.response.data.errors;
                      }

                  });
          }})
      },
      editSubComment(form){
        this.$Progress.start();
        this.$store
            .dispatch("ticket/updateTicketSubComment", form)
            .then(response => {
                this.$Progress.finish();
                this.CommentEdit = '';
                this.editMode = false;
                this.commentForm.reset();
                this.commentForm.clear();
                this.getComments();
                Toast.fire({
                    type: "success",
                    title: "Sub Comment updated successfully"
                });
            })
            .catch(error => {
                this.subCommentBtn = false;
                this.$Progress.fail();
                if (error.response) {
                    this.form.errors.errors = error.response.data.errors;
                }

            });
    },
        deleteSubComment(id){
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
                    .dispatch("ticket/deleteSubTicketComment", id)
                    .then(response => {
                        this.$Progress.finish();
                        this.getComments();
                        Toast.fire({
                            type: "success",
                            title: "Comment deleted successfully"
                        });

                    })
                    .catch(error => {
                        this.subCommentBtn = false;
                        this.showcomment = false;
                        this.$Progress.fail();
                        if (error.response) {
                            this.form.errors.errors = error.response.data.errors;
                        }

                    });
            }})
        },
       onlyUnique(value, index, self) {
            return self.indexOf(value) === index;
        }


	},
		props : ['ticket_id','ticket', 'showDialog','replyType'],
		mounted() {
            this.getReplies();
            this.getComments();
            if(this.ticketComments.length > 0 || this.ticketReplies.length > 0) {
              setTimeout(() => {
                this.dialogLoaded = false;
              }, 2000);
            }
            this.getCC();
            this.getBCC();
        },
        watch:{
            ticket: function(ticket) {
                // this.bccEmails = ticket.owner ?  ticket.owner.map(own => own.email) : []
                ticket.manager.map(man => man.email).forEach(email => {
                    this.bccEmails.push(email)
                });
                this.bccEmails = this.bccEmails.filter(this.onlyUnique)
            },
            my_employees() {
                this.employeesMails= []
                this.employees.forEach(employee => {
                    this.employeesMails.push(employee.email)
                });

            },
            my_owners() {
                this.clientsMails= []
                this.owners.forEach(owner => {
                    this.clientsMails.push(owner.email)
                });
            }
        },

        filters : {
            discussionDueDate: (value) => {
                return moment(value).fromNow()
            },
            dateFormat: (value) => {
              return moment(value).format('D MMM,H:mm')
            },
        },
		computed: {
            my_employees() {
                return this.employees;
            },
            my_owners() {
                return this.owners;
            },
            ticketReplieslength() {
              return this.ticketReplies.length;
            },
        ...mapGetters({
            user: "user/activeSingleUser",
            employees: "user/getRegularUsers",
            owners: "owner/activeOwners",
        }),
    },
    components: {
    	quillEditor,
      Template,
      ScaleLoader
    }
	}
</script>
<style scoped>
    .line{
        background-color: #234FA3;
        width: 4.3rem;
        height: .2rem;
        border-radius: 5px;
    }
  .nav-tabs .nav-link.active, .nav-tabs .nav-item.show .nav-link {
    color: #ffffff;
    background-color: #234FA3;
  }
  .nav-link {
    color: #000000;
  }
  .subReply {
    border-radius: 5px;
    background-color: #E6E6E6;
  }
#replyModal {
  width: 100%;
}

#actionButtons {
  float: right;
}
</style>
