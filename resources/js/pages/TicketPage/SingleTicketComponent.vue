<template>
<span>
  <vue-headful
      :title="'Ticket: '+ticket.name"
  />
  <span v-if="ticketLoading">
    <!-- top card start -->
    <div class="row">
      <div class="col-md-12 px-0 py-0 mb-0">
        <div class="card h-30" v-if="!editTicket">
          <div class="card-header">
            <h3 class="card-title"><span style="font-weight: bold;font-size: 1.1rem;">{{$t('Ticket.title')}}: </span>{{ticket.name}}</h3>
            <div class="card-tools">
              <TicketAttachment v-if="ticket.ticketDialogFiles || ticket.files" :ticketDialogFiles="ticket.ticketDialogFiles" :files="ticket.files"/>
              <div class="btn btn-sm" @click="editTicket = true"><i class="text-green fas fa-edit"></i></div>
              <div class="btn btn-sm" @click="deleteTicket(ticket.id)"><i class="text-red fas fa-trash"></i></div>
              <div class="btn btn-sm btn-blue" @click="addSpam"><i class="text-yellow fas fa-pastafarianism"></i>&nbsp;&nbsp;{{$t('Ticket.spam')}}</div>
              <Mail :users="owners"></Mail>
              <div class="btn btn-sm btn-blue" @click="showHistory"><i class="text-yellow fas fa-history"></i>&nbsp;&nbsp;{{$t('Ticket.time_history')}}</div>
              <div class="btn btn-sm btn-blue" @click="gotoCalndar"><i class="text-yellow fas fa-calendar-alt"></i>&nbsp;&nbsp;{{$t('Ticket.calendar')}}</div>
              <div class="btn btn-sm btn-blue" @click="showContractPopup"><i class="text-yellow fas fa-file-contract"></i>&nbsp;&nbsp;{{ $t('Ticket.contract') }}</div>

              <div class="btn btn-sm btn-blue" @click="$refs.ticketDialog.closeTicketWithReply()"><i class="fas fa-comments text-yellow mr-1"></i>close with message</div>
              <div class="btn btn-sm btn-blue" @click="$refs.ticketDialog.closeTicket()"><i class="fas fa-comment-slash text-yellow mr-1"></i>close</div>
              <div class="btn btn-sm btn-blue" @click="$refs.ticketDialog.openTicket()"><i class="fas fa-door-open text-yellow mr-1"></i>open</div>
              <div class="btn btn-sm btn-blue" @click="$refs.ticketDialog.closeTicketCreateInvoice()"><i class="fas fa-hand-holding-usd text-yellow mr-1"></i>invoice</div>
              <div class="btn btn-sm btn-blue" @click="$refs.ticketDialog.closeTicket()"><i class="fas fa-free text-yellow mr-1"></i>free</div>



            </div>
          </div>
          <div class="card-body fixed-card-height pa-0">
            <div class="row">
              <div class="col-md-3 col-sm-6 pt-0 pb-0">
                <span class="font-weight-bold">{{$t('Ticket.Number')}}: </span>
                {{ticket.number}}
              </div>
              <div class="col-md-2 col-sm-6 pt-0 pb-0">
                <span class="font-weight-bold">{{$t('Ticket.status')}}: </span>
                {{ticket.status.name | returnDoing}}
              </div>
              <div class="col-md-2 col-sm-6 pt-0 pb-0">
                <span class="font-weight-bold">{{$t('Ticket.project')}}: </span>
                <span v-if="ticket.project">
                  {{ticket.project.name}}
                </span>
                <span v-else>---</span>
              </div>
              <div class="col-md-3 col-sm-6 pt-0 pb-0">
                <span class="font-weight-bold">{{$t('Ticket.lastUpdate')}}: </span>
                {{ticket.created_at | ticketUpdatedAt}}
              </div>
              <div class="col-md-2 col-sm-6 pt-0 pb-0">
                <span class="font-weight-bold">{{$t('Ticket.estimatedTime')}}: </span>
                <span v-if="ticket.estimated_time">
                  {{ticket.estimated_time}}
                </span>
                <span v-else>---</span>
              </div>
            </div>
            <div class="row">
              <div class="col-md-3 col-xs-12 pt-0 pb-0">
                <span class="font-weight-bold">{{$t('Ticket.client')}}: </span>
                  <router-link
                    v-for="owner in ticket.owner"
                    :key="owner.id"
                    :to="'/admin/profile/' +owner.id"
                    class="ml-1">
                    {{owner.name}}
                  </router-link>,
              </div>



             <div class="col-md-3 col-xs-12 pt-0 pb-0" v-if="ticket.manager" @click="showClientAttributes" style="display: inline-flex;">
                  <b>Client(s) Attributes</b>
              </div>





              <div class="col-md-6 col-xs-12 pt-0 pb-0">
                <span class="font-weight-bold">{{$t('Ticket.assignedUsers')}}: </span>
                  <span v-if="ticket.manager.length > 0">
                    <router-link
                      v-for="manager in ticket.manager"
                      :key="manager.id"
                      :to="'/admin/profile/' +manager.id"
                      class="ml-1">
                      {{manager.name}}
                    </router-link>,
                  </span>
                  <span v-else>
                    ---
                  </span>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12 pt-0 pb-0">
                <span class="font-weight-bold">{{$t('Ticket.desc')}}: </span>
                <p class="ml-1" v-html="ticket.description"></p>
              </div>
            </div>
            <TicketAttachment v-if="ticket.ticketDialogFiles || ticket.files" :ticketDialogFiles="ticket.ticketDialogFiles" :files="ticket.files" :isModal="false"/>


          </div>
        </div>
        <div v-else>
          <editTicketComponent @cancelEdit="editTicket = false" :ticketid = ticket.id @updated = "updated()"></editTicketComponent>
        </div>
      </div>
    </div>
    <!-- top card end -->
    <task-list :tasks="tasks" :singlePage="true"></task-list>
    <TicketDialog ref="ticketDialog" :ticket_id="ticket.id"  :ticket="ticket" @createInvoice="showContractPopup" ></TicketDialog>
    <v-col cols="8" style="margin-left: 50%;transform: translateX(-50%)">
       <v-card style="border-radius: 15px">
          <center>
            <div
                id="duration-text"
                v-if="formated_time"
                v-bind:class="{'text-success': formated_time}"
            >Current time:{{ formated_time }}</div>

            <div
                id="duration-text"
                v-if="formated_time"
                class="mt-2"
                v-bind:class="{'text-success': formated_time}"
            >All time:{{ formated_all_time }}</div>

            <div
                id="duration-text"
                v-else
                v-bind:class="{'text-danger': formated_time}"
            >{{ formated_time }}</div>
          </center>
       </v-card>
    </v-col>
  </span>
  <span v-else>
    <div class="box">
      <scale-loader :loading="loader.loading" :color="loader.color" :size="loader.size"></scale-loader>
    </div>
  </span>


  <div
          class="modal fade"
          id="TimeHistory"
          tabindex="-1"
          role="dialog"
          aria-labelledby="Modal"
          aria-hidden="true"
      >
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="newRoleLabel">{{$t('Ticket.history')}}</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <v-row>
                      <v-col>
                          <div v-show="listTracking_Ticket.length > 0" class="mb-2 overflow-auto shadow-sm">
                              <v-simple-table>
                                  <thead>
                                  <tr>
                                      <th class="text-left">{{$t('Ticket.startedAt')}}</th>
                                      <th class="text-left">{{$t('Ticket.endAt')}}</th>
                                      <th class="text-left">{{$t('Ticket.duration')}}</th>
                                      <th class="text-left">Actions</th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  <tr v-for="item in listTracking_Ticket" :key="item.id">
                                      <td><span class="font-weight-light">{{ item.start_at | DateWithTime }}</span></td>
                                      <td><span class="font-weight-light">{{ item.end_at | DateWithTime }}</span></td>
                                      <td><span class="font-weight-light">{{ humanReadableFromSecounds(item.count_time) }}</span></td>
                                      <td>
                                          <button @click="editModel(item)" class="btn btn-sm">
                                              <div class="fa fa-edit fa-fw"></div>
                                          </button>
                                          <button @click="deleteTrackHistory(item.id)" class="btn btn-sm">
                                              <div class="fa fa-trash fa-fw"></div>
                                          </button>
                                      </td>
                                  </tr>
                                  </tbody>
                              </v-simple-table>
                          </div>
                      </v-col>
                  </v-row>
              </div>
          </div>
      </div>



      <!-- Modal -->
      <div
          class="modal fade"
          id="TimeModal"
          tabindex="-1"
          role="dialog"
          aria-labelledby="Modal"
          aria-hidden="true"
      >
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="newRoleLabel">{{$t('Task.editTrack')}}</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <form @submit.prevent="editTrackingTime()" @keydown="form.onKeydown($event)">
                      <div class="modal-body">
                          <div class="form-group">
                              <label for="start_at">{{$t('Task.startedAt')}}</label>
                              <date-picker
                                  v-model="form.start_at"
                                  lang="en"
                                  type="datetime"
                                  format="dd-MM-YYYY HH:mm"
                                  :minute-step="1"
                                  value-type="format"
                                  input-class="form-control"
                              ></date-picker>
                              <has-error :form="form" field="start_at"></has-error>
                          </div>
                          <div class="form-group">
                              <label for="end_at">{{$t('Task.endAt')}}</label>
                              <date-picker
                                  v-model="form.end_at"
                                  lang="en"
                                  type="datetime"
                                  format="DD-MM-YYYY HH:mm"
                                  :minute-step="1"
                                  input-class="form-control"
                                  value-type="format"
                              ></date-picker>
                              <has-error :form="form" field="end_at"></has-error>
                          </div>
                      </div>

                      <div class="modal-footer">
                          <button type="submit" class="btn btn-success">{{$t('Task.update')}}</button>
                      </div>
                  </form>
              </div>
          </div>

      </div>

    <!-- leave Modal -->
    <div
      class="modal fade"
      id="LeaveTimeModal"
      tabindex="-1"
      role="dialog"
      aria-labelledby="Modal"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="newRoleLabel">{{$t('Task.editTrack')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form @submit.prevent="saveLeaveTime()" @keydown="form.onKeydown($event)">
            <div class="modal-body">
              <div class="form-group">
                <label for="start_at">{{$t('Task.startedAt')}}</label>
                <date-picker
                  v-model="form.start_at"
                  lang="en"
                  type="datetime"
                  format="DD-MM-YYYY HH:mm"
                  :minute-step="1"
                  value-type="format"
                  input-class="form-control"
                ></date-picker>
                <has-error :form="form" field="start_at"></has-error>
              </div>
              <div class="form-group">
                <label for="end_at">{{$t('Task.endAt')}}</label>
                <date-picker
                  v-model="form.end_at"
                  lang="en"
                  type="datetime"
                  format="DD-MM-YYYY HH:mm"
                  :minute-step="1"
                  input-class="form-control"
                  value-type="format"
                ></date-picker>
                <has-error :form="form" field="end_at"></has-error>
              </div>

              <div class="form-group">
                <label for="end_at">{{$t('Task.reason')}}</label>
                <quill-editor
                  id="comments-editor"
                  v-model="form.reason"
                  ref="myQuillEditor"
                  :options="editorOption"
                ></quill-editor>
              </div>
            </div>

            <div class="modal-footer">
              <button type="submit" class="btn btn-danger" @click="cancelTracking">{{$t('Ticket.cancel')}}</button>
              <button type="submit" class="btn btn-success">{{$t('Ticket.save')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>




<!-- Modal -->
<div class="modal fade" id="clientAttributesModal" tabindex="-1" role="dialog" aria-labelledby="clientAttributesModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="clientAttributesModalLabel">Clients attributes</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" v-if="ticket.owner">
            <v-row justify="center">
                <v-expansion-panels accordion>
                <v-expansion-panel
                    v-for="(owner,i) in ticket.owner"
                    :key="i"
                >
                    <v-expansion-panel-header>{{ owner.name }}</v-expansion-panel-header>
                    <v-expansion-panel-content>
                            <v-row>
                                <v-col v-for="(attribute,index) in owner.attributes" :key="'attribute-'+index" col="3">{{ attribute.name }}</v-col>
                            </v-row>
                    </v-expansion-panel-content>
                </v-expansion-panel>
                </v-expansion-panels>
            </v-row>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>




<!-- contract Modal -->
<div class="modal fade" id="contractModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Contract</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <p v-if="ticket.owner">Client:
              <select @change="getClientData" v-model="client" class="form-control">
                <option v-for="(owner,index) in ticket.owner" :key="index" :value="owner.id">{{ owner.name }}</option>
              </select>
        </p>
        <div>
            <ul class="list-unstyled">
              <li>
                  <div class="form-row">

                    <div class="col-md-4 input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">Firstname</span>
                        </div>
                      <input class="form-control" id="first_name" type="text" name="first_name" v-model="selected_client.metadata.first_name" />
                    </div>

                    <div class="col-md-4 input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">Lastname</span>
                      </div>
                      <input class="form-control" id="last_name" type="text" name="last_name" v-model="selected_client.metadata.last_name" />
                    </div>
                    <div class="col-md-4 input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Email</span>
                    </div>
                    <input class="form-control" id="email" type="text" name="first_name" v-model="selected_client.email" />
                    </div>
                  </div>
              </li>

              <li>
                  <div class="form-row">
                    <div class="col-md-4 input-group">
                      <div class="input-group-prepend">
                          <span class="input-group-text">Company</span>
                      </div>
                      <input class="form-control" id="company" type="text" name="company" v-model="selected_client.metadata.company" />
                    </div>
                    <div class="col-md-4 input-group">
                    <div class="input-group-prepend">
                          <span class="input-group-text">Address</span>
                    </div>
                    <input class="form-control" id="address" type="text" name="address" v-model="selected_client.metadata.address" />
                    </div>
                    <div class="col-md-4 input-group">
                    <div class="input-group-prepend">
                          <span class="input-group-text">Zipcode</span>
                    </div>
                    <input class="form-control" id="postal_code" type="text" name="postal_code" v-model="selected_client.metadata.postal_code" />
                    </div>
                  </div>
              </li>




              <li>
                  <div class="form-row">
                    <div class="col-md-6 input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">State</span>
                        </div>

                      <input class="form-control" id="state" type="text" name="state" v-model="selected_client.metadata.state" />
                    </div>
                    <div class="col-md-6 input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">Country</span>
                        </div>
                      <input class="form-control" id="country" type="text" name="country" v-model="selected_client.metadata.country" />
                    </div>
                  </div>
              </li>

            </ul>
        </div>

        <div class="tasks">
          <table class="table table-hover table-sm table-bordered text-left">
            <thead class="thead-dark">
              <tr>
                <th scope="col">
                  <button class="btn btn-transparent white-text btn-sm" @click="addContructItem"><i class="fa fa-plus"></i></button>
                </th>
                <th scope="col">Title</th>
                <th scope="col">Unite price</th>
                <th scope="col">Amount</th>
                <th scope="col">Total price</th>
                <th scope="col">Vat</th>
                <th scope="col">Net</th>
                <th scope="col">Discount %</th>
                <th scope="col">Total gross</th>
              </tr>
            </thead>
            <tbody>

              <tr v-for="(item,index) in contruct_items" :key="index">
                <td><a class="btn btn-danger btn-sm" @click="removeContructItem(index)"><i class="fa fa-minus"></i></a></td>
                <td><input type="text" class="form-control" v-model="contruct_items[index].item" /></td>
                <td><input type="text" @input="calculate_item_price(index)" class="form-control" v-model="contruct_items[index].price" /></td>
                <td><input type="text" @input="calculate_item_price(index)" class="form-control" v-model="contruct_items[index].amount" /></td>
                <td><input type="text" disabled readonly class="form-control" v-model="contruct_items[index].total_price" /></td>
                <td><input type="text" @input="calculate_item_price(index)" class="form-control" v-model="contruct_items[index].vat" /></td>
                <td><input type="text" disabled readonly class="form-control" v-model="contruct_items[index].net" /></td>
                <td><input type="text" @input="calculate_item_price(index)" class="form-control" v-model="contruct_items[index].discount" /></td>
                <td><input type="text" disabled readonly class="form-control" v-model="contruct_items[index].total" /></td>
              </tr>

              <tr>
                <th scope="col">
                  <button class="btn btn-transparent white-text btn-sm" @click="addContructItem"><i class="fa fa-plus"></i></button>
                </th>
                <td colspan="6"></td>
                <td class="text-right"><strong>Total:</strong></td>
                <td><strong>{{contract_total}}</strong></td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" @click="createTicketContract">Save changes</button>
        <button type="button" class="btn btn-danger" v-if="contract_id" @click="deleteContract()">Delete Contract</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


</span>
</template>

<script>
import moment from "moment";
import ScaleLoader from 'vue-spinner/src/ScaleLoader.vue'
import { mapGetters } from "vuex";
import TicketConversations from "../../components/tickets/TicketConversations";
import TicketAttachment from "../../components/tickets/TicketAttachment";
import DatePicker from "vue2-datepicker";
import { quillEditor } from "vue-quill-editor";
import Mail from "../../components/send_mail/Template";
import editTicketComponent from "./editTicketComponent"
import TicketDialog from "./Dialog/TicketDialog.vue"


export default {
  data() {
    return {
      contract_id:null,
      loader: {
        loading: true,
        color: '#2959B5',
        size: '70px',
      },
      contruct_task_selected:[],
      hour_price: 0,
      ticket_time:0,
      client:null,
      selected_client:{
        metadata:{
            first_name : "",
            last_name  : "",
            address    : "",
            postal_code: "",
            country    : "",
            state      : "",
        }
      },
      contruct_items:[],
      ticketLoading: false,
      deleting_ticket:false,
      cancel_tracking:false,
      editTicket : false,
      dialog: false,
      ticketID:'',
      showAssignedMangers:false,
      showOwners:false,
      track_time: 0,
      window: 0,
      ticketId: this.$route.params.id,
      formated_time: null,
      formated_all_time: null,
      counter: { track_time: 0, seconds: 0 },
      listTracking_Ticket: [],
      started_at: null,
      next: null,
      form: new Form({
        id: "",
        start_at: "",
        end_at: "",
      }),
      page: 1,
      ticket_total_time_box: 0,
      editorOption: {
        modules: {
          toolbar: [
            ["bold", "italic", "underline", "strike"],
            ["blockquote", "code-block"],
            [{ list: "ordered" }, { list: "bullet" }],
          ],
        },
      },
    };
  },
  methods: {
    calculate_item_price(index){
      const item =  this.contruct_items[index];
      // item.amount = item.amount == "" ? 0 : item.amount;
      // item.vat = item.vat == "" ? 0 : item.vat;
      item.total_price = isNaN(item.amount * item.price) ? 0 : (item.amount * item.price);
      item.net = isNaN(item.total_price - item.vat) ? 0 : item.total_price - item.vat;
      item.total = isNaN(item.net - ((item.discount * item.net) / 100)) ? 0 : item.net - ((item.discount * item.net) / 100);
    },
    removeSelectedTasks(){
      this.contruct_task_selected = [];
    },
    getClientData(){
      this.selected_client = this.ticket.owner.find(own => own.id = this.client);
      if(!this.selected_client.metadata) {
        this.selected_client.metadata = {
            first_name : "",
            last_name  : "",
            address    : "",
            postal_code: "",
            country    : "",
            state      : "",
        }
      }
    },
    addContructItem(){
      this.contruct_items.push({
        item:null,
        price:0,
        amount:1,
        total_price:0,
        vat:0,
        net:0,
        discount:0,
        total:0,
      });
    },
    removeContructItem(index){
      this.contruct_items.splice(index,1);
    },
    showContractPopup(){
      // this.contruct_task_selected = [];
      // this.hour_price = 0;
      // this.contruct_items = [];
      $('#contractModal').modal('show');
    },
    showClientAttributes(){
      $('#clientAttributesModal').modal('show');
    },
    createTicketContract(){



      if(!this.selected_client){
          Toast.fire({
              type: "error",
              title: "please select client first"
          });
        return false;
      }
      let contractData = {
        total_price : this.contract_total,
        ticket_id   : this.ticket.id,
        items       : this.contruct_items,
        tasks       : this.contruct_task_selected,
        hour_price  : this.hour_price,
        ticket_time : this.ticket_total_time_box,
        client      : this.selected_client
      };
      this.$Progress.start();
      this.$store
        .dispatch("ticketcontract/createContract", {contractData})
        .then((response) => {
          $('#contractModal').modal('hide');
          this.getTicketById(this.ticket.id);
          Toast.fire({
              type: "success",
              title: "contract created successfully"
          });
          this.$Progress.finish();
        })
        .catch((error) => {
          if(error.response){
            this.$Progress.fail();
            Toast.fire({
                type: "error",
                title: error.response.data.message,
              });
            return false;
          }
        });
    },

    deleteContract(){
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
                .dispatch("ticketcontract/deleteContract",this.contract_id)
                .then((response) => {
                  $('#contractModal').modal('hide');
                  this.getTicketById(this.ticket.id);
                  this.contract_id = null;
                  this.contruct_items = [];
                  Toast.fire({
                    type: "success",
                    title: "contract deleted successfully"
                  });

                  this.$Progress.finish();
                })
                .catch((error) => {
                  console.log(error);
                  this.$Progress.fail();
                });

            };
        });
    },
    gotoCalndar(){
        this.$router.push({
            path: `/admin/ticket/${this.$route.params.id}/calendar`
        });
      },
      cancelTracking(){
        this.cancel_tracking = true;
      },
      updated(){
          this.editTicket = false;
          this.getTicketById(this.ticketId);
      },
      showHistory(){
          $("#TimeHistory").modal("show");
      },
      hideHistory(){
          $("#TimeHistory").modal("hide");
      },
    getTicketById(id) {
      this.loading = true;
      this.$Progress.start();
      this.$store
        .dispatch("ticket/getTicketById", id)
        .then((response) => {
          let data = response.data.data;

          if(data.ticketContract){
          this.contract_id = data.ticketContract.id;
          this.contruct_items = [];
          console.log(data.ticketContract.items);
          this.contruct_items = data.ticketContract.items
          .map(item => {
              return {
                item:item.item,
                price:item.item_price,
                amount:item.item_count,
                total_price:item.item_total_price,
                vat:item.item_vat,
                net:item.item_total,
                discount:item.item_discount,
                total:item.item_total,
              }
          });
          // this.contruct_task_selected = data.ticketContract ? data.ticketContract.items.filter(item =>  item.item == "task").map(item => item.item_id+"-"+item.item_count) : [];
          // this.hour_price = data.ticketContract ? parseFloat(data.ticketContract.items.find(item => item.item == "ticket time").item_price) : 0;
          this.selected_client = this.ticket.owner.find(own => own.id = data.ticketContract.user_id);
          this.client =  data.ticketContract.user_id;

          }
          this.$Progress.finish();
          this.ticketLoading = true;
        })
        .catch((error) => {
          console.log(error);
          this.$Progress.fail();
        });
      this.$store
        .dispatch("ticket/getTicketConversations", id)
        .then((response) => {
          this.$Progress.finish();
        })
        .catch((error) => {
          this.$Progress.fail();
        });
    },
    getTasksByTicketId(page = 1) {
      this.$Progress.start();
      this.$store
        .dispatch("task/getTasksByTicketId", { id: this.ticketId, page: page })
        .then((response) => {
          this.listTrackingTicket();
          this.$Progress.finish();
        })
        .catch((error) => {
          this.$Progress.fail();
        });
    },
    deleteTicket(id) {
      this.deleting_ticket = true;
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
              this.$router.push({ name: "tickets.list" });
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
    addSpam() {
      let selected = [];
      selected.push(this.ticketId);
      this.$store
        .dispatch("ticket/addTicketToSpam", { selected })
        .then((res) => {
          Toast.fire({
            type: "success",
            title: res.data.message,
          });
          this.cancel_tracking = true;
          this.$router.push({name: 'tickets.list'});
        })
        .catch((error) => {
          console.log(error);
        });
    },

    start_track() {
      this.tracking = true;
      this.counter.timer = setInterval(() => {
        this.formated_time = this.humanReadableFromSecounds(
          parseInt(++this.track_time)
        );
      }, 1000);
      let $this = this;
      this.$Progress.start();
      this.$store
        .dispatch("ticket/startTime", {
          ticket_id: this.ticketId,
        })
        .then((response) => {
          $this.counter.all_timer = setInterval(() => {
            $this.formated_all_time = $this.humanReadableFromSecounds(
              parseInt(response.data.data) + parseInt(this.track_time)
            );
          }, 1000);
        })
        .catch((error) => {
          this.$Progress.fail();
          Toast.fire({
            type: "error",
            title: error.response.data.message,
          });
        });
    },

    end_track() {
      this.tracking = false;

      let $this = this;
      this.$Progress.start();
      this.$store
        .dispatch("ticket/endTime", {
          ticket_id: this.ticket.id,
        })
        .then((response) => {
          clearInterval($this.counter.timer);
          this.formated_time = null;
        })
        .catch((error) => {
          this.$Progress.fail();
          Toast.fire({
            type: "error",
            title: error.response.data.message,
          });
        });
    },
    listTrackingTicket(ticket_id) {
      if (this.listTracking_Ticket.length > 0) {
        this.listTracking_Ticket = [];
      } else {
        this.$store
          .dispatch("ticket/getTrackHistory", {
            ticket_id: this.ticket.id,
          })
          .then((response) => {
            this.listTracking_Ticket = response.data;
          })
          .catch((error) => {
            this.$Progress.fail();
            Toast.fire({
              type: "error",
              title: error.response.data.message,
            });
          });
      }
    },
    editModel(track) {
      this.form.reset();
      $("#TimeModal").modal("show");
      this.form.fill(track);
    },
    editTrackingTime() {
      this.$Progress.start();
      this.$store
        .dispatch("ticket/editTrackHistory", this.form)
        .then((response) => {
          this.$Progress.finish();
          let index = this.listTracking_Ticket
            .map((track) => track.id)
            .indexOf(this.form.id);
          this.listTracking_Ticket[index].start_at = this.form.start_at;
          this.listTracking_Ticket[index].end_at = this.form.end_at;
          this.listTracking_Ticket[index].count_time =
            response.data.data.count_time;

          $("#TimeModal").modal("hide");

          Toast.fire({
            type: "success",
            title: response.data.message,
          });
        })
        .catch((error) => {
          this.$Progress.fail();
          Toast.fire({
            type: "error",
            title: error.response.data.message,
          });
        });
    },

    deleteTrackHistory(track_id) {
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
            .dispatch("ticket/deleteTrackHistory", {
              track_id: track_id,
            })
            .then((response) => {
              this.$Progress.finish();
              this.listTracking_Ticket = this.listTracking_Ticket.filter(
                (track) => track.id != track_id
              );
              Toast.fire({
                type: "success",
                title: response.data.message,
              });
            })
            .catch((error) => {
              this.$Progress.fail();
              Toast.fire({
                type: "error",
                title: error.response.data.message,
              });
            });
        }
      });
    },
    getCurrentTimeStamp() {
      return (
        this._padNumber(new Date().getDate()) +
        "/" +
        this._padNumber(new Date().getMonth() + 1) +
        "/" +
        this._padNumber(new Date().getFullYear()) +
        " " +
        this._padNumber(new Date().getHours()) +
        ":" +
        this._padNumber(new Date().getMinutes())
        // +
        // ":" +
        // this._padNumber(new Date().getSeconds())
      );
    },
    saveLeaveTime() {
      this.form.ticket_id = this.ticket.id;
      this.$Progress.start();
      this.$store
        .dispatch("ticket/saveTrack", this.form)
        .then((response) => {
          this.$Progress.finish();
          $("#LeaveTimeModal").modal("hide");
          this.next();
        })
        .catch((error) => {
          this.$Progress.fail();
          Toast.fire({
            type: "error",
            title: error.response.data.message,
          });
        });
    },
    getOwners() {
      this.$store
        .dispatch("owner/getOwners")
        .then(() => {})
        .catch((error) => {
          console.log(error);
        });
    },
    getEmployees() {
      // this.$store
      //   .dispatch("user/getRegularUsers", {
      //     roles: true,
      //     dropdown: true
      //   })
      //   .then(() => {})
      //   .catch(() => {});
    },
    getStatus() {
      this.$store
        .dispatch("ticket/getStatus")
        .then((response) => {})
        .catch((error) => {
          console.log(error);
        });
    },
    humanReadableFromSecounds(seconds) {
      let duration = this._readableTimeFromSeconds(seconds);
      return `${duration.hours}:${duration.minutes}:${duration.seconds}`;
    },
    _readableTimeFromSeconds: function (seconds) {
      const hours = 3600 > seconds ? 0 : parseInt(seconds / 3600, 10);
      return {
        hours: this._padNumber(hours),
        seconds: this._padNumber(seconds % 60),
        minutes: this._padNumber(parseInt(seconds / 60, 10) % 60),
      };
    },
    _padNumber: (number) =>
      number > 9 ? number : number === 0 ? "00" : "0" + number,
  },
  created() {
    this.getStatus();
  },
  mounted() {
    this.getTicketById(this.ticketId);
    this.getTasksByTicketId();
    this.started_at = this.getCurrentTimeStamp();
    this.start_track();
    this.getOwners();
    this.getEmployees();

  },

  beforeRouteLeave(to, from, next) {
    if(this.deleting_ticket === true || this.cancel_tracking ===true){
        next(true);
    }
    else{
        let started_at = this.started_at;

        let end_at =
            this._padNumber(new Date().getDate()) +
            "-" +
            this._padNumber(new Date().getMonth() + 1) +
            "-" +
            this._padNumber(new Date().getFullYear()) +
            " " +
            this._padNumber(new Date().getHours()) +
            ":" +
            this._padNumber(new Date().getMinutes())
            // +
            // ":" +
            // this._padNumber(new Date().getSeconds());

        this.form.reset();
        $("#LeaveTimeModal").modal("show");
        this.form.fill({
            start_at: this.started_at.replaceAll('/','-'),
            end_at: end_at,
            reason: "",
            to: to.path,
        });
        console.log(this.form);
        this.next = next;
        let $seconds =
            (Date.parse(this.form.end_at) - Date.parse(this.form.start_at)) / 1000;
        let minutes = $seconds / 60;
        next(false);
    }
  },
  watch: {
    listTracking_Ticket(){
      if(this.ticket.ticketContract && !this.contruct_items.find(item=> item.item == "ticket time")){
          this.contruct_items.unshift({
            item:'ticket time',
            price:0,
            amount:this.ticket.ticketContract.items.find(item=> item.item == "ticket time")? this.ticket.ticketContract.items.find(item=> item.item == "ticket time").item_count : 0,
            total_price:0,
            vat:0,
            net:0,
            discount:0,
            total:0,
          });
        }
      else{
          this.contruct_items.unshift({
            item:'ticket time',
            price:0,
            amount:Math.round(this.listTracking_Ticket.reduce((sum, item) => sum + item.count_time, 0) / 3600),
            total_price:0,
            vat:0,
            net:0,
            discount:0,
            total:0,
          });
      }
    },
    tasks(tasks){
      var $this = this;
      tasks.data.filter(item => item.status_id == 4).map(function(item){

        if(!$this.contruct_items.find(item => item.name == item.name)){
          $this.contruct_items.unshift({
            item:item.name,
            price:0,
            amount:1,
            total_price:0,
            vat:0,
            net:0,
            discount:0,
            total:0,
          });
        }


      });
    }
  },
  computed: {
    ...mapGetters({
      ticket: "ticket/activeTicket",
      tasks: "task/activeTasks",
      owners: "owner/activeOwners",
      conversations: "ticket/activeTicketConversations"
    }),
    // ticket_total_time(){
    //   return Math.round(this.listTracking_Ticket.reduce((sum, item) => sum + item.count_time, 0) / 3600);
    // },
    // total_time_price(){
    //   return (parseInt(this.ticket_total_time_box) + this.contruct_task_selected.reduce((sum,element) => sum + parseFloat(element.split('-')[1]),0))  * this.hour_price;
    // },
    contract_total(){
      const contractTotal = this.contruct_items.reduce((sum,element) => sum + parseInt(element.total),0);
      return isNaN(contractTotal) ? 0 : contractTotal;
      // return this.total_time_price + this.contruct_items.reduce((sum,element) => sum + parseInt(element.price),0)
    },

    // loading() {
    //   if(this.ticket) {
    //     return false;
    //   } else {
    //     return true;
    //   }
    // }
  },
  components: {
    TicketAttachment,
    ScaleLoader,
    DatePicker,
    quillEditor,
    Mail,
    editTicketComponent,
    TicketDialog,

  },
  filters: {
      subStrCardOwners: (value) => {
          if(value.length > 9) {
              return value.substring(0, 9) + '...';
          }
          return value;
      },
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
    ticketDueDate: (value) => {
        return moment(value).fromNow()
    },
    ticketUpdatedAt: (value) => {
        return moment(value).format('DD.MM.YYYY hh:mm')
    },
    returnDoing : value => {
        if(value == 'in-progress'){
            return 'doing';
        }else {
            return value;
        }
      },
  },
};
</script>
<style>
.direct-chat-text {
  border: 1px solid #d2d2d2;
}
.right > .direct-chat-text {
  background: #f2f2f2;
  color: #000;
  border: 1px solid #d2d6de;
}
.header {
  border-bottom: 1px dotted #000;
}

#duration-text {
  font-size: 36px;
  font-weight: 300;
}
.mx-datepicker {
  display: block;
  width: 100%;
}
.invalid-feedback {
  display: inline;
}

.toggle-div li:hover{
    background: #eaeaea;
    transition: .4s ease;
    color: #000000;
    text-decoration:none;
}
.toggle-div li a{
    color: #000000;
    text-decoration:none;
}
.toggle-div li {
    width: 100%;
}
.toggle-menu-card{
    z-index: 9;
    background-color: #ffffff;
    position: absolute;
    margin-top: 2.3rem;
    padding:0;
    width:10rem;
    border-radius:5px;
    box-shadow:0 3px 1px -2px rgba(0,0,0,.2),0 2px 2px 0 rgba(0,0,0,.14),0 1px 5px 0 rgba(0,0,0,.12);
}

/*new style*/
.ticket-header{
    height: 2.5rem;
    border-radius: 15px;
    background: linear-gradient(180deg, #979797 0%, #C8C8C8 0%, #F4F4F4 0%, rgba(194, 194, 194, 0.968902) 87.18%, rgba(152, 152, 152, 0.922083) 139.14%, rgba(151, 151, 151, 0.92) 142.86%);
}

/*new style*/

.text-green {
  color: #276D30;
}
.text-red {
  color: #BB3E3E;
}
.text-yellow {
  color: #FAB10B;
}
.tex-blue {
  color: #234FA3;
}
.bg-gray {
  background-color: #f6f6f6;
}
.bg-yellow {
  background-color: #FAB10B;
}
.btn-blue {
  background-color: #234FA3;
  color: #ffffff;
}
.btn-blue:hover {
  text-decoration: none;
  background-color: #2959B5;
  color: #ffffff;
}
.btn-red {
  background-color: #C14444;
  color: #ffffff;
}
.btn-red:hover {
  background-color: #C26A6A;
  color: #ffffff;
}
.box{
    display: flex;
    justify-content: center;
    align-items: center;
    padding-top: 4rem;
}
.fixed-card-height { height: 350px; overflow-y: scroll; }
.multiselect__tag {
  background: #234FA3 !important;
}
.multiselect__tag-icon:focus, .multiselect__tag-icon:hover {
    background: #234FA3 !important;
    color: #ffffff !important;
}
.card-title {
  white-space: pre-wrap;
}
</style>
