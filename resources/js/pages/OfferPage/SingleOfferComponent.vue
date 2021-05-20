<template>
<div class="single-offer">
        <div class="card">
            <div class="card-header">
                <div class="card-title">{{$t('Offer.offer')}}: {{offer.number}}</div>
                <div class="card-tools">
                    <MailOffer></MailOffer>
                  <a href="#" @click="sendOffer()" class="btn btn-app">
                      <i class="fas fa-envelope"></i>{{$t('Offer.sendOffer')}}
                  </a>
                  <a href="#" @click="printOffer()" class="btn btn-app">
                      <i class="fas fa-print"></i> {{$t('Offer.print')}}
                  </a>
                  <PrintOfferComponent style="display: none; " :offer="offer" />
                  <a v-if="!offer.contract" href="#" @click="createContract()" class="btn btn-app">
                    <i class="fas fa-edit"></i> {{$t('Offer.contract')}}
                  </a>
                  <router-link v-if="!offer.contract" :to="{ name: 'offer_edit', params: {id: offer.id }}" class="btn btn-app">
                      <i class="fas fa-edit"></i> {{$t('Offer.edit')}}
                  </router-link>
                  <router-link v-if="offer.contract" :to="{ name: 'contract', params: { id: offer.contract.id }}" class="btn btn-app">
                    <i class="fas fa-edit"></i> {{$t('Offer.contract')}}
                  </router-link>
                  <!-- <a href="#" @click="deleteOffer(offer.id)" class="btn btn-app">
                      <i class="fas fa-trash"></i> Delete
                  </a> -->
                </div>
            </div>
            <div class="card-body">
                <div v-if="offer" class="row">
                    <!-- Customer Data Start -->
                    <div class="col-md-6">
                        <div class="row" style="border-right: 1px solid #2d2d2d;">
                            <div class="col-md-12">
                              <h5>{{$t('ContractPage.customerData')}}</h5>
                            </div>
                            <div class="form-group col-md-6">
                              <label for="client">{{$t('ContractPage.date')}}</label>
                                <input disabled class="form-control form-control-sm" type="date" name="date" v-model="offer.date">
                            </div>
                            <template v-if="offer">
                              <div class="form-group col-md-6">
                                <label for="client">{{$t('ContractPage.client')}}</label>
                                  <input disabled class="form-control form-control-sm" type="text" name="client" v-model="offer.client.name">
                              </div>
                              <div class="form-group col-md-4">
                                <label for="title">{{$t('ContractPage.title')}}</label>
                                  <input disabled class="form-control form-control-sm" type="text" name="title" v-model="offer.client.metadata.title">
                              </div>
                              <div class="form-group col-md-4">
                                <label for="greeting">{{$t('ContractPage.greeting')}}</label>
                                  <input disabled class="form-control form-control-sm" type="text" name="greeting" v-model="offer.client.metadata.greeting">
                              </div>
                              <div class="form-group col-md-4">
                                <label for="company">{{$t('ContractPage.company')}}</label>
                                  <input disabled class="form-control form-control-sm" type="text" name="company" v-model="offer.client.metadata.company">
                              </div>
                              <div class="form-group col-md-4">
                                <label for="attention">{{$t('ContractPage.firstName')}}</label>
                                  <input disabled class="form-control form-control-sm" type="text" name="first_name" v-model="offer.client.metadata.first_name">
                              </div>
                              <div class="form-group col-md-4">
                                <label for="name">{{$t('ContractPage.name')}}</label>
                                  <input disabled class="form-control form-control-sm" type="text" name="name" v-model="offer.client.name">
                              </div>
                              <div class="form-group col-md-4">
                                <label for="street">{{$t('ContractPage.street')}}</label>
                                  <input disabled class="form-control form-control-sm" type="text" name="street" v-model="offer.client.metadata.street">
                              </div>
                              <div class="form-group col-md-4">
                                <label for="city">{{$t('ContractPage.city')}}</label>
                                  <input disabled class="form-control form-control-sm" type="text" name="city" v-model="offer.client.metadata.city">
                              </div>
                              <div class="form-group col-md-4">
                                <label for="country">{{$t('ContractPage.country')}}</label>
                                  <input disabled class="form-control form-control-sm" type="text" name="country" v-model="offer.client.metadata.country">
                              </div>
                              <div class="form-group col-md-4">
                                <label for="postal_code">{{$t('ContractPage.postalCode')}}</label>
                                  <input disabled class="form-control form-control-sm" type="text" name="postal_code" v-model="offer.client.metadata.postal_code">
                              </div>
                              <div class="form-group col-md-4">
                                <label for="telephone">{{$t('ContractPage.tel')}}</label>
                                  <input disabled class="form-control form-control-sm" type="text" name="telephone" v-model="offer.client.metadata.telephone">
                              </div>
                              <div class="form-group col-md-4">
                                <label for="mobile">{{$t('ContractPage.mobile')}}</label>
                                  <input disabled class="form-control form-control-sm" type="text" name="mobile" v-model="offer.client.metadata.mobile">
                              </div>
                              <div class="form-group col-md-4">
                                <label for="fax">{{$t('ContractPage.fax')}}</label>
                                  <input disabled class="form-control form-control-sm" type="text" name="fax" v-model="offer.client.metadata.fax">
                              </div>
                              <div class="form-group col-md-4">
                                <label for="email">{{$t('ContractPage.email')}}</label>
                                  <input disabled class="form-control form-control-sm" type="email" name="email" v-model="offer.client.metadata.email">
                              </div>
                              <div class="form-group col-md-4">
                                <label for="ebay-user">{{$t('ContractPage.eBayUser')}}</label>
                                  <input disabled class="form-control form-control-sm" type="text" name="ebay-user" v-model="offer.client.metadata.ebay_account">
                              </div>
                            </template>
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
                                <input disabled class="form-control form-control-sm" type="text" name="title" v-model="offer.title">
                            </div>
                            <div class="form-group col-md-4">
                              <label for="greeting">{{$t('ContractPage.greeting')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="greeting" v-model="offer.greeting">
                            </div>
                            <div class="form-group col-md-4">
                              <label for="first_name">{{$t('ContractPage.firstName')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="first_name" v-model="offer.first_name">
                            </div>
                            <div class="form-group col-md-4">
                              <label for="name">{{$t('ContractPage.name')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="name" v-model="offer.name">
                            </div>
                            <div class="form-group col-md-4">
                              <label for="street">{{$t('ContractPage.street')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="street" v-model="offer.street">
                            </div>
                            <div class="form-group col-md-4">
                              <label for="city">{{$t('ContractPage.city')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="city" v-model="offer.city">
                            </div>
                            <div class="form-group col-md-4">
                              <label for="country">{{$t('ContractPage.country')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="country" v-model="offer.country">
                            </div>
                            <div class="form-group col-md-4">
                              <label for="postal_code">{{$t('ContractPage.postalCode')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="postal_code" v-model="offer.postal_code">
                            </div>
                            <div class="form-group col-md-4">
                              <label for="telephone">{{$t('ContractPage.tel')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="telephone" v-model="offer.telephone">
                            </div>
                            <div class="form-group col-md-4">
                              <label for="mobile">{{$t('ContractPage.mobile')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="mobile" v-model="offer.mobile">
                            </div>
                            <div class="form-group col-md-4">
                              <label for="fax">{{$t('ContractPage.fax')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="fax" v-model="offer.fax">
                            </div>
                            <div class="form-group col-md-4">
                              <label for="email">{{$t('ContractPage.email')}}</label>
                                <input disabled class="form-control form-control-sm" type="email" name="email" v-model="offer.email">
                            </div>
                            <div class="form-group col-md-12">
                              <label for="notes">{{$t('ContractPage.notes')}}</label>
                                <textarea disabled class="form-control form-control-sm" name="notes" v-model="offer.notes"></textarea>
                            </div>
                            <div class="form-group col-md-12">
                              <label for="printed_text">{{$t('ContractPage.printedText')}}</label>
                                <textarea disabled class="form-control form-control-sm" name="printed_text" v-model="offer.printed_text"></textarea>
                            </div>
                        </div>
                    </div>
                    <!-- Delevery Address End -->
                </div>
            </div>
        </div>
        <div v-if="offer.items" class="card">
          <div class="card-header">
            <h4>{{$t('Offer.offerItems')}}</h4>
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
                    <tr v-for="item in offer.items.data" :key="item.id">
                      <td>{{item.id}}</td>
                      <td style="word-wrap: break-word;min-width: 160px;max-width: 160px;">{{item.name}}</td>
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
        <OfferConversations :offer_id="offer_id"></OfferConversations>
</div>
</template>
<script>
import {
    mapGetters
} from "vuex";
import userApi from "../../api/users";
import offerApi from "../../api/offers";
import PrintOfferComponent from './PrintOfferComponent'
import MailOffer from '../../components/offers/MailOffer'
import OfferConversations from '../../components/offers/OfferConversations'

export default {
    name: 'SingleOfferComponent',
    data() {
        return {
            offer_id: this.$route.params.id,
            loading: true,
            client: null,
            contract_data: new Form({
                client_id: '',
                offer: '',
                number: '',
                total: '',
                date: '',
                greeting: '',
                street: '',
                country: '',
                city: '',
                first_name: '',
                postal_code: '',
                telephone: '',
                mobile: '',
                title: '',
                name: '',
                fax: '',
                email: '',
                others: '',
                notes: '',
                printed_text: '',
                items: null,
            }),
        }
    },
    methods: {
        printOffer() {
            this.$htmlToPaper('printMe');
        },
        getOfferById(id) {
            this.$Progress.start();
            this.$store
                .dispatch("offer/getOfferById", id)
                .then(response => {
                    this.$Progress.finish();
                    this.loading = false;
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },
        deleteOffer(id) {
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
                        .dispatch("offer/deleteOffer", id)
                        .then(response => {
                            this.$Progress.finish();
                            Toast.fire({
                                type: "success",
                                title: response.data.message
                            });
                            this.$router.push({
                                name: "offer_list"
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
        createContract() {
            this.contract_data.fill(this.offer);
            this.contract_data.items = this.offer.items.data;
            this.contract_data.client_id = this.offer.client.id;
            this.contract_data.offer = this.offer_id;
            this.$Progress.start();
            this.$store
                .dispatch("contract/createContract", this.contract_data)
                .then(response => {
                    this.$Progress.finish();
                    Toast.fire({
                        type: "success",
                        title: response.data.message
                    });
                    this.$router.push({
                        name: 'contract',
                        params: {
                            id: response.data.data.id
                        }
                    });
                })
                .catch(error => {
                    this.$Progress.fail();
                    if (error.response) {
                        this.form.errors.errors = error.response.data.errors;
                    }
                });
        },
        sendOffer() {
            offerApi
                .send(this.offer_id)
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
        },
        getContracts() {
          this.$store
            .dispatch("contract/getContracts")
            .then(() => {
            })
            .catch(() => {
            });
        }
    },
    mounted() {
        this.getOfferById(this.offer_id);
        this.getContracts();
    },
    components: {
        PrintOfferComponent,
        MailOffer,
        OfferConversations,
    },
    computed: {
        ...mapGetters({
            offer: "offer/activeOffer",
        })
    },
}
</script>
