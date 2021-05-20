<template>
<div class="create-offer">
    <div class="card">
        <div class="card-body">
                <div class="row">
                    <div class="col">
                        <AddItemToOffer @click-to-add-items="addItems" />
                        <button class="btn btn-app" @click="createOffer"><i class="fa fa-save"></i> {{$t('Offer.save')}}</button>
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
                                <date-picker v-model="offer.date" :value="offer.date" lang="en" id="client" type="date" format="DD-MM-YYYY" :minute-step="1" value-type="format" input-class="form-control form-control-sm"></date-picker>
                            </div>
                            <div class="form-group col-md-6">
                              <label for="client">{{$t('ContractPage.client')}}</label>
                                <select @change="cleintSlected($event)" name="clients" id="" class="form-control form-control-sm">
                                    <option class="form-control form-control-sm" value=""></option>
                                    <option class="form-control form-control-sm" v-for="client in resultClients" :key="client.id" v-model="form.client_id" :value="client.id">
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
                                <input disabled class="form-control form-control-sm" type="text" name="city" v-model="form.city">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="country">{{$t('ContractPage.country')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="country" v-model="form.country">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="postal_code">{{$t('ContractPage.postalCode')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="postal_code" v-model="form.postal_code">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="telephone">{{$t('ContractPage.tel')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="telephone" v-model="form.telephone">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="mobile">{{$t('ContractPage.mobile')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="mobile" v-model="form.mobile">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="fax">{{$t('ContractPage.fax')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="fax" v-model="form.fax">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="email">{{$t('ContractPage.email')}}</label>
                                <input disabled class="form-control form-control-sm" type="email" name="email" v-model="form.email">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="ebay-user">{{$t('ContractPage.eBayUser')}}</label>
                                <input disabled class="form-control form-control-sm" type="text" name="ebay-user" v-model="form.ebay_account">
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
        </div>
    </div>

    <!-- Selected Item Start -->
    <div class="card">
      <div class="card-header">
        <div class="card-title">{{$t('Offer.offerItems')}}</div>
        <div class="card-tools">
          <EditOfferItems v-if="offer.items" :selectedItems="offer.items" @click-to-edit-items="editItems" />
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <template v-if="!offer.items">{{$t('ContractPage.noItem')}}</template>
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
                            <th></th>
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
                                <td>
                                    <button class="btn btn-sm btn-danger" @click="deleteItem(item)"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Selected Item End -->
</div>
</div>
</template>
<script>
import {
    mapGetters
} from "vuex";
import DatePicker from "vue2-datepicker";
import userApi from "../../api/users";

import AddItemToOffer from '../../components/offers/AddItemToOffer';
import EditOfferITems from '../../components/offers/EditOfferItems';

export default {
    name: "CreateOfferPage",
    data() {
        return {
            form: new Form({
                client_id: '',
                title: '',
                greeting: '',
                company: '',
                first_name: '',
                total_tax: '',
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
                items: null,
            }),
        }
    },
    methods: {
        cleintSlected(event) {
            this.form.client_id = '';
            this.form.title = '';
            this.form.greeting = '';
            this.form.company = '';
            this.form.first_name = '';
            this.form.total_tax = '';
            this.form.name = '';
            this.form.street = '';
            this.form.city = '';
            this.form.postal_code = '';
            this.form.country = '';
            this.form.telephone = '';
            this.form.fax = '';
            this.form.email = '';
            this.form.mobile = '';
            this.form.ebay_account = '';
            this.offer.client_id = '';
            this.offer.number = '';
            this.offer.total = '';
            this.offer.total_tax = '';
            this.offer.greeting = '';
            this.offer.street = '';
            this.offer.country = '';
            this.offer.city = '';
            this.offer.first_name = '';
            this.offer.postal_code = '';
            this.offer.telephone = '';
            this.offer.mobile = '';
            this.offer.title = '';
            this.offer.name = '';
            this.offer.fax = '';
            this.offer.email = '';
            this.offer.others = '';
            this.offer.notes = '';
            this.offer.printed_text = '';
            this.offer.items = null;
            this.resultClients.forEach(el => {
              if(el.id == event.target.value) {
                this.form.client_id = el.id;
                this.form.company = el.metadata.company;
                this.form.first_name = el.metadata.first_name;
                this.form.name = el.name;
                this.form.street = el.metadata.street;
                this.form.city = el.metadata.city;
                this.form.postal_code = el.metadata.postal_code;
                this.form.country = el.metadata.country;
                this.form.telephone = el.metadata.telephone;
                this.form.fax = el.metadata.fax;
                this.form.email = el.email;
                this.form.mobile = el.metadata.mobile;
                this.form.ebay_account = el.metadata.ebay_account;
                this.offer.client_id = el.id;
                this.offer.street = el.metadata.street;
                this.offer.country = el.metadata.country;
                this.offer.city = el.metadata.city;
                this.offer.first_name = el.metadata.first_name;
                this.offer.postal_code = el.metadata.postal_code;
                this.offer.telephone = el.metadata.telephone;
                this.offer.mobile = el.metadata.mobile;
                this.offer.name = el.name;
                this.offer.fax = el.metadata.fax;
                this.offer.email = el.email;
                this.offer.printed_text = 'Vielen Dank Für lhren Auftrag.';
              }
            })
        },
        addItem() {
            $("#CreateItem").modal("show");
        },
        addItems(value) {
            this.offer.items = value;
        },
        editItems(value) {
            this.offer.items = value;
        },
        deleteItem(selectedItem) {
            selectedItem.qty = 0;
            selectedItem.total = 0;
            this.offer.items.splice(this.offer.items.indexOf(selectedItem), 1);
        },
        getCategories() {
            this.$Progress.start();
            this.$store
                .dispatch("item/getCategories")
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
        createOffer() {
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
            if (!this.offer.items) {
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
                });
                this.offer.total = total;
                this.offer.total_tax = total_tax;
            }
            this.$Progress.start();
            this.$store
                .dispatch("offer/createOffer", this.offer)
                .then(response => {
                    this.$Progress.finish();
                    Toast.fire({
                        type: "success",
                        title: response.data.message
                    });
                    console.log(response.data.data.id);
                    this.$router.push({
                        name: 'offer',
                        params: {
                            id: response.data.data.id
                        }
                    });
                })
                .catch(error => {
                    this.$Progress.fail();
                    console.log(error);
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
        this.getCategories();
        this.offer.date = new Date().toISOString().slice(0,10);
    },
    components: {
        DatePicker,
        AddItemToOffer,
        EditOfferITems,
    }
}
</script>
<style scoped>
</style>
