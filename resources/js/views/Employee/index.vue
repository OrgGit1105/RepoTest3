<template>
  <div>
    <div class="container-fluid">
      <div class="container-fluid-body mt-5 mb-5">
        <div class="col-md-12 py-5 form-group">
          <div class="justify-content-start">
            <div class="basic"><h1 class="pl-3 title-info"> {{ $t('LANGUAGES.TEXT_EMPLOYEE_LIST') }}</h1></div>
          </div>
          <div class="d-flex justify-content-center mt-5">
            <div class="select-time">
              <span>{{ timeDisplay.year }} 年</span>
              <span>{{ timeDisplay.month }} 月</span>
            </div>
          </div>
          <b-form border="none" @submit="onSubmit($event)">
            <div class="form-header">
              <h2 class="pt-2 form-group-filter">
                <span class="pr-1"><b-icon icon="chevron-down" /></span>{{ $t('LANGUAGES.TEXT_FILTER') }}
              </h2>
              <h3 class="pt-2" @click="clearAll">{{ $t('LANGUAGES.TEXT_CLEAR_ALL') }}</h3>
              <div class="pt-4 pb-4">
                <!-- 1 -->
                <b-form-group id="input-group-1">
                  <div>
                    <b-form-checkbox
                      id="cbRiskcore"
                      v-model="statusRiskCore"
                      size="lg"
                      value="accepted"
                      unchecked-value="not_accepted"
                    >
                      <span class="label-name">{{ $t('LANGUAGES.TEXT_SCORE_ABOVE') }}</span>
                    </b-form-checkbox>
                  </div>
                </b-form-group>

                <!-- 2 -->
                <b-form-group v-show="roleId === headQuarter" id="input-group-2" class="show-by-role">
                  <b-row>
                    <b-col md="3">
                      <b-form-checkbox
                        id="cbDeparture"
                        v-model="statusDepature"
                        dusk="company_branch"
                        size="lg"
                        value="accepted"
                        unchecked-value="not_accepted"
                      >
                        <span class="label-name">{{ $t('LANGUAGES.TEXT_DEPARTURE') }}</span>
                      </b-form-checkbox>
                    </b-col>
                    <b-col
                      v-show="
                        statusDepature === 'accepted' ? true : false
                      "
                      class="depature-status"
                      md="3"
                    >
                      <div>
                        <b-form-select
                          v-model="queryData.company_branch"
                          dusk="company"
                          :options="companyOption"
                        />
                      </div>
                    </b-col>
                  </b-row>
                </b-form-group>
                <!-- 3 -->
                <b-form-group id="input-group-3">
                  <b-row>
                    <b-col md="3">
                      <b-form-checkbox
                        id="cbEmployeeNumber"
                        v-model="statusEmployeeNumber"
                        dusk="employee_number"
                        size="lg"
                        value="accepted"
                        unchecked-value="not_accepted"
                      >
                        <span class="label-name">{{ $t('LANGUAGES.TEXT_EMPLOYEE_CODE') }}</span>
                      </b-form-checkbox>
                    </b-col>
                    <b-col
                      v-show="
                        statusEmployeeNumber === 'accepted' ? true : false
                      "
                      class="employee-status"
                      md="3"
                    >
                      <div class="input-name-search">
                        <b-input-group>
                          <b-form-input
                            v-model="queryData.employee_code"
                            dusk="employee_code"
                            :placeholder=" $t('LANGUAGES.TEXT_PLACEHOLDER_EMPLOYEE_CODE')"
                            :formatter="formatText"
                          />
                        </b-input-group>
                      </div>

                    </b-col>
                  </b-row>
                </b-form-group>

                <!-- 4 -->
                <b-form-group id="input-group-4">
                  <b-row>
                    <b-col md="3">
                      <b-form-checkbox
                        id="cbEmployeeName"
                        v-model="statusEmployeeName"
                        size="lg"
                        value="accepted"
                        unchecked-value="not_accepted"
                      >
                        <span class="label-name">{{ $t('LANGUAGES.TEXT_EMPLOYEE_NAME') }}</span>
                      </b-form-checkbox>
                    </b-col>
                    <b-col
                      v-show="
                        statusEmployeeName === 'accepted' ? true : false
                      "
                      class="employee-name-status"
                      md="3"
                    >
                      <div class="input-name-search">
                        <b-input-group>
                          <b-form-input
                            v-model="queryData.employee_name"
                            dusk="employee_name"
                            name="employee_name"
                            :placeholder=" $t('LANGUAGES.TEXT_PLACEHOLDER_ENTER_EMPLOYEE_NAME')"
                            :formatter="formatText"
                          />
                        </b-input-group>
                      </div>

                    </b-col>
                  </b-row>
                </b-form-group>
                <b-button variant="secondary" type="submit" class="text-uppercase btn-apply">{{ $t('LANGUAGES.TEXT_BUTTON_APPLY') }} </b-button>
              </div>
            </div>
          </b-form>
          <div class="Table">
            <b-table
              id="my-table"
              class="text-center w-100 bg-dx-grey-blur mb-0"
              :fields="fields"
              :items="listEmployee"
              responsive="sm"
              :current-page="queryData.page"
              show-empty
              :no-local-sorting="noSort"
              @sort-changed="sortingChanged"
            >
              <template #cell(result)="result">
                <b-button
                  :id="'btn-employee-'+ result.item.employee_code"
                  class="btn btn-pdf fs-14"
                  @click="openChart(result.item.employee_code)"
                ><b-icon icon="bar-chart-line" /> {{ $t('LANGUAGES.TEXT_RESULT') }}</b-button>
              </template>
              <template #empty="">
                {{ $t('LANGUAGES.TEXT_NO_DATA') }}
              </template>
            </b-table>
          </div>
          <div class="card-body pagianation d-flex justify-content-center">
            <b-pagination
              v-model="queryData.page"
              :per-page="queryData.per_page"
              :total-rows="queryData.total_records"
              aria-controls="my-table"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import * as EmployeeApi from '../../api/employee';
import { MakeToast } from '../../utils/toast_message';
import * as CompanyBranchApi from '../../api/company_branch';
import * as CONFIGS from '../../configs';
import moment from 'moment';
import * as RetirementApi from '../../api/retirement';
export default {
  name: 'EmployeeList',
  data() {
    return {
      timeDisplay: {
        month: '',
        year: '',
      },
      roleId: this.$store.getters.role_id,
      statusDepature: 'not_accepted',
      statusEmployeeNumber: 'not_accepted',
      statusEmployeeName: 'not_accepted',
      statusRiskCore: 'not_accepted',
      headQuarter: CONFIGS.UserRoleId.HEAD_QUARTER,
      selected: null,
      noSort: true,
      error: {
        start_date: null,
        end_date: null,
      },
      interviewDeparuteOption: [],
      companyOption: [{ value: '', text: '拠点を入力してください', disabled: true }],
      queryData: {
        page: 1,
        per_page: 20,
        total_records: 0,
        company_branch: '',
        employee_code: '',
        employee_name: '',
        threshold_value: '',
        column_name: '',
        sort: '',
      },
      fields: [
        { key: 'retirement_score_percent', sortable: true, label: this.$t('LANGUAGES.TEXT_SCORE'), class: 'retirement_score', dusk: 'retirement_score' },
        { key: 'employee_code', sortable: true, label: this.$t('LANGUAGES.TEXT_EMPLOYEE_CODE'), class: 'employee_code', dusk: 'employee_code' },
        { key: 'employee_name', sortable: true, label: this.$t('LANGUAGES.TEXT_EMPLOYEE_NAME'), class: 'candidate_name', dusk: 'candidate_name' },
        { key: 'company.name', sortable: true, label: this.$t('LANGUAGES.TEXT_DEPARTURE'), class: 'company.name company_branch', dusk: 'company_branch' },
        { key: 'result', sortable: false, label: '', class: 'result' },
      ],
    };
  },
  computed: {
    companyBranch() {
      return this.$store.getters.listBranch;
    },
    listEmployee() {
      return this.$store.getters.listEmployee;
    },
    pageChange() {
      return this.queryData.page;
    },
    branchUser() {
      return this.$store.getters.branchUser;
    },
  },
  watch: {
    companyBranch() {
      // this.companyOption.push({ value: '', text: '面接拠点を入力してください', disabled: true });
      for (let i = 0; i < this.companyBranch.length; i++) {
        this.companyOption.push({ value: this.companyBranch[i].id, text: this.companyBranch[i].name });
      }
    },
    statusDepature() {
      if (this.statusDepature === 'not_accepted'){
        this.queryData.company_branch = '';
      }
    },
    statusEmployeeNumber() {
      if (this.statusEmployeeNumber === 'not_accepted'){
        this.queryData.employee_code = '';
      }
    },
    statusEmployeeName() {
      if (this.statusEmployeeName === 'not_accepted'){
        this.queryData.employee_name = '';
      }
    },
    statusRiskCore() {
      if (this.statusRiskCore === 'not_accepted'){
        this.queryData.threshold_value = '';
      } else {
        this.queryData.threshold_value = true;
      }
    },
    pageChange() {
      this.getAllEmployee();
    },
    branchUser() {
      this.companyOption.push({ value: this.branchUser.id, text: this.branchUser.name });
      this.queryData.company_branch = this.branchUser.id;
    },
  },
  async created() {
    if (this.roleId === this.headQuarter) {
      this.getAllCompanyBranch();
    } else {
      this.getCompanyBranchByUser();
    }
    await this.getMonthAndYear();
    await this.getAllEmployee();
  },
  methods: {
    async getAllEmployee(e) {
      if (e){
        e.preventDefault();
      }
      // console.log('This query data', this.queryData);
      const body = { ...this.queryData };
      [
        'page',
        'per_page',
        'company_branch',
        'employee_code',
        'employee_name',
        'threshold_value',
        'column_name',
        'sort',
      ].forEach((element) => {
        if (body[element] === '') {
          delete body[element];
        }
      });
      delete body.total_records;
      this.openLoading();
      await EmployeeApi.getAllEmployee(body)
        .then((response) => {
          if (response.code === 200) {
            MakeToast({
              variant: 'success',
              title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
            });
            if (response.data) {
              // console.log('Manhh', response.data.data);
              this.queryData.total_records = response.data.data.pagination.total_records;
              const listEmployee = response.data.data.result;
              // console.log('listEmployee===>', listEmployee);
              const listEmployeeFormat = [];
              listEmployee.length > 0 && listEmployee.forEach(item => {
                listEmployeeFormat.push({
                  ...item,
                  retirement_score_percent: item.retirement_score_percent !== null ? parseFloat(item.retirement_score_percent).toFixed(2) : '-',
                });
              });
              // console.log('listEmployeeFormat===>', listEmployeeFormat);
              this.$store.dispatch('app/saveListEmployee', listEmployeeFormat);
              this.closeLoading();
            }
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
    onSubmit($event) {
      this.getAllEmployee($event);
    },
    openLoading() {
      this.$store.dispatch('loading/setLoading', true);
    },
    closeLoading() {
      this.$store.dispatch('loading/setLoading', false);
    },
    clearAll() {
      this.statusDepature = 'not_accepted';
      this.statusEmployeeNumber = 'not_accepted';
      this.statusEmployeeName = 'not_accepted';
    },

    async getAllCompanyBranch() {
      await CompanyBranchApi.getAllCompanyBranch()
        .then((response) => {
          // console.log('response===>', response);
          if (response.code === 200) {
            const listBranch = response.data.result;
            this.$store.dispatch('app/saveListBranch', listBranch);
          }
        })
        .catch((error) => {
          MakeToast({
            variant: 'warning',
            title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
            content: error.message,
          });
        });
    },
    async getCompanyBranchByUser() {
      await CompanyBranchApi.getCompanyBranchByUser()
        .then((response) => {
          if (response.code === 200) {
            // console.log('branchUser ===>', response.data);
            const branchUser = response.data;
            this.$store.dispatch('app/saveBranchUser', branchUser);
          }
        })
        .catch((error) => {
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
            const maxTime = response.data.max_month_year ? moment(response.data.max_month_year).format(
              'YYYY/MM/DD'
            ) : '';
            this.timeDisplay.month = 1 + moment(maxTime, 'YYYY/MM/DD').month();
            this.timeDisplay.year = moment(maxTime, 'YYYY/MM/DD').year();
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
    formatText(e) {
      return String(e).substring(0, 255);
    },
    openChart(id) {
      if (id && id !== '') {
        this.$router.push({ path: `/employee/chart/${id}` }, (onAbort) => {});
      }
    },
    sortingChanged(ctx) {
      // ctx.sortBy   ==> Field key for sorting by (or null for no sorting)
      // ctx.sortDesc ==> true if sorting descending, false otherwise
      this.queryData.column_name = ctx.sortBy === 'company.name' ? 'company_branch' : ctx.sortBy;
      this.queryData.sort = ctx.sortDesc;
      // console.log('1', this.queryData.column_name);
      // console.log('2', this.queryData.sort);
      this.getAllEmployee();
    },
  },
};
</script>
<style lang="scss" scoped>

.title-info {
    text-transform: uppercase;
    color: #3189bb;
    font-size: 25px;
}
.basic {
  border-left: 10px solid orange;
  left: 20px;
  position: absolute;
}
  .select-time {
     padding: 5px 10px 5px 10px;
    font-weight: 600;
    font-size: 19px;
    margin-bottom: 0px;
    border: 1px solid darkgray;
    border-radius: 7px;
  }
.label-name {
      font-weight: 500;
    font-size: 15px;
  padding-left: 10px;
}
.prediction-item {
  margin-top: 60px;
}
.btn-apply {
  border: none;
  background: #0f68b1;
  &:hover {
    background: #307bbc;
    color:#fff;
  }
}
.btn-pdf {
  background-color: dodgerblue;
  border: none;
    &:hover {
    background: #3b9eff;
    color:#fff;
  }
}
.btn-result {
  background-color:dodgerblue;
  border: none;
    &:hover {
    background: royalblue;
    color:#fff;
  }
}
.form-group {
      position: relative;
      .form-group-filter {
        position: absolute;
        top: -35px;
        left: 110px;
        background: #fff;
        padding: 0px 8px;
      }
    }
.form-header {
    margin-top: 50px;
    border: 2px solid #888888;
    border-left: 0;
    border-right: 0;
    padding: 30px 0px 0 0;
    position: relative;
}
h3 {
  text-decoration: underline;
  cursor: pointer;
}
.btn-search {
  background-color: ghostwhite;
  border-left: none;
  border-color: beige;
}

::v-deep .text-center.table-responsive-sm {
      margin: 50px 0px;
      // border: 1px solid #888888;
      border: none !important;
      border-right: 0;
      border-top: 0;
      .b-table {
        width: 100%;
        th.b-table-sort-icon-left {
          border: 0.7px solid #888888;
          border-left: 0;
          min-width: 100% !important;
        }
        td {
          border: 0.1px solid #888888;
          background: #ffffff;
        }
      }
    }
::v-deep .table thead
{
  background: #e5e5e5;
}
::v-deep td {
      border: 0.1px solid #888888;
      font-size: 14px;
      padding: 0.75rem 0.45rem;
      line-height: 32px;
      white-space: nowrap;
}
::v-deep  .table th, .table td {
    border: 0.1px solid #888888;
}
 th {
  border: 0.9px solid #888888;
  background-position: right calc(0.75rem / 2) center !important;

}
::v-deep th > div {
  white-space: nowrap;
  letter-spacing: 1.2px;
}
::v-deep .custom-select {
  border: 1px solid #ced4da !important;
}
::v-deep .bi-search {
  position: absolute;
  top: 12px;
  right: 8px;
  font-size: 16px;
  cursor: pointer;
}

.input-name-search {
  position: relative;
}
::v-deep button.btn.btn-sm {
  width: auto;
}
.custom-select:focus {
  box-shadow: none !important;
}
::v-deep .page-link {
  padding: 3px 10px;
}
::v-deep .page-link:hover {
  border: 1px solid #0f68b1 !important;
}
::v-deep .b-form-btn-label-control.form-control > .form-control {
  font-size: 14px !important;
}

::v-deep select:required:invalid {
  color: #111111;
}
::v-deep  option[value=""][disabled] {
 display: none !important;
  color: blue !important;
}
::v-deep option {
  color: black;
}
::v-deep .custom-select {
  color: #6b727a !important;
}
.container-fluid {
  width: 80%;
  margin: auto;
}

</style>
