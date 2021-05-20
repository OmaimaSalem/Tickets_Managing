<template>
<div class="categories">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Mail Template Categories</div>
            <div class="card-tools">
                <button class="btn btn-success btn-sm" @click="createModal">Create Category</button>
            </div>
        </div>
        <div class="card-body">
            <vue-bootstrap4-table v-if="resultCategorires" :rows="resultCategorires" :columns="columns" :config="config" :total-rows="total_rows" :classes="classes">
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
    <CreateTemplateCategory :form="form"></CreateTemplateCategory>
    <UpdateTemplateCategory :form="form"></UpdateTemplateCategory>
</div>
</template>
<script>
import {mapGetters} from "vuex";
import VueBootstrap4Table from "vue-bootstrap4-table";
import CreateTemplateCategory from '../../components/send_mail/CreateCategory';
import UpdateTemplateCategory from '../../components/send_mail/UpdateCategory';

export default {
    name: 'MailTemplateCategoriesComponent',
    data() {
        return {
            isLoading: false,
            total_rows: 0,
            form: new Form({
                id: '',
                name: '',
            }),
            columns: [{
                    label: "ID#",
                    name: "id",
                    sort: true,
                    row_text_alignment: "text-center"
                },
                {
                    label: "Name",
                    name: "name",
                    sort: true,
                    row_text_alignment: "text-center"
                },
                {
                    label: "Created at",
                    name: "created_at",
                    sort: true,
                },
                {
                    label: "action",
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
                    placeholder: "Enter custom Search text",
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
                .dispatch("template/getCategories")
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
            $("#CreateTemplateCategory").modal("show");
        },
        editModal(category) {
            console.log('test');
            this.form.reset();
            $("#UpdateTemplateCategory").modal("show");
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
                        .dispatch("template/deleteCategory", id)
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
        CreateTemplateCategory,
        UpdateTemplateCategory,
    },
    computed: {
        ...mapGetters({
            categories: "template/allCategories",
            user: "user/activeSingleUser",
        }),
        resultCategorires() {
            return this.categories.data;
        }
    },
    mounted() {
        this.getCategories();
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
