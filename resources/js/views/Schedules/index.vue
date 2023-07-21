<template>
  <div>
    <div>
      <full-calendar 
        :events="fcEvents" 
        locale="en"
        @changeMonth="changeMonth"
        @eventClick="eventClick"
        @dayClick="dayClick">
      </full-calendar>
    </div>
    <b-modal v-model="modalShow" :title="this.date_click" centered id="modal-center" :config="calendarConfig">
      <div v-for="item in this.one_day">
        <div :class="item.title.includes('Remote') ? 'remote' : 'take-off'" >{{item.title}}</div>
      </div>
      <template #modal-footer>
        <div class="w-100">
          <b-button
            id="close-schedule"
            variant="outline-secondary"
            size="lg"
            class="float-left"
            @click="modalShow = false">
            Close
          </b-button>
        </div>
      </template>
    </b-modal>
  </div>
</template>
<script>
import { getAllSchedules } from '../../api/schedules';
export default {
    name: 'SchedulesManagement',
    data() {
      return {
        fcEvents: [],
        year_months: new Date().toISOString().substr(0, 7),
        modalShow: false,
        date_click: '',
        one_day: [], 
        calendarConfig: {
        eventRender: function (fcEvents, element) {
          element.addClass(fcEvents.cssClass);
        }
      },
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
        'dayClick'(day, jsEvent) {
          const date = new Date(day);
          const year = date.getFullYear();
          const month = String(date.getMonth() + 1).padStart(2, '0');
          const days = String(date.getDate()).padStart(2, '0');
          const dayClick = `${year}-${month}-${days}`;
          const formattedDate = `${year}-${month}`;
            if(this.year_months == formattedDate) {
              this.modalShow = true;
              const filterDate = new Date(dayClick);
              const filteredArray = this.fcEvents.filter((obj) => {
                  const objDate = new Date(obj.start);
                  return objDate.getTime() === filterDate.getTime();
              });
              this.date_click = dayClick;
              this.one_day = filteredArray;
            }
        },
        'eventClick'(event, jsEvent, pos) {
          this.month_click = new Date(event.start).toISOString().slice(0, 7);
            if (this.year_months == this.month_click) { 
              this.modalShow = true;
              const filterDate = new Date(event.start);
              const filteredArray = this.fcEvents.filter((obj) => {
                  const objDate = new Date(obj.start);
                  return objDate.getTime() === filterDate.getTime();
              });
              this.date_click = event.start;
              this.one_day = filteredArray;
            }
        }
    }
}
</script>

<style scoped>
#close-schedule {
  color: #70afe1;
  padding: 0 40px 0 40px;
  border-color:#70afe1 !important;
}

#close-schedule:hover {
 background-color: white;
}
.remote {
  background-color: #C7E6FD;
  margin-bottom: 10px;
  padding-left: 10px;
  width: 50%;
  border-radius: 10px;
}
.take-off {
  background-color: antiquewhite;
  margin-bottom: 10px;
  padding-left: 10px;
  width: 50%;
  border-radius: 10px;
}
.comp-full-calendar {
  max-width: none !important;
}
::v-deep .work-remote {
  background-color: #C7E6FD !important;
  width: 70%;
  border-radius: 6px;
}
::v-deep .titelOff {
  background-color: antiquewhite !important;
  width: 70%;
  border-radius: 6px;
}
</style>