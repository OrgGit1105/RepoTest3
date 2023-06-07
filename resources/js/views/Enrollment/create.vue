/* eslint-disable vue/max-attributes-per-line */
<template>
  <div>
    <div class="container-fluid">
      <div class="container-fluid-body mt-5 mb-5">
        <div
          class="text-white text-center"
        >
          <h1 class=" p-3 header-title">{{ $t('LANGUAGES.TEXT_ENROLLMENT_PREDICTION') }}</h1>
        </div>
        <div class="body">
          <b-col xl="12" lg="12" md="12" sm="12">
            <div class="justify-content-start py-5">
              <div class="basic pl-4">
                <h1 class="pl-3 text-dx-blue-light-2 title-info">{{ $t('LANGUAGES.TEXT_BASIC_ITEM') }}</h1>
              </div>
            </div>
            <b-form class="py-5 px-4" @submit="onSubmit($event)">
              <div>
                <!-- 1 -->
                <b-form-group>
                  <b-row>
                    <b-col lg="3">
                      <label class="label-name">{{ $t('LANGUAGES.TEXT_INTERVIEW_DATE') }}</label>
                    </b-col>
                    <b-col lg="9">
                      <b-form-datepicker
                        id="txtInterViewDate"
                        v-model="form.interview_date"
                        locale="ja"
                        :state="error.interview_date"
                        placeholder="Interview Date"
                        @input="handleChangeForm($event, 'interview_date')"
                      />
                      <b-form-invalid-feedback :state="error.interview_date">
                        {{ $t('LANGUAGES.TEXT_PLEASE_SELECT_CHECK_DATE') }}
                      </b-form-invalid-feedback>
                    </b-col>
                  </b-row>
                </b-form-group>
                <!-- 2 -->
                <b-form-group>
                  <b-row>
                    <b-col lg="3">
                      <label class="label-name">{{ $t('LANGUAGES.TEXT_INTERVIEW_DEPARTURE') }}</label>
                    </b-col>
                    <b-col lg="9">
                      <b-form-select
                        id="txtInterViewDeparute"
                        v-model="form.company_branch_id"
                        dusk="company_branch_id"
                        :state="error.company_branch_id"
                        placeholder="Interview Deparute"
                        :options="interviewDeparuteOption"
                        @change="handleChangeForm($event, 'company_branch_id')"
                      />
                      <b-form-invalid-feedback :state="error.company_branch_id">
                        {{ $t('LANGUAGES.ERROR_PLEASE_SELECT_INTERVIEW_BRANCH') }}
                      </b-form-invalid-feedback>
                    </b-col>
                  </b-row>
                </b-form-group>
                <!-- 3 -->
                <b-form-group>
                  <b-row>
                    <b-col lg="3">
                      <label class="label-name">{{ $t('LANGUAGES.TEXT_CANDIDATE_NAME') }}</label>
                    </b-col>
                    <b-col lg="9">
                      <b-form-input
                        id="txtCandidateName"
                        v-model="form.candidate_name"
                        dusk="candidate_name"
                        :state="error.candidate_name"
                        :placeholder="$t('LANGUAGES.TEXT_PLACEHOLDER_CANDIDATE_NAME')"
                        :formatter="formatText"
                        @input="handleChangeForm($event, 'candidate_name')"
                      />
                      <b-form-invalid-feedback :state="error.candidate_name">
                        {{ $t('LANGUAGES.ERROR_PLEASE_INPUT_CANDIDATE_NAME') }}
                      </b-form-invalid-feedback>
                    </b-col>
                  </b-row>
                </b-form-group>

                <div class="justify-content-start py-5">
                  <div class="basic">
                    <h1 class="pl-3 title-info text-dx-blue-light-2">{{ $t('LANGUAGES.TEXT_PREDICTION_ITEM') }}</h1>
                  </div>
                  <p class="mt-4 notification-field">{{ $t('LANGUAGES.TEXT_NOTIFICATION_ENROLLMENT_CREATE') }}</p>
                </div>
                <!-- 4 -->
                <b-form-group class="prediction-item mt-0">
                  <b-row>
                    <b-col lg="3">
                      <label
                        class="label-name"
                      >{{ $t('LANGUAGES.TEXT_AGE_AT_TIME_OF_JOINING_THE_COMPANY') }}</label>
                    </b-col>
                    <b-col lg="9">
                      <b-form-input
                        id="txtAgeJoining"
                        v-model="form.joining_age"
                        dusk="joining_age"
                        type="text"
                        onkeydown="javascript: return event.keyCode === 8 || event.keyCode === 46 ? true : !isNaN(Number(event.key))"
                        :placeholder="$t('LANGUAGES.TEXT_PLACEHOLDER_AGE_AT_TIME_OF_JOINING_THE_COMPANY')"
                        :formatter="formatNumber"
                      />
                    </b-col>
                  </b-row>
                </b-form-group>
                <!-- 5 -->
                <b-form-group>
                  <b-row>
                    <b-col lg="3">
                      <label class="label-name">{{ $t('LANGUAGES.TEXT_SPOUSE') }}</label>
                    </b-col>
                    <b-col lg="9">
                      <b-form-select
                        id="txtSpouse"
                        v-model="form.spouse"

                        dusk="spouse"
                        :options="spouse"
                        :placeholder="$t('LANGUAGES.TEXT_PLACEHOLDER_CANDIDATE_NAME')"
                      />

                    </b-col>
                  </b-row>
                </b-form-group>

                <!-- 6 -->
                <b-form-group>
                  <b-row>
                    <b-col lg="3">
                      <label class="label-name">{{ $t('LANGUAGES.TEXT_DEPENDENTS') }}</label>
                    </b-col>
                    <b-col lg="9">
                      <b-form-select
                        id="txtDependents"
                        v-model="form.dependents"

                        dusk="dependents"
                        :options="dependents"
                        :placeholder="$t('LANGUAGES.TEXT_SELECT_OPTION_DEPENDENT')"
                      />

                    </b-col>
                  </b-row>
                </b-form-group>
                <!-- 7 -->
                <b-form-group>
                  <b-row>
                    <b-col lg="3">
                      <label class="label-name">{{ $t('LANGUAGES.TEXT_NUMBER_OF_WORK_HISTORY') }}</label>
                    </b-col>
                    <b-col lg="9">
                      <b-form-select
                        id="txtWordHistory"
                        v-model="form.worked_years"

                        dusk="worked_years"
                        :options="numberofworkhistory"
                        placeholder="NUMBER OF WORK HISTORY"
                      />

                    </b-col>
                  </b-row>
                </b-form-group>
                <!-- 8 -->
                <b-form-group>
                  <b-row>
                    <b-col lg="3">
                      <label class="label-name">{{ $t('LANGUAGES.TEXT_FINAL_EDUCATION') }}</label>
                    </b-col>
                    <b-col lg="9">
                      <b-form-select
                        id="txtFinalEducation"
                        v-model="form.final_education"

                        dusk="final_education"
                        :placeholder="$t('LANGUAGES.TEXT_SELECT_OPTION_FINAL_EDUCATION')"
                        :options="finalEducation"
                      />
                    </b-col>
                  </b-row>
                </b-form-group>
                <!-- 9 -->
                <b-form-group>
                  <b-row>
                    <b-col lg="3">
                      <label class="label-name">{{ $t('LANGUAGES.TEXT_SHORTEST_SERVICE_PERIOD') }}</label>
                    </b-col>
                    <b-col lg="9">
                      <b-form-input
                        id="txtShortest"
                        v-model="form.shortest_service"
                        dusk="shortest_service"
                        :placeholder="$t('LANGUAGES.TEXT_PLACEHOLDER_SHORTEST_SERVICE_PERIOD')"
                        type="text"
                        onkeydown="javascript: return event.keyCode === 8 || event.keyCode === 46 ? true : !isNaN(Number(event.key))"
                        :formatter="formatNumber"
                      />
                    </b-col>
                  </b-row>
                </b-form-group>
              </div>
              <div class="d-flex justify-content-center pt-4">
                <div class="save-simulation">
                  <b-form-checkbox
                    id="checkbox-1"
                    v-model="form.is_accepted"
                    class="cursor-pointer"
                    size="lg"
                    value="accepted"
                    unchecked-value="not_accepted"
                  >
                    <p class="cursor-pointer">{{ $t('LANGUAGES.TEXT_CHECKBOX_SAVE_THIS_SIMULATION_DATA') }}</p>
                  </b-form-checkbox>
                </div>
              </div>
              <div class="d-flex justify-content-center py-4">
                <b-button
                  class="fs-18 btn btn-simulation text-uppercase"
                  type="submit"
                  @submit="onSubmit($event)"
                >{{ $t('LANGUAGES.TEXT_BUTTON_SIMULATION') }}</b-button>
              </div>
            </b-form>
          </b-col>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import * as CONFIGS from '../../configs';
import * as EnrollmentApi from '../../api/enrollment';
import * as CompanyBranchApi from '../../api/company_branch';
import { MakeToast } from '../../utils/toast_message';
import moment from 'moment';
// import * as Regexr from '../../utils/regexr';
export default {
  name: 'EnrollmentCreate',
  data() {
    return {
      id: '',
      headQuarter: CONFIGS.UserRoleId.HEAD_QUARTER,
      form: {
        interview_date: new Date(),
        company_branch_id: '',
        candidate_name: '',
        joining_age: '',
        spouse: '',
        dependents: '',
        worked_years: '',
        final_education: '',
        shortest_service: '',
        is_accepted: 'accepted',
      },
      interviewDeparuteOption: [{ value: '', text: '拠点を入力してください', disabled: true }],
      spouse: CONFIGS.SpouseOption,
      dependents: CONFIGS.NumberDependentsOption,
      numberofworkhistory: CONFIGS.NumberOfWorkHistoryOption,
      finalEducation: CONFIGS.FinalEducationOption,
      error: {
        interview_date: null,
        company_branch_id: null,
        candidate_name: null,
      },
    };
  },
  computed: {
    companyBranch() {
      return this.$store.getters.listBranch;
    },
    roleId() {
      return this.$store.getters.role_id;
    },
    branchUser() {
      return this.$store.getters.branchUser;
    },
  },
  watch: {
    companyBranch() {
      // this.interviewDeparuteOption.push({ value: '', text: '拠点を入力してください', disabled: true });
      for (let i = 0; i < this.companyBranch.length; i++) {
        this.interviewDeparuteOption.push({ value: this.companyBranch[i].id, text: this.companyBranch[i].name });
      }
      // console.log('interviewDeparuteOption', this.companyBranch);
    },
    branchUser() {
      this.interviewDeparuteOption.push({ value: this.branchUser.id, text: this.branchUser.name });
    },
  },
  created() {
    this.closeLoading();
    if (this.roleId === this.headQuarter) {
      this.getAllCompanyBranch();
    } else {
      this.getCompanyBranchByUser();
    }
  },
  methods: {
    checkValidate() {
      if (!this.form.interview_date) {
        this.error.interview_date = false;
      } else {
        this.error.interview_date = true;
      } if (!this.form.company_branch_id) {
        this.error.company_branch_id = false;
      } if (!this.form.candidate_name) {
        this.error.candidate_name = false;
      } if (Object.keys(this.error).every((k) => this.error[k] === false)) {
        return false;
      } else {
        if (Object.keys(this.error).every((k) => this.error[k] === true)) {
          return true;
        }
      }
    },
    handleChangeForm(event, field, type) {
      let newValue = event;
      if (type === 'number') {
        // console.log('Da chay vao day', newValue);
        newValue = newValue.replace(/\D/g, '');
      }
      switch (field) {
        case 'interview_date':
          if (newValue.length > 0) {
            this.error.interview_date = true;
          } else {
            this.error.interview_date = false;
          }
          break;
        case 'company_branch_id':
          if (newValue.length === 0) {
            this.error.company_branch_id = false;
          } else {
            this.error.company_branch_id = true;
          }
          break;
        case 'candidate_name':
          if (newValue.length > 0) {
            this.error.candidate_name = true;
          } else {
            this.error.candidate_name = false;
          }
          break;
        default:
          break;
      }
    },
    onSubmit(e) {
      e.preventDefault();
      this.checkValidate();
      if (this.checkValidate() === true) {
        // console.log('Manhhhhhh1', this.form.interview_date);
        // console.log('Manhhhhhh2', this.checkValidate() === true);
        this.form.interview_date = moment(this.form.interview_date).format('YYYY/MM/DD');
        this.openLoading();
        console.log('Data gui di', this.form);
        EnrollmentApi.createEnrollment(this.form)
          .then((response) => {
            // console.log('Response', response);
            if (response.code === 200) {
              MakeToast({
                variant: 'success',
                title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
                content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_CREATE_ENROLLMENT_SUCCESSFULLY'),
              });
              if (response.data){
                // console.log('Response', response.data);
                this.id = response.data.id;
                this.nextToResult(this.id);
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
        // this.form.is_accepted = 'not_accepted';
        e.preventDefault();
        e.stopPropagation();
      }
    },
    openLoading() {
      this.$store.dispatch('loading/setLoading', true);
    },
    closeLoading() {
      this.$store.dispatch('loading/setLoading', false);
    },
    nextToResult(id) {
      if (id && id !== '') {
        this.$router.push({ path: `/enrollment/result/${id}` }, (onAbort) => {});
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
    formatText(e) {
      return String(e).substring(0, 255);
    },
    formatNumber(e) {
      return String(e).substring(0, 3);
    },
  },
};
</script>
<style scoped>
.body {
  border-radius: 20px;
}
.title-info {
  border-left: 9px solid #fb9a09;
  text-transform: uppercase;
  color: #3189bb;
  font-size: 25px;
}
.label-name {
  font-weight: 500;
  font-size: 15px;
  text-transform: uppercase;
}
.prediction-item {
  margin-top: 60px;
}
.btn {
  border: 0;
}
.btn-simulation {background: #549dc7;}
.btn-simulation:hover {
  background: #3189bb !important;
  color: #fff !important;
}
.form-control {
  border: 1px solid #ced4da
}
.save-simulation p {
  font-size: 15px;
  font-weight: 500;
  line-height: 28px;
}
.header-title {
  background: #3189bb;
  font-size: 35px;
  border-radius: 20px;
}
.container-fluid {
  width: 90%;
}
.container-fluid-body {
  border: 1px solid #cccccc;
  border-top: 0;
  border-top-left-radius: 20px;
  border-top-right-radius: 20px;
}
/* ::v-deep select:first-child:disabled {
  color: #6f737c;
}
::v-deep option {
  color: #111111;
} */
/* select:disabled */
/* option:disabled */
/* ::v-deep select > option {
  color: red !important;
} */
/* ::v-deep option[value=""][disabled] {
  display: none !important;
  color: #6f737c;
}
select:invalid { color: #6f737c; }
::v-deep .custom-select {
  color: #6b727a !important;
} */

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
.notification-field {
  letter-spacing: 1.3px;
}
</style>
