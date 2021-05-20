<template>
<div class="contracts">
    <div class="card">
        <div class="card-header">
            <div class="card-title">{{$t('ContractPage.contracts')}}</div>
        </div>
        <div class="card-body">
            <vue-bootstrap4-table v-if="resultContracts" :rows="resultContracts" :columns="columns" :config="config" :total-rows="total_rows" :classes="classes">
                <template slot="sort-asc-icon">
                    <i class="fas fa-sort-up"></i>
                </template>
                <template slot="sort-desc-icon">
                    <i class="fas fa-sort-down"></i>
                </template>
                <template slot="no-sort-icon">
                    <i class="fas fa-sort"></i>
                </template>
                <template slot="number" slot-scope="props">
                    <router-link :to="{ name: 'contract', params: { id: props.row.id }}">{{ props.cell_value }}</router-link>
                </template>
                <template slot="created_at" slot-scope="props">
                    {{ props.cell_value | DateOnly }}
                </template>
                <template slot="action-buttons" slot-scope="props">
                    <a href="#" @click="deleteContract(props.row.id)" class="btn btn-danger btn-xs">
                        <i class="fas fa-trash fa-fw"></i>
                    </a>
                </template>
            </vue-bootstrap4-table>
        </div>
    </div>
    <UpdateContractModel :form="form" />
</div>
</template>
<script>
import {
    mapGetters
} from "vuex";
import VueBootstrap4Table from "vue-bootstrap4-table";
import UpdateContractModel from '../../components/contracts/UpdateContractModel';
import contractApi from "../../api/contracts";

export default {
    name: 'ContractsComponent',
    data() {
        return {
            isLoading: false,
            form: new Form({
                number: '',
            }),
            total_rows: 0,
            editMode: false,
            columns: [
                {
                    label: this.$t('ContractPage.contractNum'),
                    name: "number",
                    sort: true,
                    row_text_alignment: "text-center"
                },
                {
                    label: this.$t('ContractPage.title'),
                    name: "total",
                    sort: true,
                    row_text_alignment: "text-center"
                },
                {
                    label: this.$t('ContractPage.client'),
                    name: "client.name",
                    sort: true,
                    row_text_alignment: "text-center"
                },
                {
                    label: this.$t('ContractPage.createdAt'),
                    name: "created_at",
                    sort: true,
                },
                {
                    label: this.$t('ContractPage.action'),
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
                    placeholder: this.$t('ContractPage.enterCustom'),
                    init: {
                        value: ""
                    },
                    card_mode: false,
                },
            },
        }
    },
    methods: {
        getContracts() {
            this.$store
                .dispatch("contract/getContracts")
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
        editModal(item) {
            this.editMode = true;
            this.form.reset();
            $("#Modal").modal("show");
            this.form.fill(item);
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
                    contractApi
                        .delete(id)
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
                            Toast.fire({
                                type: "error",
                                title: error.response.data.message
                            });
                        });
                }
            });
        }
    },
    components: {
        VueBootstrap4Table,
        UpdateContractModel
    },
    computed: {
        ...mapGetters({
            contracts: "contract/allContracts",
            user: "user/activeSingleUser",
        }),
        resultContracts() {
            return this.contracts.data;
        }
    },
    mounted() {
        this.getContracts();
    }
}
</script>
