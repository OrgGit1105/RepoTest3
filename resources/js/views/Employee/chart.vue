
<template>
  <div class="container-fluid">
    <div class="container-fluid-body mt-5 mb-5">
      <div class="row">
        <div class="col-md-12 py-5">
          <div>
            <div class="row">
              <div class="col-8">
                <h1 class="pl-3 title-info"> {{ $t('LANGUAGES.TEXT_EMPLOYEE_PERIOD_GRAPH') }}</h1>
              </div>
              <div class="col-4 info-left">
                <div class="p-2">
                  <label>{{ $t('LANGUAGES.TEXT_DEPARTURE') }}：</label>
                  <span>{{ employeeDetailInfo ? employeeDetailInfo.companyName : '' }}</span>
                </div>
                <div class="p-2">
                  <label>{{ $t('LANGUAGES.TEXT_EMPLOYEE_CODE') }}：</label>
                  <span>{{ employeeDetailInfo ? employeeDetailInfo.employeeCode : '' }}</span>
                </div>
                <div class="p-2">
                  <label>{{ $t('LANGUAGES.TEXT_EMPLOYEE_NAME') }}：</label>
                  <span>{{ employeeDetailInfo ? employeeDetailInfo.employeeName : '' }}</span>
                </div>
              </div>
            </div>
            <div id="chart">
              <apexchart id="apex-line" type="line" height="500" :options="chartOptions" :series="series" />
            </div>
            <div class="py-3">
              <b-button class="btn-back" @click="$router.go(-1)">
                <div class="photo">
                  <div>
                    <img class="img_back" :src="require(`../../assets/images/back.png`)" alt="">
                  </div>
                  <span class="text_back">{{ $t('LANGUAGES.TEXT_BUTTON_RETURN') }}</span>
                </div>
              </b-button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { MakeToast } from '../../utils/toast_message';
import * as EmployeeApi from '../../api/employee';
import * as RetirementApi from '../../api/retirement';
import moment from 'moment';
// import * as CONVERT from '../../utils/convert';
export default {
  name: 'EmployeeDetail',
  data() {
    return {
      listMonthYear: [],
      monthYear: {
        minDate: '',
        maxDate: '',
      },
      employeeId: this.$route.params.id,
      currentYear: new Date().getFullYear(),
      timeYear: new Date().getFullYear(),
      employeeInfo: {},
      series: [{
        name: 'Score',
        data: [],
      }],
      chartOptions: {
        chart: {
          id: 'apex-line',
          height: 500,
          type: 'line',
          toolbar: {
            show: true,
            tools: {
              download: false,
              selection: true,
              zoom: false,
              zoomin: false,
              zoomout: false,
              pan: true,
              reset: false,
            },
          },
          zoom: {
            enabled: true,
            type: 'x',
            resetIcon: {
              offsetX: -10,
              offsetY: 0,
              fillColor: '#fff',
              strokeColor: '#37474F',
            },
            selection: {
              background: '#90CAF9',
              border: '#0D47A1',
            },
          },
        },
        dataLabels: {
          enabled: false,
        },
        stroke: {
          width: 2,
        },
        title: {
          text: '',
          align: 'center',
        },
        grid: {
          row: {
            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
            opacity: 0.5,
          },
        },
        xaxis: {
          categories: this.listMonthYear,
          range: 11,
          labels: {
            rotate: 0,
            // rotateAlways: true,
          },
        },
        yaxis: {
          min: 0,
          max: 100,
          tickAmount: 5,
          decimalsInFloat: 2,
        },
        tooltip: {
          y: {
            formatter: function(val) {
              return parseFloat(val).toFixed(2);
            },
          },
        },
      },
    };
  },
  computed: {
    employeeDetailList() {
      return this.$store.getters.employeeDetailList;
    },
    employeeDetailInfo() {
      return this.$store.getters.employeeDetailInfo;
    },
  },
  watch: {
    employeeDetailList() {
      if (this.employeeDetailList.length > 0){
        const listEmployeeDetail = this.employeeDetailList;
        // console.log('listEmployeeDetail ==>', listEmployeeDetail);
        const listPoint = [];
        const listCategory = [];
        const listMonthYear = [];
        // console.log('Month Year ==>', this.monthYear);
        const dateStart = moment(this.monthYear.minDate);
        const dateEnd = moment(this.monthYear.maxDate);
        var interim = dateStart.clone();
        while (dateEnd.format('x') > interim.format('x') || interim.format('M') === dateEnd.format('M')) {
          listMonthYear.push(interim.format('MM/YYYY'));
          listCategory.push(this.converMonthYearToJapanese(interim.format('YYYY/MM')));
          interim.add(1, 'month');
        }
        // console.log('listMonthYear ====>', listMonthYear);
        listMonthYear.length > 0 && listMonthYear.forEach(item => {
          const employeeInfo = listEmployeeDetail.find(x => x.month_year === item);
          // console.log('employeeInfo ====>', employeeInfo);
          listPoint.push(employeeInfo ? employeeInfo.retirement_score_percent : null);
        });
        this.series = [{
          data: listPoint,
        }];
        this.chartOptions = {
          ...this.chartOptions,
          xaxis: {
            categories: listCategory,
          },
        };
      } else {
        this.series = [{
          data: [],
        }];
        this.chartOptions = {};
      }
    },
  },
  async created() {
    await this.getMonthAndYear();
    await this.getEmployeeInfo();
  },
  methods: {
    async getEmployeeInfo() {
      this.openLoading();
      await EmployeeApi.getEmployeeById({ employee_id: this.employeeId })
        .then((response) => {
          // console.log('Data return', response);
          if (response.code === 200) {
            if (response.data) {
              if (response.data.length > 0) {
                const dataReturn = response.data[0].employees;
                // console.log(employeeDetailList);
                const employeeDetailList = response.data.sort((a, b) => {
                  a = moment(a.month_year, 'MM/YYYY').format('X');
                  b = moment(b.month_year, 'MM/YYYY').format('X');
                  return a - b;
                });
                // console.log('employeeDetailList ==>', employeeDetailList);
                const employeeInfo = { employeeCode: dataReturn.employee_code, employeeName: dataReturn.employee_name, companyName: dataReturn.company.name };
                this.$store.dispatch('app/saveEmployeeDetailInfo', employeeInfo);
                this.$store.dispatch('app/saveEmployeeDetailList', employeeDetailList);
              } else {
                this.$store.dispatch('app/saveEmployeeDetailInfo', {});
                this.$store.dispatch('app/saveEmployeeDetailList', []);
              }
            }
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
    nextTime() {
      const currentYear = this.timeYear;
      // this.timeYear = currentYear + 1;
      const newYear = currentYear + 1;
      if (newYear > new Date().getFullYear()) {
        return;
      } else {
        this.timeYear = newYear;
        this.getEmployeeInfo();
      }
    },
    backTime() {
      const currentYear = this.timeYear;
      this.timeYear = currentYear - 1;
      this.getEmployeeInfo();
    },
    openLoading() {
      this.$store.dispatch('loading/setLoading', true);
    },
    closeLoading() {
      this.$store.dispatch('loading/setLoading', false);
    },
    async getMonthAndYear() {
      this.openLoading();
      await RetirementApi.getMonthYear()
        .then((response) => {
          if (response.code === 200) {
            this.monthYear.minDate = response.data.min_month_year;
            this.monthYear.maxDate = response.data.max_month_year;
            // console.log('monthYear ==>', moment(response.data.max_month_year, 'YYYY/MM/DD').format('YYYY'));
            // this.$store.dispatch('app/saveMonthYear', this.monthYear);
          }
          this.closeLoading();
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
    converMonthYearToJapanese(str) {
      if (str) {
        const arrMonthYear = str.split('/');
        return arrMonthYear[0] + '年 ' + arrMonthYear[1] + '月';
      } else {
        return '';
      }
    },
  },
};
</script>

<style scoped>
 .title-info {
    border-left: 9px solid #fb9a09;
    color: #3189bb;
    text-transform: uppercase;
    margin-bottom: 60px;
  }
  .info-left {
    border: 1px solid darkgray;
    padding: 12px;
  }
  #chart {
    padding-top: 50px;
  }
  label {
    font-weight: 600;
  }
  /* span {
    font-weight: 500;
  } */

  .btn-back {
      display: inline-block;
      width: 160px;
      left: 25px;
      top: 50px;
      background: white;
      border: 1px solid #CCCCCC;
      box-sizing: border-box;
      border-radius: 15px;
  }

  .photo {
      display: flex;
      justify-content: space-around;
  }

  .img_back {
    display: block;
    max-width: 50px;
    max-height:50px;
    width: auto;
    height: auto;
  }

  .text_back {
      font-weight: bold;
      font-size: 23px;
      color: #828282;
      font-style: normal;
      margin: auto 0;
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
    .poin-chart {
    height: 515px;
    width: 12px;
    position: absolute;
    left: 53px;
    top: 257px;
  }
  .line-poin {
    height: 86px;
    display: none;
  }
  .container-fluid {
  width: 80%;
  margin: auto;
}
.btn-secondary:active {
  background-color: transparent !important;
}
.btn-secondary:focus {
  box-shadow: none;
}
</style>
