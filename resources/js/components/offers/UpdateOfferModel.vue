<template>
  <div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">{{$t('Offer.offerNum')}}: {{form.number}}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form @submit.prevent="editOffer(form)" method="POST">
          <div class="modal-body">

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{$t('Offer.close')}}</button>
            <button type="button" class="btn btn-primary">{{$t('Offer.saveChanges')}}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
<script>
  export default {
    props: ['form'],
    methods: {
      editOffer(data) {
        console.log(data);
      }
    }
  }
</script>
