<template>
  <full-calendar 
   :events="fcEvents" 
   :locale="'ja'" 
   @changeMonth="changeMonth"
    >
  </full-calendar>
</template>
<script>
import { getAllSchedules } from '../../api/schedules';
// var demoEvents = this.dataSchedules;
export default {
    data() {
        return {
            // dataSchedules: [],
            fcEvents: [],
            year_months: new Date().toISOString().substr(0, 7)
        }
    },
    components: {
        'full-calendar': require('vue-fullcalendar')
    },
    created() {
        this.getAllSchedulesByDate();
    },
    methods: {
        async getAllSchedulesByDate() {
            let PARAMS = {
                year_month: this.year_months
            }
            await getAllSchedules(PARAMS)
            .then((response) => {
                if (response.code === 200) {
                    this.fcEvents = response.data;
                }
            }).catch((error) => {
                    this.fcEvents = [];
            });
        }, 
        'changeMonth'(start, end, current) {
            this.year_months = new Date(current).toISOString().substr(0, 7);
            this.getAllSchedulesByDate();
        },
        
    }

}
</script>