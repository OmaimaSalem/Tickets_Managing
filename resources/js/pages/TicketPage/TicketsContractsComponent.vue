<template>
  <div>
      <button class="btn btn-primary float-right" @click="exportContracts">Export</button>
      <v-simple-table dense fixed-header>
                    <template v-slot:default>
                      <thead>
                        <tr>
                          <th class="text-left">{{ $t("ContractPage.firstName") }}</th>
                          <th width="text-left" class="text-left">{{ $t("ContractPage.lastName") }}</th>
                          <th class="text-left">Email</th>
                          <th width="text-left" class="text-left">{{ $t("ContractPage.company") }}</th>
                          <th width="text-left" class="text-left">{{ $t("ContractPage.address") }}</th>
                          <th width="text-left" class="text-left">{{ $t("ContractPage.Zipcode") }}</th>
                          <th width="text-left" class="text-left">{{ $t("ContractPage.city") }}</th>
                          <th width="text-left" class="text-left">{{ $t("ContractPage.country") }}</th>
                          <th width="text-left" class="text-left">{{ $t("ContractPage.issuedat") }}</th>
                          <th width="text-left" class="text-left">{{ $t("ContractPage.Actions") }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="contract in contracts.data" :key="contract.id">
                              <template v-if="contract.user">

                                                          <td>
                              {{ contract.user.metadata.first_name }}
                          </td>                          
                          <td>
                              {{ contract.user.metadata.last_name }}
                          </td>                          
                          <td>
                              {{ contract.user.email }}
                          </td>  
                          <td>
                              {{ contract.user.metadata.company }}
                          </td>                          
                          <td>
                              {{ contract.user.metadata.address }}
                          </td>                          
                         <td>
                              {{ contract.user.metadata.postal_code }}
                          </td>                          
                         <td>
                              {{ contract.user.metadata.state }}
                          </td>                          
                         <td>
                              {{ contract.user.metadata.country }}
                          </td>                          
                         <td>
                              {{ contract.created_at }}
                          </td>                          
                          <td>
                            <i @click.prevent="editContract(contract)" style="color:#81B488" class="fa fa-edit important-actions"></i>
                            <i @click.prevent="deleteContract(contract.id)" style="color:#C64848" class="fa fa-trash ml-1 important-actions"></i>
                          </td>

                              </template>
                              <template v-else>
                                <td>Contract has no user</td>
                              </template>

                        </tr>
                      </tbody>
                    </template>
                  </v-simple-table>
                  <pagination
                    :data="contracts"
                    :limit="parseInt(2)"
                    size="small"
                    @pagination-change-page="getContracts"
                  >
                    <span slot="prev-nav">&lt; Previous</span>
                    <span slot="next-nav">Next &gt;</span>
                  </pagination>











<!-- contract Modal -->
<div class="modal fade" id="contractModal" tabindex="-1" role="dialog" v-if="contract">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Contract</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <p v-if="contract.owner">Client: 
              <select @change="getClientData" v-model="client" class="form-control">
                <option v-for="(owner,index) in contract.owner" :key="index" :value="owner.id">{{ owner.name }}</option>
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

        <div class="tasks" v-if="contract.ticket">
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
        <button type="button" class="btn btn-primary" @click="editTicketContract">update</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>




  </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
  data(){
    return {
      selected:[],
      contruct_items:[],
      hour_price: 0,
      contract:null,
      ticket_total_time_box:null,
      contruct_task_selected:[],
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
    }
  },
  methods: {
    exportContracts(){

    window.open(
        this.$base_url +
          "/v-api/ticket_contract/excel-export",
        "_blank"
      );

      // this.$Progress.start();
      // this.$store
      //   .dispatch("ticketcontract/exportContracts")
      //   .then((response) => {
      //     console.log(response);
      //     this.$Progress.finish();
      //   })
      //   .catch((error) => {
      //     console.log(error);
      //     this.$Progress.fail();
      //   });
    },
    calculate_item_price(index){
      const item =  this.contruct_items[index];
      item.total_price = isNaN(item.amount * item.price) ? 0 : (item.amount * item.price);
      item.net = isNaN(item.total_price - item.vat) ? 0 : item.total_price - item.vat;
      item.total = isNaN(item.net - ((item.discount * item.net) / 100)) ? 0 : item.net - ((item.discount * item.net) / 100);
    },

    editTicketContract(){
      let contractData = {
        total_price : this.contract_total,
        ticket_id   : this.contract.ticket.id,
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
          this.getContracts();
          this.$Progress.finish();
        })
        .catch((error) => {
          console.log(error);
          this.$Progress.fail();
        });
    },
    editContract(contract){
      this.contract = contract;
      this.selected_client = this.contract.user;
      
      this.contruct_items = this.contract.items
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
      
      $("#contractModal").modal("show");
    },
    deleteContract(id){
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
                .dispatch("ticketcontract/deleteContract",id)
                .then((response) => {
                  this.getContracts();
                  this.$Progress.finish();
                })
                .catch((error) => {
                  console.log(error);
                  this.$Progress.fail();
                });

            };
        });
    },
    removeSelectedTasks(){
      this.contruct_task_selected = [];
    },
    addContructItem(){
      this.contruct_items.push({
        item:null,
        price:null,
        amount:1,
        total_price:null,
        vat:null,
        net:null,
        discount:0,
        total:null,
      });
    },
    removeContructItem(index){
      this.contruct_items.splice(index,1);
    },

    getContracts() {
        this.$Progress.start();
        this.$store
        .dispatch("ticketcontract/getContracts")
        .then((response) => {
          this.$Progress.finish();
        })
        .catch((error) => {
          console.log(error);
          this.$Progress.fail();
        });
  },

  }, 
  computed:{
    ...mapGetters({
        contracts: "ticketcontract/getContracts",
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

  },
  mounted(){
    this.getContracts();
  }
}
</script>

<style>

</style>