<template>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                  <div class="card-title">
                    {{ $t("Layout.dashboard") }}
                  </div>
                  <div class="card-tools">
                    <router-link to="/admin/calendar" class="nav-link text-secondary">
                      <i class="fas fa-calendar-alt"></i>    {{today_date}}
                    </router-link>
                  </div>
                </div>

                <div class="card-body">
                    <div class="row">
                      <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box text-white" style="background-color: #A9597D;">
                          <div class="inner">
                            <h3 v-if="tickets.unread">{{tickets.unread}}</h3>
                            <h3 v-else>0</h3>

                            <p>{{ $t("Ticket.newTickets") }}</p>
                          </div>
                          <div class="icon">
                            <i class="ion ion-bag"></i>
                          </div>
                          <router-link :to="{name: 'tickets.list', params: {filter: 'unread'}}" class="small-box-footer">
                            {{ $t("Ticket.moreinfo") }} <i class="fas fa-arrow-circle-right"></i>
                          </router-link>
                        </div>
                      </div>
                      <!-- ./col -->
                      <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box text-white" style="background-color: #E0D4BC">
                          <div class="inner">
                            <h3 v-if="tickets.unreplied">{{tickets.unreplied}}</h3>
                            <h3 v-else>0</h3>
                           <p>{{ $t("Ticket.UnRepliedTicket") }}</p>
                            
                          </div>
                          <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                          </div>
                          <router-link :to="{name: 'tickets.list', params: {filter: 'unreplied'}}" class="small-box-footer">
                              {{ $t("Ticket.moreinfo") }}<i class="fas fa-arrow-circle-right"></i>
                          </router-link>
                        </div>
                      </div>
                      <!-- ./col -->
                      <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box text-white" style="background-color: #706E70">
                          <div class="inner">
                            <h3 v-if="tickets.due_today">{{tickets.due_today}}</h3>
                            <h3 v-else>0</h3>
                           <p>{{ $t("Ticket.DueTodayTickets") }}</p>
                            
                          </div>
                          <div class="icon">
                            <i class="ion ion-person-add"></i>
                          </div>
                          <router-link :to="{name: 'tickets.list', params: {filter: 'due_today'}}" class="small-box-footer">
                              {{ $t("Ticket.moreinfo") }} <i class="fas fa-arrow-circle-right"></i>
                          </router-link>
                        </div>
                      </div>
                      <!-- ./col -->
                      <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box text-white" style="background-color: #6B5A62">
                          <div class="inner">
                            <h3 v-if="tickets.open">{{tickets.open}}</h3>
                            <h3 v-else>0</h3>
                           <p>{{ $t("Ticket.openTicket") }}</p>

                            
                          </div>
                          <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                          </div>
                          <router-link :to="{name: 'tickets.list', params: {filter: 'open'}}" class="small-box-footer">
                            {{ $t("Ticket.moreinfo") }} <i class="fas fa-arrow-circle-right"></i>
                          </router-link>
                        </div>
                      </div>
                      <!-- ./col -->
                    </div>
                    <!-- ./row -->
                    <div class="row">
                      <div class="col-md-6">
                        <div v-if="loadingDone" class="chart">
                            <TicketsPieChart :chart-data="datacollection" :height="250" :options="chartTicktOptions"></TicketsPieChart>
                        </div>
                      </div>
                      <!-- ./col -->
                      <div class="col-md-6">
                          <div v-if="loadingDone" class="chart">
                              <TicketsLineChart :chart-data="weekData" :height="250" :options="chartTicktOptions"></TicketsLineChart>
                          </div>
                      </div>
                      <!-- ./col -->
                    </div>
                    <!-- ./row -->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
import moment from "moment";
import TicketsPieChart from '../../components/Dashboard/Tickets/TicketPieChartComponent';
import TicketsLineChart from '../../components/Dashboard/Tickets/TicketLineChartComponent';
import tickets from "../../api/tickets";
export default {
  data() {
    return {
      loadingDone: false,
      chartTicktOptions: {
        responsive: true
      },
        pieChartData:[],
        lineChartData:[],
        lineChartDays:[],
    }
  },
  methods: {
      getUserById(id) {
          this.$Progress.start();
          this.$store
              .dispatch("user/getUserById", id)
              .then(response => {
                  this.$Progress.finish();
              })
              .catch(error => {
                  this.$Progress.fail();
              });
      },
      getTicketFiltered(){
        this.$store
          .dispatch("ticket/getTicketFiltered")
          .then(response => {
            this.loadingDone = true
              this.pieChartData.push(response.data.ticketStatus.open);
              this.pieChartData.push(response.data.ticketStatus.pending);
              this.pieChartData.push(response.data.ticketStatus.inProgress);
              this.pieChartData.push(response.data.ticketStatus.done);

              response.data.days.forEach(day =>{
                  this.lineChartDays.push(day);
              });

              response.data.weekData.forEach(data =>{
                  this.lineChartData.push(data);
              });
          })
          .catch(error => {
            console.log(error);
          });
      },
      getTickets(){
        this.$store
          .dispatch("ticket/getTickets")
          .then(response => {
            this.loadingDone = true
          })
          .catch(error => {
            console.log(error);
          });
      },
      getOwners() {
        this.$store
          .dispatch("owner/getOwners")
          .then(() => {})
          .catch(error => {
            console.log(error);
          });
      },
      getStatus() {
        this.$store
          .dispatch("ticket/getStatus")
          .then(response => {})
          .catch(error => {
            console.log(error);
          });
      },
      fillPieChart() {
        this.datacollection = {
          
          labels: ['Open','Pending','In-Progress','Done'],
          datasets: [
            {
              label: 'Tickets Status',
              backgroundColor: ['#A9597D' , '#E0D4BC', '#706E70', '#6B5A62'],
              data: this.pieChartData
            },
          ]
        };
      },
      fillLineChart() {
          this.weekData = {
              labels: this.lineChartDays,
              datasets: [
                  {
                    
                      label: 'Tickets Report',
                      pointBackgroundColor:['#985070','#985070','#985070','#985070','#985070','#985070','#985070' ],
                      backgroundColor: ['rgba(160, 70, 111,0.7)'],
                      data: this.lineChartData,
                      fill: true,
                      borderColor: 'rgba(160, 70, 111,0.0)',
                      lineTension: 0.2
                  },
              ]
          };
      },
  },
  computed: {
    ...mapGetters({
      tickets: "ticket/getFilterTicketsCount",
      allTickets: "ticket/activeTickets",
      user: "user/activeSingleUser"
    }),
    TicketsCount() {
      if(this.tickets.data) {
          return this.tickets.data.length;
      }
    },
    today_date() {
      return moment().format('MMMM Do YYYY, h:mm a');
    },
  },
  components: {
      TicketsPieChart,
      TicketsLineChart
  },
  mounted() {
    this.getTicketFiltered();
    this.getUserById(this.$userId);
    this.getOwners();
    this.getStatus();
    this.fillPieChart();
    this.fillLineChart();

  }
}
</script>

