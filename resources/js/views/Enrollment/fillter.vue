<template>
  <div class="col-md-12 p-5 py-0 form-group">
    <b-form border="none" @submit="onSubmit($event)">
      <div class="form-header">
        <h2 class="pt-2 form-group-filter">
          <span class="pr-1" :class="roleId"><b-icon icon="chevron-down" /></span>{{ $t('LANGUAGES.TEXT_FILTER') }}
        </h2>
        <h3 class="pt-2" @click="clearAll">{{ $t('LANGUAGES.TEXT_CLEAR_ALL') }}</h3>
        <div class="py-5">
          <!-- 1 -->
          <b-form-group v-if="!access || roleId === headQuarter" id="input-group-1">
            <b-row>
              <b-col md="3">
                <b-form-checkbox
                  id="cbInterviewDate"
                  v-model="statusInterviewDate"
                  size="lg"
                  value="accepted"
                  unchecked-value="not_accepted"
                >
                  <span class="label-name">{{ access ? $t('LANGUAGES.TEXT_COMPANY_BRANCH') : $t('LANGUAGES.TEXT_INTERVIEW_DATE') }}</span>
                </b-form-checkbox>
              </b-col>
              <b-col
                v-show="
                  statusInterviewDate === 'accepted' ? true : false
                "
                class="interview-status"
                md="3"
              >
                <b-form-datepicker
                  v-if="!access"
                  id="startDate"
                  v-model="queryData.start_date"
                  locale="ja"
                  :placeholder="$t('LANGUAGES.TEXT_NO_DATA_SELECTED_FIRST')"
                  name="date-picker"
                  :state="error.start_date"
                  @input="handleChangeForm($event, 'start_date')"
                />
                <b-form-invalid-feedback v-if="!access" :state="error.start_date">
                  Please Select Check Date
                </b-form-invalid-feedback>
                <b-form-select
                  v-if="access"
                  id="company"
                  v-model="queryData2.company"
                  dusk="company"
                  :options="companyOption"
                />
              </b-col>
              <span
                v-show="
                  statusInterviewDate === 'accepted' ? true : false
                "
                v-if="!access"
                class="fs-20"
              >~</span>
              <b-col
                v-show="
                  statusInterviewDate === 'accepted' ? true : false
                "
                md="3"
              >
                <b-form-datepicker
                  v-if="!access"
                  id="endDate"
                  v-model="queryData.end_date"
                  locale="ja"
                  :state="error.end_date"
                  :placeholder="$t('LANGUAGES.TEXT_NO_DATA_SELECTED_SECOND')"
                  @input="handleChangeForm($event, 'end_date')"
                />
                <b-form-invalid-feedback v-if="!access" :state="error.end_date">
                  Please Select Check Date
                </b-form-invalid-feedback>
              </b-col>
            </b-row>
          </b-form-group>
          <!-- 2 -->
          <b-form-group id="input-group-2" class="show-by-role">
            <b-row>
              <b-col md="3">
                <b-form-checkbox
                  v-if="!access && roleId === headQuarter"
                  id="cbInterviewDepature"
                  v-model="statusInterviewDeparture"
                  dusk="statusDepature"
                  size="lg"
                  value="accepted"
                  unchecked-value="not_accepted"
                >
                  <span class="label-name">{{ access ? $t('LANGUAGES.TEXT_EMPLOYEE_CODE') : $t('LANGUAGES.TEXT_INTERVIEW_DEPARTURE') }}</span>
                </b-form-checkbox>
                <b-form-checkbox
                  v-if="access"
                  id="cbInterviewDepature"
                  v-model="statusInterviewDeparture"
                  dusk="statusDepature"
                  size="lg"
                  value="accepted"
                  unchecked-value="not_accepted"
                >
                  <span class="label-name">{{ access ? $t('LANGUAGES.TEXT_EMPLOYEE_CODE') : $t('LANGUAGES.TEXT_INTERVIEW_DEPARTURE') }}</span>
                </b-form-checkbox>
              </b-col>
              <b-col
                v-show="
                  statusInterviewDeparture === 'accepted' ? true : false
                "
                class="interview-departure-status"
                md="3"
              >
                <div v-if="!access && roleId === headQuarter">
                  <b-form-select
                    v-model="queryData.company_branch_id"
                    :options="interviewDeparuteOption"
                  />
                </div>
                <div v-if="access">
                  <b-input-group>
                    <b-form-input
                      id="userCode"
                      v-model="queryData2.user_code"
                      dusk="userCode"
                      :placeholder="$t('LANGUAGES.TEXT_PLACEHOLDER_ENTER_EMPLOYEE_NAME')"
                    />
                  </b-input-group>
                </div>
              </b-col>
            </b-row>
          </b-form-group>
          <!-- 3 -->
          <b-form-group id="input-group-3">
            <b-row>
              <b-col md="3">
                <b-form-checkbox
                  id="cbCandidateName"
                  v-model="statusCandidateName"
                  dusk="statusCandidateName"
                  size="lg"
                  value="accepted"
                  unchecked-value="not_accepted"
                >
                  <span class="label-name">{{ access ? $t('LANGUAGES.TEXT_EMPLOYEE_NAME') : $t('LANGUAGES.TEXT_CANDIDATE_NAME') }}</span>
                </b-form-checkbox>
              </b-col>
              <b-col
                v-show="
                  statusCandidateName === 'accepted' ? true : false
                "
                class="candidate-name-status"
                md="3"
              >
                <div v-if="!access" class="input-name-search">
                  <b-input-group>
                    <b-form-input
                      v-model="queryData.candidate_name"
                      type="search"
                      dusk="candidate_name"
                      :placeholder="access ? $t('LANGUAGES.TEXT_PLACEHOLDER_ENTER_EMPLOYEE_NAME') : $t('LANGUAGES.TEXT_PLACEHOLDER_ENTER_CANDIDATE_NAME')"
                      :formatter="formatText"
                    />
                  </b-input-group>
                  <b-icon icon="search" />
                </div>
                <div v-if="access">
                  <b-input-group>
                    <b-form-input
                      id="userName"
                      v-model="queryData2.user_name"
                      dusk="userName"
                      :placeholder="access ? $t('LANGUAGES.TEXT_PLACEHOLDER_ENTER_EMPLOYEE_NAME') : $t('LANGUAGES.TEXT_PLACEHOLDER_ENTER_CANDIDATE_NAME')"
                      :formatter="formatText"
                    />
                  </b-input-group>
                  <b-icon icon="search" class="icon-search-employee" />
                </div>
              </b-col>
            </b-row>
          </b-form-group>
          <b-button variant="secondary" type="submit" class="text-uppercase btn-apply">{{ $t('LANGUAGES.TEXT_BUTTON_APPLY') }} </b-button>
        </div>
      </div>
    </b-form>
    <div v-if="!access" class="Table">
      <b-table
        id="my-table"
        class="text-center w-100 bg-dx-grey-blur mb-0"
        :items="listEnrollment"
        :fields="fields1"
        responsive="sm"
        :current-page="queryData.page"
        show-empty
        :no-local-sorting="noSort"
        @sort-changed="sortingChanged"
      >
        <template v-if="!access" #cell(result)="result">
          <b-button
            class="bg-dx-blue fs-14 btn-result"
            @click="nextToResult(result.item.id)"
          ><b-icon icon="file-bar-graph" />{{ $t('LANGUAGES.TEXT_RESULT') }}</b-button>
        </template>
        <template v-if="!access" #cell(pdf)="pdf">
          <b-button
            class="bg-dx-orange btn btn-pdf fs-14"
            @click="openModalPDF(pdf.item.id)"
          ><b-icon icon="download" /> {{ $t('LANGUAGES.TEXT_PDF') }}</b-button>
        </template>
        <template #empty="">
          {{ $t('LANGUAGES.TEXT_NO_DATA') }}
        </template>
      </b-table>
    </div>
    <div v-if="access" class="Table">
      <b-table
        id="my-table"
        class="text-center w-100 bg-dx-grey-blur mb-0"
        :items="listEmployee"
        :fields="fields2"
        responsive="sm"
        :current-page="queryData2.page"
        show-empty
      >
        <template #empty="">
          {{ $t('LANGUAGES.TEXT_NO_DATA') }}
        </template>
      </b-table>
    </div>
    <div class="card-body pagianation d-flex justify-content-center">
      <b-pagination
        v-if="!access"
        v-model="queryData.page"
        :per-page="queryData.per_page"
        :total-rows="queryData.total_records"
        aria-controls="my-table"
      />
    </div>
  </div>
</template>
<script>
import * as EnrollmentApi from '../../api/enrollment';
import { MakeToast } from '../../utils/toast_message';
import moment from 'moment';
import * as CONFIGS from '../../configs/index';
import * as CompanyBranchApi from '../../api/company_branch';
import * as CONVERT from '../../utils/convert';
export default {
  name: 'FillterList',
  props: { access: { type: String, default: () => {
    return;
  }, require: false }, id: { type: Number, default: () => {
    return;
  }, require: false }},
  data() {
    return {
      roleId: this.$store.getters.role_id,
      statusInterviewDate: 'not_accepted',
      statusInterviewDeparture: 'not_accepted',
      statusCandidateName: 'not_accepted',
      selected: null,
      headQuarter: CONFIGS.UserRoleId.HEAD_QUARTER,
      noSort: true,
      error: {
        start_date: null,
        end_date: null,
      },
      interviewDeparuteOption: [],
      companyOption: [],
      queryData: {
        page: 1,
        per_page: 20,
        total_records: 0,
        candidate_name: '',
        company_branch_id: '',
        start_date: '',
        end_date: '',
        column_name: '',
        sort: '',
      },
      queryData2: {
        page: 1,
        per_page: 20,
        company: '',
        user_code: '',
        user_name: '',
        total_records: 0,
        column_name: '',
        sort: '',
      },
      fields1: [
        { key: 'interview_date', sortable: true, label: this.$t('LANGUAGES.TEXT_INTERVIEW_DATE'), class: 'interview_date' },
        { key: 'company_branchs', sortable: true, label: this.$t('LANGUAGES.TEXT_INTERVIEW_DEPARTURE'), class: 'company_branchs' },
        { key: 'candidate_name', sortable: true, label: this.$t('LANGUAGES.TEXT_CANDIDATE_NAME'), class: 'candidate_name' },
        { key: 'joining_age', sortable: true, label: this.$t('LANGUAGES.TEXT_JOINING_AGE'), class: 'joining_age' },
        { key: 'spouse', sortable: true, label: this.$t('LANGUAGES.TEXT_SPOUSE'), class: 'spouse' },
        { key: 'dependents', sortable: true, label: this.$t('LANGUAGES.TEXT_DEPENDENTS'), class: 'dependent' },
        {
          key: 'worked_years',
          sortable: true,
          label: this.$t('LANGUAGES.TEXT_WORKED_YEARS'),
          class: 'worked_years',
        },
        { key: 'final_education', sortable: true, label: this.$t('LANGUAGES.TEXT_EDUCATION'), class: 'final_education' },
        { key: 'shortest_service', sortable: true, label: this.$t('LANGUAGES.TEXT_SHORTEST_SERVICE'), class: 'shortest_service' },
        { key: 'result', sortable: true, label: this.$t('LANGUAGES.TEXT_RESULT'), class: 'result' },
        { key: 'pdf', sortable: true, label: this.$t('LANGUAGES.TEXT_PDF'), class: 'pdf' },
      ],
      fields2: [
        { key: 'diference_point', sortable: true, label: this.$t('LANGUAGES.TEXT_DIFFERENCE_POINT'), class: 'th-header' },
        { key: 'employee_code', sortable: true, label: this.$t('LANGUAGES.TEXT_EMPLOYEE_CODE'), class: 'th-header' },
        { key: 'employee_name', sortable: true, label: this.$t('LANGUAGES.TEXT_EMPLOYEE_NAME'), class: 'th-header' },
        { key: 'company.name', sortable: true, label: this.$t('LANGUAGES.TEXT_COMPANY_BRANCH'), class: 'th-header' },
        {
          key: 'worked_year',
          sortable: true,
          label: this.$t('LANGUAGES.TEXT_WORKED_YEARS'),
          class: 'th-header',
        },
        { key: 'date_joining_company', sortable: true, label: this.$t('LANGUAGES.TEXT_DATE_JOINING_COMPANY'), class: 'th-header' },
        { key: 'date_out_company', sortable: true, label: this.$t('LANGUAGES.TEXT_DATE_OUT_COMPANY'), class: 'th-header' },
        { key: 'joining_age_company', sortable: true, label: this.$t('LANGUAGES.TEXT_JOINING_AGE_COMPANY'), class: 'th-header' },
        { key: 'oldString', sortable: true, label: this.$t('LANGUAGES.TEXT_EVALUATE'), class: 'th-header' },
        { key: 'spouse', sortable: true, label: this.$t('LANGUAGES.TEXT_SPOUSE'), class: 'th-header' },
        { key: 'marriedString', sortable: true, label: this.$t('LANGUAGES.TEXT_EVALUATE'), class: 'th-header' },
        { key: 'dependents', sortable: true, label: this.$t('LANGUAGES.TEXT_DEPENDENTS'), class: 'th-header' },
        { key: 'dependentString', sortable: true, label: this.$t('LANGUAGES.TEXT_EVALUATE'), class: 'th-header' },
        { key: 'total_worked', sortable: true, label: this.$t('LANGUAGES.TEXT_TOTAL_WORKED'), class: 'th-header' },
        { key: 'companyString', sortable: true, label: this.$t('LANGUAGES.TEXT_EVALUATE'), class: 'th-header' },
        { key: 'final_education', sortable: true, label: this.$t('LANGUAGES.TEXT_EDUCATION'), class: 'th-header' },
        { key: 'educationString', sortable: true, label: this.$t('LANGUAGES.TEXT_EVALUATE'), class: 'th-header' },
        { key: 'shortest_service', sortable: true, label: this.$t('LANGUAGES.TEXT_SHORTEST_SERVICE'), class: 'th-header' },
        { key: 'rangeWorkString', sortable: true, label: this.$t('LANGUAGES.TEXT_EVALUATE'), class: 'th-header' },
        { key: 'timePrediction', sortable: true, label: this.$t('LANGUAGES.TEXT_TIME_PREDICTION'), class: 'th-header' },
        { key: 'overallReview', sortable: true, label: this.$t('LANGUAGES.TEXT_OVERALL_REVIEW'), class: 'th-header' },
      ],
    };
  },
  computed: {
    listEnrollment() {
      return this.$store.getters.listEnrollment;
    },
    // roleId() {
    //   return this.$store.getters.role_id;
    // },
    // Query data of List Enrollment
    currChange() {
      return this.queryData.page;
    },
    // Query data of Result Enrollment
    pageChange() {
      return this.queryData2.page;
    },
    listEmployee() {
      return this.$store.getters.listEmployee;
    },
    companyBranch() {
      return this.$store.getters.listBranch;
    },
    branchUser() {
      return this.$store.getters.branchUser;
    },
  },
  watch: {
    currChange() {
      this.getAllEnrollment();
    },
    listEmployee() {
      if (this.listEmployee.length > 0) {
        this.queryData2.total_records = this.listEmployee.total;
      }
    },
    pageChange() {
      // console.log('Ban vua chon', this.queryData2.page);
      this.getCandidateResultInfo();
    },
    statusInterviewDate() {
      if (this.statusInterviewDate === 'not_accepted'){
        this.queryData2.company = '';
        this.queryData.start_date = '';
        this.queryData.end_date = '';
      }
    },
    statusInterviewDeparture() {
      if (this.statusInterviewDeparture === 'not_accepted'){
        this.queryData2.user_code = '';
        this.queryData.company_branch_id = '';
      }
    },
    statusCandidateName() {
      if (this.statusCandidateName === 'not_accepted'){
        this.queryData2.user_name = '';
        this.queryData.candidate_name = '';
      }
    },
    companyBranch() {
      this.companyOption.push({ value: '', text: '面接拠点を入力してください', disabled: true });
      this.interviewDeparuteOption.push({ value: '', text: '面接拠点を入力してください', disabled: true });
      for (let i = 0; i < this.companyBranch.length; i++) {
        this.interviewDeparuteOption.push({ value: this.companyBranch[i].id, text: this.companyBranch[i].name });
        this.companyOption.push({ value: this.companyBranch[i].id, text: this.companyBranch[i].name });
      }
    },
    branchUser() {
      this.interviewDeparuteOption.push({ value: this.branchUser.id, text: this.branchUser.name });
      this.companyOption.push({ value: this.branchUser.id, text: this.branchUser.name });
    },
  },
  created() {
    if (!this.access){
      this.getAllEnrollment();
    }
    if (this.roleId === this.headQuarter) {
      this.getAllCompanyBranch();
    } else {
      this.getCompanyBranchByUser();
    }
  },
  methods: {
    async getAllEnrollment(e) {
      if (e){
        e.preventDefault();
      }
      this.checkValidate();
      if (this.checkValidate() === true) {
        const body = { ...this.queryData };

        [
          'page',
          'per_page',
          'candidate_name',
          'company_branch_id',
          'start_date',
          'end_date',
          'column_name',
          'sort',
        ].forEach((element) => {
          if (body[element] === '') {
            delete body[element];
          }
        });
        delete body.total_records;
        this.openLoading();
        await EnrollmentApi.getAllEnrollment(body)
          .then((response) => {
          // console.log('Data return', response);
            if (response.code === 200) {
              MakeToast({
                variant: 'success',
                title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
              // content: 'Get All Enrollment Successfull !',
              });
              if (response.data) {
                this.queryData.total_records =
                response.data.pagination.total_records;
                const listEnrollment = response.data.result;
                listEnrollment.forEach((element) => {
                  element.interview_date = moment(element.interview_date).format(
                    'YYYY/MM/DD'
                  );
                  element.spouse = CONVERT.convertSpouse(element.spouse);
                  element.company_branchs = element.company_branchs.name;
                  element.final_education = this.renderEducationName(
                    element.final_education);
                });
                // console.log('After format', listEnrollment);
                this.$store.dispatch('app/saveListEnrollment', listEnrollment);
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
      } else {
        e.preventDefault();
        e.stopPropagation();
      }
    },
    // Get Enrollment Info
    async getCandidateResultInfo(e) {
      if (e){
        e.preventDefault();
      }
      const body = { ...this.queryData2 };
      [
        'page',
        'per_page',
        'company',
        'user_code',
        'user_name',
        'column_name',
        'sort',
      ].forEach(
        (element) => {
          if (body[element] === '') {
            delete body[element];
          }
          delete body.total_records;
        }
      );
      // console.log('Body =>>>', body);
      this.openLoading();

      // console.log('Da vao duoc method')

      await EnrollmentApi.getCandidateResult(this.id, body)
        .then((response) => {
          // console.log('Da goi duoc API thanhc gon')
          //   console.log('Data return', response);
          if (response.code === 200) {
            MakeToast({
              variant: 'success',
              title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
              content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_GET_CANDIDATE_RESULT_SUCCESSFULLY'),
            });
            if (response.data) {
              // console.log('Data', response.data);
              const dataReturn = response.data;
              // STORE EVALUATION
              const evaluation = dataReturn.config;
              this.$store.dispatch('app/saveListEvaluation', evaluation);
              // STORE INFO Enrollment Prediction
              const listInfo = response.data.enrollment;
              listInfo.interviewDate = moment(
                response.data.enrollment.interview_date
              ).format('YYYY/MM/DD');
              listInfo.companyBranch =
                response.data.enrollment.company_branchs.name;
              listInfo.candidateName = response.data.enrollment.candidate_name;
              this.$store.dispatch('app/saveListInfo', listInfo);
              // STORE Table
              // this.queryData.total_records =
              //   response.data.pagination.total_records;
              const listEmployee = response.data.users;
              listEmployee.forEach((element) => {
                element.date_joining_company = moment(
                  element.date_joining_company
                ).format('YYYY/MM/DD');
                element.date_out_company = element.date_out_company ? moment(element.date_out_company).format(
                  'YYYY/MM/DD'
                ) : '-';
                element.spouse = CONVERT.convertSpouse(element.spouse);
                element.final_education = CONVERT.renderEducationName(
                  element.final_education
                );
                element.diference_point = parseInt(element.diference_point);
                element.timePrediction = parseFloat(element.timePrediction).toFixed(1);
                element.employee_name = element.employee_name ? element.employee_name : '-';
                element.joining_age_company = element.joining_age_company ? element.joining_age_company : '-';
              });
              // console.log('After format', listEmployee);
              this.$store.dispatch('app/saveListEmployee', listEmployee);
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
      if (!this.access){
        this.getAllEnrollment($event);
      } else {
        this.getCandidateResultInfo($event);
      }
    },
    openLoading() {
      this.$store.dispatch('loading/setLoading', true);
    },
    closeLoading() {
      this.$store.dispatch('loading/setLoading', false);
    },
    clearAll() {
      this.statusInterviewDate = 'not_accepted';
      this.statusInterviewDeparture = 'not_accepted';
      this.statusCandidateName = 'not_accepted';
    },
    openModalPDF(id) {
      this.$emit('handleChange', id);
    },
    nextToResult(id) {
      if (id && id !== '') {
        this.$router.push({ path: `/enrollment/result/${id}` }, (onAbort) => {});
      }
    },
    renderEducationName(education) {
      switch (education) {
        case 1:
          return this.$t('LANGUAGES.TEXT_PRIMARY_SCHOOL');
        case 2:
          return this.$t('LANGUAGES.TEXT_JUNIOR_HIGH_SCHOOL');
        case 3:
          return this.$t('LANGUAGES.TEXT_HIGH_SCHOOL');
        case 4:
          return this.$t('LANGUAGES.TEXT_SKILL_SCHOOL');
        case 5:
          return this.$t('LANGUAGES.TEXT_INTERMEDIATE');
        case 6:
          return this.$t('LANGUAGES.TEXT_COLLEGE');
        case 7:
          return this.$t('LANGUAGES.TEXT_UNIVERSITY');
        default:
      }
    },
    async getAllCompanyBranch() {
      await CompanyBranchApi.getAllCompanyBranch()
        .then((response) => {
          if (response.code === 200) {
            // console.log('listCompany===>', response);
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
    handleChangeForm(event, field) {
      // const newValue = event;
      // console.log('Gia tri b vua chon', newValue);
      // console.log('O ban vua chon', field);
      switch (field) {
        case 'start_date':
          if (this.queryData.end_date !== '' && this.queryData.start_date !== '' && moment(this.queryData.start_date).format('X') > moment(this.queryData.end_date).format('X')) {
            this.error.start_date = false;
          } else {
            this.error.start_date = true;
            this.error.end_date = true;
          }
          break;
        case 'end_date':
          if (this.queryData.end_date !== '' && this.queryData.start_date !== '' && moment(this.queryData.end_date).format('X') < moment(this.queryData.start_date).format('X')) {
            this.error.end_date = false;
          } else {
            this.error.end_date = true;
            this.error.start_date = true;
          }
          break;
        default:
          break;
      }
    },
    checkValidate() {
      if (this.error.start_date !== false && this.error.end_date !== false) {
        return true;
      } else {
        return false;
      }
    },
    formatText(e) {
      return String(e).substring(0, 255);
    },
    sortingChanged(ctx) {
      this.queryData.column_name = ctx.sortBy === 'company.name' ? 'company_branch' : ctx.sortBy;
      this.queryData.sort = ctx.sortDesc;
      this.getAllEnrollment();
    },
  },
};
</script>
<style lang="scss" scoped>

.basic {
  border-left: 10px solid orange;
  left: 20px;
  position: absolute;
}
.Table {
width: 100%;
   overflow-x: scroll !important;
   cursor: pointer;
}
.Table::-webkit-scrollbar-track
{
	-webkit-box-shadow: inset 0 0 2px rgba(0,0,0,0.3);
	border-radius: 10px;
	background-color: #F5F5F5;
}

.Table::-webkit-scrollbar
{
	background-color: #F5F5F5;
  height: 7px;
}

.Table::-webkit-scrollbar-thumb
{
	border-radius: 10px;
	-webkit-box-shadow: inset 0 0 2px rgba(61, 61, 61, 0.3);
	background-color: #989898;
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
  background-color: darkorange;
  border: none;
    &:hover {
    background: gold;
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
        top: 17px;
        left: 110px;
        background: #fff;
        padding: 0px 8px;
      }
    }
.form-header {
    border: 2px solid #888888;
    border-left: 0;
    border-right: 0;
    padding: 50px 0px;
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
}
::v-deep  .table th, .table td {
    border: 0.1px solid #888888;
}
 th {
  border: 0.9px solid #888888;
  background-position: right calc(0.75rem / 2) center !important;

}
::v-deep th > div {
  width: 150px;
  letter-spacing: 1.2px;
  white-space: nowrap;
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
// ::v-deep select:first-child:disabled {
//   color: #6f737c;
// }
// ::v-deep option {
//   color: #111111;
// }
// ::v-deep option[value=""][disabled] {
//   display: none !important;
//   color: #6f737c;
// }
// select:required:invalid { color: #6f737c; }

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

::v-deep .icon-search-employee {
  position: absolute;
    top: 11px !important;
    right: 24px !important;
    font-size: 16px;
    cursor: pointer;
}

</style>
