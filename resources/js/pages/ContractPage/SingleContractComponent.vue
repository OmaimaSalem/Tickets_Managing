<template>
<div class="single-contract">
    <template v-if="!loading">
        <div class="card">
            <div class="card-header">
                <div class="card-title">{{$t('ContractPage.contracts')}}: {{contract.number}}</div>
                <div class="card-tools">
                  <a href="#" @click="sendContract()" class="btn btn-app">
                      <i class="fas fa-envelope"></i> {{$t('ContractPage.sendContract')}}
                  </a>
                  <a href="#" @click="printContract()" class="btn btn-app">
                      <i class="fas fa-print"></i> {{$t('ContractPage.print')}}
                  </a>
                  <PrintContractComponent style="display: none; " :contract="contract" />
                    <!-- <a href="#" @click="deleteContract(contract.id)" class="btn btn-danger btn-xs">
                        <i class="fas fa-trash fa-fw"></i>
                    </a> -->
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Customer Data Start -->
                    <div class="col-md-6">
                        <div class="row" style="border-right: 1px solid #2d2d2d;">
                            <div class="col-md-12">
                                <h5>{{$t('ContractPage.customerData')}}</h5>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="client">{{$t('ContractPage.date')}}</label>
                                <input disabled class="form-control form-control-sm" type="date" name="date" v-model="contract.date">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="client">{{$t('ContractPage.client')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="client" v-model="contract.client.name">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="title">{{$t('ContractPage.title')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="title" v-model="contract.client.metadata.title">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="greeting">{{$t('ContractPage.greeting')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="greeting" v-model="contract.client.metadata.greeting">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="company">{{$t('ContractPage.company')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="company" v-model="contract.client.metadata.company">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="attention">{{$t('ContractPage.firstName')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="first_name" v-model="contract.client.metadata.first_name">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="name">{{$t('ContractPage.name')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="name" v-model="contract.client.name">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="street">{{$t('ContractPage.street')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="street" v-model="contract.client.metadata.street">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="city">{{$t('ContractPage.city')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="city" v-model="contract.client.metadata.city">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="country">{{$t('ContractPage.country')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="country" v-model="contract.client.metadata.country">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="postal_code">{{$t('ContractPage.postalCode')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="postal_code" v-model="contract.client.metadata.postal_code">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="telephone">{{$t('ContractPage.tel')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="telephone" v-model="contract.client.metadata.telephone">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="mobile">{{$t('ContractPage.mobile')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="mobile" v-model="contract.client.metadata.mobile">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="fax">{{$t('ContractPage.fax')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="fax" v-model="contract.client.metadata.fax">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="email">{{$t('ContractPage.email')}}</label>
                                <input disabled class="form-control form-control-sm" type="email" name="email" v-model="contract.client.metadata.email">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="ebay-user">{{$t('ContractPage.eBayUser')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="ebay-user" v-model="contract.client.metadata.ebay_account">
                            </div>
                        </div>
                    </div>
                    <!-- Customer Data End -->
                    <!-- Delevery Address Start -->
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <h5>{{$t('ContractPage.deleveryAddress')}}</h5>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="title">{{$t('ContractPage.title')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="title" v-model="contract.title">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="greeting">{{$t('ContractPage.greeting')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="greeting" v-model="contract.greeting">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="first_name">{{$t('ContractPage.firstName')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="first_name" v-model="contract.first_name">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="name">{{$t('ContractPage.name')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="name" v-model="contract.name">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="street">{{$t('ContractPage.street')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="street" v-model="contract.street">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="city">{{$t('ContractPage.city')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="city" v-model="contract.city">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="country">{{$t('ContractPage.country')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="country" v-model="contract.country">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="postal_code">{{$t('ContractPage.postalCode')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="postal_code" v-model="contract.postal_code">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="telephone">{{$t('ContractPage.tel')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="telephone" v-model="contract.telephone">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="mobile">{{$t('ContractPage.mobile')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="mobile" v-model="contract.mobile">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="fax">{{$t('ContractPage.fax')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="fax" v-model="contract.fax">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="email">{{$t('ContractPage.email')}}</label>
                                <input disabled class="form-control form-control-sm" type="email" name="email" v-model="contract.email">
                            </div>
                            <div class="form-group col-md-12">
                                <label for="notes">{{$t('ContractPage.notes')}}</label>
                                <textarea disabled class="form-control form-control-sm" name="notes" v-model="contract.notes"></textarea>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="printed_text">{{$t('ContractPage.printedText')}}</label>
                                <textarea disabled class="form-control form-control-sm" name="printed_text" v-model="contract.printed_text"></textarea>
                            </div>
                        </div>
                    </div>
                    <!-- Delevery Address End -->
                </div>
            </div>
        </div>
        <!-- Selected Item Start -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">{{$t('ContractPage.contractItems')}}</div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <table class="table table-hover table-sm">
                            <thead>
                                <th>ID</th>
                                <th>{{$t('ContractPage.name')}}</th>
                                <th>{{$t('ContractPage.price')}}</th>
                                <th>{{$t('ContractPage.qty')}}</th>
                                <th>{{$t('ContractPage.totalNoTax')}}</th>
                                <th>{{$t('ContractPage.tax')}}%</th>
                                <th>{{$t('ContractPage.taxPrice')}}</th>
                                <th>{{$t('ContractPage.total')}}</th>
                            </thead>
                            <tbody>
                                <tr v-for="item in contract.items.data" :key="item.id">
                                    <td>{{item.id}}</td>
                                    <td>{{item.name}}</td>
                                    <td>{{item.price}}</td>
                                    <td>{{item.qty}}</td>
                                    <td>{{item.total_price}}</td>
                                    <td>19%</td>
                                    <td>{{item.tax}}</td>
                                    <td>{{item.total}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Selected Item End -->
    </template>
    <div class="card" v-else>
        <div class="card-body">
            {{$t('ContractPage.loading')}}
        </div>
    </div>
</div>
</template>
<script>
import {
    mapGetters
} from "vuex";
import userApi from "../../api/users";
import contractApi from "../../api/contracts";
import PrintContractComponent from './PrintContractComponent';

export default {
    name: 'SingleContractComponent',
    data() {
        return {
            contract_id: this.$route.params.id,
            loading: true,
            client: null,
        }
    },
    methods: {
      printContract() {
        this.$htmlToPaper('printContract');
      },
      getContractById(id) {
          this.$Progress.start();
          this.$store
              .dispatch("contract/getContractById", id)
              .then(response => {
                  this.$Progress.finish();
                  this.loading = false;
              })
              .catch(error => {
                  this.$Progress.fail();
              });
      },
      deleteContract(id) {
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
                      .dispatch("contract/deleteContract", id)
                      .then(response => {
                          this.$Progress.finish();
                          Toast.fire({
                              type: "success",
                              title: response.data.message
                          });
                          this.$router.push({
                              name: "contract_list"
                          });
                      })
                      .catch(error => {
                          console.log(error);
                          this.$Progress.fail();
                          Toast.fire({
                              type: "error",
                              title: error.response.data.message
                          });
                      });
              }
          });
      },
      sendContract() {
          contractApi
              .send(this.contract_id)
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
    },
    mounted() {
        this.getContractById(this.contract_id);
    },
    computed: {
        ...mapGetters({
            contract: "contract/activeContract",
        })
    },
    components: {
      PrintContractComponent,
    }
}
</script>
