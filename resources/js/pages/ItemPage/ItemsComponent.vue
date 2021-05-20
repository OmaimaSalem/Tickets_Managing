<template>
<div class="items">
    <div class="card">
        <div class="card-header">
            <div class="card-title">{{$t('Category.items')}}</div>
            <div class="card-tools">
                <button class="btn btn-success btn-sm" @click="createModal">{{$t('Category.createCategory')}}</button>
            </div>
        </div>
        <div class="card-body">
            <vue-bootstrap4-table v-if="resultItems" :rows="resultItems" :columns="columns" :config="config" :total-rows="total_rows" :classes="classes">
                <template slot="sort-asc-icon">
                    <i class="fas fa-sort-up"></i>
                </template>
                <template slot="sort-desc-icon">
                    <i class="fas fa-sort-down"></i>
                </template>
                <template slot="no-sort-icon">
                    <i class="fas fa-sort"></i>
                </template>
                <template slot="name" class="fixed-column" slot-scope="props">
                    {{ props.cell_value }}
                </template>
                <template slot="created_at" slot-scope="props">
                    {{ props.cell_value | DateOnly }}
                </template>
                <template slot="description" slot-scope="props">
                    {{ wrapDescription(props.cell_value, 25) }}
                </template>
                <template slot="price" slot-scope="props">
                    {{ props.cell_value }}
                </template>
                <template slot="action-buttons" slot-scope="props">
                    <a href="#" @click="editModal(props.row)" class="btn btn-primary btn-xs">
                        <i class="fas fa-edit fa-fw"></i>
                    </a>
                    <a href="#" @click="deleteItem(props.row.id)" class="btn btn-danger btn-xs">
                        <i class="fas fa-trash fa-fw"></i>
                    </a>
                </template>
            </vue-bootstrap4-table>
        </div>
    </div>
    <CreateItemComponent :form="form" />
    <UpdateItemComponent :form="form" />
</div>
</template>
<script>
import {mapGetters} from "vuex";
import VueBootstrap4Table from "vue-bootstrap4-table";
import UpdateItemComponent from '../../components/items/UpdateItemModelComponent'
import CreateItemComponent from '../../components/items/CreateItemModelComponent'
import i18n from '../../../../src/i18n.js'

export default {
    name: 'ItemsComponent',
    data() {
        return {
            isLoading: false,
            total_rows: 0,
            form: new Form({
                id: '',
                name: '',
                description: '',
                price: '',
                item_category_id: '',
            }),
            columns: [{
                    label: "ID#",
                    name: "id",
                    sort: true,
                    row_text_alignment: "text-center"
                },
                {
                    label: this.$t('Category.name'),
                    name: "name",
                    sort: true,
                    row_text_alignment: "text-center"
                },
                {
                    label: this.$t('Category.description'),
                    name: "description",
                    sort: true,
                    row_text_alignment: "text-center"
                },
                {
                    label: this.$t('Category.price'),
                    name: "price",
                    sort: true,
                    row_text_alignment: "text-center"
                },
                {
                    label: this.$t('Category.createdAt'),
                    name: "created_at",
                    sort: true,
                },
                {
                    label: this.$t('Category.action'),
                    name: "action-buttons"
                }
            ],
            classes: {
                table: {
                    "table-sm": true
                }
            },
            config: {
                server_mode: false,
                card_mode: false,
                pagination: true,
                pagination_info: true,
                per_page: 15,
                show_refresh_button: false,
                show_reset_button: false,
                global_search: {
                    visibility: false,
                    showClearButton: false,
                    placeholder: this.$t('Category.enterCustom'),
                    init: {
                        value: ""
                    },
                    card_mode: false,
                },
            },
        }
    },
    methods: {
        getItems() {
          this.$Progress.start();
            this.$store
                .dispatch("item/getItems")
                .then(response => {
                  console.log('success');
                    this.$Progress.finish();
                    this.isLoading = false;
                    this.total_rows = response.data.data.total;
                })
                .catch(error => {
                  console.log('errrrrrr');
                    this.$Progress.fail();
                    this.isLoading = false;
                });
        },
        createModal() {
            this.form.reset();
            $("#CreateItem").modal("show");
        },
        editModal(item) {
            this.editMode = true;
            this.form.reset();
            $("#UpdateItem").modal("show");
            this.form.fill(item);
        },
        deleteItem(id) {
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
                        .dispatch("item/deleteItem", id)
                        .then(response => {
                            this.$Progress.finish();
                            Toast.fire({
                                type: "success",
                                title: response.data.message
                            });
                        })
                        .catch(error => {
                            console.log(error);
                            this.$Progress.fail();
                            if (error.response) {
                                this.form.errors.errors = error.response.data.errors;
                            }
                        });
                }
            });
        },
        wrapDescription(str, n){
          return (str.length > n) ? str.substr(0, n-1) + ' ....' : str;
        }
    },
    components: {
        VueBootstrap4Table,
        UpdateItemComponent,
        CreateItemComponent,
    },
    computed: {
        ...mapGetters({
            items: "item/allItems",
            user: "user/activeSingleUser",
        }),
        resultItems() {
            return this.items.data;
        },
    },
    mounted() {
        this.getItems();
    }
}
</script>
<style scoped>
  .fixed-column {
    word-wrap: break-word;
    min-width: 160px;
    max-width: 160px;
  }
</style>
