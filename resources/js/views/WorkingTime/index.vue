<template>
  <div>
    <div class="container-fluid w-90">
      <div class="container-fluid-body mt-5 mb-5">
        <div class="use-management-title">
          <div class="card-body p-5">
            <div class="d-flex justify-content-between align-items-center">
              <div class="basic">
                <h1 class="title">Working time Management</h1>
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
            <i class="el-icon-circle-plus-outline custom-icon-add cursor-pointer" @click="showModalAdd()" />
            <div class="box-search align-items-center" :class="display">
              <el-input
                v-model="formSearch.search"
                placeholder="検索"
                prefix-icon="el-icon-search"
                @keyup.enter.native="handleSearch()"
              />
              <i class="el-icon-close cursor-pointer" @click="closeInputSearch()" />
            </div>
            <div class="d-flex justify-content-end align-items-center">
              <img :class="displaySearch" class="icon-search cursor-pointer" :src="require(`../../assets/images/icon-search.png`)" @click="openInputSearch()">
              <div class="select-custom">
                <el-select v-model="employeeValue" placeholder="Select" class="el-select-custom" @change="fillSearch(employeeValue)">
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
              <i class="el-icon-download custom-icon-down cursor-pointer" @click="exportDataWorkingTime" />
            </div>
          </div>
          <hr class="line">
          <div>
            <el-table
              :data="listWorkingTimes"
              style="width: 100%"
              :row-style="rowWorkingStyle"
              @row-click="showDetail"
            >
              <el-table-column
                prop="warning"
                label=""
                width="250"
                align="center"
              >
                <template slot-scope="scope">
                  <div :class="scope.row.warning ? 'warning' : ''">
                    <i :class="scope.row.warning ? 'el-icon-warning' : ''" />
                    <span>{{ scope.row.warning }}</span>
                  </div>
                </template>
              </el-table-column>
              <el-table-column
                prop="type_date"
                label="Type"
                width="350"
                align="center"
              >
                <template slot-scope="scope">
                  <div :class="getClass(scope.row.type_date)">
                    <strong>{{ scope.row.type_date }}</strong>
                  </div>
                </template>
              </el-table-column>
              <el-table-column
                prop="user_name"
                label="Employee name"
                width="250"
                align="center"
              />
              <el-table-column
                prop="date"
                label="Date"
                align="center"
              />
              <el-table-column
                prop="in_time"
                label="IN"
                align="center"
              />
              <el-table-column
                prop="out_time"
                label="OUT"
                align="center"
              />
              <el-table-column
                prop="registration_type"
                label="Input type"
                align="center"
              />
            </el-table>
          </div>
        </div>

        <div class="use-management-pagianation">
          <div class="card-body pagianation">
            <el-pagination
              background
              layout="prev, pager, next"
              class="d-flex justify-content-center"
              :page-size="pagination.per_page"
              :total="pagination.total_records"
              :current-page.sync="pagination.current_page"
              @current-change="getWorkingTime"
            />
          </div>
        </div>

        <!-- Modal add new -->
        <el-dialog class="title-add-working" title="Add Working time" :visible.sync="openModalAdd" width="35%" @close="resetForm('ruleForm')">
          <el-form ref="ruleForm" :model="form" :rules="rules" label-width="120px" label-position="top">
            <div class="d-flex flex-row justify-content-between flex-wrap">
              <el-form-item label="Employee Name" required prop="userId">
                <el-select v-model="form.userId" placeholder="Please select employee name" style="width: 250px;">
                  <el-option
                    v-for="item in listEmployee"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  />
                </el-select>
              </el-form-item>
              <el-form-item label="Working Type" required prop="type_date">
                <el-select v-model="form.type_date" placeholder="Please select working type" style="width: 250px;">
                  <el-option
                    v-for="item in listWorkingType"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  />
                </el-select>
              </el-form-item>
            </div>
            <hr class="line">
            <p class="title-working mb-3">{{ isNaN(form.type_date) ? form.type_date : updateStatusHeader() }} Time</p>
            <p class="label-custom">In Time</p>
            <div class="date-time-custom">
              <el-form-item prop="inDate" class="item-date">
                <el-date-picker
                  v-model="form.inDate"
                  type="date"
                  format="yyyy/MM/dd"
                  value-format="yyyy-MM-dd"
                  style="width: 100%;"
                />
              </el-form-item>

              <el-form-item prop="inTime" class="item-time">
                <el-time-picker
                  v-model="form.inTime"
                  format="HH:mm:ss"
                  value-format="HH:mm:ss"
                  style="width: 100%;"
                />
              </el-form-item>
            </div>

            <p class="label-custom">Out Time</p>
            <div class="date-time-custom">
              <el-form-item prop="outDate" class="item-date">
                <el-date-picker
                  v-model="form.outDate"
                  type="date"
                  format="yyyy/MM/dd"
                  value-format="yyyy-MM-dd"
                  style="width: 100%;"
                />
              </el-form-item>
              <el-form-item prop="outTime" class="item-time">
                <el-time-picker
                  v-model="form.outTime"
                  format="HH:mm:ss"
                  value-format="HH:mm:ss"
                  style="width: 100%;"
                />
              </el-form-item>
            </div>
          </el-form>

          <span slot="footer" class="dialog-footer">
            <el-button class="btn-cancle-custom" @click="resetForm('ruleForm')">Cancel</el-button>
            <el-button class="btn-add-custom" type="primary" @click="submitForm('ruleForm')">Add</el-button>
          </span>
        </el-dialog>
      </div>
    </div>
  </div>
</template>

<script>
import { getArrving, createNewWorkingTime } from '../../api/working_time';
import { getAllUserWithoutPagination } from '../../api/user';
import { MakeToast } from '../../utils/toast_message';
import moment from 'moment';
import axios from 'axios';
import Cookies from 'js-cookie';
export default {
  name: 'WorkingTimeManagement',
  data() {
    return {
      formSearch: {
        search: '',
        userId: '',
        date: [],
        // date: [ '', '' ],
      },
      pagination: {
        current_page: 1,
        per_page: 10,
        total_records: 0,
        isDisable: false,
      },
      listWorkingTimes: [],
      listEmployee: [],
      listWorkingType: [
        { id: 1, name: 'Working' },
        { id: 2, name: 'Remote' },
        { id: 3, name: 'Take off' },
        { id: 4, name: 'Special day off' },
      ],
      employeeValue: '',
      form: {
        userId: '',
        inDate: '',
        inTime: '',
        outDate: '',
        outTime: '',
        type_date: '',
      },
      display: 'd-none',
      displaySearch: 'd-block',
      openModalAdd: false,
      rules: {
        userId: [
          { required: true, message: 'Please select Employee Name', trigger: 'change' },
        ],
        inDate: [
          { required: true, message: 'Please pick a date in', trigger: 'change' },
        ],
        inTime: [
          { required: true, message: 'Please pick a time in', trigger: 'change' },
        ],
        outDate: [
          { required: true, message: 'Please pick a date out', trigger: 'change' },
        ],
        outTime: [
          { required: true, message: 'Please pick a time out', trigger: 'change' },
        ],
      },
    };
  },
  watch: {
    'form.type_date': function() {
      const isRequired = this.form.type_date !== 1;
      this.rules.outDate[0].required = isRequired;
      this.rules.outTime[0].required = isRequired;
    },
  },

  created() {
    this.handleDate();
    this.getWorkingTime();
    this.getListEmployee();
  },
  methods: {
    submitForm(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          this.createNew();
        } else {
          return false;
        }
      });
    },
    resetForm(formName) {
      this.$refs[formName].resetFields();
      this.openModalAdd = false;
    },
    openInputSearch() {
      this.display = 'd-flex';
      this.displaySearch = 'd-none';
    },
    closeInputSearch() {
      this.display = 'd-none';
      this.displaySearch = 'd-block';
    },
    rowWorkingStyle({ row, rowIndex }) {
      return { 'cursor': 'pointer' };
    },
    showModalAdd: function() {
      this.openModalAdd = true;
    },
    showDetail: function(row, column, event) {
      this.$router.push({ path: `/working-time/detail/${row.id}` });
    },
    openLoading() {
      this.$store.dispatch('loading/setLoading', true);
    },
    closeLoading() {
      this.$store.dispatch('loading/setLoading', false);
    },
    closeModal(formName) {
      this.openModalAdd = false;
      this.form = {
        userId: '',
        inDate: '',
        inTime: '',
        outDate: '',
        outTime: '',
      };
      this.$refs[formName].resetFields();
    },
    async getWorkingTime() {
      let PARAMS = {};
      if (this.search !== ''){
        PARAMS = {
          key_search: this.formSearch.search,
          start_date: this.formSearch.date[0],
          // start_date: this.formSearch.date[0],
          end_date: this.formSearch.date[1],
          user_id: this.employeeValue,
          per_page: this.pagination.per_page,
          page: this.pagination.current_page,
        };
      }
      await getArrving(PARAMS)
        .then((response) => {
          if (response.code === 200) {
            this.listWorkingTimes = response.data.result;
            this.pagination = response.data.pagination;
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
    handleSearch() {
      this.getWorkingTime();
    },
    fillSearch() {
      this.getWorkingTime();
    },
    fillDate() {
      this.$store.dispatch('app/saveStartDate', this.formSearch.date[0]);
      this.$store.dispatch('app/saveEndtDate', this.formSearch.date[1]);
      this.getWorkingTime();
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
    async createNew() {
      const PARAMS = {
        user_id: this.form.userId,
        in_time: this.form.inDate + ' ' + this.form.inTime,
        out_time: this.form.outDate + ' ' + this.form.outTime,
        type_date: this.form.type_date,
      };
      await createNewWorkingTime(PARAMS)
        .then((response) => {
          if (response.code === 200) {
            this.resetForm('ruleForm');
            MakeToast({
              variant: 'success',
              title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
              content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_CREATE_SUCCESSFULLY'),
            });
            this.getWorkingTime();
          } else {
            MakeToast({
              variant: 'danger',
              title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_FAILED'),
              content: response.message,
            });
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
    async exportDataWorkingTime() {
      const URL = '/api/arriving_report/download?start_date=' + this.formSearch.date[0] +
        '&end_date=' + this.formSearch.date[1] + '&key_search=' + this.formSearch.search + '&user_id=' + this.employeeValue;
      axios.get(URL, {
        responseType: 'blob',
      }).then((response) => {
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        const fileName = 'working-time.xlsx';
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
        this.formSearch.date = [moment(moment().clone().weekday(1), 'MMMM Do YYYY').format('YYYY-MM-DD'), moment(moment().clone().weekday(5), 'MMMM Do YYYY').format('YYYY-MM-DD')];
      }
    },
    getClass(type_date) {
      switch (type_date) {
        case 'Working':
          return 'type_working';
        case 'Remote':
          return 'type_remote';
        case 'Take off':
          return 'type_take_off';
        case 'Special day off':
          return 'type_special_day_off';
        default:
          return '';
      }
    },
    updateStatusHeader() {
      const item = this.listWorkingType.find(item => this.form.type_date === item.id);
      return item ? item.name : '';
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
.btn-date {
  background: #0070C9;
  border-radius: 20px;
  text-align: center;
}
.text-btn {
  font-weight: 700;
  font-size: 14px;
  line-height: 21px;
  color: #FFFFFF;
}
.line-bottom {
  width: 95%;
  height: 1px;
  color: rgba(63, 63, 63, 0.4);
  margin: 0 auto;
}
.fill {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 90%;
  margin: 0 auto;
}
.icon-d {
  width: 15px;
  height: 10px;
}
.el-select-custom {
  width: 175px;
  margin: 0 20px;
}
::v-deep .el-select-custom .el-input__inner {
  border: unset;
  border-radius: unset;
  color: #0070C9 !important;
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
::v-deep .box-search .el-input__inner {
  border: 1px solid rgba(63, 63, 63, 0.4);
  border-radius: 5px;
  padding-left: 40px;
}
::v-deep .box-search .el-icon-search {
  color: #3F3F3F;
  font-weight: bolder;
  font-size: 20px;
}
::v-deep .box-search ::placeholder {
  color: #8A8A8A;
}
::v-deep .box-search .el-icon-close {
  margin-left: 10px;
  font-size: 25px;
  color: #8A8A8A;
}
.cursor-pointer {
  cursor: pointer;
}
.custom-icon-add {
  color: #0070C9;
  font-size: 30px;
  font-weight: bolder;
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
::v-deep .type_remote {
  margin: auto;
  padding: 7px 0 5px 0;
  width: 50%;
  border-radius: 10px;
  background-color: #80ffbf;
}
::v-deep .type_working {
  margin: auto;
  padding: 7px 0 5px 0;
  width: 50%;
  border-radius: 10px;
  background-color: #ccf2ff;
}
::v-deep .type_take_off {
  margin: auto;
  padding: 7px 0 5px 0;
  width: 50%;
  border-radius: 10px;
  background-color: #ffcc99;
}
::v-deep .type_special_day_off {
  margin: auto;
  padding: 7px 0 5px 0;
  width: 50%;
  border-radius: 10px;
  background-color: #90EE90;
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
::v-deep .btn-cancle-custom {
  border: 1px solid #0070C9;
  color: #0070C9;
  width: 100px;
}
::v-deep .btn-add-custom {
  background: #0070C9;
  border-radius: 5px;
  width: 100px;
}
.btn-danger:hover {
  color: #fff !important;
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
::v-deep .warning {
  color: red;
  border: 1px solid;
  width: 40%;
  margin-left: 60%;
}
</style>
