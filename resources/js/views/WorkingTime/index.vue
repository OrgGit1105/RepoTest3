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
                <button class="btn btn-date d-flex align-items-center">
                  <b-icon class="text-btn" icon="chevron-left" />
                  <span class="text-btn">3月20日 -  4月18日</span>
                  <b-icon class="text-btn" icon="chevron-right" />
                </button>
              </div>
            </div>
          </div>
        </div>
        <hr class="line-bottom">
        <div class="use-management-title-table mt-5">
          <div class="fill">
            <i class="el-icon-circle-plus-outline custom-icon-add cursor-pointer" @click="showModalAdd()"></i>
            <div class="box-search align-items-center" :class="display">
              <el-input
                placeholder="検索"
                prefix-icon="el-icon-search"
                v-model="formSearch.search"
                @keyup.enter.native="handleSearch()">
              </el-input>
              <i class="el-icon-close cursor-pointer" @click="closeInputSearch()"></i>
            </div>
            <div class="d-flex justify-content-end align-items-center">
              <img :class="displaySearch" class="icon-search cursor-pointer" :src="require(`../../assets/images/icon-search.png`)" @click="openInputSearch()">
              <template class="select-custom">
                <el-select v-model="employeeValue" placeholder="Select" class="el-select-custom">
                    <el-option
                      class="el-option-custom"
                      label="All Employee"
                      value="">
                    </el-option>
                    <el-option
                      v-for="item in listEmployee"
                      :key="item.id"
                      :label="item.name"
                      :value="item.id"
                      >
                    </el-option>
                </el-select>
              </template>
            <i class="el-icon-download custom-icon-down cursor-pointer"></i>
            </div>
          </div>
          <hr class="line">
          <template class="">
            <el-table
              :data="listWorkingTimes"
              style="width: 100%"
              :row-style="rowWorkingStyle"
              @row-click="showDetail">
              <el-table-column
                prop="id"
                label="No"
                width="350"
                align="center">
              </el-table-column>
              <el-table-column
                prop="user.name"
                label="Employee name"
                width="250"
                align="center">
              </el-table-column>
              <el-table-column
                prop="in_time"
                label="IN"
                align="center">
              </el-table-column>
              <el-table-column
                prop="out_time"
                label="OUT"
                align="center">
              </el-table-column>
              <el-table-column
                prop="out_time"
                label="Input type"
                align="center">
              </el-table-column>
            </el-table>
          </template>
        </div>

        <div class="use-management-pagianation">
          <div class="card-body pagianation">
            <b-pagination
              v-model="pagination.current_page"
              :per-page="pagination.per_page"
              :total-rows="pagination.total_records"
              aria-controls="my-table"
              :disabled="pagination.isDisable"
            />
          </div>
        </div>

        <!-- Modal add new -->
        <el-dialog class="title-add-working" title="Add Working time" :visible.sync="openModalAdd" width="35%">
          <el-form :model="form" label-width="120px" label-position="top">
            <el-form-item label="Employee Name" required>
              <el-select v-model="form.userId" placeholder="Please select employee name">
                  <el-option
                    v-for="item in listEmployee"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                    >
                  </el-option>
              </el-select>
            </el-form-item>
          </el-form>
          <hr class="line">
          <p class="title-working m-0">Working Time</p>
          <el-form :model="form" ref="form" label-width="120px" label-position="top">
            <el-form-item custom-class="label-cusom" label="In Time" required>
              <el-col :span="7" class="mr-3">
                <el-date-picker
                  v-model="form.inDate"
                  type="date"
                  format="yyyy/MM/dd"
                  value-format="yyyy-MM-dd"
                  style="width: 100%;">
                </el-date-picker>
              </el-col>
                <span>{{ errors.first('indate') }}</span>
              <el-col :span="6">
                <el-time-picker
                  v-model="form.inTime"
                  format="HH:mm:ss"
                  value-format="HH:mm:ss"
                  style="width: 100%;">
                </el-time-picker>
              </el-col>
            </el-form-item>

            <el-form-item label="Out Time" required>
              <el-col :span="7" class="mr-3">
                <el-date-picker
                  v-model="form.outDate"
                  type="date"
                  format="yyyy/MM/dd"
                  value-format="yyyy-MM-dd"
                  style="width: 100%;">
                </el-date-picker>
              </el-col>
              <el-col :span="6">
                <el-time-picker
                  v-model="form.outTime"
                  format="HH:mm:ss"
                  value-format="HH:mm:ss"
                  style="width: 100%;">
                </el-time-picker>
              </el-col>
            </el-form-item>
          </el-form>

          <span slot="footer" class="dialog-footer">
            <el-button class="btn-cancle-custom" @click="openModalAdd = false">Cancel</el-button>
            <el-button class="btn-add-custom" type="primary" @click="createNew()">Add</el-button>
          </span>
        </el-dialog>

      </div>
    </div>
  </div>
</template>

<script>
import { getArrving, createNewWorkingTime } from '../../api/working_time';
import { getAllUser } from '../../api/user';
import { MakeToast } from '../../utils/toast_message';
import * as CONFIGS from '../../configs/index';
export default {
  name: 'WorkingTimeManagement',
  data() {
    return {
      formSearch: {
        search: '',
        startDate: '',
        endDate: ''
      },
      pagination: {
        current_page: 1,
        per_page: 20,
        total_records: 0,
        isDisable: false,
      },
      listWorkingTimes: [],
      listEmployee: [],
      employeeValue: '',
      form: {
        userId: '',
        inDate: '',
        inTime: '',
        outDate: '',
        outTime: '',
      },
      display: 'd-none',
      displaySearch: 'd-block',
      openModalAdd: false,
    };
  },
  created() {
    this.getWorkingTime();
    this.getListEmployee();
  },
  methods: {
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
    async getWorkingTime() {
      let PARAMS = {};
      if(this.search !== ''){
        PARAMS = {
          key_search: this.formSearch.search,
          start_date: this.formSearch.startDate,
          end_date: this.formSearch.endDate,
        }
      }
      await getArrving(PARAMS)
        .then((response) => {
          if (response.code === 200) {
            this.listWorkingTimes = response.data.result;
            MakeToast({
              variant: 'success',
              title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
              content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_LOGIN_SUCCESSFULLY'),
            });
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
    handleSearch() {
      this.getWorkingTime();
    },
    async getListEmployee() {
      const PARAMS = {};
      await getAllUser(PARAMS)
        .then((response) => {
          if (response.code === 200) {
            this.listEmployee = response.data.result;
          }
        })
        .catch((error) => {
          this.listEmployee = [];
        });
    },
    async createNew() {
      const PARAMS = {
        user_id: this.form.userId,
        in_time: this.form.inDate + ' ' + this.form.inTime,
        out_time: this.form.outDate + ' ' + this.form.outTime,
        registration_type: 'ipad'
      };
      console.log('PARAMS ADD', PARAMS)
      await createNewWorkingTime(PARAMS)
        .then((response) => {
          if (response.code === 200) {
            MakeToast({
              variant: 'success',
              title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
              content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_IMPORT_DATA_SUCCESSFULLY'),
            });
            this.getListEmployee();
          }
        })
        .catch((error) => {
          MakeToast({
            variant: 'warning',
            title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
            content: error.message,
          });
        });
    }
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
  color: #0070C9;
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
::v-deep .title-add-working .el-dialog__title, .title-working {
  font-weight: 600;
  font-size: 32px;
  line-height: 48px;
  color: #000000;
}
::v-deep label.el-form-item__label {
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
.pagination {
  display: flex;
  justify-content: center;
  vertical-align: middle;
  margin-top: 20px;
  margin-bottom: 10px;
}
::v-deep .page-link {
  padding: 3px 10px;
}
::v-deep .page-item {
  cursor: pointer;
}
.btn-danger:hover {
  color: #fff !important;
}
::v-deep .page-link:hover {
  border: 1px solid #0f68b1 !important;
}
</style>
