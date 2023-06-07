<template>
  <div class="col-md-10" style="margin: auto">
    <div class="justify-content-start p-5">
      <div class="basic"><h1 class="pl-3 title-info text-dx-blue-light-2">{{ $t('LANGUAGES.TEXT_CSV_IMPORT') }}</h1></div>
    </div>
    <div class="card m-auto">
      <div class="card-header bg-light text-dark fs-20">{{ $t('LANGUAGES.TEXT_CSV_DATA') }}</div>
      <div class="card-body">
        <div class="row p-5 align-items-center" style="margin: auto">
          <b-col lg="6" class="d-flex justify-content-center">
            <label class="label-name fs-18 text-uppercase">{{ $t('LANGUAGES.TEXT_IMPORT_DATA_CSV') }}:</label>
          </b-col>
          <b-col lg="6" class="d-flex justify-content-center">
            <button
              type="file"
              name="file_xlsx"
              class="btn-select fs-16 p-2 border border-light"
              @click="selectFile()"
            >
              <b-icon icon="file-earmark-plus" aria-hidden="true" />
              {{ $t('LANGUAGES.TEXT_SELECT_FILE') }}
            </button>
            <input
              id="xlsx"
              ref="file"
              name="file_xlsx"
              accept=".xlsx"
              type="file"
              class="d-none"
              @change="uploadCSV($event)"
            >
          </b-col>
          <!-- Modal Notification -->
          <b-modal id="bv-modal-notification" hide-footer hide-header>
            <header class="style-modal-header p-3 bg-primary text-white">
              <h4>{{ $t('LANGUAGES.TEXT_MODAL_NOTIFICATION_CSV') }}</h4>
            </header>
            <div>
              <div class="d-block p-4 style-modal">
                <h4 class="mb-0">{{ $t('LANGUAGES.TEXT_LIST_ERROR_IMPORT') }}</h4>
                <ul v-if="listError" class="content-error">
                  <li v-for="(item, index) in listError" :key="index">
                    <span v-if="item.row"> {{ item.row }} :</span>
                    <span>{{ item.title }}{{ item.error }}</span>
                  </li>
                </ul>
              </div>
            </div>
            <div class="justify-content-end d-flex p-3">
              <b-button
                class="mt-3 w-25 fs-12 btn btn-accept bg-dx-blue"
                squared
                @click="hideModal()"
              >{{ $t('LANGUAGES.TEXT_BUTTON_YES') }}</b-button>
              <b-button
                class="mt-3 ml-3 w-25 fs-12 btn btn-close"
                squared
                @click="hideModal()"
              >{{ $t('LANGUAGES.TEXT_BUTTON_CLOSE') }}</b-button>
            </div>
          </b-modal>
        </div>
      </div>
    </div>
    <div class="p-5 m-auto text-center">
      <button class="w-20 btn btn-import bg-dx-orange p-3 text-white" @click="onSubmit">
        {{ $t('LANGUAGES.TEXT_PERFORM_AN_IMPORT') }}
      </button>
    </div>
  </div>
</template>

<script>
import xlsx from 'xlsx';
import moment from 'moment';
import { MakeToast } from '../../utils/toast_message';
import * as CONVERT from '../../utils/convert';
import * as REGEXR from '../../utils/regexr';
import * as DataManagementApi from '../../api/employee';
import * as CompanyBranchApi from '../../api/company_branch';
// import * as CONFIGS from '../../configs/index';
export default {
  name: 'CSV',
  data() {
    return {
      listData: [],
      message: '',
      listError: [],
      checkValidate: false,
      files: '',
      listCompanyBranch: [],
    };
  },
  computed: {
    companyBranch() {
      return this.$store.getters.listBranch;
    },
    roleId() {
      return this.$store.getters.role_id;
    },
  },
  watch: {
    companyBranch() {
      for (let i = 0; i < this.companyBranch.length; i++) {
        this.listCompanyBranch.push(this.companyBranch[i].name);
      }
    },
  },
  created() {
    this.getAllCompanyBranch();
  },
  methods: {
    getDuplicateArrayElements(arr){
      const results = [];
      const listDuplicate = [];
      const list = [];
      for (let i = 0; i < arr.length; i++) {
        const findItem = results.findIndex((item) => item.content === arr[i].employee_code);
        // console.log('findItem', findItem);
        if (findItem === -1) {
          results.push({ content: arr[i].employee_code, row: i + 2 });
        } else {
          listDuplicate.push({ content: arr[i].employee_code, row: i + 2 });
          if (!list.includes(arr[i].employee_code)) {
            list.push(arr[i].employee_code);
          }
        }
      }

      list.forEach((element) => {
        const findItem = results.find((item) => item === element);
        if (findItem) {
          listDuplicate.push(findItem);
        }
      });
      return listDuplicate;
    },
    async onSubmit() {
      if (this.checkValidate === true && this.listError.length === 0) {
        // console.log('Da vao Submit Manh', this.listData);
        this.listData.forEach((element) => {
          element.final_education = element.final_education ? CONVERT.renderEducationNameToNumber(element.final_education) : '';
          element.employee_code = String(element.employee_code);
          element.spouse = element.spouse ? CONVERT.renderSpouseToNumber(element.spouse) : '';
          element.employee_name = element.employee_name ? element.employee_name : '';
        });
        this.openLoading();
        await DataManagementApi.createDataImport(this.listData)
          .then((response) => {
            if (response.code === 200) {
              this.closeLoading();
              MakeToast({
                variant: 'success',
                title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
                content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_IMPORT_DATA_SUCCESSFULLY'),
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
      } else {
        MakeToast({
          variant: 'warning',
          title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
          content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_IMPORT_FALSE_PLEASE_SELECT_FILE_CSV_TO_IMPORT'),
        });
      }
    },
    selectFile() {
      const fileInputElement = this.$refs.file;
      fileInputElement.click();
    },
    async getAllCompanyBranch() {
      await CompanyBranchApi.getAllCompanyBranch()
        .then((response) => {
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
    showModal(data) {
      const dataError = data;
      const checkTitle = ['employee_code', 'employee_name', 'company_branch', 'total_worked', 'date_joining_company', 'date_out_company', 'joining_age_company', 'spouse', 'dependents', 'worked_year', 'final_education', 'shortest_service'];
      dataError.forEach((e) => {
        if (checkTitle.includes(e.title)){
          e.title = this.modifyTitle(e.title);
        }
      });
      this.listError = dataError;
      // console.log('this.listError', this.listError);
      this.$bvModal.show('bv-modal-notification');
    },
    hideModal() {
      this.$bvModal.hide('bv-modal-notification');
      this.listError = [];
    },
    openLoading() {
      this.$store.dispatch('loading/setLoading', true);
    },
    closeLoading() {
      this.$store.dispatch('loading/setLoading', false);
    },
    modifyHeaders(propertyName) {
      // console.log('propertyName ', propertyName);
      const propertiesNameArr = CONVERT.processText(propertyName);
      // console.log('propertiesNameArr', propertiesNameArr);
      switch (propertiesNameArr[0]) {
        case '社員番号':
          return 'employee_code';

        case '社員名':
          return 'employee_name';

        case '拠点':
          return 'company_branch';

        case '勤続年数':
          return 'total_worked';

        case '入社日':
          return 'date_joining_company';

        case '退社日':
          return 'date_out_company';
        case '入社時年齢':
          return 'joining_age_company';
        case '配偶者':
          return 'spouse';
        case '扶養人数':
          return 'dependents';
        case '職歴数':
          return 'worked_year';
        case '最終学歴':
          return 'final_education';

        case '最短勤続期間(ヶ月)':
          return 'shortest_service';
        default:
      }
    },
    modifyTitle(titleName) {
      // console.log('titleName ', titleName);
      switch (titleName) {
        case 'employee_code':
          return '社員番号';

        case 'employee_name':
          return '社員名';

        case 'company_branch':
          return '拠点';

        case 'total_worked':
          return '勤続年数';
        case 'date_joining_company':
          return '入社日';

        case 'date_out_company':
          return '退社日';

        case 'joining_age_company':
          return '入社時年齢';

        case 'spouse':
          return '配偶者';

        case 'dependents':
          return '扶養人数';

        case 'worked_year':
          return '職歴数';

        case 'final_education':
          return '最終学歴';

        case 'shortest_service':
          return '最短勤続期間(ヶ月)';

        default:
      }
    },
    uploadCSV(e) {
      this.files = e.target.files;
      const listError = [];
      // console.log('file', e.target.files[0]);
      if (!this.files.length) {
        return;
      } else if (e.target.files[0].size >= 5242880) {
        document.getElementById('xlsx').value = '';
        return MakeToast({
          variant: 'warning',
          title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
          content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_IMPORT_FALSE_PLEASE_SELECT_FILE_LESS_THAN_5MB'),
        });
      } else if (!REGEXR.regCSV.test(this.files[0].name.toLowerCase())) {
        document.getElementById('xlsx').value = '';
        return MakeToast({
          variant: 'warning',
          title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
          content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_IMPORT_FALSE_PLEASE_SELECT_FILE_CSV_TO_IMPORT'),
        });
      } else {
        const fileReader = new FileReader();
        fileReader.onload = (ev) => {
          try {
            const data = ev.target.result;
            // console.log('data', data);
            // console.log('type of', typeof data);
            // const UTF8 = CONVERT.convertUTF8(data);
            // console.log('manhhhh', UTF8);
            const XLSX = xlsx;
            const workbook = XLSX.read(data, {
              type: 'binary',
              cellText: false,
              cellDates: true,
            });
            const wsname = workbook.SheetNames[0];
            // console.log(workbook);
            const ws = XLSX.utils.sheet_to_json(workbook.Sheets[wsname]);
            const excellist = [];
            const listProperty = ['入社日', '入社時年齢', '勤続年数', '扶養人数', '退社日', '拠点', '最短勤続期間(ヶ月)', '最終学歴', '社員名', '社員番号', '職歴数', '配偶者'];
            // Edit data
            for (var i = 0; i < ws.length; i++) {
              excellist.push(ws[i]);
            }
            // console.log('excellist', excellist);
            const listHeader = Object.keys(ws[0]);
            const checkColumn = [];
            // console.log('manh', checkColumn);
            // console.log('Check 12 column', Object.keys(checkColumn).length);
            for (let a = 0; a < listHeader.length; a++) {
              if (listProperty.indexOf(listHeader[a]) === -1) {
                checkColumn.push(listHeader[a]);
              }
            }
            if (checkColumn.length > 0) {
              return MakeToast({
                variant: 'warning',
                title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
                content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_IMPORT_FALSE_COLUMN_INVALID'),
              });
            } else {
              // console.log('Da chay vao day', excellist);
              // console.log('listProperty', listProperty);
              const list = [];
              for (let i = 0; i < listProperty.length; i++) {
                const japaneseKey = listProperty[i];
                list.push({ originalKey: listProperty[i], japaneseKey, englishKey: this.modifyHeaders(japaneseKey) });
              }
              // console.log('list', list);
              const modifyExcelList = [];
              excellist.forEach((element) => {
                let obj = {};
                for (const o in element) {
                  const findProperty = list.find((item) => item.originalKey === o);
                  obj = { ...obj, [findProperty.englishKey]: element[o] };
                }
                modifyExcelList.push(obj);
              });
              // console.log('manh', modifyExcelList);
              for (let i = 0; i < modifyExcelList.length; i++) {
                modifyExcelList[i].company_branch = CONVERT.processText(modifyExcelList[i].company_branch).join('');
                // modifyExcelList[i].employee_name = CONVERT.processText(modifyExcelList[i].employee_name).join('');
                modifyExcelList[i].final_education = modifyExcelList[i].final_education ? CONVERT.processText(modifyExcelList[i].final_education).join('') : '';
                modifyExcelList[i].date_joining_company = modifyExcelList[i].date_joining_company ? moment(
                  modifyExcelList[i].date_joining_company
                ).format('YYYY/MM/DD') : '';
                modifyExcelList[i].date_out_company = modifyExcelList[i].date_out_company ? moment(
                  modifyExcelList[i].date_out_company
                ).format('YYYY/MM/DD') : '';
                modifyExcelList[i].shortest_service = modifyExcelList[i].shortest_service || modifyExcelList[i].shortest_service === 0
                  ? modifyExcelList[i].shortest_service : '';
                modifyExcelList[i].worked_year = modifyExcelList[i].worked_year || modifyExcelList[i].worked_year === 0
                  ? modifyExcelList[i].worked_year : '';
                modifyExcelList[i].dependents = modifyExcelList[i].dependents || modifyExcelList[i].dependents === 0
                  ? modifyExcelList[i].dependents : '';
                modifyExcelList[i].total_worked = modifyExcelList[i].total_worked || modifyExcelList[i].total_worked === 0
                  ? modifyExcelList[i].total_worked : '';
                modifyExcelList[i].spouse = modifyExcelList[i].spouse ? modifyExcelList[i].spouse : '';
                modifyExcelList[i].joining_age_company = modifyExcelList[i].joining_age_company || modifyExcelList[i].joining_age_company === 0
                  ? modifyExcelList[i].joining_age_company : '';
              }
              // modifyExcelList.shift();
              this.listData = modifyExcelList;
              // console.log('this.listData', this.listData);
              // Case List Data Not Null
              const listNotNull = ['date_joining_company', 'employee_code'];
              const listCheckNumber = ['shortest_service', 'worked_year', 'dependents', 'total_worked'];
              const listEmployeeCode = [];
              for (let i = 0; i < this.listData.length; i++) {
                for (let j = 0; j < listNotNull.length; j++) {
                  if (!this.listData[i][listNotNull[j]]) {
                    listError.push({ row: i + 2, title: listNotNull[j], error: this.$t('LANGUAGES.ERROR_NOT_NULL') });
                  }
                }
                for (let k = 0; k < listCheckNumber.length; k++) {
                  if (this.listData[i][listCheckNumber[k]] && !REGEXR.regOnlyNumber.test(this.listData[i][listCheckNumber[k]])) {
                    listError.push({ row: i + 2, title: listCheckNumber[k], error: this.$t('LANGUAGES.ERROR_INVALID') });
                  }
                }
                // Check Joining Age Company
                if (this.listData[i].joining_age_company && !REGEXR.regOnlyNumber.test(this.listData[i].joining_age_company)){
                  listError.push({ row: i + 2, title: 'joining_age_company', error: this.$t('LANGUAGES.ERROR_INVALID') });
                }
                // Check Spouse
                const listCheckSpouse = ['あり', 'なし'];
                if (this.listData[i].spouse && listCheckSpouse.indexOf(this.listData[i].spouse) === -1){
                  listError.push({ row: i + 2, title: 'spouse', error: this.$t('LANGUAGES.ERROR_WRONG_FORMAT') });
                }
                // Check Final Education
                const educationListCheck = ['小学校', '中学校', '高等学校', '専門学校', '高等専門学校', '短期大学', '大学'];
                if (this.listData[i].final_education && educationListCheck.indexOf(this.listData[i].final_education) === -1){
                  listError.push({ row: i + 2, title: this.listData[i].final_education, error: this.$t('LANGUAGES.ERROR_WRONG_FORMAT') });
                }
                // Check Company Branch
                if (this.listCompanyBranch.indexOf(this.listData[i].company_branch) === -1){
                  listError.push({ row: i + 2, title: this.listData[i].company_branch, error: this.$t('LANGUAGES.ERROR_NOT_EXIT') });
                }
                // Check Date Join Company
                if (this.listData[i].date_joining_company && CONVERT.checkFormatDate(this.listData[i].date_joining_company) === false) {
                  listEmployeeCode.push({ row: i + 2, title: this.listData[i].date_joining_company, error: this.$t('LANGUAGES.ERROR_WRONG_FORMAT') });
                }
                // Check Date Out Company
                if (this.listData[i].date_out_company && CONVERT.checkFormatDate(this.listData[i].date_out_company) === false) {
                  listEmployeeCode.push({ row: i + 2, title: this.listData[i].date_out_company, error: this.$t('LANGUAGES.ERROR_WRONG_FORMAT') });
                }
                // Check Employee Duplicate
                if (this.listData[i].employee_code){
                  listEmployeeCode.push(this.listData[i].employee_code);
                }
              }
              const listDuplicate = this.getDuplicateArrayElements(this.listData);
              // console.log('listDuplicate', listDuplicate);
              // console.log('listEmployeeCode', listEmployeeCode);
              if (listDuplicate.length > 0){
                for (let w = 0; w < listDuplicate.length; w++) {
                  listError.push({ row: listDuplicate[w].row, title: listDuplicate[w].content, error: this.$t('LANGUAGES.ERROR_DUPLICATE') });
                }
              }
              if (listError.length > 0) {
                this.showModal(listError);
              } else {
                // console.log('OKEEEE', this.listData);
                this.checkValidate = true;
                return MakeToast({
                  variant: 'success',
                  title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
                  content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_UPLOAD_FILE_SUCCESSFULLY'),
                });
              }
            }
          } catch (e) {
            return MakeToast({
              variant: 'warning',
              title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
              content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_UPLOAD_FILE_FAILURE'),
            });
          }
        };
        fileReader.readAsBinaryString(this.files[0]);
        var input = document.getElementById('xlsx');
        if (input) {
          input.value = '';
        }
      }
    },
  },
};
</script>

<style scoped>
.label-name {font-weight: 500;}
.bi-file-earmark-plus {
  margin-right: 3px;
}
.title-info {
  border-left: 9px solid #fb9a09;
  text-transform: uppercase;
    color: #3189bb;
  font-size: 25px;
}
.card {
  width: 60%;
}
.card-header {
  text-transform: uppercase;
}
button.btn-select {
  background-color: white;
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
  position: relative;
  width: 232px;
}
.btn-select:focus {
  outline: 0;
}
.btn-import {
  background: #fb8c00;
}
.btn-import:hover {
  background-color: #d57700;
  border-color: #c87000;
}
button.btn-select:hover {
  cursor: pointer;
  box-shadow: 2px 2px 25px 3px #ececec;
  transition: all 0.4s ease;
}
.style-modal {
  border-bottom: 1px solid #dee2e6;
  overflow-y: scroll;
  height: 300px;
}
.style-modal-header{
  border-bottom: 1px solid #dee2e6;
}
.style-modal h4 {
  font-weight: 300 !important;
  margin-bottom: 0px !important;
}
.btn-accept:hover {
  box-shadow: 0 5px 11px 0 rgb(0 0 0 / 18%), 0 4px 15px 0 rgb(0 0 0 / 15%);
  background: #0f68b1 !important;
  transition: all 0.2s ease-in-out;
  color: #ffffff;
}
.btn-close:hover {
  box-shadow: 0 5px 11px 0 rgb(0 0 0 / 18%), 0 4px 15px 0 rgb(0 0 0 / 15%);
  background-color: #0f68b1 !important;
  transition: all 0.2s ease-in-out;
  color: #ffffff;
  border: 1px solid #0f68b1 !important;
}
::v-deep #bv-modal-delete___BV_modal_body_ {
  padding: 0 !important;
}
::v-deep #bv-modal-delete___BV_modal_content_ {
  border: 0 !important;
  border-radius: 0 !important;
}
.btn-close {
  background-color: transparent !important;
  color: #0f68b1;
  border: 1px solid #0f68b1 !important;
}
.btn-accept {
  background-color: transparent !important;
  color: #0f68b1;
  border: 1px solid #0f68b1 !important;
}
ul.content-error {
  list-style-type: square;
  color: red;
}
</style>
