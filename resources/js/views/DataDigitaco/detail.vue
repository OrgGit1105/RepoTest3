<template>
  <div>
    <div class="container-fluid">
      <div class="container-fluid-body mt-5 mb-5">
        <div class="col-md-12 p-5 py-0 form-group">
          <div class="justify-content-start top-title pb-5">
            <div class="basic"><h1 class="pl-3 title-info">{{ $t('LANGUAGES.TEXT_DATA_DIGITACO') }}</h1></div>
          </div>
          <div class="table">
            <b-table
              id="my-table"
              class="text-center w-100 bg-dx-grey-blur mb-0"
              :items="listItems"
              :fields="fields"
              responsive="sm"
              :current-page="queryData.page"
              show-empty
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
          <b-button class="btn-back" @click="returnToIndex">
            <div class="photo">
              <div>
                <img class="img_back" :src="require(`../../assets/images/back.png`)" alt="">
              </div>
              <span class="text_back">{{ $t('LANGUAGES.TEXT_BUTTON_RETURN') }}</span>
            </div>
          </b-button>

        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { getOneDataDigitaco } from '../../api/data_digitaco';
import { MakeToast } from '../../utils/toast_message';
import _ from 'lodash';
export default {
  name: 'DataDigitacoDetails',
  data() {
    return {
      fileinput: '',
      interviewDeparuteOption: [],
      companyOption: [],
      queryData: {
        page: 1,
        per_page: 100,
        total_records: 0,
      },
      id: this.$route.params.id,
      type: this.$route.params.type,
      fields: [],
      listItems: [],
    };
  },
  computed: {
    detailDataDigitaco() {
      return this.$store.getters.detailDataDigitaco;
    },
    currentChange() {
      return this.queryData.page;
    },
  },

  watch: {
    currentChange() {
      this.getDataDetail();
    },
    detailDataDigitaco() {
      // console.log('manh da di vao day', this.detailDataDigitaco);
      // console.log(this.detailDataDigitaco.header[0][0]);
      const listHeader = _.get(this.detailDataDigitaco.header, '[0][0]', []);
      if (listHeader.length > 0) {
        for (let i = 0; i < listHeader.length; i++) {
          this.fields.push({
            key: listHeader[i],
            label: listHeader[i],
          });
        }
        // console.log('Manh', this.fields);
      }
      this.listItems = _.get(this.detailDataDigitaco.contents, 'result', []);
    },
  },

  created() {
    this.getDataDetail();
  },

  methods: {
    async getDataDetail() {
      const pagination = { ...this.queryData };
      delete pagination.total_records;
      this.openLoading();
      await getOneDataDigitaco(this.id, this.type, pagination)
        .then(async(response) => {
          if (response.code === 200) {
            if (response.data) {
              this.queryData.total_records = response.data.contents.total;
              const detailDataDigitaco = response.data;
              this.$store.dispatch('app/saveDetailDataDigitaco', detailDataDigitaco);
            }
            this.closeLoading();
            MakeToast({
              variant: 'success',
              title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
            });
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
    returnToIndex(){
      this.$router.push('/datadigitaco/index');
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
.title-info {
      text-transform: uppercase;
    color: #3189bb;
    font-size: 25px;
}
.prediction-item {
  margin-top: 60px;
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
    position: sticky;
    top: -2px;
    background: #e5e5e5;
    z-index: 99999;
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
::v-deep .table thead th {
    min-width: 155px;
}
::v-deep th > div {
  letter-spacing: 1.2px;
  min-height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  white-space: nowrap;
}
// ::v-deep tbody:hover tr:not(:hover) td{
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

.btn-back {
    display: inline-block;
    width: 160px;
    left: 33px;
    top: 50px;
    background: white;
    border: 1px solid #CCCCCC;
    box-sizing: border-box;
    border-radius: 15px;
}

.photo {
    display: flex;
    justify-content: space-around;
}

.img_back {
  display: block;
  max-width: 50px;
  max-height:50px;
  width: auto;
  height: auto;
}

.text_back {
    font-weight: bold;
    font-size: 23px;
    color: #828282;
    font-style: normal;
    margin: auto 0;
}

.table-route {
  position: relative;
  top: 20%;
}

.table {
width: 100%;
   overflow-x: scroll !important;
   cursor: pointer;
   height: 83vh;
    overflow-y: scroll;
}
.table::-webkit-scrollbar-track
{
	-webkit-box-shadow: inset 0 0 2px rgba(0,0,0,0.3);
	border-radius: 10px;
	background-color: #F5F5F5;
}

.table::-webkit-scrollbar
{
	background-color: #F5F5F5;
  height: 7px;
  width: 7px;
}

.table::-webkit-scrollbar-thumb
{
	border-radius: 10px;
	-webkit-box-shadow: inset 0 0 2px rgba(61, 61, 61, 0.3);
	background-color: #989898;
}
.top-title { margin-bottom: 50px;}
  .container-fluid {
  width: 90%;
  margin: auto;
}
.btn-secondary:active {
  background-color: transparent !important;
}
.btn-secondary:focus {
  box-shadow: none !important;
}
</style>
