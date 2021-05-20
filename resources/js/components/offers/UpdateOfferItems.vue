<template>
  <div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
          <div class="modal-body">
            <div class="row">
              <div class="form-group col-md-6">
                <label for="category">{{$t('Offer.category')}}</label>
                <select @change="categorySlected($event)" name="category" id="" class="form-control form-control-sm">
                  <option class="form-control form-control-sm" value=""></option>
                  <option class="form-control form-control-sm" v-for="category in categories.data" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
              </div>
              <div class="col-md-6">
                <div v-if="!items">{{$t('Offer.selectCategory')}}</div>
                <div v-else>
                  <table class="table table-hover table-sm">
                    <thead>
                      <th>{{$t('Offer.name')}}</th>
                      <th>{{$t('Offer.price')}}</th>
                      <th>{{$t('Offer.desc')}}</th>
                    </thead>
                    <tbody>
                      <tr v-for="item in items" :key="item.id" @click="addItem(item)">
                        <td>{{item.name}}</td>
                        <td>{{item.price}}</td>
                        <td>{{item.description}}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <template v-if="!selectedItems">
                  {{$t('Offer.noItem')}}
                </template>
                <template v-else>
                  <table class="table table-hover table-sm">
                    <thead>
                      <th>ID</th>
                      <th>{{$t('Offer.name')}}</th>
                      <th>{{$t('Offer.price')}}</th>
                      <th>{{$t('Offer.qty')}}</th>
                      <th>{{$t('Offer.totalNoTax')}}</th>
                      <th>{{$t('Offer.tax')}}%</th>
                      <th>{{$t('Offer.taxPrice')}}</th>
                      <th>{{$t('Offer.total')}}</th>
                      <th>{{$t('Offer.action')}}</th>
                    </thead>
                    <tbody>
                      <tr v-for="selectedItem in selectedItems" :key="selectedItem.id">
                        <td>{{ selectedItem.id }}</td>
                        <td>{{ selectedItem.name }}</td>
                        <td>{{ selectedItem.price }}</td>
                        <td><input type="text" autofocus class="form-control-inline" @input="calculate(selectedItem)" v-model="selectedItem.qty"/></td>
                        <td>{{ selectedItem.total_price }}</td>
                        <td>19%</td>
                        <td>{{ selectedItem.tax_price }}</td>
                        <td>{{ selectedItem.total }}</td>
                        <td><button class="btn btn-primary btn-sm" @click="deleteItem(selectedItem)"><i class="fa fa-trash"></i></button></td>
                      </tr>
                    </tbody>
                  </table>
                </template>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-success" @click="add">{{$t('Offer.add')}}</button>
          </div>
      </div>
    </div>
  </div>
</template>
<script>
import { mapGetters } from "vuex";
import VueBootstrap4Table from "vue-bootstrap4-table";
import categoryApi from "../../api/categories";
  export default {
    name: 'AddItemToOffer',
    props: ['this.selectedItems'],
    data() {
      return {
        items: null,
        selectedItems: [],
      }
    },
    computed: {
      ...mapGetters({
        clients: "user/activeUsers",
        categories: "category/allCategories",
      }),
    },
    methods: {
      categorySlected(event) {
      this.$Progress.start();
      categoryApi
        .show(event.target.value)
        .then(response => {
          this.$Progress.finish();
          this.items  = response.data.data.items.data
        })
        .catch(error => {
          console.log(error);
          this.$Progress.fail();
          Toast.fire({
            type: "error",
            title: error.response.data.message
          });
        });
      },
      addItem(item) {
        if(this.selectedItems.includes(item)){
          Toast.fire({
            type: "error",
            title: "Item already selected"
          });
        } else {
          this.selectedItems.push(item);
        }
      },
      deleteItem(selectedItem) {
        selectedItem.qty = 0;
        selectedItem.total = 0;
        this.selectedItems.splice(this.selectedItems.indexOf(selectedItem), 1);
      },
      calculate(item) {
        this.selectedItems.filter(selectedItem => {
          if(item.id == selectedItem.id) {
            let tax = (item.price * 0.19) * item.qty;
            selectedItem.tax_price = tax;
            let total = item.price * item.qty;
            selectedItem.total_price = total;
            return selectedItem.total = tax + total;
          }
        })
      },
      add() {
        $("#Modal").modal("hide");
        this.$emit('click-to-add-items', this.selectedItems);
      }
    },
    components: {
      VueBootstrap4Table,
    },
    mounted() {
    },
  }
</script>
<style scoped>
  .form-control-inline {
    min-width: 0;
    width: 60px;
    display: inline;
}
</style>
