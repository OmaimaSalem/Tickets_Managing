<template>
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{$t('AttributesEmails.AttributesEmailsList')}}</h3>

                    <div class="card-tools">
                        <button v-if="$can('attribute-emails-create')"
                            type="submit"
                            class="btn btn-success btn-sm"
                            @click="newEmailTemplate"
                        >
                            <i class="fas fa-plus fa-fw"></i>
                            <span class="d-none d-lg-inline">
                               {{$t('AttributesEmails.NewTemplate')}}
                            </span>
                        </button>

                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                    <vue-bootstrap4-table
                        :rows="attributesEmails"
                        :columns="columns"
                        :config="config"
                        :total-rows="total_rows"
                        :classes="classes"
                        @on-change-query="onChangeQuery"
                        @on-download="onChangeQuery"

                    >


                    <template slot="email_time" slot-scope="props">
                        {{ props.row.email_time.replace(/_/g, " ") | capitalize }}
                    </template>


                    <template slot="action-buttons" slot-scope="props">
                        <a v-if="$can('attribute-emails-edit')" href="#" @click="editEmailTemplate(props.row)" class="btn btn-primary btn-xs">
                            <i class="fas fa-edit fa-fw"></i>
                        </a>
                        <a v-if="$can('attribute-emails-delete')" href="#" @click="deleteEmailTemplate(props.row.id)" class="btn btn-danger btn-xs">
                            <i class="fas fa-trash fa-fw"></i>
                        </a>
                    </template>


                    </vue-bootstrap4-table>
                </div>
            </div>
        </div>



<!-- Modal -->
<div class="modal fade" id="templateModal" tabindex="-1" role="dialog" aria-labelledby="templateModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="templateModal">{{$t('AttributesEmails.Modaltitle')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
                    <div class="form-group">
                        <label for="user_type">{{$t('AttributesEmails.usertype')}}</label>
                        <select v-model="form.user_type" @change="select_user_type"  class="form-control" id="user_type">
                            <option value="all">{{$t('AttributesEmails.All')}}</option>
                            <option value="client">{{$t('AttributesEmails.Client')}}</option>
                            <option value="allclients">{{$t('AttributesEmails.AllClients')}}</option>
                            <option value="employee">{{$t('AttributesEmails.Employee')}}</option>
                            <option value="allemployees">{{$t('AttributesEmails.AllEmployees')}}</option>
                        </select>
                    </div>
                    <has-error :form="form" field="user_type"></has-error>



                    <div v-if="attributes && attributes.length > 0 " class="form-group">
                        <label for="Attribute">{{$t('AttributesEmails.Attribute')}}</label>
                        <select v-model="form.attribute_filter" class="form-control">
                            <option v-for="(attribute,index) in attributes" :key="index" :value="attribute.id">{{ attribute.name }}</option>
                        </select>
                    </div>
                    <has-error :form="form" field="attribute_filter"></has-error>




                        <div class="form-group" v-if="getAttributeById(form.attribute_filter) && getAttributeById(form.attribute_filter).type=='dropdown'">
                              <label for="value">{{$t('AttributesEmails.value')}}</label>
                            <select class="form-control" v-model="form.attribute_filter_data">
                                <option
                                v-for="(value,index) in getAttributeById(form.attribute_filter).values"
                                :key="index"
                                :value="value"
                                >{{ value }}</option>
                            </select>
                        </div>

                        <div class="form-group" v-if="getAttributeById(form.attribute_filter) && getAttributeById(form.attribute_filter).type=='text'">
                            <label for="value">{{$t('AttributesEmails.value')}}</label>
                            <input type="text" class="form-control" v-model="attribute_filter_data" />
                        </div>

                        <div class="form-group" v-if="getAttributeById(form.attribute_filter) && getAttributeById(form.attribute_filter).type=='checkbox'">
                            <div
                            class="form-check"
                            v-for="(value,cindex) in getAttributeById(form.attribute_filter).values"
                            :key="cindex"
                            >
                            <input
                                class="form-check-input"
                                type="checkbox"
                                :value="value"
                                :name="'checkbox['+value+']'"
                                v-model="attribute_filter_data"
                            />
                            <label class="form-check-label" for="defaultCheck1">{{ value }}</label>
                            </div>
                        </div>

                        <div class="form-group" v-if="getAttributeById(form.attribute_filter) && getAttributeById(form.attribute_filter).type=='radiobutton'">
                            <div
                            class="form-check"
                            v-for="(value,cindex) in getAttributeById(form.attribute_filter).values"
                            :key="cindex"
                            >
                            <label class="form-check-label">
                                <label for="value">Value</label>
                                <input
                                type="radio"
                                class="form-check-input"
                                :value="value"
                                v-model="form.attribute_filter_data"
                                name="value"
                                />
                                {{ value }}
                            </label>
                            </div>
                        </div>
                    <has-error :form="form" field="attribute_value"></has-error>


                    <div class="form-group">
                        <label for="user_type">{{$t('AttributesEmails.startedAt')}}</label>
                            <date-picker
                                v-model="form.mail_start"
                                lang="en"
                                type="datetime"
                                format="DD-MM-YYYY HH:mm"
                                :minute-step="15"
                                value-type="format"
                                input-class="form-control"
                            ></date-picker>
                    </div>
                    <has-error :form="form" field="started_at"></has-error>





                    <div class="form-group">
                        <label for="email_type">{{$t('AttributesEmails.time')}}</label>
                        <select v-model="form.email_time" class="form-control" id="email_time">
                            <option value="daily">{{$t('AttributesEmails.Daily')}}</option>
                            <option value="weekly">{{$t('AttributesEmails.Weekly')}}</option>
                            <option value="last_day_in_the_month">{{$t('AttributesEmails.LastDayInTheMonth')}}</option>
                            <option value="every_15_day">{{$t('AttributesEmails.Every15Day')}}</option>
                            <option value="every_year">{{$t('AttributesEmails.EveryYear')}}</option>
                            <option value="every_three_months">{{$t('AttributesEmails.Every3Month')}}</option>
                            <option value="every_six_months">{{$t('AttributesEmails.Every6Month')}}</option>
                            <option value="specific_date">{{$t('AttributesEmails.SpecificDate')}}</option>
                        </select>
                    </div>
                    <has-error :form="form" field="email_time"></has-error>



                <div class="form-group">
                 <label for="content">{{$t('AttributesEmails.Message')}}</label>
                <quill-editor v-model="form.email_content"
                  id="comments-editor"
                  ref="myQuillEditor"
                  :options="editorOption"

                ></quill-editor>
                <has-error :form="form" field="email_content"></has-error>
                </div>






      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{$t('AttributesEmails.close')}}</button>
        <button v-if="!editMode" type="button" class="btn btn-primary" @click="save_template">{{$t('AttributesEmails.saveChanges')}}</button>
        <button v-if="editMode" type="button" class="btn btn-primary" @click="update_template(form.id)">Update</button>
      </div>
    </div>
  </div>
</div>

    </div>
</template>

<script>
import { mapGetters } from "vuex";
import VueBootstrap4Table from "vue-bootstrap4-table";
import moment from "moment";
import { quillEditor } from "vue-quill-editor";
import DatePicker from "vue2-datepicker";
// require styles
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";

export default {
    data() {
        return {
            editMode: false,
            isDisabled: false,
            emails: [],
            attribute_filter_data:[],
            form: new Form({
                    id: null,
                    user_type: "all",
                    attribute_filter_data: [],
                    attribute_filter: null,
                    mail_start: null,
                    email_time: null,
                    email_content: ''
            }),
            priorityList: ["normal", "high", "low"],
            editorOption: {
                modules: {
                    toolbar: [
                        ["bold", "italic", "underline", "strike"],
                        ["blockquote", "code-block"],
                        [{ list: "ordered" }, { list: "bullet" }]
                    ]
                }
            },
            columns: [
                {
                    label: 'user type',
                    name: "user_type",
                    filter: {
                        type: "simple",
                    },
                    sort: true,
                },
                {
                    label: 'attribute',
                    name: "attribute_name",
                    filter: {
                        type: "simple",
                    },
                    sort: true,
                },
                {
                    label: 'value',
                    name: "attribute_value",
                    filter: {
                        type: "simple",
                    },
                    sort: true,
                },
                {
                    label: 'Time',
                    name: "email_time",
                    filter: {
                        type: "simple",
                    },
                    sort: true,
                },
                {
                    label: 'date',
                    name: "mail_start",
                    filter: {
                        type: "simple",
                    },
                    sort: true,
                },
                {
                    label: 'Actions',
                    name: "action-buttons",
                },

            ],
            config: {
                server_mode: true,
                card_mode: false,
                show_refresh_button: false,
                pagination: true,
                pagination_info: true,
                per_page: 15,
                global_search: {
                    placeholder: "search",
                    init: {
                        value: ""
                    }
                }
            },
            classes: {
                table: {
                    "table-sm": true
                }
            },
            queryParams: {
                sort: [],
                filters: [],
                global_search: "",
                per_page: 15,
                page: 1
            },
            total_rows: 0
        };
    },
    methods: {

        onChangeQuery(queryParams) {
        this.queryParams = queryParams;
        this.getAttributesEmails();
        },
        getAttributeById($id){
            return this.attributes.find(
                items => items.id == $id
            );
        },
        getAttributesEmails() {
            this.$Progress.start();
            this.$store
                .dispatch("attribute/getAttributesEmails",{
                    queryParams: this.queryParams,
                    page: this.queryParams.page,
                    index: true
                })
                .then(response => {
                    this.total_rows = response.data.data.total;
                this.$Progress.finish();
                })
                .catch(error => {

                this.$Progress.fail();
            });
        },
        select_user_type($template = null) {
            this.getAttributes($template);
        },
        getAttributes($template) {
             this.$Progress.start();
            this.$store
                    .dispatch("attribute/getAttributes", this.form.user_type)
                    .then(resoponse => {
                    if($template){
                        this.form.attribute_filter = $template.attribute_id;
                        this.form.attribute_filter_data =  $template.attribute_value;
                    }

                    this.$Progress.finish();
                    })
                    .catch(error => {
                    this.$Progress.fail();
                    });
        },


        newEmailTemplate(){
            this.editMode = false,

            this.form.reset();
            this.form.clear();
            $("#templateModal").modal("show");
        },



        editEmailTemplate($template){
            this.editMode = true;
            this.form.fill($template);
            this.select_user_type($template);

            $("#templateModal").modal("show");
        },

        validation() {
            if(this.form.user_type == "all" || this.form.user_type == undefined) {
                this.form.errors.errors = {user_type : 'user type field required'}
                return false;
            }else{
               this.form.errors.errors.user_type = null;
            }

            if( this.form.attribute_filter == undefined && (this.form.user_type != 'allclients' &&  this.form.user_type != 'allemployees')) {
                this.form.errors.errors = {attribute_filter : 'Attribute field required'}
                return false;
            }else{
               this.form.errors.errors.attribute_filter = null;
            }

            if( this.form.attribute_filter_data == undefined && (this.form.attribute_filter && this.form.attribute_filter.type == "checkbox" && this.attribute_filter_data == undefined) && (this.form.user_type != 'allclients' &&  this.form.user_type != 'allemployees')) {
                this.form.errors.errors = {attribute_value : 'Attribute value field required'}
                return false;
            }else{
               this.form.errors.errors.attribute_value = null;
            }

            if( this.form.mail_start == undefined) {
                this.form.errors.errors = {started_at : 'Started at value field required'}
                return false;
            }else{
               this.form.errors.errors.started_at = null;
            }

            if( this.form.email_time == undefined) {
                this.form.errors.errors = {email_time : 'Time value field required'}
                return false;
            }else{
               this.form.errors.errors.email_time = null;
            }

            if( this.form.email_content == undefined || this.form.email_content == '') {
                this.form.errors.errors = {email_content : 'Message field required'}
                return false;
            }else{
               this.form.errors.errors.email_content = null;
            }
            return true;
        },

        save_template(){
            if(!this.validation()){
                return false;
            }

            let $attribute = this.getAttributeById(this.form.attribute_filter);

            let $data = {
                "attribute_value"     : $attribute && $attribute.type == "checkbox" ? this.attribute_filter_data : this.form.attribute_filter_data,
                "attribute_name"      : $attribute && $attribute.name ? $attribute.name : '',
                "attribute_id"        : $attribute ? $attribute.id : '',
                "mail_start"          : this.form.mail_start,
                "email_time"          : this.form.email_time,
                "email_content"       : this.form.email_content,
                "user_type"           : this.form.user_type
            }





            this.$Progress.start();
                this.$store
                    .dispatch("attribute/addAttributeEmail", $data)
                    .then(resoponse => {
                      $("#templateModal").modal("hide");
                       this.$Progress.finish();
                    })
                    .catch(error => {
                        console.log(error);
                    this.$Progress.fail();
                    });
        },
        update_template($id){

            if(!this.validation()){
                return false;
            }

            let $attribute = this.getAttributeById(this.form.attribute_filter);


            let $data = {
                "id"                  : $id,
                "attribute_value"     : $attribute.type == "checkbox" ? this.attribute_filter_data : this.form.attribute_filter_data,
                "attribute_name"      : $attribute.name,
                "attribute_id"        : $attribute.id,
                "mail_start"          : this.form.mail_start,
                "email_time"          : this.form.email_time,
                "email_content"       : this.form.email_content,
                "user_type"           : this.form.user_type
            }



            this.$Progress.start();
                this.$store
                    .dispatch("attribute/editAttributeEmail", $data)
                    .then(resoponse => {
                      $("#templateModal").modal("hide");
                       this.$Progress.finish();
                    })
                    .catch(error => {
                        console.log(error);
                        this.$Progress.fail();
                    });
        },

        deleteEmailTemplate($template_id){




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

                this.$store
                                    .dispatch("attribute/deleteAttributeEmail", $template_id)
                                    .then(resoponse => {
                                    this.$Progress.finish();
                                    })
                                    .catch(error => {
                                        console.log(error);
                                        this.$Progress.fail();
                                    });
                        }
    });
    }
    },
    components: {
        VueBootstrap4Table,
        quillEditor,
        DatePicker
    },
    created() {
    },
    mounted() {
        this.getAttributesEmails();
    },
    computed: {
        ...mapGetters({
            attributesEmails : "attribute/attributesEmailsGetter",
            attributes: "attribute/attributesGetter"
        }),
    },
    filters: {
        capitalize: function (value) {
            if (!value) return ''
            value = value.toString()
            return value.charAt(0).toUpperCase() + value.slice(1)
        }
    }

};
</script>

<style scoped>
.mx-datepicker {
    display: block;
    width: unset;
}
</style>
