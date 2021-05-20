<template>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"> {{$t('AttributesEmails.Emailtemplates')}}</h3>
          <div class="card-tools">

          </div>
        </div>
        <div class="filter">
<div class="form-row  col-md-12">
                    <div class="form-group col-md-3">
                        <label for="user_type">{{$t('AttributesEmails.usertype')}}</label>
                        <select @change="select_user_type" v-model="user_type" class="form-control" id="user_type">
                             <option value="all">{{$t('AttributesEmails.All')}}</option>
                            <option value="client">{{$t('AttributesEmails.Client')}}</option>
                           <option value="employee">{{$t('AttributesEmails.Employee')}}</option>
                        </select>
                    </div>
                    <div v-if="attributes" class="form-group col-md-3">
                        <label for="Attribute">{{$t('AttributesEmails.Attribute')}}</label>
                        <select v-model="attribute_filter" class="form-control">
                            <option v-for="(attribute,index) in attributes" :key="index" :value="attribute">{{ attribute.name }}</option>
                        </select>
                    </div>
                  <div class="form-group col-md-3" v-if="attribute_filter && attribute_filter.type=='dropdown'">
                           <label for="value">{{$t('AttributesEmails.Attribute')}}</label>
                            <select class="form-control" v-model="attribute_filter_data">
                                <option
                                v-for="(value,index) in attribute_filter.values"
                                :key="index"
                                :value="value"
                                >{{ value }}</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3" v-if="attribute_filter && attribute_filter.type=='text'">
                            <label for="value">{{$t('AttributesEmails.value')}}</label>
                            <input type="text" class="form-control" v-model="attribute_filter_data" />
                        </div>

                        <div class="form-group col-md-3" v-if="attribute_filter && attribute_filter.type=='checkbox'">
                            <div
                            class="form-check"
                            v-for="(value,cindex) in attribute_filter.values"
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

                        <div class="form-group col-md-3" v-if="attribute_filter && attribute_filter.type=='radiobutton'">
                            <div
                            class="form-check"
                            v-for="(value,cindex) in attribute_filter.values"
                            :key="cindex"
                            >
                            <label class="form-check-label">
                                <label for="value">{{$t('AttributesEmails.value')}}</label>
                                <input
                                type="radio"
                                class="form-check-input"
                                :value="value"
                                v-model="attribute_filter_data"
                                name="value"
                                />
                                {{ value }}
                            </label>
                            </div>
                        </div>


                    <div class="form-group col-md-3">
                        <label for="user_type">Started at</label>
                            <date-picker
                                v-model="mail_start_date"
                                lang="en"
                                type="datetime"
                                format="DD-MM-YYYY HH:mm"
                                :minute-step="15"
                                value-type="format"
                                input-class="form-control"
                            ></date-picker>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="email_type">time</label>
                        <select v-model="email_time" class="form-control" id="email_time">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="last_day_in_the_month">Last Day In The Month</option>
                            <option value="every_15_day">Every 15 Day</option>
                            <option value="every_year">Every Year</option>
                            <option value="every_three_months">Every 3 Month</option>
                            <option value="every_six_months">Every 6 Month</option>
                            <option value="specific_date">Specific Date</option>
                        </select>
                    </div>

                <div class="form-group col-md-12">
                 <label for="content">Message</label>
                <quill-editor v-model="email_content"
                  id="comments-editor"
                  ref="myQuillEditor"
                ></quill-editor>
                </div>

                <button class="btn btn-primary" @click="save_template"> Save </button>


                </div>



        </div>

        <!-- /.card-header -->
        <div class="card-body table-responsive p-0">

        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->
    </div>
  </div>
</template>
<script>
import { mapGetters } from "vuex";
import { quillEditor } from "vue-quill-editor";
import DatePicker from "vue2-datepicker";

export default {
    data() {
        return {
            user_type: "all",
            attribute_filter_data: [],
            attribute_filter: null,
            mail_start_date: null,
            email_time: null,
            email_content: ''
        }
    },
    methods: {
        select_user_type() {
            this.getAttributes();
        },
        getAttributes() {
                this.$Progress.start();
                this.$store
                    .dispatch("attribute/getAttributes", this.user_type)
                    .then(resoponse => {
                    this.$Progress.finish();
                    })
                    .catch(error => {
                    this.$Progress.fail();
                    });
        },
        save_template(){
           let $data = {
                "attribute_value"     : this.attribute_filter_data,
                "attribute_name"      : this.attribute_filter.name,
                "attribute_id"        : this.attribute_filter.id,
                "mail_start"          : this.mail_start_date,
                "email_time"          : this.email_time,
                "email_content"       : this.email_content
            }



                this.$Progress.start();
                this.$store
                    .dispatch("attribute/addAttributeEmail", $data)
                    .then(resoponse => {
                    this.$Progress.finish();
                    })
                    .catch(error => {
                    this.$Progress.fail();
                    });
        }
    },
    computed: {
        ...mapGetters({
            attributes: "attribute/attributesGetter"
        })
    },
    mounted() {
        //this.getAttributes();
    },
    components: {
       quillEditor,
       DatePicker
    }



}
</script>
<style>
</style>
