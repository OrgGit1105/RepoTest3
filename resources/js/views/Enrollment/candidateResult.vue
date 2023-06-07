<template>
  <div>
    <div class="container-fluid">
      <div class="container-fluid-body mt-5 mb-5">
        <div
          class="bg-primary text-white text-center"
          style="border-radius: 20px"
        >
          <h1 class="p-3">{{ $t('LANGUAGES.TEXT_ENROLLMENT_PREDICTION_RESULT') }}</h1>
        </div>
        <div class="body">
          <div class="col-md-12 p-5 py-0 form-group">
            <div class="candidate-result">
              <div class="card-body">
                <Result :data-query="queryData" :page-select="pageSelect" />
                <div border="none" class="table-result-list">
                  <h1 class="pl-3 title-info mb-2">{{ $t('LANGUAGES.TEXT_SIMILAR_EMPLOYEE_DATA') }}</h1>
                  <div class="EnrollmentList">
                    <FillterList
                      :id="Number(id)"
                      access="1"
                    />
                  </div>
                  <button class="btn-return" @click="$router.go(-1)">
                    <b-icon icon="arrow-left-square" />
                    <span class="text-return">{{ $t('LANGUAGES.TEXT_BUTTON_RETURN') }}</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import FillterList from './fillter.vue';
import Result from './result.vue';
export default {
  name: 'ENROLLMENT',
  components: {
    FillterList,
    Result,
  },
  data() {
    return {
      id: this.$route.params.id,
      queryData: {
        page: 1,
        per_page: 20,
        total_records: '',
        company: '',
        user_code: '',
        user_name: '',
      },
      pageSelect: 0,
      isExit: null,
    };
  },
  computed: {
    listEmployee() {
      return this.$store.getters.listEmployee;
    },
    evaluation() {
      return this.$store.getters.listEvaluation;
    },
    listInfo() {
      return this.$store.getters.listInfo;
    },
  },
  watch: {
  },
  created() {
    // console.log('This router', this.$router);
  },
  methods: {
  },
};
</script>
<style lang="scss" scoped>
.body {
  overflow-x: hidden;
}
.EnrollmentList div.container-fluid {
  width: 101% !important;
}
.candidate-result {
  .title-info {
    border-left: 9px solid #fb9a09;
    color: #3189bb;
    text-transform: uppercase;
    font-size: 25px;
  }
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
  background-color: #0f68b1;
  font-size: 20px;
  color: white;
  padding: 5px 15px;
  border-radius: 5px;
  border: none;
  cursor: pointer;
  &:hover {
    background: #307bbc;
    color: #fff;
    border: none;
  }
}

.btn-search {
  background-color: ghostwhite;
  border-left: none;
  border-color: beige;
}
.container-fluid {
  width: 90%;
  .container-fluid-body {
    border: 1px solid #cccccc;
    border-top: 0;
    border-top-left-radius: 20px;
    border-top-right-radius: 20px;
    .form-group {
      position: relative;
      .form-group-filter {
        position: absolute;
        top: 80px;
        left: 110px;
        background: #fff;
        padding: 0px 8px;
      }
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
          // border-right: 0.9px solid #888888;
          // border-top: 0.9px solid #888888;
          background: #ffffff;
        }
      }
    }
  }
}
::v-deep th {
  font-size: 15px;
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
    color: #fff;
  }
}
.table-result {
  tr {
    border-bottom: 0.9px solid #ccc;
    color: #adadad;
    font-size: 24px;
    font-weight: 500;
    td {
      border: 0;
      text-align: inherit;
    }
  }
  .result-rank {
    font-size: 100px;
    text-transform: uppercase;
    font-weight: 600;
    text-align: center;
    margin-bottom: 0;
  }
  .result-rank-alpha {
    color: #3189bb;
    text-align: center;
    font-weight: bold;
    font-size: 33px;
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
.result-rank {
  color: #fb9a09;
}
.text-return {
  font-size: 15px;
  font-weight: 400;
}
@media only screen and (max-width: 768px) {
  .col-md-12 {
    padding: 0 !important;
  }
  .container-fluid{
    width: 100%;
  }
}
</style>
