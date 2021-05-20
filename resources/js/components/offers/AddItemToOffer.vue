<template>
  <span class="add-item-to-offer">
    <button type="button" class="btn btn-app" data-toggle="modal" data-target="#exampleModal" data-backdrop="static" data-keyboard="false">
      <i class="fa fa-plus"></i> {{$t('Offer.items')}}
    </button>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <script type="application/javascript" src="/dist/js/checktree.js"></script>
                    <div class="card">
                        <div class="card-body">
                            <ul class="checktree">
                                <li v-for="category in categories.data" :key="category.id">
                                    <label :for="category.name">{{category.name}}</label>
                                    <ul>
                                        <li v-for="item in category.items.data" :key="item.id">
                                            <input type="checkbox" :id="item.name" :value="item" v-model="selectedItems" />
                                            <label :for="item.name">{{item.name}}</label>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <table width="100%" class="table table-hover table-sm table-bordered text-left">
                                <thead class="thead-dark">
                                    <th>ID</th>
                                    <th>{{$t('Offer.name')}}</th>
                                    <th>{{$t('Offer.desc')}}</th>
                                    <th>{{$t('Offer.price')}}</th>
                                    <th>{{$t('Offer.quantity')}}</th>
                                    <th>{{$t('Offer.tax')}}</th>
                                    <th>{{$t('Offer.total')}}</th>
                                </thead>
                                <tbody>
                                    <tr v-if="selectedItems.length < 1">
                                        <td>
                                            {{$t('Offer.noItem')}}
                                        </td>
                                    </tr>
                                    <tr v-else v-for="selectedItem in selectedItems" :key="selectedItem.id">
                                        <td width="3%" class="align-middle">{{selectedItem.id}}</td>
                                        <td width="20%"><input class="item_input ml-3 form-control" type="text" v-model="selectedItem.name" name="name" /></td>
                                        <td width="37%"><input class="item_area ml-3 form-control" type="text" v-model="selectedItem.description" name="description" /></td>
                                        <td width="10%"><input class="item_qty ml-3 form-control" type="number" @input="calculate(selectedItem)" v-model="selectedItem.price" name="price" /></td>
                                        <td width="10%"><input class="item_qty ml-3 form-control" type="number" @input="calculate(selectedItem)" v-model="selectedItem.qty" name="qty" /></td>
                                        <td width="10%" class="align-middle">19%</td>
                                        <td width="10%" class="align-middle">{{selectedItem.total}}</td>
                                    </tr>
                                    <tr>
                                        <td width="3%" class="align-middle"><button class="btn btn-primary btn-sm" @click="createItem"><i class="fa fa-plus"></i></button></td>
                                        <td width="20%"><input class="item_input ml-3 form-control" type="text" v-model="newItem.name" /></td>
                                        <td width="37%"><input class="item_area ml-3 form-control" type="text" v-model="newItem.description" /></td>
                                        <td width="10%"><input class="item_qty ml-3 form-control" type="number" @input="calculateNewItem(newItem)" v-model="newItem.price" /></td>
                                        <td width="10%"><input class="item_qty ml-3 form-control" type="number" @input="calculateNewItem(newItem)" v-model="newItem.qty" /></td>
                                        <td width="10%" class="align-middle">19%</td>
                                        <td width="10%" class="align-middle">{{newItem.total}}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <hr>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{$t('Offer.close')}}</button>
                    <button type="button" class="btn btn-primary" @click="addItem"><i class="fa fa-save"></i>{{$t('Offer.save')}}</button>
                </div>
            </div>
        </div>
    </div>
</span>
</template>
<script>
import {
    mapGetters
} from "vuex";
import categoryApi from "../../api/items";
export default {
    name: 'AddItemToOffer',
    data() {
        return {
            selectedItems: [],
            newItem: {
                name: '',
                description: '',
                price: '',
                qty: '',
                total_price: '',
                total: '',
                tax_price: '',
                item_category_id: 1
            }
        }
    },
    computed: {
        ...mapGetters({
            categories: "item/allCategories",
        }),
    },
    methods: {
        addItem() {
            this.$emit('click-to-add-items', this.selectedItems)
            $('#exampleModal').modal('hide');
        },
        deleteItem(selectedItem) {
            selectedItem.qty = 0;
            selectedItem.total = 0;
            this.selectedItems.splice(this.selectedItems.indexOf(selectedItem), 1);
        },
        calculate(item) {
            this.selectedItems.filter(selectedItem => {
                if (item.id == selectedItem.id) {
                    let tax = (item.price * 0.19) * item.qty;
                    selectedItem.tax_price = tax;
                    let total = item.price * item.qty;
                    selectedItem.total_price = total;
                    selectedItem.total = tax + total;
                }
            })
        },
        calculateNewItem(newItem) {
            let tax = (newItem.price * 0.19) * newItem.qty;
            this.newItem.tax_price = tax;
            let total = newItem.price * newItem.qty;
            this.newItem.total_price = total;
            this.newItem.total = tax + total;
        },
        getCategories() {
            this.$store
                .dispatch("item/getCategories")
                .then(() => {
                    this.$Progress.finish();
                    this.isLoading = false;
                    this.total_rows = response.data.data.total;
                })
                .catch(error => {
                    this.$Progress.fail();
                    this.isLoading = false;
                });
        },
        createItem() {
            this.$Progress.start();
            this.$store
                .dispatch("item/createItem", this.newItem)
                .then(response => {
                    let item = {};
                    item.id = response.data.data.id;
                    item.created_at = response.data.data.created_at;
                    item.updated_at = response.data.data.updated_at;
                    item.name = response.data.data.name;
                    item.description = response.data.data.description;
                    item.price = response.data.data.price;
                    item.qty = this.newItem.qty;
                    let tax = (item.price * 0.19) * item.qty;
                    item.tax_price = tax;
                    let total = response.data.data.price * item.qty;
                    item.total_price = total;
                    item.total = tax + total;
                    this.selectedItems.push(item);
                    this.getCategories();
                    this.$Progress.finish();
                    Toast.fire({
                        type: "success",
                        title: response.data.message
                    });
                })
                .then(() => {
                    this.newItem.name = '';
                    this.newItem.category_id = '1'
                    this.newItem.description = '';
                    this.newItem.price = '';
                    this.newItem.tax_price = '';
                    this.newItem.total_price = '';
                    this.newItem.total = '';
                })
                .catch(error => {
                    this.$Progress.fail();
                    if (error.response) {
                        this.form.errors.errors = error.response.data.errors;
                    }
                });
        },
    },
    mounted() {
        $(function() {
            $("ul.checktree").checktree();
        });
    }
}
</script>
<style scoped>
ul {
    list-style-type: none;
    margin: 3px;
}

ul.checktree li:before {
    height: 1em;
    width: 12px;
    border-bottom: 1px dashed;
    content: "";
    display: inline-block;
    top: -0.3em;
}

ul.checktree li {
    border-left: 1px dashed;
}

ul.checktree li:last-child:before {
    border-left: 1px dashed;
}

ul.checktree li:last-child {
    border-left: none;
}

.item_input {
    width: 150px;
}

.item_area {
    width: 340px;
}

.item_qty {
    width: 80px;
}

</style>
