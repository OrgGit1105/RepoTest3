<template>
  <div class=" table-result ">
    <div class="d-flex justify-content-between">
      <div class="basic col-md-4 pl-0">
        <h1 class="pl-3 title-info">{{ $t('LANGUAGES.TEXT_BASIC_ITEM') }}</h1>
      </div>
      <div class="col-md-8">
        <b-table-simple class="w-100">
          <b-tbody>
            <b-tr>
              <b-td>{{ $t('LANGUAGES.TEXT_INTERVIEW_DATE') }}</b-td>
              <b-td>{{ listInfo.interviewDate }}</b-td>
            </b-tr>
            <b-tr>
              <b-td>{{ $t('LANGUAGES.TEXT_INTERVIEW_DEPARTURE') }}</b-td>
              <b-td>{{ listInfo.companyBranch }}</b-td>
            </b-tr>
            <b-tr>
              <b-td>{{ $t('LANGUAGES.TEXT_CANDIDATE_NAME') }}</b-td>
              <b-td>{{ listInfo.candidateName }}</b-td>
            </b-tr>
          </b-tbody>
        </b-table-simple>
      </div>
    </div>
    <div class="row mt-5">
      <div class="col-md-6 ">
        <div>
          <h1 class="pl-3 title-info">{{ $t('LANGUAGES.TEXT_COMPREHENSIVE_EVALUATION') }}</h1>
          <p class="result-rank">{{ evaluation ? evaluation.review : '' }}</p>
        </div>
        <div>
          <h1 class="pl-3 title-info">{{ $t('LANGUAGES.TEXT_ENROLLMENT_PERIOD') }}</h1>
          <p class="result-rank">{{ evaluation.point ? parseFloat(evaluation.point).toFixed(1) : '' }}</p>
        </div>
        <div>
          <h1 class="pl-3 title-info"> {{ $t('LANGUAGES.TEXT_ENROLLMENT_PERIOD_GRAPH') }}</h1>
          <div><apexchart width="100%" type="radar" :options="options" :series="series" /></div>
        </div>
      </div>
      <div class="col-md-6 ">
        <h1 class="pl-3 title-info mb-4">{{ $t('LANGUAGES.TEXT_EVALUATION') }}</h1>
        <b-table-simple class="w-100 table-rank-alpha">

          <b-tbody>
            <b-tr>
              <b-td>{{ $t('LANGUAGES.TEXT_JOINING_AGE') }}</b-td>
              <b-td class="result-rank-alpha">{{ evaluation.old }}</b-td>
            </b-tr>
            <b-tr>
              <b-td>{{ $t('LANGUAGES.TEXT_SPOUSE') }}</b-td>
              <b-td class="result-rank-alpha">{{ evaluation.married }}</b-td>
            </b-tr>
            <b-tr>
              <b-td>{{ $t('LANGUAGES.TEXT_DEPENDENTS') }}</b-td>
              <b-td class="result-rank-alpha">{{ evaluation.dependents }}</b-td>
            </b-tr>
            <b-tr>
              <b-td>{{ $t('LANGUAGES.TEXT_NUMBER_OF_WORK_EXPERIENCE') }}</b-td>
              <b-td class="result-rank-alpha">{{ evaluation.company }}</b-td>
            </b-tr>
            <b-tr>
              <b-td>{{ $t('LANGUAGES.TEXT_FINAL_EDUCATION') }}</b-td>
              <b-td class="result-rank-alpha">{{ evaluation.education }}</b-td>
            </b-tr>
            <b-tr>
              <b-td>{{ $t('LANGUAGES.TEXT_MINIMUM_SERVICE_PERIOD(MONTH)') }}</b-td>
              <b-td class="result-rank-alpha">{{ evaluation.time }}</b-td>
            </b-tr>
          </b-tbody>
        </b-table-simple>
      </div>
    </div>
  </div>
</template>

<script>
import * as EnrollmentApi from '../../api/enrollment';
import { MakeToast } from '../../utils/toast_message';
import moment from 'moment';
import * as CONVERT from '../../utils/convert';

export default {
  name: 'Result',
  props: { modelId: { type: Number, default: () => {
    return;
  }, require: false }},
  data() {
    return {
      options: {
        chart: {
          id: 'vuechart-example',
        },
        xaxis: {
          categories: ['入社時年齢', '配偶者', '扶養人数', '職歴数', '最終学歴', '最短勤続期間(ヶ月)'],
        },
      },
      series: [{
        name: 'series-1',
        data: [],
      }],
      selected: null,
      id: this.$route.params.id,
    };
  },
  computed: {
    evaluation() {
      return this.$store.getters.listEvaluation;
    },
    listInfo() {
      return this.$store.getters.listInfo;
    },
  },
  watch: {
    evaluation() {
      if (this.$store.getters.listEvaluation){
        const listEvaluation = this.evaluation;
        const listValue = ['old', 'married', 'dependents', 'company', 'education', 'time'];
        const formatList = [];
        for (let i = 0; i < listValue.length; i++) {
          const key = listEvaluation[listValue[i]];
          formatList.push(this.renderPoin(key));
        }
        // console.log('Ket qua cuoi cung', formatList);
        this.series = [{
          data: formatList,
        }];
      }
    },
    queryData() {
      // console.log('Da vao day', this.queryData);
      this.getCandidateResultInfo();
    },
    selectPage() {
      // console.log('Page gui xuong', this.pageSelect);
      this.queryData.page = this.pageSelect;
      this.getCandidateResultInfo();
    },
  },
  created() {
    // console.log('Da chay vao day');
    this.getCandidateResultInfo();
  },
  methods: {
    async getCandidateResultInfo() {
      const body = { ...this.queryData };
      [
        'page',
        'per_page',
        'company',
        'user_code',
        'user_name',
      ].forEach((element) => {
        if (body[element] === '') {
          delete body[element];
        }
        delete body.total_records;
      });
      if (this.modelId && this.modelId !== '') {
        this.id = this.modelId;
      }
      this.openLoading();
      await EnrollmentApi.getCandidateResult(this.id, body)
        .then((response) => {
          //   console.log('Data return', response);
          if (response.code === 200) {
            MakeToast({
              variant: 'success',
              title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
              content: this.modelId ? this.$t('LANGUAGES.TEXT_TOAST_CONTENT_GET_PREVIEW_PDF_SUCCESSFULLY') : this.$t('LANGUAGES.TEXT_TOAST_CONTENT_GET_CANDIDATE_RESULT_SUCCESSFULLY'),
            });
            if (response.data) {
              // console.log('Data', response.data);
              const dataReturn = response.data;
              // STORE EVALUATION
              const evaluation = dataReturn.config;
              this.$store.dispatch('app/saveListEvaluation', evaluation);
              // STORE INFO Enrollment Prediction
              const listInfo = response.data.enrollment;
              listInfo.interviewDate = moment(response.data.enrollment.interview_date).format(
                'YYYY/MM/DD'
              );
              listInfo.companyBranch = response.data.enrollment.company_branchs.name;
              listInfo.candidateName = response.data.enrollment.candidate_name;
              this.$store.dispatch('app/saveListInfo', listInfo);
              // STORE Table
              // this.queryData.total_records =
              //   response.data.pagination.total_records;
              const listEmployee = response.data.users;
              // console.log('listEmployee', listEmployee.data);
              listEmployee.forEach((element) => {
                element.date_joining_company = moment(element.date_joining_company).format(
                  'YYYY/MM/DD'
                );
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
    returnToIndex() {
      this.$router.push('/enrollment/index');
    },
    openLoading() {
      this.$store.dispatch('loading/setLoading', true);
    },
    closeLoading() {
      this.$store.dispatch('loading/setLoading', false);
    },
    renderPoin(poin) {
      // console.log('Poin', poin);
      switch (poin) {
        case 'A':
          return 5;

        case 'B':
          return 4;

        case 'C':
          return 3;

        case 'D':
          return 2;

        case 'E':
          return 1;
        default:
      }
    },
  },
};
</script>
<style lang="scss" scoped>
.EnrollmentList div.container-fluid{
    width: 101% !important;
}
.candidate-result {
  .title-info {
    border-left: 9px solid #fb9a09;
    color: #3189bb;
    text-transform: uppercase;
  }
}
 .title-info {
    border-left: 9px solid #fb9a09;
    color: #3189bb;
    text-transform: uppercase;
  }
#chart {
  max-width: 350px;
  margin: 35px auto;
}
.label-name {
  font-size: 17px;
  padding-left: 10px;
}
.prediction-item {
  margin-top: 60px;
}

.form-header {
  border: 2px solid black;
  border-left: 1px;
  border-right: 1px;
  padding: 50px 0px;
  margin-top: 60px;
}
h3 {
  text-decoration: underline;
}
.btn-return {
   background-color: royalblue;
   font-size: 20px;
  color: white;
    padding: 5px 15px;
    border-radius: 5px;
  border: none;
  cursor: pointer;
    &:hover {
    background: mediumblue;
    color:#fff;
    border: none;
  }
}

.btn-search {
  background-color: ghostwhite;
  border-left: none;
  border-color: beige;
}

    ::v-deep .text-center.table-responsive-sm {
      margin: 50px 0px;
      border: 1px solid #888888;
      border-right: 0;
      border-top: 0;
      .b-table {
        width: 100%;
        th.b-table-sort-icon-left {
          border: 0.9px solid #888888;
          border-left: 0;
          min-width: 100% !important;
        }
        td {
          border-right: 0.9px solid #888888;
          border-top: 0.9px solid #888888;
          background: #ffffff;
        }
      }
    }
.table-result-list {
  position: relative;
  margin-top: 70px;
}
::v-deep .custom-select {
  border: 1px solid;
}
::v-deep .bi-search {
  position: absolute;
  top: 8px;
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
.btn-apply {
  border: none;
  &:hover {
    background: #307bbc;
    color:#fff;
  }
}
.table-result {
  tr {
    border-bottom: 0.9px solid #ccc;
    color: #adadad;
    font-size: 20px;
    font-weight: 500;
    td {
      border: 0;
      text-align:inherit
    }
  }
  .result-rank {
    font-size: 100px;
    text-transform: uppercase;
    font-weight: 600;
    text-align: center;
    margin-bottom: 0;
    color: orange;
  }
  .result-rank-alpha {
    text-align:center ;
    font-weight:bold;
    font-size: 33px;
    color: #3189bb;
  }
  .table-rank-alpha {
    tr {
      line-height: 45px;
    }
  }
}
::v-deep .apexcharts-toolbar {
  display: none !important;
}

@media only screen and (max-width: 768px) {
  .title-info{
    font-size: 23px;
  }
  .card-body{
    padding: 0%;
  }
  td {
    width: 50%;
    font-size: 12px;
  }
  .col-md-12 {
    padding: 0;
  }
}

</style>
