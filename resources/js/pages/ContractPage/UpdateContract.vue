<template>
<div class="update-contract">
    <template v-if="!loading">
        <div class="card">
            <div class="card-body">
                <form @submit.prevent="updateContract" class="form form-horizontal" method="post">
                    <div class="row text-right">
                        <div class="col">
                            <button class="btn btn-success btn-sm" type="submit">{{$t('ContractPage.updateContract')}}</button>
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
                                    <label for="date">{{$t('ContractPage.date')}}</label>
                                    <br />
                                    <date-picker v-model="contract.date" lang="en" id="date" type="date" format="DD-MM-YYYY" :minute-step="1" value-type="format" input-class="form-control form-control-sm"></date-picker>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="client">{{$t('ContractPage.client')}}</label>
                                    <select @change="cleintSlected($event)" name="clients" id="" class="form-control form-control-sm">
                                        <option class="form-control form-control-sm"></option>
                                        <option class="form-control form-control-sm" v-for="client in resultClients" :key="client.id" v-model="form.client_id" :value="client.id" :selected="client.id == contract.client_id">
                                            {{ client.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="title">{{$t('ContractPage.title')}}</label>
                                    <input class="form-control form-control-sm" type="text" name="title" v-model="form.title">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="greeting">{{$t('ContractPage.greeting')}}</label>
                                    <input class="form-control form-control-sm" type="text" name="greeting" v-model="form.greeting">
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="company">{{$t('ContractPage.company')}}</label>
                                    <input class="form-control form-control-sm" type="text" name="company" v-model="form.company">
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="attention">{{$t('ContractPage.firstName')}}</label>
                                    <input class="form-control form-control-sm" type="text" name="first_name" v-model="form.first_name">
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="name">{{$t('ContractPage.name')}}</label>
                                    <input class="form-control form-control-sm" type="text" name="name" v-model="form.name">
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="street">{{$t('ContractPage.street')}}</label>
                                    <input class="form-control form-control-sm" type="text" name="street" v-model="form.street">
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="city">{{$t('ContractPage.city')}}</label>
                                    <input class="form-control form-control-sm" type="text" name="city" v-model="form.city">
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="country">{{$t('ContractPage.country')}}</label>
                                    <input class="form-control form-control-sm" type="text" name="country" v-model="form.country">
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="postal_code">{{$t('ContractPage.postalCode')}}</label>
                                    <input class="form-control form-control-sm" type="text" name="postal_code" v-model="form.postal_code">
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="telephone">{{$t('ContractPage.tel')}}</label>
                                    <input class="form-control form-control-sm" type="text" name="telephone" v-model="form.telephone">
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="mobile">{{$t('ContractPage.mobile')}}</label>
                                    <input class="form-control form-control-sm" type="text" name="mobile" v-model="form.mobile">
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="fax">{{$t('ContractPage.fax')}}</label>
                                    <input class="form-control form-control-sm" type="text" name="fax" v-model="form.fax">
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="email">{{$t('ContractPage.email')}}</label>
                                    <input class="form-control form-control-sm" type="email" name="email" v-model="form.email">
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="ebay-user">{{$t('ContractPage.eBayUser')}}</label>
                                    <input class="form-control form-control-sm" type="text" name="ebay-user" v-model="form.ebay_account">
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
                                    <input class="form-control form-control-sm" type="text" name="title" v-model="contract.title">
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="greeting">{{$t('ContractPage.greeting')}}</label>
                                    <input class="form-control form-control-sm" type="text" name="greeting" v-model="contract.greeting">
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="first_name">{{$t('ContractPage.firstName')}}</label>
                                    <input class="form-control form-control-sm" type="text" name="first_name" v-model="contract.first_name">
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="name">{{$t('ContractPage.name')}}</label>
                                    <input class="form-control form-control-sm" type="text" name="name" v-model="contract.name">
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="street">{{$t('ContractPage.street')}}</label>
                                    <input class="form-control form-control-sm" type="text" name="street" v-model="contract.street">
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="city">{{$t('ContractPage.city')}}</label>
                                    <input class="form-control form-control-sm" type="text" name="city" v-model="contract.city">
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="country">{{$t('ContractPage.country')}}</label>
                                    <input class="form-control form-control-sm" type="text" name="country" v-model="contract.country">
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="postal_code">{{$t('ContractPage.postalCode')}}</label>
                                    <input class="form-control form-control-sm" type="text" name="postal_code" v-model="contract.postal_code">
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="telephone">{{$t('ContractPage.tel')}}</label>
                                    <input class="form-control form-control-sm" type="text" name="telephone" v-model="contract.telephone">
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="mobile">{{$t('ContractPage.mobile')}}</label>
                                    <input class="form-control form-control-sm" type="text" name="mobile" v-model="contract.mobile">
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="fax">{{$t('ContractPage.fax')}}</label>
                                    <input class="form-control form-control-sm" type="text" name="fax" v-model="contract.fax">
                                </div>
                                <div class="form-group col-md-4">
                                  <label for="email">{{$t('ContractPage.email')}}</label>
                                    <input class="form-control form-control-sm" type="email" name="email" v-model="contract.email">
                                </div>
                                <div class="form-group col-md-12">
                                  <label for="notes">{{$t('ContractPage.notes')}}</label>
                                    <textarea class="form-control form-control-sm" name="notes" v-model="contract.notes"></textarea>
                                </div>
                                <div class="form-group col-md-12">
                                  <label for="printed_text">{{$t('ContractPage.printedText')}}</label>
                                    <textarea class="form-control form-control-sm" name="printed_text" v-model="contract.printed_text"></textarea>
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
              <div class="card-title">{{$t('ContractPage.contractItems')}}</div>
                <div class="card-tools">
                    <a href="#" class="btn btn-primary btn-sm mr-2" @click="addItem">{{$t('ContractPage.updateItem')}}</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <template v-if="!contract.items || contract.items.length < 1">{{$t('ContractPage.noItem')}}</template>
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
                                <tr v-for="item in contract.items" :key="item.id">
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
        </div>
        <!-- Selected Item End -->
        <template v-if="contract.items">
            <UpdateContractItems @click-to-add-items="addItems" v-if="contract.items.length > 0" :existItems="contract.items" />
        </template>
    </template>
    <div v-else class="card">
        <div class="card-body">{{$t('ContractPage.loading')}}</div>
    </div>
</div>
</template>
<script>
import {
    mapGetters
} from "vuex";
import DatePicker from "vue2-datepicker";
import userApi from "../../api/users";
import contractApi from "../../api/contracts";

import UpdateContractItems from '../../components/contracts/UpdateContractItems';

export default {
    name: "UpdateContractPage",
    data() {
        return {
            contract_id: this.$route.params.id,
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
            contract: new Form({
                client_id: '',
                id: '',
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
        getContractById(id) {
            this.$Progress.start();
            this.form.clear();
            this.contract.clear();
            contractApi
                .show(id)
                .then(response => {
                    this.$Progress.finish();
                    this.contract.fill(response.data.data);
                    this.contract.client_id = response.data.data.client.id;
                    this.contract.items = response.data.data.items.data;
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
            this.contract.clear();
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
                    if (this.contract.items.length > 0) {
                        let items = this.contract.items;
                        this.contract.fill(response.data.data.metadata);
                        this.contract.items = items;
                    } else {
                        this.contract.fill(response.data.data.metadata);
                    }
                    this.contract.client_id = response.data.data.id;
                    this.contract.name = response.data.data.name;
                    this.contract.email = response.data.data.email;
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
            $("#UpdateItem").modal("show");
        },
        addItems(value) {
            this.contract.items = value;
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
        updateContract() {
            if (!this.contract.client_id) {
                Toast.fire({
                    type: "error",
                    title: 'No client selected'
                });
                return false;
            }
            if (!this.contract.date) {
                Toast.fire({
                    type: "error",
                    title: 'Contract date missing'
                });
                return false;
            }
            if (!this.contract.items) {
                Toast.fire({
                    type: "error",
                    title: 'No item selected'
                });
                return false;
            } else {
                this.contract.items.forEach(item => {
                    let total = 0;
                    total += item.total;
                    this.contract.total = total;
                });
            }
            this.$Progress.start();
            this.$store
                .dispatch("contract/editContract", this.contract)
                .then(response => {
                    this.$Progress.finish();
                    Toast.fire({
                        type: "success",
                        title: response.data.message
                    });
                    this.$router.push({
                        name: 'contract_list'
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
        this.getContractById(this.contract_id);
        this.getCategories();
        this.loading = false;
    },
    components: {
        DatePicker,
        UpdateContractItems,
    }
}
</script>
