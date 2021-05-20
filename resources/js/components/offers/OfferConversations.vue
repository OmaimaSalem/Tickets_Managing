<template>
  <div v-if="offerConversations" class="offer-conversations">
    <div class="card">
      <div class="card-header">
          <h4>{{$t('Offer.offerConversation')}}</h4>
      </div>
      <div class="card-body">
        <div v-for="offerConversation in offerConversations.data"  :key="offerConversation.id">
          <div
    			class="overflow-auto p-3 mb-3 mb-md-0 mr-md-3 bg-light"
    			v-html="offerConversation.conversation"
    			style="min-height:100px; max-height: 600px;"
    			></div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { mapGetters } from "vuex";
  export default {
    name: 'OfferConversation',
    props: ['offer_id'],
    methods: {
      getOfferConversations(id) {
        this.$store
          .dispatch("offer/getOfferConversations", id)
          .then(response => {
            this.$Progress.finish();
            this.loading = false;
          })
          .catch(error => {
            this.$Progress.fail();
          });
      }
    },
    computed: {
      ...mapGetters({
        offerConversations: "offer/activeOfferConversations",
      })
    },
    mounted() {
      this.getOfferConversations(this.offer_id);
    }
  }
</script>
