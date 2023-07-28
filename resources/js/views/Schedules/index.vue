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
    <a class="button-chart" @click="showModalChat = true"><img class="custom-image" :src="logo" alt="V-FACE" @click="generateRandomNumber"></a>
    <div v-if="showModalChat" class="modal-chart">
      <div class="modal-header chart-header">
        <strong>V-Face x GPT</strong>
        <button class="close close-chart" @click="showModalChat = false"><span>&ndash;</span></button>
      </div>
      <div class="modal-content chart-content">
        <div class="content-right">
          <p>Is {{ randomElement }}</p>
          <div><img class="custom-image" :src="logoImage" alt="V-FACE"></div>
        </div>
        <div class="content-left">
          <div><img class="custom-image" :src="logoImage" alt="V-FACE"></div>
          <p>Who comes to work late this month?</p>
        </div>
      </div>
      <div class="modal-footer chart-footer">
        <input type="text" class="chart-input">
        <button class="chart-submit">Send</button>
      </div>
    </div>
  </div>
</template>
<script>
import { getAllSchedules, exportSchedules } from '../../api/schedules';
const logo = require('@/assets/images/chatgpt-icon.png');
const logoImage = require('@/assets/images/logo.png');
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
      logo,
      logoImage,
      randomNumber: null,
      myArray: [
        'i.kohei',
        'Phạm Thị Trang',
        'Ly Văn Phương',
      ],
    };
  },
  created() {
    this.getAllSchedulesByDate();
  },
  methods: {
    generateRandomNumber() {
      const randomIndex = Math.floor(Math.random() * this.myArray.length);
      this.randomElement = this.myArray[randomIndex];
    },
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
      const month_click = new Date(event.start).toISOString().slice(0, 7);
      if (this.year_months === month_click) {
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
      return;
    }, async exportDataSchedules() {
      const PARAMS = {
        year_month: this.year_months,
      };
      await exportSchedules(PARAMS)
        .then((response) => {
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement('a');
          link.href = url;
          const fileName = 'Schedule' + this.year_month + '.xlsx';
          link.setAttribute('download', fileName);
          document.body.appendChild(link);
          link.click();
        }).catch(() => {
          this.fcEvents = [];
        });
    },
  },
};
</script>

<style scoped>
#close-schedule {
  color: #70afe1;
  padding: 0 40px 0 40px;
  border-color: #70afe1 !important;
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

.modal-chart {
  position: fixed;
  bottom: 10%;
  right: 2%;
  width: 25%;
  height: 45%;
  background-color: white;
  border: 1px solid rgba(0, 0, 0, 0.2);
  border-top-left-radius: calc(0.3rem - 1px) !important;
  border-top-right-radius: calc(0.3rem - 1px);
  z-index: 9999;
}

.button-chart {
  position: fixed;
  bottom: 10%;
  right: 2%;
  z-index: 9998;
  cursor: pointer;
}

::v-deep .more-events {
  display: none;
}

.chart-content {
  height: 72% !important;
  border: none !important;
}

.chart-input {
  width: 80%;
  border-radius: 6px;
  border: none;
  padding: 3px;
}

.chart-input:focus {
  border: none;
}

.chart-header {
  background-color: #0070c9;
  color: white;
  position: relative;
}

.chart-header strong {
  position: inherit;
  left: 40%;
}

.close-chart {
  background-color: white;
  opacity: initial;
  padding: revert;
  margin: inherit;
  border-radius: 6px;
}

.custom-image {
  width: 55px;
}

.chart-footer {
  background-color: #E6E6E6;
  flex-wrap: nowrap;
}

.chart-submit {
  background-color: #40729A;
  color: white;
  border-radius: 10%;
  border: none;
  padding: 3px;
  width: 16%;
}

.content-right {
  text-align: right;
  display: flex;
  align-self: flex-end;
}

.content-right p {
  margin: auto;
}

.content-left {
  align-self: initial;
  text-align: left;
  display: flex;
  max-width: max-content;
}

.content-left p {
  margin: auto;
}

.modal-content {
  flex-direction: column-reverse !important;
}
</style>
