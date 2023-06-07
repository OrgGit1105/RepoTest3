<template>
  <div class="container-fluid">
    <div class="container-fluid-body mt-5 mb-5">
      <div class="row">
        <div class="col-md-12 py-5">
          <div>
            <h1 class="pl-3 title-info"> {{ $t('LANGUAGES.TEXT_ENROLLMENT_PERIOD_GRAPH') }}</h1>
            <div class="d-flex flex-column time mb-3">
              <div class="d-flex justify-content-center pb-4">
                <!-- <h4>12 month 2020 year</h4> -->
              </div>
              <div class="d-flex justify-content-center date-time">
                <button class="d-flex align-items-center pr-3 fs-20 btn-select" @click="backTime()">
                  <b-icon id="btn-prev" class="btn-prev" icon="arrow-left" />
                </button>
                <div class="select-time">
                  <span>{{ monthYear.year }}年</span>
                  <span>{{ monthYear.month }}月</span>
                </div>
                <button class="d-flex align-items-center pl-3 fs-20 btn-select" @click="nextTime()">
                  <b-icon id="btn-next" class="btn-next" icon="arrow-right" />
                </button>
              </div>
            </div>
          </div>
          <div v-if="isNoData" id="chart" class="chart">
            <apexchart
              overflow-x="scoll"
              type="bar"
              :options="chartOptions"
              :series="series"
              dusk="chart"
            />
          </div>
          <div v-if="!isNoData">
            <h1 class="d-flex justify-content-center mt-3">{{ $t('LANGUAGES.TEXT_NO_DATA') }} </h1>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import * as RetirementApi from '../../api/retirement';
import { MakeToast } from '../../utils/toast_message';
import * as CONFIGS from '../../configs/index';
import moment from 'moment';
const urlAPI = {
  urlRetirement: `/retirement_prediction_chart`,
};
export default {
  name: 'Retirement',
  data() {
    return {
      monthYear: {
        month: '',
        year: '',
      },
      isNoData: false,
      maxDefaultMonth: '',
      maxDefaultYear: '',
      minDefaultMonth: '',
      minDefaultYear: '',
      // isDisable: true,
      headQuarter: CONFIGS.UserRoleId.HEAD_QUARTER,
      series: [{
        name: this.$t('LANGUAGES.TEXT_NUMBER_OF_RETIREMENT_PREDICTION_EMPLOYEES'),
        data: [],
        colors: '#3189BB',
      }, {
        name: this.$t('LANGUAGES.TEXT_RETIREMENT_FORECAST_RATIO'),
        data: [],
        colors: '#FB9A09',
      }],
      chartOptions: {
        chart: {
          type: 'bar',
          height: 800,
          toolbar: {
            show: true,
          },
          zoom: {
            enabled: true,
          },
        },
        plotOptions: {
          bar: {
            horizontal: false,
            barHeight: '70%',
            columnWidth: '80%',
            endingShape: 'rounded',
          },
          column: {
            pointPadding: 0,
            borderWidth: 0,
          },
        },
        dataLabels: {
          enabled: false,
        },
        colors: ['#3189BB', '#FB9A09'],
        stroke: {
          show: true,
          // width: 10,
          colors: ['transparent'],
        },
        grid: {
          row: {
            opacity: 0.5,
            width: '1100px',
            overflowX: scroll,
          },
          padding: {
            left: 50, // or whatever value that works
            right: 30, // or whatever value that works
          },
          margin: {
            left: 10,
            right: 40,
          },
        },
        xaxis: {
          categories: [],
        },
        fill: {
          opacity: 1,
          colors: ['#3189BB', '#FB9A09'],
        },
        markers: {
          colors: ['#3189BB', '#FB9A09'],
        },
        legend: {
          show: true,
          labels: {
            colors: ['#3189BB', '#FB9A09'],
          },
        },
        yaxis: [
          {
            axisTicks: {
              show: true,
            },
            axisBorder: {
              show: true,
              color: '#3189BB',
            },
            labels: {
              show: true,
              style: {
                color: '#3189BB',
              },
              formatter: function(val, index) {
                return val.toFixed(0);
              },
            },
            title: {
              text: '(人)',
              rotate: 360,
              style: {
                color: '#3189BB',
              },

            },
          },
          {
            min: 0,
            max: 100,
            tickAmount: 5,
            decimalsInFloat: 2,
            seriesName: this.$t('LANGUAGES.TEXT_RETIREMENT_FORECAST_RATIO'),
            opposite: true,
            axisTicks: {
              show: true,
            },
            axisBorder: {
              show: true,
              color: '#FB9A09',
            },
            labels: {
              style: {
                color: '#FB9A09',
              },
              formatter: function(val, index) {
                return parseFloat(val).toFixed(2);
              },
            },
            title: {
              text: '(%)',
              rotate: 180,
              style: {
                color: '#FB9A09',
              },
            },
          },
        ],
        tooltip: {
          y: {
            formatter: function(val, { seriesIndex }) {
              if (seriesIndex === 0) {
                return val + ' 人';
              } else {
                return parseFloat(val).toFixed(2) + ' %';
              }
            },
          },
        },
        noData: {
          text: 'No Data',
          align: 'center',
          verticalAlign: 'middle',
          offsetX: 0,
          offsetY: 0,
          style: {
            color: undefined,
            fontSize: '14px',
            fontFamily: undefined,
          },
        },
      },
    };
  },
  computed: {
    listRetirement() {
      return this.$store.getters.listRetirement;
    },
    roleId() {
      return this.$store.getters.role_id;
    },
    getMonthYear() {
      return this.$store.getters.monthYear;
    },
  },
  watch: {
    listRetirement() {
      console.log('Da vao day', this.listRetirement);
      if (this.listRetirement && this.listRetirement.the_blue.length > 0 && this.listRetirement.the_orange.length > 0){
        const retirementList = this.listRetirement;
        // console.log('retirementList', this.listRetirement);
        const columnBlue = retirementList.the_blue;
        const columnOrange = retirementList.the_orange;
        columnBlue && columnBlue.sort((a, b) => {
          a = a.position;
          b = b.position;
          return a - b;
        });
        columnOrange && columnOrange.sort((a, b) => {
          a = a.position;
          b = b.position;
          return a - b;
        });
        // console.log('1', columnBlue);
        // console.log('2', columnOrange);

        const listPointBlue = [];
        const listPointOrg = [];
        const listBranchName = [];
        if (columnBlue.length > 0) {
        //   // this.charWidth = columnBlue.length * 80 + (columnBlue.length - 1) * 30;
          for (let i = 0; i < columnBlue.length; i++) {
            // columnBlue[i].numberofemployee = columnBlue[i].numberofemployee.toFixed(0);
            listPointBlue.push(Number(columnBlue[i].numberofemployee));
            listBranchName.push(columnBlue[i].name);
          }
        }
        const newOption = { ...this.chartOptions };
        newOption.xaxis.categories = listBranchName;
        if (this.roleId !== this.headQuarter) {
          newOption.plotOptions.bar.columnWidth = '22%';
          newOption.stroke.width = 10;
          newOption.chart.height = 700;
        }
        this.chartOptions = newOption;
        // console.log('listBranchName', listBranchName);
        if (columnOrange.length > 0) {
          for (let i = 0; i < columnOrange.length; i++) {
            // columnOrange[i].branchname_value = columnOrange[i].branchname_value.toFixed(0);
            listPointOrg.push(Number(columnOrange[i].branchname_value));
          }
        }
        // console.log('list blue', listPointBlue);
        // console.log('list org', listPointOrg);
        this.series = [{
          name: this.$t('LANGUAGES.TEXT_NUMBER_OF_RETIREMENT_PREDICTION_EMPLOYEES'),
          data: listPointBlue,
          colors: '#3189BB',
        }, { name: this.$t('LANGUAGES.TEXT_RETIREMENT_FORECAST_RATIO'),
          data: listPointOrg,
          colors: '#FB9A09' }];
        this.isNoData = true;
      } else {
        this.isNoData = false;
        this.series = [{
          data: [],
        }];
        this.chartOptions = {};
      }
    },
    getMonthYear() {
      // console.log('Manh', this.getMonthYear);
      this.monthYear.month = 1 + moment(this.getMonthYear.maxTime, 'YYYY/MM/DD').month();
      this.monthYear.year = moment(this.getMonthYear.maxTime, 'YYYY/MM/DD').year();
    },
  },
  async created() {
    await this.getMonthAndYear();
    await this.getListAllRetirement();
  },
  methods: {
    async getListAllRetirement() {
      this.openLoading();
      // Fix month 1 because wait Api update
      await RetirementApi.getAllRetirement({ url: urlAPI.urlRetirement, query: { month: this.monthYear.month, year: this.monthYear.year }})
        .then((response) => {
          if (response.code === 200) {
            // console.log(response.data);
            const listRetirement = response.data;
            this.$store.dispatch('app/saveListRetirement', listRetirement);
            this.closeLoading();
          }
          if (response.code === 201) {
            this.closeLoading();
            MakeToast({
              variant: 'warning',
              title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
              content: response.message,
            });
          }
        })
        .catch((error) => {
          this.closeLoading();
          MakeToast({
            variant: 'warning',
            title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
            content: error.message,
          });
        });
    },
    async getMonthAndYear() {
      this.openLoading();
      await RetirementApi.getMonthYear()
        .then((response) => {
          if (response.code === 200 && response.data) {
            this.maxDefaultMonth = 1 + moment(response.data.max_month_year, 'YYYY/MM/DD').month();
            this.maxDefaultYear = moment(response.data.max_month_year, 'YYYY/MM/DD').year();
            this.minDefaultMonth = 1 + moment(response.data.min_month_year, 'YYYY/MM/DD').month();
            this.minDefaultYear = moment(response.data.min_month_year, 'YYYY/MM/DD').year();
            const maxTime = response.data.max_month_year ? moment(response.data.max_month_year).format(
              'YYYY/MM/DD'
            ) : '';
            const minTime = response.data.min_month_year ? moment(response.data.min_month_year).format(
              'YYYY/MM/DD'
            ) : '';
            this.$store.dispatch('app/saveMonthYear', { maxTime, minTime });
            this.closeLoading();
          }
        })
        .catch((error) => {
          this.closeLoading();
          MakeToast({
            variant: 'warning',
            title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
            content: error.message,
          });
        });
    },
    openLoading() {
      this.$store.dispatch('loading/setLoading', true);
    },
    closeLoading() {
      this.$store.dispatch('loading/setLoading', false);
    },
    nextTime() {
      const currentMonth = this.monthYear.month;
      const currentYear = this.monthYear.year;
      // console.log(3, currentYear);
      const nextMonth = currentMonth === 12 ? 1 : currentMonth + 1;
      // console.log(this.maxDefaultMonth);
      if (nextMonth > this.maxDefaultMonth && currentYear === this.maxDefaultYear) {
        return;
      } else {
        const nextYear = nextMonth === 1 ? currentYear + 1 : currentYear;
        this.monthYear = { year: nextYear, month: nextMonth };
        this.getListAllRetirement();
      }
    },
    backTime() {
      const currentMonth = this.monthYear.month;
      const currentYear = this.monthYear.year;
      const previosMonth = currentMonth === 1 ? 12 : currentMonth - 1;
      if (previosMonth < this.minDefaultMonth && currentYear === this.minDefaultYear) {
        return;
      } else {
        const previosYear = previosMonth === 12 ? currentYear - 1 : currentYear;
        this.monthYear = { month: previosMonth, year: previosYear };
        this.getListAllRetirement();
      }
    },

  },
};
</script>

<style scoped>
  .container-fluid {
    width: 80%;
  }
 .title-info {
    border-left: 9px solid #fb9a09;
    color: #3189bb;
    text-transform: uppercase;
    font-size: 25px;
  }
  .time {
    padding: 20px;
  }
  .select-time {
     padding: 5px 10px 5px 10px;
    font-weight: 400;
    font-size: 19px;
    margin-bottom: 0px;

  }
  .date-time {
    align-items: center;
    box-shadow: 0 3px 6px rgb(0 0 0 / 16%), 0 3px 6px rgb(0 0 0 / 23%);
    border-radius: 3px;
    margin: auto;
  }
  .btn-select {
    padding: 0.7rem;
    width: 3rem;
    font-size: 1.2rem;
    background: #f3f3f3;
    color: #888;
    border: 0 solid #dbdbdb;
    text-align: center;
    text-shadow: 0 1px 0 rgb(255 255 255 / 60%);
    cursor: pointer;
  }
  .btn-select:hover {
    background: #dadada;
    color: #555555;
    transition: 0.3s ease-in-out;
  }
  ::v-deep .apexcharts-inner {
    overflow: scroll;
  }
  ::v-deep .apexcharts-legend {
  display: block;
  }
  ::v-deep .apexcharts-legend-series {
    padding: 5px;
  }
  ::v-deep .apexcharts-legend-marker {
  border-radius: 7px !important;
  }
  ::v-deep .apexcharts-legend-text {
  font-size: 14px !important;
  }
  ::v-deep line{
      stroke: rgb(224, 224, 224) !important;
  }
  ::v-deep .apexcharts-gridlines-horizontal {
    border-right: rgb(224, 224, 224) !important;
  }
  ::v-deep .apexcharts-gridline {
    display: none !important;
  }
  ::v-deep .apexcharts-toolbar {
    display: none !important;
  }
  ::v-deep span.apexcharts-legend-marker{
    background-color: #3189BB !important;
      height: 22px !important;
      width: 22px !important;
      left: 0px;
      top: 0px;
      border-width: 0px;
      border-color: rgb(255, 255, 255);
      border-radius: 100% !important;
  }
  .poin-chart {
    height: 500px;
    width: 12px;
    position: absolute;
    left: 94px;
    top: 288px;
  }
  #chart{
    position: relative;
  }
  .breackpoin{
    color: darkgray;
  }
</style>
