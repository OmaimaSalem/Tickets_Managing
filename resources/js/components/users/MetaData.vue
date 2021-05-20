<template>
  <div class="card" style="background-color: #F3F3F3">
    <div class="card-header">
      <label class="card-title font-weight-light">{{$t('User.profileDetails')}}</label>
      <div class="card-tools">
        <router-link
          :to="{ name: 'profile.edit', params: {id: this.$route.params.id }}"
          class="btn btn-primary btn-xs"
        >
          <i class="fas fa-edit fa-fw"></i> {{$t('User.editProfile')}}
        </router-link>
      </div>
    </div>

    <div class="card-body" v-if="user.metadata">

      <v-row>
        <v-col md="6" sm="6">
          <label for="first_name">{{$t('User.firstName')}}:</label>
        </v-col>
        <v-col md="6" sm="6">
          <div class="font-weight-light">{{ user.metadata.first_name }}</div>
        </v-col>
      </v-row>
      <v-row>
        <v-col md="6" sm="6">
          <label for="last_name">{{$t('User.lastName')}}:</label>
        </v-col>
        <v-col md="6" sm="6">
          <div class="font-weight-light">{{ user.metadata.last_name }}</div>
        </v-col>
      </v-row>
      <v-row>
        <v-col md="6" sm="6">
          <label for="company">{{$t('User.companyName')}}:</label>
        </v-col>
        <v-col md="6" sm="6">
          <div class="font-weight-light">{{ user.metadata.company }}</div>
        </v-col>
      </v-row>
      <v-row>
        <v-col md="6" sm="6">
          <label for="address">{{$t('User.address')}}:</label>
        </v-col>
        <v-col md="6" sm="6">
          <div class="font-weight-light">{{ user.metadata.address }}</div>
        </v-col>
      </v-row>


      <v-row>
        <v-col md="6" sm="6">
          <label for="language">{{$t('User.lang')}}:</label>
        </v-col>
        <v-col md="6" sm="6">
          <div class="font-weight-light">{{ user.metadata.language }}</div>
        </v-col>
      </v-row>
      <v-row>
        <v-col md="6" sm="6">
          <label for="language">{{$t('User.gender')}}:</label>
        </v-col>
        <v-col md="6" sm="6">
          <div class="font-weight-light">{{ user.metadata.gender }}</div>
        </v-col>
      </v-row>
      <v-row>
        <v-col md="6" sm="6">
          <label for="telephone">{{$t('User.tele')}}:</label>
        </v-col>
        <v-col md="6" sm="6">
          <div class="font-weight-light">{{ user.metadata.telephone }}</div>
        </v-col>
      </v-row>
      <v-row>
        <v-col md="6" sm="6">
          <label for="mobile">{{$t('User.mobile')}}:</label>
        </v-col>
        <v-col md="6" sm="6">
          <div class="font-weight-light">{{ user.metadata.mobile }}</div>
        </v-col>
      </v-row>

      <v-row>
        <v-col md="6" sm="6">
          <label for="website">{{$t('User.website')}}:</label>
        </v-col>
        <v-col md="6" sm="6">
          <div class="font-weight-light">{{ user.metadata.website }}</div>
        </v-col>
      </v-row>
      <v-row>
        <v-col md="6" sm="6">
          <label for="birth_date">{{$t('User.birthDate')}}:</label>
        </v-col>
        <v-col md="6" sm="6">
          <div class="font-weight-light">{{ user.metadata.birth_date | DateOnly }}</div>
        </v-col>
      </v-row>
      <v-row>
        <v-col md="6" sm="6">
          <label for="fax">{{$t('User.fax')}}:</label>
        </v-col>
        <v-col md="6" sm="6">
          <div class="font-weight-light">{{ user.metadata.fax }}</div>
        </v-col>
      </v-row>
      <v-row>
        <v-col md="6" sm="6">
          <label for="signature">{{$t('User.signature')}}:</label>
        </v-col>
        <v-col md="6" sm="6">
          <div class="font-weight-light" v-html="user.metadata.signature"></div>
        </v-col>
      </v-row>


      <!-- <div class="row">
        <div class="form-group col-sm-12 col-md-3">
          <label for="first_name">{{$t('User.firstName')}}:</label>
          <div class="font-weight-light">{{ user.metadata.first_name }}</div>
        </div>
        <div class="form-group col-sm-12 col-md-3">
          <label for="last_name">{{$t('User.lastName')}}:</label>
          <div class="font-weight-light">{{ user.metadata.last_name }}</div>
        </div>
        <div class="form-group col-sm-12 col-md-3">
          <label for="company">{{$t('User.companyName')}}:</label>
          <div class="font-weight-light">{{ user.metadata.company }}</div>
        </div>
        <div class="form-group col-sm-12 col-md-3">
          <label for="address">{{$t('User.address')}}:</label>
          <div class="font-weight-light">{{ user.metadata.address }}</div>
        </div>
      </div>
      <hr>
      <div class="row">
        <div class="form-group col-sm-12 col-md-3">
          <label for="language">{{$t('User.lang')}}:</label>
          <div class="font-weight-light">{{ user.metadata.language }}</div>
        </div>
        <div class="form-group col-sm-12 col-md-3">
          <label for="language">{{$t('User.gender')}}:</label>
          <div class="font-weight-light">{{ user.metadata.gender }}</div>
        </div>
        <div class="form-group col-sm-12 col-md-3">
          <label for="telephone">{{$t('User.tele')}}:</label>
          <div class="font-weight-light">{{ user.metadata.telephone }}</div>
        </div>
        <div class="form-group col-sm-12 col-md-3">
          <label for="mobile">{{$t('User.mobile')}}:</label>
          <div class="font-weight-light">{{ user.metadata.mobile }}</div>
        </div>
      </div>
      <hr>
      <div class="row">
        <div class="form-group col-sm-12 col-md-3">
          <label for="website">{{$t('User.website')}}:</label>
          <div class="font-weight-light">{{ user.metadata.website }}</div>
        </div>
        <div class="form-group col-sm-12 col-md-3">
          <label for="birth_date">{{$t('User.birthDate')}}:</label>
          <div class="font-weight-light">{{ user.metadata.birth_date | DateOnly }}</div>
        </div>
        <div class="form-group col-sm-12 col-md-3">
          <label for="fax">{{$t('User.fax')}}:</label>
          <div class="font-weight-light">{{ user.metadata.fax }}</div>
        </div>
        <div class="form-group col-sm-12">
          <label for="signature">{{$t('User.signature')}}:</label>
          <div class="font-weight-light" v-html="user.metadata.signature"></div>
        </div>
      </div> -->
    </div>



    <div class="card-body text-center" v-else>
      <router-link
          :to="{ name: 'profile.edit', params: {id: this.$route.params.id }}"
          class="btn btn-success btn-xs"
        >
          <i class="fas fa-plus fa-fw"></i> {{$t('User.moreDetails')}}
        </router-link>
    </div>
  </div>
</template>
<script>
import { mapGetters } from "vuex";

export default {
  props: {
    user: {
      type: Object,
      required: true
    }
  },
  computed: {
    // ...mapGetters({
    //   user: "user/activeSingleUser"
    // })
  }
};
</script>

<style scoped>
.col{
    padding-bottom:0px;padding-top:0px;
    line-height: 1rem;
}
</style>
