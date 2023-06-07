<template>
  <div>
    <div class="container-fluid">
      <div class="container-fluid-body mt-5 mb-5">
        <div class="col-md-12 p-5 py-0 form-group">
          <div class="justify-content-start pb-5">
            <div class="basic"><h1 class="pl-3 title-info"> {{ $t('LANGUAGES.TEXT_DATA_DIGITACO') }}</h1></div>
          </div>
          <div class="Table">
            <b-table
              id="my-table"
              class="text-center w-100 bg-dx-grey-blur mb-0"
              :items="listDataDigitaco"
              :fields="fields"
              responsive="sm"
              :current-page="queryData.page"
              show-empty
            >
              <template #cell(file_name_data_point)="file_name_data_point">
                <a
                  id="file_name_data_point"
                  class="bg-white text-dark fs-14 digitaco-point"
                  @click="nextToResult(file_name_data_point.field.key, file_name_data_point.item.id )"
                >{{ file_name_data_point.item.file_name_data_point }}</a>
              </template>
              <template #cell(file_name_data_driving)="file_name_data_driving">
                <a
                  id="file_name_data_driving"
                  class="bg-white text-dark fs-14 digitaco-driving"
                  @click="nextToResult( file_name_data_driving.field.key, file_name_data_driving.item.id )"
                >{{ file_name_data_driving.item.file_name_data_driving }}</a>
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
import { getAllDataDigitaco } from '../../api/data_digitaco';
import { MakeToast } from '../../utils/toast_message';
export default {

  name: 'DataDigitacoList',

  data() {
    return {
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
      },
      fields: [
        { key: 'getting_date', label: this.$t('LANGUAGES.TEXT_GETTING_DATE'), class: 'getting_date' },
        { key: 'file_name_data_point', label: this.$t('LANGUAGES.TEXT_FILE_NAME_POINT'), class: 'file_name_data_point' },
        { key: 'file_name_data_driving', label: this.$t('LANGUAGES.TEXT_FILE_NAME_DRIVING'), class: 'file_name_data_driving' },
      ],
    };
  },
  computed: {
    listDataDigitaco() {
      return this.$store.getters.listDataDigitaco;
    },
    currChange() {
      return this.queryData.page;
    },
  },

  watch: {
    currChange() {
      this.getListAllData();
    },
  },

  created() {
    this.getListAllData();
  },
  methods: {
    async getListAllData(e) {
      const pagination = { ...this.queryData };
      delete pagination.total_records;
      this.openLoading();
      await getAllDataDigitaco(pagination)
        .then((response) => {
          // this.queryData.page = response.data.pagination.current_page;
          this.queryData.total_records = response.data.pagination.total_records;
          const listDataDigitaco = response.data.result;
          this.queryData.total_records = response.data.pagination.total_records;
          this.$store.dispatch('app/saveDataDigitaco', listDataDigitaco);
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

    nextToResult(types, id){
      const type = types === 'file_name_data_point' ? 'point' : 'driving';
      if (id && id !== '') {
        this.$router.push({ path: `/datadigitaco/detail/${id}/${type}` }, (onAbort) => {});
      }
    },
    openLoading() {
      this.$store.dispatch('loading/setLoading', true);
    },
    closeLoading() {
      this.$store.dispatch('loading/setLoading', false);
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
a:hover {
  cursor: pointer;
}
.title-info {
      text-transform: uppercase;
    color: #3189bb;
    font-size: 25px;
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
    margin-top: 65px;
    border: 2px solid #888888;
    border-left: 0;
    border-right: 0;
    padding: 50px 0px;
    position: relative;
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
// ::v-deep tbody:hover tr:not(:hover) td {
//     opacity: .24;
//     transition: opacity 100ms cubic-bezier(0.455, 0.03, 0.515, 0.955);
// }
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
.bg-white:before {
  content: "→";
  transition: all ease .5s;
  position:relative;
  left:-10px;
  opacity:0;
}

.bg-white:hover:before {
  content: "→";
  transition: all ease .5s;
  left:-5px;
  opacity:1;
}
a {
  text-decoration: none;
}
  .container-fluid {
  width: 80%;
  margin: auto;
}
</style>
