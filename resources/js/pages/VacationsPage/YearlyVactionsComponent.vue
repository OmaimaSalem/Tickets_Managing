<template>
    <div>
         <label>{{$t('Vacation.year')}} <date-picker
                v-model="year"
                lang="en"
                type="year"
                format="YYYY"
                :minute-step="1"
                input-class="form-control"
                value-type="format"
                @change="getYearData"
        >
        </date-picker></label>
        <br />
        <label>{{$t('Vacation.annual')}} <input type="text" v-model="vacation.annual" /></label>
        <label>{{$t('Vacation.casual')}} <input type="text" v-model="vacation.casual" /></label>
        <br />
        <button @click="setYearData(vacation)">{{$t('Vacation.save')}}</button>
        <hr />
<table>
  <thead>
    <tr>
      <th>{{$t('Vacation.day')}}</th>
      <th>{{$t('Vacation.vacation')}}</th>
    </tr>
  </thead>
  <tbody>

    <tr v-for="(day,index) in days" :key="index">
      <td><label :for="'day['+day.name+']'">{{ day.name }}</label></td>
      <td><input type="checkbox" :id="'day['+day.name+']'" v-model="day.on" /> </td>
    </tr>

  </tbody>
  <tfoot>
    <tr>
      <th>{{$t('Vacation.day')}}</th>
      <th>{{$t('Vacation.vacation')}}</th>
    </tr>
  </tfoot>
</table>
        <button @click="setWeekVacations()">{{$t('Vacation.save')}}</button>
    </div>
</template>



<script>
import DatePicker from "vue2-datepicker";
import { mapGetters } from "vuex";

export default {
  components: { DatePicker },
    data() {
        return {
            year: new Date().getFullYear().toString(),
            days : [
                {
                    name : 'Monday',
                    on   : false,
                },
                {
                    name : 'Tuesday',
                    on   : false,
                },
                {
                    name : 'Wednesday',
                    on   : false,
                },
                {
                    name : 'Thursday',
                    on   : false,
                },
                {
                    name : 'Friday',
                    on   : false,
                },
                {
                    name : 'Saturday',
                    on   : false,
                },
                {
                    name : 'Sunday',
                    on   : false,
                },
            ]
        }
    },
    methods: {
        getYearData() {
            this.$Progress.start();
            this.$store.dispatch("vacation/getYearData", { 'year' : this.year})
            .then(response => {
                this.$Progress.finish();
            }).catch(error => {
                this.$Progress.fail();
            })
        },
        getWeekVacations() {
            this.$Progress.start();
            this.$store.dispatch("vacation/getWeekVacations", { 'year' : this.year})
            .then(response => {
                this.days.forEach(element => {
                    if(this.weekVacations.indexOf(element.name) === -1){
                        element.on = false;
                    }else {
                        element.on = true;
                    }
                });

            }).catch(error => {
                this.$Progress.fail();
            })
        },
        setYearData(vacation) {
            this.$Progress.start();
            vacation.year = this.year;
            this.$store.dispatch("vacation/setYearData", { vacation })
            .then(response => {
                this.$Progress.finish();
            }).catch(error => {
                this.$Progress.fail();
            })
        },
        setWeekVacations() {
            let vacations = this.days.filter(item => item.on).map(item => item.name);
            this.$Progress.start();
            this.$store.dispatch("vacation/setWeekVacations", { vacations: vacations, year: this.year })
            .then(response => {
                this.$Progress.finish();
            }).catch(error => {
                this.$Progress.fail();
            })
        }

    },
    computed: {
    ...mapGetters({
      vacation: "vacation/getYear",
      weekVacations: "vacation/getWeekVacations",
    }),
    },
    mounted(){
       this.getYearData()
       this.getWeekVacations()
    },
}
</script>
