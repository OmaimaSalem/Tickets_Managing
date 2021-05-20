<template>
  <div>
    <!-- Profile Image -->
    <!-- <div class="card card-primary card-outline">
      <div class="card-body box-profile">
        <div class="text-center">
          <img
            class="profile-user-img img-fluid img-circle"
            src="/../assets/img/profile.png"
            alt="User profile picture"
          />
        </div>

        <h3 class="profile-username text-center">{{ user.name }}</h3>
        <p class="text-muted text-center">{{ user.type }}</p>
        <ul class="list-group list-group-unbordered mb-3">
          <li class="list-group-item">
            <b>{{$t('User.projects')}}</b>
            <a class="float-right">{{ projectCount }}</a>
          </li>
          <li class="list-group-item">
            <b>{{$t('User.tickets')}}</b>
            <a class="float-right">{{ ticketCount }}</a>
          </li>
          <li class="list-group-item">
            <b>{{$t('User.tasks')}}</b>
            <a class="float-right">{{ taskCount }}</a>
          </li>
        </ul>
      </div> -->
      <!-- /.card-body -->
    <!--</div>-->
    <!-- /.card -->


     <!-- <div class="card" style="background-color: #F3F3F3">
        <div class="row">
            <div class="col-xs-6 col-md-2">
                <div class="text-center">
                    <img
                        class="profile-user-img img-fluid img-circle sm"
                        src="/../assets/img/profile.png"
                        alt="User profile picture"
                        style="margin-top:.3rem"
                    />
                </div>
                <h3 class="profile-username text-center">{{ user.name }}</h3>
                <p class="text-muted text-center">{{ user.type }}</p>
            </div>
            <div class="col-xs-6 col-md-7">
                <ul class="list-group" style="background-color : #F3F3F3; padding:0.5rem">
                    <li class="list-group-item listStyle">
                        <label class="float-left">{{$t('User.projects')}}</label>
                        <a class="float-right">{{ projectCount }}</a>
                    </li>
                    <li class="list-group-item listStyle">
                        <label class="float-left">{{$t('User.tickets')}}</label>
                        <a class="float-right">{{ ticketCount }}</a>
                    </li>
                    <li class="list-group-item listStyle">
                        <label class="float-left">{{$t('User.tasks')}}</label>
                        <a class="float-right">{{ taskCount }}</a>
                    </li>
                </ul>
            </div>

            <div class="col-xs-3 col-md-3" style="margin-left: -1rem;margin-top:1rem">
                <div class="stat-container">
                    <label class="float-left ml-2">{{ $t('Ticket.openTicket') }}</label>
                    <label class="float-right ticketsCount">{{openTickets}}</label>
                </div>

                <div class="stat-container">
                    <label class="float-left ml-2">{{ $t('Ticket.pendingTickets') }}</label>
                    <label class="float-right ticketsCount">{{pendingTickets}}</label>
                </div>

                <div class="stat-container">
                    <label class="float-left ml-2">{{ $t('Ticket.doingTickets') }} </label>
                    <label class="float-right ticketsCount">{{doingTickets}}</label>
                </div>

                <div class="stat-container">
                    <label class="float-left ml-2"> {{ $t('Ticket.doneTickets') }}</label>
                    <label class="float-right ticketsCount">{{doneTickets}}</label>
                </div>
            </div>
        </div>
    </div>  -->


    <v-card color="#F3F3F3">
      <v-row>
        <v-col>
          <v-row>
            <v-col md="12" sm="12" class="pad" align-self="center">
              <div class="text-center">
                  <img
                      class="profile-user-img img-fluid img-circle sm"
                      src="/../assets/img/profile.png"
                      alt="User profile picture"
                      style="margin-top:.3rem"
                  />
              </div>
              <h3 class="profile-username text-center bold">{{ user.name }}</h3>
              <h5 class="profile-username text-center">{{ user.email }}</h5>
              <h5 class="text-muted text-center">{{ user.type }}</h5>
              
              <!-- <div>
                <h5 class="text-muted text-center">Attributes
                  <v-btn icon @click="showAttributes" style="width: 1.5rem;height: 1.5rem;">
                      <i style="font-size: .6rem" class="fa fa-ellipsis-v"></i>
                  </v-btn>
                </h5>
                <div v-if="attributes"  class="toggle-menu-card" style="margin:0;margin-left:3rem">
                    <div class="content" >
                        <ul style="list-style: none;text-align: center;" class="toggle-div" v-for="attribute in user.attributes" :key="attribute.id">
                            <li style="display: inline-flex; width: max-content;">
                                <span style="margin-left: 0.5rem">
                                    {{attribute.name}}:
                                </span>
                                <span> {{attribute.value}} </span>
                            </li>
                        </ul>
                    </div>
                </div>
              </div> -->
            </v-col>
          </v-row>
          <v-row>
            <v-col md="12" sm="12" class="pad">
              <ul class="list-group" style="background-color : #F3F3F3;">
                  <li class="list-group-item listStyle">
                      <label class="float-left">{{$t('User.projects')}}</label>
                      <a class="float-right">{{ projectCount }}</a>
                  </li>
                  <li class="list-group-item listStyle">
                      <label class="float-left">{{$t('User.tickets')}}</label>
                      <a class="float-right">{{ ticketCount }}</a>
                  </li>
                  <li class="list-group-item listStyle">
                      <label class="float-left">{{$t('User.tasks')}}</label>
                      <a class="float-right">{{ taskCount }}</a>
                  </li>
              </ul>


            </v-col>
          </v-row>
          <v-row>
            <v-col md="12" sm="12" class="pad">
                <div class="stat-container">
                    <label class="float-left ml-2">{{ $t('Ticket.openTicket') }} </label>
                    <label class="float-right ticketsCount">{{openTickets}}</label>
                </div>

                <div class="stat-container">
                    <label class="float-left ml-2">{{ $t('Ticket.pendingTickets') }} </label>
                    <label class="float-right ticketsCount">{{pendingTickets}}</label>
                </div>

                <div class="stat-container">
                    <label class="float-left ml-2">{{ $t('Ticket.doingTickets') }}</label>
                    <label class="float-right ticketsCount">{{doingTickets}}</label>
                </div>

                <div class="stat-container">
                    <label class="float-left ml-2">{{ $t('Ticket.doneTickets') }} </label>
                    <label class="float-right ticketsCount">{{doneTickets}}</label>
                </div>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-card>


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
  data(){
    return{
      attributes: false
    }
  },
  methods:{
    showAttributes(){
      this.attributes = !this.attributes
    }
  },
  computed: {
    ...mapGetters({
      projectCount: "user/ProjectCountPerClient",
      ticketCount: "user/TicketCountPerClient",
      taskCount: "user/TotlaTaskCountPerClient",
      openTickets:"user/openTicketsCountPerClient",
      doneTickets:"user/doneTicketsCountPerClient",
      doingTickets:"user/doingTicketsCountPerClient",
      pendingTickets:"user/pendingTicketsCountPerClient",
    })
  }
};
</script>

<style scoped>
.stat-container{
    border-radius: 15px;
    /*text-align: center;*/
    height: 2rem;
    background-image: linear-gradient(180deg, white, #8b8b8b);
    margin-bottom: .5rem;
    /* margin-left: 1.2em; */
}

.list-group-item {
    background-color: #F3F3F3;
    border: none;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    padding-bottom:0;padding-top:0
}
.ticketsCount{
    margin-right:2rem;
}
.listStyle{
    padding: 0.5rem;
    padding-bottom:0;padding-top:0
}
.pad{
    padding-bottom:0;padding-top:0
}
</style>
