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
              <div class="basic">
                <div>
                  <el-date-picker
                    v-model="formSearch.date"
                    type="daterange"
                    align="right"
                    start-placeholder="Start Date"
                    end-placeholder="End Date"
                    value-format="yyyy-MM-dd"
                    first-day-of-week="1"
                    @blur="fillDate()"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
        <hr class="line-bottom">
        <div class="use-management-title-table mt-5">
          <div class="fill">
            <div class="d-flex justify-content-end align-items-center">
              <div class="select-custom">
                <el-select v-if="userinfo.role_id === 1" v-model="employeeValue" placeholder="Select" class="el-select-custom" @change="fillSearch(employeeValue)">
                  <el-option
                    class="el-option-custom"
                    label="All Employee"
                    value=""
                  />
                  <el-option
                    v-for="item in listEmployee"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  />
                </el-select>
              </div>
              <i class="el-icon-download custom-icon-down cursor-pointer" @click="exportDataAnalytic" />
            </div>
          </div>
          <hr class="line">
          <div>
            <el-table
              :data="listAnalytic"
              style="width: 100%"
              :row-style="rowWorkingStyle"
              :cell-class-name="cellStyle"
              @row-click="showDetail"
            >
              <el-table-column
                prop="user_name"
                label="Employee name"
                width="350"
                align="center"
              />
              <el-table-column
                prop="work_day"
                label="Work Day"
                width="350"
                align="center"
              />
              <el-table-column
                prop="remote_day"
                label="Remote Work"
                align="center"
              />
              <el-table-column
                prop="off_day"
                label="Day Off"
                align="center"
              />
              <el-table-column
                prop="special_off_day"
                label="Special"
                align="center"
              />
            </el-table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { getAllAnalytic } from '../../api/analytic';
import { getAllUserWithoutPagination } from '../../api/user';
import moment from 'moment';
import { MakeToast } from '../../utils/toast_message';
import axios from 'axios';
import Cookies from 'js-cookie';
import { getToken } from '../../utils/getToken';
export default {
  name: 'AnalyticsManagement',
  data() {
    return {
      formSearch: {
        userId: '',
        date: [],
      },
      listAnalytic: [],
      listEmployee: [],
      employeeValue: '',
      userinfo: '',
    };
  },
  created() {
    this.handleDate();
    this.getListEmployee();
    this.getListAllAnalytic();
    this.getUserInfo();
  },
  methods: {
    getUserInfo() {
      this.userinfo = JSON.parse(Cookies.get('userInfo'));
    },
    rowWorkingStyle({ row, rowIndex }) {
      return { 'cursor': 'pointer' };
    },
    async getListAllAnalytic() {
      let PARAMS = {};
      if (this.search !== ''){
        PARAMS = {
          start_date: this.formSearch.date[0],
          end_date: this.formSearch.date[1],
          user_id: this.employeeValue,
        };
      }
      await getAllAnalytic(PARAMS)
        .then((response) => {
          if (response.code === 200) {
            this.listAnalytic = response.data;
          }
        })
        .catch((error) => {
          MakeToast({
            variant: 'danger',
            title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_FAILED'),
            content: error.message,
          });
        });
    },
    fillSearch() {
      this.getListAllAnalytic();
    },
    fillDate() {
      this.$store.dispatch('app/saveStartDate', this.formSearch.date[0]);
      this.$store.dispatch('app/saveEndtDate', this.formSearch.date[1]);
      this.getListAllAnalytic();
    },
    async getListEmployee() {
      await getAllUserWithoutPagination()
        .then((response) => {
          if (response.code === 200) {
            this.listEmployee = response.data;
          }
        })
        .catch(() => {
          this.listEmployee = [];
        });
    },
    showDetail: function(row, column, event) {
      this.$router.push({ path: `/analytics/detail/${row.user_id}` });
    },
    async exportDataAnalytic() {
      const URL = '/api/analytic/download?start_date=' + this.formSearch.date[0] +
        '&end_date=' + this.formSearch.date[1] + '&user_id=' + this.employeeValue;
      const token = getToken();
      axios.get(URL, {
        responseType: 'blob',
        headers: {
          Authorization: token,
        },
      }).then((response) => {
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        const fileName = 'analytic.xlsx';
        link.setAttribute('download', fileName);
        document.body.appendChild(link);
        link.click();
      }).catch((error) => {
        console.log(error);
      });
    },
    handleDate() {
      if (Cookies.get('startDate') !== '' && Cookies.get('endDate') !== '') {
        this.formSearch.date = [Cookies.get('startDate'), Cookies.get('endDate')];
      } else {
        this.formSearch.date = [moment().startOf('month').format('YYYY-MM-DD'), moment().endOf('month').format('YYYY-MM-DD')];
      }
    },
    cellStyle({ row, column, rowIndex, columnIndex }) {
      if (column.property === 'special_off_day' && row.special_off_day > 3) {
        return 'red-cell-special-day-off';
      }
      return '';
    },
  },
};
</script>

<style scoped>
@import '../../../sass/config.scss';
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
.fill {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  width: 90%;
  margin: 0 auto;
}
.el-select-custom {
  width: 175px;
  margin: 0 20px;
}
::v-deep .el-select-custom .el-input__inner {
  border: unset;
  border-radius: unset;
  color: #0070C9;
  font-size: 16px;
  font-weight: 500;
  text-align: center;
}
::v-deep .el-select-custom .el-input .el-select__caret {
  color: #0070C9;
  font-weight: bolder;
  font-size: 20px;
  margin-top: 3px;
}
.cursor-pointer {
  cursor: pointer;
}
.custom-icon-down {
  color: #0070C9;
  font-size: 26px;
  font-weight: 600;
}
.use-management-title-table {
  padding: 0 45px;
}
.line {
  width: 100%;
  height: 1px;
  color: rgba(63, 63, 63, 0.4);
  margin: 10px auto;
}
::v-deep .title-add-working .el-dialog__title, .title-working {
  font-weight: 600;
  font-size: 32px;
  line-height: 48px;
  color: #000000;
}
::v-deep label.el-form-item__label, .label-custom {
  margin-bottom: 10px;
  padding: unset!important;
  font-weight: 500;
  font-size: 16px;
  line-height: 24px;
  color: #666666;
}
::v-deep .el-dialog__body {
  padding: 20px 40px 10px 40px
}
::v-deep .el-dialog__footer {
  border-top: 1px solid rgba(63, 63, 63, 0.3);
}
::v-deep .title-add-working .el-dialog {
  border-radius: 5px;
}
::v-deep .page-link:hover {
  border: 1px solid #0f68b1 !important;
}
::v-deep .date-time-custom {
  display: flex;
  justify-content: space-between;
  width: 60%;
}
::v-deep .date-time-custom .item-date {
  width: 55%;
}
::v-deep .date-time-custom .item-time {
  width: 40%;
}
::v-deep .red-cell-special-day-off {
  color: red !important;
}
</style>
