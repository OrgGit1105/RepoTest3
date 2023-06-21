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
                <template>
                  <el-date-picker
                    v-model="formSearch.date"
                    type="daterange"
                    align="right"
                    start-placeholder="Start Date"
                    end-placeholder="End Date"
                    value-format="yyyy-MM-dd"
                    firstDayOfWeek="1"
                    @blur="fillDate()"
                    >
                  </el-date-picker>
                </template>

              </div>
            </div>
          </div>
        </div>
        <hr class="line-bottom">
        <div class="use-management-title-table mt-5">
          <div class="fill">
            <div class="d-flex justify-content-end align-items-center">
              <template class="select-custom">
                <el-select v-model="employeeValue" placeholder="Select" class="el-select-custom" @change="fillSearch(employeeValue)">
                    <el-option
                      class="el-option-custom"
                      label="All Employee"
                      value="">
                    </el-option>
                    <el-option
                      v-for="item in listEmployee"
                      :key="item.id"
                      :label="item.name"
                      :value="item.id"
                      >
                    </el-option>
                </el-select>
              </template>
            <i class="el-icon-download custom-icon-down cursor-pointer"></i>
            </div>
          </div>
          <hr class="line">
          <template class="">
            <el-table
              :data="listAnalytic"
              style="width: 100%"
              :row-style="rowWorkingStyle">
              <el-table-column
                prop="name"
                label="Employee name"
                width="400"
                align="center">
              </el-table-column>
              <el-table-column
                prop="work_day"
                label="Work Day"
                width="400"
                align="center">
              </el-table-column>
              <el-table-column
                prop="remote_work"
                label="Remote Work"
                align="center">
              </el-table-column>
              <el-table-column
                prop="day_off"
                label="Day Off"
                align="center">
              </el-table-column>
            </el-table>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { getArrving, createNewWorkingTime } from '../../api/working_time';
import { getAllUser } from '../../api/user';
import { MakeToast } from '../../utils/toast_message';
import moment from 'moment';
export default {
  name: 'WorkingTimeManagement',
  data() {
    return {
      formSearch: {
        userId: '',
        date: [ moment(moment().clone().weekday(1), 'MMMM Do YYYY').format('YYYY-MM-DD'), moment(moment().clone().weekday(5), 'MMMM Do YYYY').format('YYYY-MM-DD') ],
      },
      listAnalytic: [
        { name: 'IKeda Kohei', work_day: '13', remote_work: '1', day_off: '1' },
        { name: 'IKeda Kohei', work_day: '13', remote_work: '1', day_off: '1' },
        { name: 'IKeda Kohei', work_day: '13', remote_work: '1', day_off: '1' },
        { name: 'IKeda Kohei', work_day: '13', remote_work: '1', day_off: '1' },
        { name: 'IKeda Kohei', work_day: '13', remote_work: '1', day_off: '1' },
      ],
      listEmployee: [],
      employeeValue: '',
    };
  },
  created() {
    this.getAnalyticList();
    this.getListEmployee();
  },
  methods: {
    rowWorkingStyle({ row, rowIndex }) {
      return { 'cursor': 'pointer' };
    },
    openLoading() {
      this.$store.dispatch('loading/setLoading', true);
    },
    closeLoading() {
      this.$store.dispatch('loading/setLoading', false);
    },
    async getAnalyticList() {
      // let PARAMS = {};
      // if(this.search !== ''){
      //   PARAMS = {
      //     key_search: this.formSearch.search,
      //     start_date: this.formSearch.date[0],
      //     start_date: this.formSearch.date[0],
      //     end_date: this.formSearch.date[1],
      //     user_id: this.employeeValue,
      //     per_page: this.pagination.per_page,
      //     page: this.pagination.current_page,
      //   }
      // }
      // await getArrving(PARAMS)
      //   .then((response) => {
      //     if (response.code === 200) {
      //       this.listAnalytic = response.data.result;
      //       this.pagination = response.data.pagination;
      //     }
      //   })
      //   .catch((error) => {
      //     MakeToast({
      //       variant: 'danger',
      //       title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_FAILED'),
      //       content: error.message,
      //     });
      //   });
    },
    fillDate() {
      this.getAnalyticList();
    },
    async getListEmployee() {
      const PARAMS = {};
      await getAllUser(PARAMS)
        .then((response) => {
          if (response.code === 200) {
            this.listEmployee = response.data.result;
          }
        })
        .catch((error) => {
          this.listEmployee = [];
        });
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
</style>
