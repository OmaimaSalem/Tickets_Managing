<template>
<div class="categories">
    <div class="card">
        <div class="card-header">
            <div class="card-tools">
                <button class="btn btn-success btn-sm" @click="createModal"> {{$t('Category.createCategory')}}</button>
            </div>
        </div>
        <div class="card-body">
            <vue-bootstrap4-table v-if="resultCategories" :rows="resultCategories" :columns="columns" :config="config" :total-rows="total_rows" :classes="classes">
                <template slot="sort-asc-icon">
                    <i class="fas fa-sort-up"></i>
                </template>
                <template slot="sort-desc-icon">
                    <i class="fas fa-sort-down"></i>
                </template>
                <template slot="no-sort-icon">
                    <i class="fas fa-sort"></i>
                </template>
                <template slot="name" slot-scope="props">
                    {{ props.cell_value }}
                </template>
                <template slot="created_at" slot-scope="props">
                    {{ props.cell_value | DateOnly }}
                </template>
                <template slot="description" slot-scope="props">
                    {{ props.cell_value }}
                </template>
                <template slot="action-buttons" slot-scope="props">
                    <a href="#" @click="editModal(props.row)" class="btn btn-primary btn-xs">
                        <i class="fas fa-edit fa-fw"></i>
                    </a>
                    <a href="#" @click="deleteCategory(props.row.id)" class="btn btn-danger btn-xs">
                        <i class="fas fa-trash fa-fw"></i>
                    </a>
                </template>
            </vue-bootstrap4-table>
        </div>
    </div>
    <CreateCategoryComponent :form="form" />
    <UpdateCategoryComponent :form="form" />
</div>
</template>
<script>
import {
    mapGetters
} from "vuex";
import VueBootstrap4Table from "vue-bootstrap4-table";
import categoryApi from "../../api/categories";
import UpdateCategoryComponent from '../../components/categories/UpdateCategoryModelComponent'
import CreateCategoryComponent from '../../components/categories/CreateCategoryModelComponent'

export default {
    name: 'ItemsComponent',
    data() {
        return {
            isLoading: false,
            form: new Form({
                id: '',
                name: '',
                description: '',
                items: null,
            }),
            total_rows: 0,
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
        getCategories() {
            this.$store
                .dispatch("category/getCategories")
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
        createModal() {
            this.form.reset();
            $("#CreateCategory").modal("show");
        },
        editModal(category) {
            this.editMode = true;
            this.form.reset();
            $("#UpdateCategory").modal("show");
            this.form.fill(category);
        },
        deleteCategory(id) {
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
                        .dispatch("category/deleteCategory", id)
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
        }
    },
    components: {
        VueBootstrap4Table,
        UpdateCategoryComponent,
        CreateCategoryComponent,
    },
    computed: {
        ...mapGetters({
            categories: "category/allCategories",
            user: "user/activeSingleUser",
        }),
        resultCategories() {
            return this.categories.data;
        }
    },
    mounted() {
        this.getCategories();
    }
}
</script>
