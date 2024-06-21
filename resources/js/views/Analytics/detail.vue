<template>
  <div>
    <div class="container-fluid w-90">
      <div class="container-fluid-body mt-5 mb-5">
        <div class="use-management-title">
          <div class="card-body p-5">
            <div class="d-flex justify-content-between align-items-center">
              <div class="basic">
                <h1 class="title">Analytics</h1>
              </div>
            </div>
            <hr class="line">
            <p
              class="back-list cursor-pointer all-analytics"
              @click="listAllAnalytic()"
            >
              <i class="el-icon-arrow-left icon-back-list" /> All Analytics
            </p>
            <div class="use-management-title-table mt-3">
              <div class="employee-name">
                <div>
                  <strong>Employee name</strong>
                  <div>{{ nameEmployee }}</div>
                </div>
                <div>
                  <strong>Paid off ({{ getDateToday() }})</strong>
                  <div :style="{ color: paidOffRemain < 0 ? 'red' : '' }">
                    {{ paidOffRemain }}
                  </div>
                </div>
              </div>
              <div class="fill mt-5">
                <h1 class="titel-emotion">Emotion Statistics</h1>
                <div
                  class="d-flex justify-content-end align-items-center all-date"
                >
                  <p class="back-list cursor-pointer" @click="getAllEmmotions">
                    All date
                    <i class="el-icon-arrow-down icon-back-list ml-1" />
                  </p>
                  <p class="back-list cursor-pointer">
                    <i
                      class="el-icon-download custom-icon-down cursor-pointer ml-3"
                      @click="exportDataEmotions"
                    />
                  </p>
                </div>
              </div>
              <hr class="line">
              <div class="chart">
                <h1>highcharts</h1>
                <highcharts
                  ref="chart"
                  class="hc"
                  :options="chartOptionsConfig"
                />
              </div>

              <div>
                <el-table :data="emmotionStatistics" style="width: 100%">
                  <el-table-column
                    prop="time"
                    label="Date"
                    width="350"
                    align="center"
                    :formatter="formatDate"
                  />
                  <el-table-column
                    prop="happy"
                    label="Happy"
                    width="250"
                    align="center"
                  />
                  <el-table-column prop="sad" label="Sad" align="center" />
                  <el-table-column prop="angry" label="Angry" align="center" />
                  <el-table-column
                    prop="confused"
                    label="Confused"
                    align="center"
                  />
                  <el-table-column
                    prop="disgusted"
                    label="Disgusted"
                    align="center"
                  />
                  <el-table-column
                    prop="surprised"
                    label="Surprised"
                    align="center"
                  />
                  <el-table-column prop="calm" label="Calm" align="center" />
                  <el-table-column prop="fear" label="Fear" align="center" />
                </el-table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { getEmotions } from '../../api/analytic';
import axios from 'axios';
import { getToken } from '../../utils/getToken';
import moment from 'moment';
import Highcharts from 'highcharts';
import exportingInit from 'highcharts/modules/exporting';
exportingInit(Highcharts);

export default {
  name: 'AnalyticsManagement',
  data() {
    return {
      emmotionStatistics: [],
      nameEmployee: '',
      search: '',
      paidOffRemain: '',
      pagination: {
        // current_page: 1,
        // per_page: 20,
        total_records: 0,
        isDisable: false,
      },

      chartOptionsConfig: {
        chart: {
          type: 'line',
        },
        title: {
          text: 'Temperature Example',
          align: 'left',
        },

        subtitle: {
          text: 'Irregular time data',
          align: 'left',
        },

        xAxis: {
          type: 'datetime',
          dateTimeLabelFormats: {
            day: '%Y<br/>%m-%d',
            month: '%Y-%m',
            year: '%Y',
          },
          labels: {
            format: '{value:%Y-%m-%d}',
            rotation: 90,
            align: 'left',
          },
        },

        yAxis: {
          title: {
            text: 'Temperature (°C)',
          },
          min: 0,
          max: 100,
        },

        legend: {
          layout: 'vertical',
          align: 'right',
          verticalAlign: 'middle',
        },

        plotOptions: {
          series: {
            marker: {
              enabled: true,
            },
          },
        },

        series: [{
          name: 'Happy',
          data: [
            // [Date.UTC(2020, 10, 23), 19.83],
            // [Date.UTC(2020, 10, 24), 11.02],
            // [Date.UTC(2020, 10, 25), 27.21],
            // [Date.UTC(2020, 10, 26), 27.21],
          ],
        },
        {
          name: 'Sad',
          data: [],
        },
        {
          name: 'Angry',
          data: [],
        },
        {
          name: 'Confused',
          data: [],
        },
        {
          name: 'Disgusted',
          data: [],
        },
        {
          name: 'Surprised',
          data: [],
        },
        {
          name: 'Calm',
          data: [],
        },
        {
          name: 'Fear',
          data: [],
        },
        ],

        responsive: {
          rules: [{
            condition: {
              maxWidth: 1000,
            },
            chartOptions: {
              plotOptions: {
                series: {
                  marker: {
                    radius: 2.5,
                  },
                },
              },
            },
          }],
        },
      },
    };
  },

  watch: {
    emmotionStatistics(newValue){
      if (newValue){
        // thực hiện custome lại các mảng
        this.handleGetHappyEmotions(newValue);
        this.handleGetSadEmotions(newValue);
        this.handleGetAngryEmotions(newValue);
        this.handleGetConfusedEmotions(newValue);
        this.handleGetDisgustedEmotions(newValue);
        this.handleGetSurprisedEmotions(newValue);
        this.handleGetCalmEmotions(newValue);
        this.handleGetFearEmotions(newValue);
      }
    },
  },
  created() {
    this.getEmmotionStatistics();
    // this.getUser();
  },

  methods: {
    handleGetHappyEmotions(newValue){
      const happyArray = newValue.map(item => {
        const [year, month, day] = moment(item.time).format('YYYY-MM-DD').split('-').map(Number);
        const happyValue = item.happy;
        return [Date.UTC(year, month - 1, day), happyValue]; // Tạo mảng mới với Date.UTC và giá trị happy
      });
      this.chartOptionsConfig['series'][0]['data'] = happyArray.sort((a, b) => a[0] - b[0]);
    },
    handleGetSadEmotions(newValue){
      const happyArray = newValue.map(item => {
        const [year, month, day] = moment(item.time).format('YYYY-MM-DD').split('-').map(Number);
        const happyValue = item.sad;
        return [Date.UTC(year, month - 1, day), happyValue]; // Tạo mảng mới với Date.UTC và giá trị happy
      });
      this.chartOptionsConfig['series'][1]['data'] = happyArray.sort((a, b) => a[0] - b[0]);
    },
    handleGetAngryEmotions(newValue){
      const happyArray = newValue.map(item => {
        const [year, month, day] = moment(item.time).format('YYYY-MM-DD').split('-').map(Number);
        const happyValue = item.angry;
        return [Date.UTC(year, month - 1, day), happyValue]; // Tạo mảng mới với Date.UTC và giá trị happy
      });
      this.chartOptionsConfig['series'][2]['data'] = happyArray.sort((a, b) => a[0] - b[0]);
    },
    handleGetConfusedEmotions(newValue){
      const happyArray = newValue.map(item => {
        const [year, month, day] = moment(item.time).format('YYYY-MM-DD').split('-').map(Number);
        const happyValue = item.confused;
        return [Date.UTC(year, month - 1, day), happyValue]; // Tạo mảng mới với Date.UTC và giá trị happy
      });
      this.chartOptionsConfig['series'][3]['data'] = happyArray.sort((a, b) => a[0] - b[0]);
    },
    handleGetDisgustedEmotions(newValue){
      const happyArray = newValue.map(item => {
        const [year, month, day] = moment(item.time).format('YYYY-MM-DD').split('-').map(Number);
        const happyValue = item.disgusted;
        return [Date.UTC(year, month - 1, day), happyValue]; // Tạo mảng mới với Date.UTC và giá trị happy
      });
      this.chartOptionsConfig['series'][4]['data'] = happyArray.sort((a, b) => a[0] - b[0]);
    },
    handleGetSurprisedEmotions(newValue){
      const happyArray = newValue.map(item => {
        const [year, month, day] = moment(item.time).format('YYYY-MM-DD').split('-').map(Number);
        const happyValue = item.surprised;
        return [Date.UTC(year, month - 1, day), happyValue]; // Tạo mảng mới với Date.UTC và giá trị happy
      });
      this.chartOptionsConfig['series'][5]['data'] = happyArray.sort((a, b) => a[0] - b[0]);
    },
    handleGetCalmEmotions(newValue){
      const happyArray = newValue.map(item => {
        const [year, month, day] = moment(item.time).format('YYYY-MM-DD').split('-').map(Number);
        const happyValue = item.calm;
        return [Date.UTC(year, month - 1, day), happyValue]; // Tạo mảng mới với Date.UTC và giá trị happy
      });
      this.chartOptionsConfig['series'][6]['data'] = happyArray.sort((a, b) => a[0] - b[0]);
    },
    handleGetFearEmotions(newValue){
      const happyArray = newValue.map(item => {
        const [year, month, day] = moment(item.time).format('YYYY-MM-DD').split('-').map(Number);
        const happyValue = item.fear;
        return [Date.UTC(year, month - 1, day), happyValue]; // Tạo mảng mới với Date.UTC và giá trị happy
      });
      this.chartOptionsConfig['series'][7]['data'] = happyArray.sort((a, b) => a[0] - b[0]);
    },
    async getEmmotionStatistics() {
      const PARAMS = {
        user_id: this.$route.params.id,
        search: this.search,
        // per_page: this.pagination.per_page,
        // page: this.pagination.current_page,
      };
      await getEmotions(PARAMS)
        .then((response) => {
          if (response.code === 200) {
            this.emmotionStatistics = response.data.emotions;
            this.nameEmployee = response.data.user['name'];
            this.paidOffRemain = response.data.user['paid_off'];
          }
        })
        .catch(() => {
          this.getEmmotionStatistics = [];
        });
    },
    // async getUser() {
    //   const id = this.$route.params.id;
    //   await UserApi.getOneUser(id)
    //     .then((response) => {
    //       this.nameEmployee = response.data.name;
    //       this.paidOffRemain = response.data.paid_off;
    //     })
    //     .catch(() => {
    //       let userInfo = Cookies.get('userInfo');
    //       userInfo = JSON.parse(userInfo);
    //       this.nameEmployee = userInfo.role_id === 2 ? userInfo.name : '';
    //     });
    // },
    formatDate(row, column) {
      const date = new Date(row.time);
      const year = date.getFullYear();
      const month = date.getMonth() + 1;
      const day = date.getDate();
      return `${year}-${month.toString().padStart(2, '0')}-${day
        .toString()
        .padStart(2, '0')}`;
    },
    getAllEmmotions() {
      this.search = 'all';
      this.getEmmotionStatistics();
    },
    listAllAnalytic() {
      this.$router.push({ path: `/analytics/index` });
    },
    async exportDataEmotions() {
      const URL =
        '/api/analytic/export-emotions?user_id=' + this.$route.params.id;
      const token = getToken();
      axios
        .get(URL, {
          responseType: 'blob',
          headers: {
            Authorization: token,
          },
        })
        .then((response) => {
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement('a');
          link.href = url;
          const fileName = 'emotions.xlsx';
          link.setAttribute('download', fileName);
          document.body.appendChild(link);
          link.click();
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getDateToday() {
      return moment().format('YYYY-MM-DD');
    },
  },
};
</script>
<style scoped>
@import "../../../sass/config.scss";
.title {
  font-style: normal;
  font-weight: 600;
  font-size: 40px;
  color: #000000;
  margin: 0;
}
.line-bottom {
  width: 95%;
  height: 1px;
  color: rgba(63, 63, 63, 0.4);
  margin: 0 auto;
}
.el-icon-download {
  color: #0070c9;
  font-size: 21px;
  font-weight: 600;
}
.all-date {
  cursor: pointer;
  color: #0070c9;
  font-size: 16px;
  font-weight: 500;
}
.el-icon-arrow-left {
  color: #0070c9;
  font-size: 23px;
  font-weight: 600;
}
.el-icon-arrow-down {
  color: #0070c9;
  font-size: 19px;
  font-weight: 600;
}
.employee-name {
  font-size: 20px;
  display: flex;
  gap: 3rem;
}
.all-analytics {
  cursor: pointer;
  color: #0070c9;
  font-size: 23px;
  font-weight: 400;
}
.titel-emotion {
  font-weight: 600;
  font-size: 35px;
}

.highcharts-figure, .highcharts-data-table table {
    min-width: 310px;
    max-width: 800px;
    margin: 1em auto;
}

.highcharts-data-table table {
	font-family: Verdana, sans-serif;
	border-collapse: collapse;
	border: 1px solid #EBEBEB;
	margin: 10px auto;
	text-align: center;
	width: 100%;
	max-width: 500px;
}
.highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
}
.highcharts-data-table th {
	font-weight: 600;
    padding: 0.5em;
}
.highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
    padding: 0.5em;
}
.highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
}
.highcharts-data-table tr:hover {
    background: #f1f7ff;
}
</style>
