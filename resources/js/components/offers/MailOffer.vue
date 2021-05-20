<template>
  <span class="mail-offer">
    <a href="#" @click="showModal" class="btn btn-app">
        <i class="fas fa-envelope"></i> {{$t('Offer.sendMail')}}
    </a>
    <div
      class="modal fade"
      id="Modal"
      tabindex="-1"
      role="dialog"
      aria-labelledby="newModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="newOfferLabel">{{$t('Offer.msgReply')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row mb-3">
              <div class="col-sm-12">
                <div class="input-group">
                    <input type="text" v-model="form.subject" name="subject" placeholder="Subject" class="form-control">
                    <span class="input-group-append">
                      <button type="button" @click="createOfferMailFromModel" class="btn btn-primary">{{$t('Offer.send')}}</button>
                    </span>
                  </div>
              </div>
              <div class="form-group mt-3 col-sm-12">
                <Multiselect
                  v-model="form.to"
                  :multiple="true"
                  :options="users.data"
                  :close-on-select="true"
                  :placeholder="$t('Offer.sendTo')"
                  label="name"
                  track-by="name"
                ></Multiselect>
                <has-error :form="form" field="role"></has-error>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <wysiwyg v-model="form.offer_mail" />
              </div>
            </div>
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
import offerApi from '../../api/offers';
import userApi from '../../api/users';
import Multiselect from 'vue-multiselect';
  export default {
    name: 'MailOffer',
    data() {
      return {
        mails: [],
        offerId: this.$route.params.id,
        form: new Form({
          offer_id: this.$route.params.id,
          offer_mail: '',
          subject: '',
          to: '',
        }),
      }
    },
    components: {
      Multiselect
    },
    methods: {
      showModal() {
        $("#Modal").modal("show");
      },
      createOfferMailFromModel() {
        offerApi
            .postOfferConversation(this.form)
            .then(response => {
                this.$Progress.finish();
                Toast.fire({
                    type: "success",
                    title: response.data.message
                });

            })
            .then(() => {
              this.form.offer_mail = '';
              this.form.subject = '';
              this.form.to = '';
            })
            .catch(error => {
                this.$Progress.fail();
                Toast.fire({
                    type: "error",
                    title: error.response.data.message
                });
            });
        $("#Modal").modal("hide");
      },
    },
    computed: {
      ...mapGetters({
          users: "user/activeUsers",
      })
    }
  }
</script>
<style scoped>
.modal-dialog {
width: 100%;
min-width: 100%;
height: 100%;
margin: 0 auto;
padding: 0;
}

.modal-content {
height: auto;
min-height: 100%;
border-radius: 0;
}
</style>
