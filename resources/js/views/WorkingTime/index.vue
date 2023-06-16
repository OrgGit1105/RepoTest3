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
                <button class="btn btn-date d-flex align-items-center" @click="toCreatePage">
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
            <i class="el-icon-circle-plus-outline custom-icon-add cursor-pointer" @click="createNew"></i>
            <div class="box-search align-items-center" :class="display">
              <el-input
                placeholder="検索"
                prefix-icon="el-icon-search"
                v-model="search">
              </el-input>
              <i class="el-icon-close cursor-pointer" @click="closeInputSearch()"></i>
            </div>
            <div class="d-flex justify-content-end align-items-center">
              <img :class="displaySearch" class="icon-search cursor-pointer" :src="require(`../../assets/images/icon-search.png`)" @click="openInputSearch()">
              <template class="select-custom">
                <el-select v-model="value" placeholder="Select" class="el-select-custom">
                  <el-option
                    class="el-option-custom"
                    :label="'All Employee'">
                  </el-option>
                  <el-option
                    v-for="item in selectEmployee"
                    :key="item.value"
                    :label="item.label"
                    :value="item.value"
                    divided>
                  </el-option>
                </el-select>
              </template>
            <i class="el-icon-download custom-icon-down cursor-pointer"></i>
            </div>
          </div>
          <hr class="line">
          <template class="">
            <el-table
              :data="listUser"
              style="width: 100%"
              :row-style="rowWorkingStyle"
              @row-click="showDetail">
              <el-table-column
                prop="no"
                label="No"
                width="350"
                align="center">
              </el-table-column>
              <el-table-column
                prop="employee"
                label="Employee name"
                width="250"
                align="center">
              </el-table-column>
              <el-table-column
                prop="in"
                label="IN"
                align="center">
              </el-table-column>
              <el-table-column
                prop="out"
                label="OUT"
                align="center">
              </el-table-column>
              <el-table-column
                prop="type"
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
            <el-form-item label="Activity zone">
              <el-select v-model="form.region" placeholder="please select your zone">
                <el-option label="Zone one" value="shanghai"></el-option>
                <el-option label="Zone two" value="beijing"></el-option>
              </el-select>
            </el-form-item>
          </el-form>
          <hr class="line">
          <p class="title-working m-0">Working Time</p>
          <el-form :model="form" label-width="120px" label-position="top">
            <el-form-item custom-class="label-cusom" label="In Time">
              <el-col :span="7" class="mr-3">
                <el-date-picker type="date" v-model="form.date1" style="width: 100%;"></el-date-picker>
              </el-col>
              <el-col :span="5">
                <el-time-picker v-model="form.date2" style="width: 100%;"></el-time-picker>
              </el-col>
            </el-form-item>

            <el-form-item label="Out Time">
              <el-col :span="7" class="mr-3">
                <el-date-picker type="date" v-model="form.date1" style="width: 100%;"></el-date-picker>
              </el-col>
              <el-col :span="5">
                <el-time-picker v-model="form.date2" style="width: 100%;"></el-time-picker>
              </el-col>
            </el-form-item>
          </el-form>

          <span slot="footer" class="dialog-footer">
            <el-button class="btn-cancle-custom" @click="openModalAdd = false">Cancel</el-button>
            <el-button class="btn-add-custom" type="primary" @click="openModalAdd = false">Add</el-button>
          </span>
        </el-dialog>

      </div>
    </div>
  </div>
</template>

<script>
import { getArrving } from '../../api/working_time';
// import { MakeToast } from '../../utils/toast_message';
import * as CONFIGS from '../../configs/index';
export default {
  name: 'WorkingTimeManagement',
  data() {
    return {
      search: '',
      pagination: {
        current_page: 1,
        per_page: 20,
        total_records: 0,
        isDisable: false,
      },
      headQuarter: CONFIGS.UserRoleId.HEAD_QUARTER,
      infoModel: {},
      listUser: [
        { no: '1', employee: 'kohei', in: '2023-10-10-10:10', out: '2023-10-10-10:10', type: '2023-10-10-10:10', id: '1' },
        { no: '2', employee: 'kohei', in: '2023-10-10-10:10', out: '2023-10-10-10:10', type: '2023-10-10-10:10', id: '2' },
        { no: '3', employee: 'kohei', in: '2023-10-10-10:10', out: '2023-10-10-10:10', type: '2023-10-10-10:10', id: '3' },
        { no: '4', employee: 'kohei', in: '2023-10-10-10:10', out: '2023-10-10-10:10', type: '2023-10-10-10:10', id: '4' },
        { no: '5', employee: 'kohei', in: '2023-10-10-10:10', out: '2023-10-10-10:10', type: '2023-10-10-10:10', id: '5' },
        { no: '6', employee: 'kohei', in: '2023-10-10-10:10', out: '2023-10-10-10:10', type: '2023-10-10-10:10', id: '6' },
      ],
      selectEmployee: [
        { value: '1', text: 'All Employee' },
        { value: '1', text: 'IKeda Kohei' },
        { value: '1', text: 'IKeda Kohei' },
        { value: '1', text: 'IKeda Kohei' },
        { value: '1', text: 'IKeda Kohei' },
        { value: '1', text: 'IKeda Kohei' },
        { value: '1', text: 'IKeda Kohei' },
      ],
      form: {
        region: '',
        date1: '',
        date2: '',
      },
      display: 'd-none',
      displaySearch: 'd-block',
      openModalAdd: false,
    };
  },
  created() {
    this.getWorkingTime();
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
    createNew: function() {
      this.openModalAdd = true;
    },
    showDetail: function(row, column, event) {
      console.log(row.id);
      this.$router.push({ name: 'WorkingTimeManagementDetail', id: row.id });
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
          key_search: this.search,
        }
      }
      console.log('hy',PARAMS);
      await getArrving(PARAMS)
        .then((response) => {
          console.log('hy rs', response);
        })
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
