/* eslint-disable keyword-spacing */
/* eslint-disable keyword-spacing */
/* eslint-disable padded-blocks */
/* eslint-disable keyword-spacing */
<template>
  <div class="col-md-10 data-management" style="margin: auto">
    <div class="justify-content-start p-5">
      <div class="basic"><h1 class="pl-3 title-info"> {{ $t('LANGUAGES.TEXT_DATA_MANAGEMENT') }}</h1></div>
    </div>
    <!-- <div class="card-header bg-light text-dark">DATA MANAGEMENT</div> -->
    <div class="card-body Table" style="padding: 0; overflow: auto;">
      <b-table
        id="my-table"
        class="text-center w-100 mb-0"
        :items="dataManagement.length !== 0 ? dataManagement : []"
        :fields="fields"
        :current-page="queryData.page"
        responsive="sm"
        hover
        show-empty
        :no-local-sorting="noSort"
        @sort-changed="sortingChanged"
      >
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
</template>

<script>
import * as DataManagementApi from '../../api/employee';
import { MakeToast } from '../../utils/toast_message';
// import * as CONFIGS from '../../configs';
import * as CONVERT from '../../utils/convert';
import moment from 'moment';
export default {
  name: 'DataManagement',
  data() {
    return {
      noSort: true,
      queryData: {
        page: 1,
        per_page: 30,
        total_records: 0,
        column_name: '',
        sort: '',
      },
      fields: [
        { key: 'employee_code', sortable: true, class: 'employee_code', label: this.$t('LANGUAGES.TEXT_EMPLOYEE_CODE') },
        { key: 'employee_name', sortable: true, class: 'employee_name', label: this.$t('LANGUAGES.TEXT_EMPLOYEE_NAME') },
        { key: 'company.name', sortable: true, class: 'company_branch', label: this.$t('LANGUAGES.TEXT_COMPANY_BRANCH') },
        { key: 'total_worked', sortable: true, class: 'total_worked', label: this.$t('LANGUAGES.TEXT_TOTAL_WORKED') },
        { key: 'date_joining_company', sortable: true, class: 'date_joining_company', label: this.$t('LANGUAGES.TEXT_DATE_JOINING_COMPANY') },
        { key: 'date_out_company', sortable: true, class: 'date_out_company', label: this.$t('LANGUAGES.TEXT_DATE_OUT_COMPANY') },
        { key: 'joining_age_company', sortable: true, class: 'joining_age_company', label: this.$t('LANGUAGES.TEXT_JOINING_AGE_COMPANY') },
        { key: 'spouse', sortable: true, class: 'spouse', label: this.$t('LANGUAGES.TEXT_SPOUSE') },
        { key: 'dependents', sortable: true, class: 'dependents', label: this.$t('LANGUAGES.TEXT_DEPENDENTS') },
        { key: 'worked_year', sortable: true, class: 'worked_year', label: this.$t('LANGUAGES.TEXT_WORKED_YEAR') },
        { key: 'final_education', sortable: true, class: 'final_education', label: this.$t('LANGUAGES.TEXT_FINAL_EDUCATION') },
        { key: 'shortest_service', sortable: true, class: 'shortest_service', label: this.$t('LANGUAGES.TEXT_SHORTEST_SERVICE') },
      ],
    };
  },
  computed: {
    dataManagement() {
      return this.$store.getters.dataManagement;
    },
    currentChange() {
      return this.queryData.page;
    },
  },
  watch: {
    currentChange() {
      this.getAllData();
    },
  },
  created() {
    this.getAllData();
  },
  methods: {
    async getAllData() {
      const body = { ...this.queryData };
      delete body.total_records;
      this.openLoading();
      await DataManagementApi.getAllData(body)
        .then((response) => {
          if (response.code === 200) {
            MakeToast({
              variant: 'success',
              title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
            });
            if (response.data) {
              this.queryData.total_records = response.data.pagination.total_records;
              const dataManagement = response.data.result;
              // console.log('dataManagement===>', dataManagement);
              dataManagement.forEach((element) => {
                element.date_joining_company = moment(element.date_joining_company).format(
                  'YYYY/MM/DD'
                );
                element.date_out_company = element.date_out_company ? moment(element.date_out_company).format(
                  'YYYY/MM/DD'
                ) : '-';
                element.spouse = element.spouse || element.spouse === 0 ? CONVERT.convertSpouse(element.spouse) : '-';
                element.final_education = element.final_education || element.final_education === 0 ? CONVERT.renderEducationName(element.final_education) : '-';
                element.employee_name = element.employee_name ? element.employee_name : '-';
                element.joining_age_company = element.joining_age_company || element.joining_age_company === 0
                  ? element.joining_age_company : '-';

                element.shortest_service = element.shortest_service || element.shortest_service === 0
                  ? element.shortest_service : '-';
                element.worked_year = element.worked_year || element.worked_year === 0
                  ? element.worked_year : '-';
                element.dependents = element.dependents || element.dependents === 0
                  ? element.dependents : '-';
                element.total_worked = element.total_worked || element.total_worked === 0
                  ? element.total_worked : '-';
              });
              this.$store.dispatch('app/saveDataManagement', dataManagement);
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
    openLoading() {
      this.$store.dispatch('loading/setLoading', true);
    },
    closeLoading() {
      this.$store.dispatch('loading/setLoading', false);
    },
    sortingChanged(ctx) {
      this.queryData.column_name = ctx.sortBy === 'company.name' ? 'company_branch' : ctx.sortBy;
      this.queryData.sort = ctx.sortDesc;
      this.getAllData();
    },
  },
};
</script>

<style scoped>
.basic {
  /* border-left: 10px solid orange; */
  left: 20px;
}
.title-info {
  border-left: 9px solid #fb9a09;
  text-transform: uppercase;
    color: #3189bb;
  font-size: 25px;
}
::v-deep td {
      border: 0.1px solid #888888;
      font-size: 14px;
      padding: 0.75rem 0.45rem;
}
::v-deep  .table th, .table td {
    border: 0.1px solid #888888;
}
::v-deep thead {
    position: sticky;
    top: -1px;
    background: #e5e5e5;
}
th {

  text-align: center;
  vertical-align: middle;
  background: #e5e5e5;
  color: black;
}
::v-deep th > div {
  width: 140px;
  letter-spacing: 1.2px;
  white-space: nowrap;
}

.table-route {
  position: relative;
  top: 20%;
}

.table-route thead th {
  position: sticky;
  top: 0;
}
.Table {
width: 100%;
   overflow-x: scroll !important;
   cursor: pointer;
   max-height: 70vh;
    overflow-y: scroll;
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
  width: 7px;
}

.Table::-webkit-scrollbar-thumb
{
	border-radius: 10px;
	-webkit-box-shadow: inset 0 0 2px rgba(61, 61, 61, 0.3);
	background-color: #989898;
}
td {
  text-align: center;
  vertical-align: middle;
}
::v-deep .table thead th {

    border-bottom: 0;
    font-size: 15px;
}
::v-deep .table.b-table > thead > tr > [aria-sort].b-table-sort-icon-left, .table.b-table > tfoot > tr > [aria-sort].b-table-sort-icon-left {
  background-position: right calc(0.75rem / 2) center !important;
  padding-right: calc(0.75rem + 0.65em)  !important;
}
::v-deep .page-link {
  padding: 3px 10px;
}
::v-deep .page-link:hover {
  border: 1px solid #0f68b1 !important;
}
/* ::v-deep #my-table {
  background: #f6f6f6 !important;
} */
.data-management{
  overflow-x: hidden !important;
}

::v-deep tbody {
   max-height: 400px;
   overflow-y: scroll !important;
   cursor: pointer;
   background: #fff;
}

</style>
