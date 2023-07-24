<template>
  <div>
    <div class="container-fluid w-90">
      <div class="container-fluid-body">
        <div class="schedules-management-title">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div class="basic">
                <h1 class="title">Schedules</h1>
              </div>
            </div>
          </div>
        </div>
        <hr class="line-bottom">
      </div>
    </div>
    <div>
      <full-calendar
        :events="fcEvents"
        locale="en"
        @changeMonth="changeMonth"
        @eventClick="eventClick"
        @dayClick="dayClick"
        @moreClick="moreClick"
      />
    </div>
    <b-modal id="modal-center" v-model="modalShow" :title="date_click" centered :config="calendarConfig">
      <div v-for="item in one_day" :key="item.id">
        <div :class="item.title.includes('Remote') ? 'remote' : 'take-off'">{{ item.title }}</div>
      </div>
      <template #modal-footer>
        <div class="w-100">
          <b-button
            id="close-schedule"
            variant="outline-secondary"
            size="lg"
            class="float-left"
            @click="modalShow = false"
          >
            Close
          </b-button>
        </div>
      </template>
    </b-modal>
    <button class="button-chart" @click="showModalChat = true">Mở modal</button>
    <div v-if="showModalChat" class="modal-chart">
      <div class="modal-content">
        <span class="close" @click="showModalChat = false">&times;</span>
        <p>Nội dung modal</p>
      </div>
    </div>
  </div>
</template>
<script>
import { getAllSchedules } from '../../api/schedules';
export default {
  name: 'SchedulesManagement',
  components: {
    'full-calendar': require('vue-fullcalendar'),
  },
  data() {
    return {
      fcEvents: [],
      year_months: new Date().toISOString().substr(0, 7),
      modalShow: false,
      date_click: '',
      one_day: [],
      calendarConfig: {
        eventRender: function(fcEvents, element) {
          element.addClass(fcEvents.cssClass);
        },
      },
      showModalChat: false,
    };
  },
  created() {
    this.getAllSchedulesByDate();
  },
  methods: {
    async getAllSchedulesByDate() {
      const PARAMS = {
        year_month: this.year_months,
      };
      await getAllSchedules(PARAMS)
        .then((response) => {
          if (response.code === 200) {
            this.fcEvents = response.data;
          }
        }).catch(() => {
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
      if (this.year_months === formattedDate) {
        const filterDate = new Date(dayClick);
        const filteredArray = this.fcEvents.filter((obj) => {
          const objDate = new Date(obj.start);
          return objDate.getTime() === filterDate.getTime();
        });
        this.date_click = dayClick;
        this.one_day = filteredArray;
        if (this.one_day.length !== 0) {
          this.modalShow = true;
        }
      }
    },
    'eventClick'(event, jsEvent, pos) {
      this.month_click = new Date(event.start).toISOString().slice(0, 7);
      if (this.year_months === this.month_click) {
        const filterDate = new Date(event.start);
        const filteredArray = this.fcEvents.filter((obj) => {
          const objDate = new Date(obj.start);
          return objDate.getTime() === filterDate.getTime();
        });
        this.date_click = event.start;
        this.one_day = filteredArray;
        if (this.one_day.length !== 0) {
          this.modalShow = true;
        }
      }
    },
    'moreClick'(day, events, jsEvent) {
      this.modalShow = true;
      return;
    },
  },
};
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
  padding-left: 3rem !important;
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
.line-bottom {
  margin-left: 2rem !important;
}
.card-body {
  padding-top: 2rem !important;
  padding-left: 2.1rem !important;
}
::v-deep .comp-full-calendar * {
    box-sizing: unset !important;
}
.modal-chart{
  position: fixed;
  bottom: 0;
  right: 0;
  width: 300px;
  height: 200px;
  background-color: white;
  border: 1px solid black;
  z-index: 9999;
}
.button-chart {
  position: fixed;
  bottom: 0;
  left: 0;
}
</style>
