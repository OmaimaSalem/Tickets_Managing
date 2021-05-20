<template>
  <div class="update-offer">
    <div class="card">
      <div class="card-body">
          <form @submit.prevent="updateOffer" class="form form-horizontal" method="post">
              <div class="row text-right">
                  <div class="col">
                      <button class="btn btn-app" type="submit"><i class="fas fa-save"></i>{{$t('Offer.updateOffer')}}</button>
                  </div>
              </div>
              <div class="row">
                  <!-- Customer Data Start -->
                  <div class="col-md-6">
                      <div class="row" style="border-right: 1px solid #2d2d2d;">
                          <div class="col-md-12">
                            <h5>{{$t('ContractPage.customerData')}}</h5>
                          </div>
                          <div class="form-group col-md-6">
                            <label for="client">{{$t('ContractPage.date')}}</label>
                              <br />
                              <date-picker v-model="offer.date" lang="en" id="client" type="date" format="DD-MM-YYYY" :minute-step="1" value-type="format" input-class="form-control form-control-sm"></date-picker>
                          </div>
                          <div class="form-group col-md-6">
                            <label for="client">{{$t('ContractPage.client')}}</label>
                              <select @change="cleintSlected($event)" name="clients" id="" class="form-control form-control-sm">
                                  <option class="form-control form-control-sm"></option>
                                  <option class="form-control form-control-sm" v-for="client in resultClients" :key="client.id" v-model="form.client_id" :value="client.id" :selected="client.id == offer.client_id">
                                      {{ client.name }}
                                  </option>
                              </select>
                          </div>
                          <div class="form-group col-md-4">
                            <label for="title">{{$t('ContractPage.title')}}</label>
                              <input disabled class="form-control form-control-sm" type="text" name="title" v-model="form.title">
                          </div>
                          <div class="form-group col-md-4">
                            <label for="greeting">{{$t('ContractPage.greeting')}}</label>
                              <input disabled class="form-control form-control-sm" type="text" name="greeting" v-model="form.greeting">
                          </div>
                          <div class="form-group col-md-4">
                            <label for="company">{{$t('ContractPage.company')}}</label>
                              <input disabled class="form-control form-control-sm" type="text" name="company" v-model="form.company">
                          </div>
                          <div class="form-group col-md-4">
                            <label for="attention">{{$t('ContractPage.firstName')}}</label>
                              <input disabled class="form-control form-control-sm" type="text" name="first_name" v-model="form.first_name">
                          </div>
                          <div class="form-group col-md-4">
                            <label for="name">{{$t('ContractPage.name')}}</label>
                              <input disabled class="form-control form-control-sm" type="text" name="name" v-model="form.name">
                          </div>
                          <div class="form-group col-md-4">
                            <label for="street">{{$t('ContractPage.street')}}</label>
                              <input disabled class="form-control form-control-sm" type="text" name="street" v-model="form.street">
                          </div>
                          <div class="form-group col-md-4">
                            <label for="city">{{$t('ContractPage.city')}}</label>
                              <input disabled class="form-control form-control-sm" type="text" name="city" v-model="form.metadata.city">
                          </div>
                          <div class="form-group col-md-4">
                            <label for="country">{{$t('ContractPage.country')}}</label>
                              <input disabled class="form-control form-control-sm" type="text" name="country" v-model="form.metadata.country">
                          </div>
                          <div class="form-group col-md-4">
                            <label for="postal_code">{{$t('ContractPage.postalCode')}}</label>
                              <input disabled class="form-control form-control-sm" type="text" name="postal_code" v-model="form.metadata.postal_code">
                          </div>
                          <div class="form-group col-md-4">
                            <label for="telephone">{{$t('ContractPage.tel')}}</label>
                              <input disabled class="form-control form-control-sm" type="text" name="telephone" v-model="form.metadata.telephone">
                          </div>
                          <div class="form-group col-md-4">
                            <label for="mobile">{{$t('ContractPage.mobile')}}</label>
                              <input disabled class="form-control form-control-sm" type="text" name="mobile" v-model="form.metadata.mobile">
                          </div>
                          <div class="form-group col-md-4">
                            <label for="fax">{{$t('ContractPage.fax')}}</label>
                              <input disabled class="form-control form-control-sm" type="text" name="fax" v-model="form.metadata.fax">
                          </div>
                          <div class="form-group col-md-4">
                            <label for="email">{{$t('ContractPage.email')}}</label>
                              <input disabled class="form-control form-control-sm" type="email" name="email" v-model="form.metadata.email">
                          </div>
                          <div class="form-group col-md-4">
                            <label for="ebay-user">{{$t('ContractPage.eBayUser')}}</label>
                              <input disabled class="form-control form-control-sm" type="text" name="ebay-user" v-model="form.metadata.ebay_account">
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
          </form>
      </div>
    </div>
    <!-- Selected Item Start -->
    <div class="card">
      <div class="card-header">
          <div class="card-title">{{$t('Offer.offerItems')}}</div>
          <div class="card-tools">
            <template v-if="offer.items">
              <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editModal">
                  <i class="fa fa-edit mr-2"></i>
                  <span v-if="offer.items.length > 0"> {{offer.items.length}} {{$t('Offer.item/s')}}</span>
              </button>
                  <EditOfferItems :selectedItems="offer.items" @click-to-edit-items="editItems" />
              </template>
          </div>
      </div>
      <div class="card-body">
          <div class="row">
              <div class="col">
                  <template v-if="!offer.items || offer.items.length < 1">{{$t('ContractPage.noItem')}}</template>
                  <table v-else class="table table-hover table-sm">
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
                          <tr v-for="item in offer.items" :key="item.id">
                              <td>{{item.id}}</td>
                              <td>{{item.name}}</td>
                              <td>{{item.price}}</td>
                              <td>{{item.qty}}</td>
                              <td>{{item.total_price}}</td>
                              <td>19%</td>
                              <td>{{item.tax_price}}</td>
                              <td>{{item.total}}</td>
                          </tr>
                      </tbody>
                  </table>
              </div>
          </div>
        </div>
        <!-- Delevery Address End -->
      </div>
    <!-- Selected Item Start -->
</div>
</template>
<script>
import {
    mapGetters
} from "vuex";
import DatePicker from "vue2-datepicker";
import userApi from "../../api/users";
import offerApi from "../../api/offers";

import EditOfferITems from '../../components/offers/EditOfferItems';

export default {
    name: "UpdateOfferPage",
    data() {
        return {
            offer_id: this.$route.params.id,
            loading: true,
            form: new Form({
                client_id: '',
                title: '',
                greeting: '',
                company: '',
                first_name: '',
                name: '',
                street: '',
                city: '',
                postal_code: '',
                country: '',
                telephone: '',
                fax: '',
                email: '',
                mobile: '',
                ebay_account: ''
            }),
            offer: new Form({
                client_id: '',
                id: '',
                number: '',
                total: '',
                total_tax: '',
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
                items: [],
            }),
        }
    },
    methods: {
        getOfferById(id) {
            this.$Progress.start();
            this.form.clear();
            this.offer.clear();
            offerApi
                .show(id)
                .then(response => {
                    this.$Progress.finish();
                    this.offer.fill(response.data.data);
                    this.offer.client_id = response.data.data.client.id;
                    this.offer.items = response.data.data.items.data;
                    this.form.fill(response.data.data.client.metadata);
                    this.form.client_id = response.data.data.client.id;
                    this.form.name = response.data.data.client.name;
                    this.form.email = response.data.data.client.email;
                    this.loading = false;
                })
                .catch(error => {
                    this.$Progress.fail();
                    Toast.fire({
                        type: "error",
                        title: error.response.data.message
                    });
                });
        },
        cleintSlected(event) {
            this.$Progress.start();
            this.form.clear();
            this.offer.clear();
            userApi
                .show(event.target.value)
                .then(response => {
                    this.$Progress.finish();
                    if (response.data.data.metadata == null) {
                        Toast.fire({
                            type: "error",
                            title: 'Metadata not found for this client'
                        });
                    }
                    this.form.fill(response.data.data.metadata);
                    if (this.offer.items.length > 0) {
                        let items = this.offer.items;
                        this.offer.fill(response.data.data.metadata);
                        this.offer.items = items;
                    } else {
                        this.offer.fill(response.data.data.metadata);
                    }
                    this.offer.client_id = response.data.data.id;
                    this.offer.name = response.data.data.name;
                    this.offer.email = response.data.data.email;
                    this.form.client_id = response.data.data.id;
                    this.form.name = response.data.data.name;
                    this.form.email = response.data.data.email;
                })
                .catch(error => {
                    this.$Progress.fail();
                    Toast.fire({
                        type: "error",
                        title: error.response.data.message
                    });
                });
        },
        addItem() {
            $("#Modal").modal("show");
        },
        editItems(value) {
            this.offer.items = value;
        },
        getCategories() {
            this.$Progress.start();
            this.$store
                .dispatch("category/getCategories")
                .then(response => {
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },
        getClients() {
            this.$Progress.start();
            this.$store
                .dispatch("user/getUsers")
                .then(response => {
                    this.$Progress.finish();
                })
                .catch(error => {
                    this.$Progress.fail();
                });
        },
        updateOffer() {
            if (!this.offer.client_id) {
                Toast.fire({
                    type: "error",
                    title: 'No client selected'
                });
                return false;
            }
            if (!this.offer.date) {
                Toast.fire({
                    type: "error",
                    title: 'Offer date missing'
                });
                return false;
            }
            if (this.offer.items.length < 1) {
                Toast.fire({
                    type: "error",
                    title: 'No item selected'
                });
                return false;
            } else {
                let total = 0;
                let total_tax = 0;
                this.offer.items.forEach(item => {
                    total += item.total;
                    total_tax += item.tax_price;
                    this.offer.total = total;
                    this.offer.total_tax = total_tax;
                });
            }
            this.$Progress.start();
            this.$store
                .dispatch("offer/editOffer", this.offer)
                .then(response => {
                    this.$Progress.finish();
                    Toast.fire({
                        type: "success",
                        title: response.data.message
                    });
                    this.$router.push({
                        name: 'offer',
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
        }
    },
    computed: {
        ...mapGetters({
            clients: "user/activeUsers",
        }),
        resultClients() {
            return this.clients.data;
        },
    },
    mounted() {
        this.getClients();
        this.getOfferById(this.offer_id);
        this.getCategories();
        this.loading = false;
    },
    components: {
        DatePicker,
        EditOfferITems,
    }
}
</script>
