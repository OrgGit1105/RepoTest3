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
            </div>
          </div>
        </div>

        <hr class="line-bottom">

        <div class="use-management-title-table mt-5">
          <p class="back-list cursor-pointer" @click="listWorkingRecord()"> <i class="el-icon-arrow-left icon-back-list" /> All Working Records </p>
          <div class="card-body p-card-body">
            <el-form ref="ruleForm" :model="dataWorkingTimeRecord" :rules="rules" label-width="120px" label-position="top">
              <!-- Working Record -->
              <div class="d-flex justify-content-between align-items-center">
                <div class="basic">
                  <h1 class="title-record m-0">Working Record</h1>
                </div>
                <div class="basic">
                  <el-button class="btn-add-custom" type="primary" @click="submitForm('ruleForm')">Save</el-button>
                </div>
              </div>
              <hr class="line">
              <div class="cover-working-record">
                <div class="working-record">
                  <p class="header-working-record fw-5">No</p>
                  <p class="header-working-record fw-5">Employee name</p>
                  <p class="header-working-record fw-5">Input Type</p>
                  <p class="header-working-record fw-5">Type</p>
                  <p class="header-working-record">{{ dataWorkingTimeRecord.id }}</p>
                  <p class="header-working-record">{{ dataWorkingTimeRecord.user ? dataWorkingTimeRecord.user.name : '' }}</p>
                  <p class="header-working-record">{{ dataWorkingTimeRecord.registration_type }}</p>
                  <div class="header-working-record">
                    <el-select
                      id="type_date"
                      v-model="dataWorkingTimeRecord.type_date"
                      :style="{ width: '150px' }"
                    >
                      <el-option
                        v-for="item in listWorkingType"
                        :key="item.id"
                        :label="item.name"
                        :value="item.id"
                      />
                    </el-select>
                  </div>
                </div>
              </div>

              <!-- Working Time -->
              <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="basic">
                  <h1 class="title-record">{{ updateStatusHeader() }} Time</h1>
                </div>
                <div class="basic" />
              </div>
              <hr class="line">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <p class="title-time">Date</p>
                  <div class="d-flex justify-content-start align-items-center">
                    <el-form-item prop="date">
                      <el-date-picker
                        v-model="dataWorkingTimeRecord.date"
                        type="date"
                        format="yyyy-MM-dd"
                        value-format="yyyy-MM-dd"
                      />
                    </el-form-item>
                  </div>
                </div>
                <div>
                  <p class="title-time">In Time</p>
                  <div class="d-flex justify-content-start align-items-center">
                    <el-form-item prop="in_time">
                      <el-time-picker
                        v-model="dataWorkingTimeRecord.in_time"
                        format="HH:mm:ss"
                        value-format="HH:mm:ss"
                      />
                    </el-form-item>
                  </div>
                </div>
                <div>
                  <p class="title-time">Out Time</p>
                  <div class="d-flex justify-content-start align-items-center">
                    <el-form-item prop="out_time">
                      <el-time-picker
                        v-model="dataWorkingTimeRecord.out_time"
                        format="HH:mm:ss"
                        value-format="HH:mm:ss"
                      />
                    </el-form-item>
                  </div>
                </div>
              </div>

              <!-- Break Time -->
              <div class="d-flex justify-content-between align-items-center mt-2">
                <div class="basic">
                  <h1 class="title-record">Break Time</h1>
                </div>
                <div class="basic" />
              </div>
              <hr class="line">
              <div class="">
                <el-table
                  :data="dataWorkingTimeRecord.break_time ? dataWorkingTimeRecord.break_time : []"
                  style="width: 100%"
                  :row-style="rowWorkingStyle"
                >
                  <el-table-column
                    label="No"
                    align="center"
                  >
                    <template slot-scope="{ $index }">
                      {{ $index + 1 }}
                    </template>
                  </el-table-column>
                  <el-table-column
                    prop="date"
                    label="Date"
                    align="center"
                  />
                  <el-table-column
                    prop="go_into_time"
                    label="Go into time"
                    align="center"
                  />
                  <el-table-column
                    prop="go_out_time"
                    label="Go out time"
                    align="center"
                  />
                </el-table>
              </div>
              <!-- Remark -->
              <div class="d-flex justify-content-between align-items-center mt-2">
                <div class="basic">
                  <h1 class="title-record">Remark</h1>
                </div>
                <div class="basic" />
              </div>
              <hr class="line">
              <el-form-item>
                <el-input
                  v-model="dataWorkingTimeRecord.remark"
                  type="textarea"
                  :rows="5"
                  class="textarea-style"
                />
              </el-form-item>
            </el-form>
          </div>
          <p class="delete-record cursor-pointer" @click="showModalDelete = true"> Delete Working Record </p>
        </div>

        <!-- Modal delete -->
        <el-dialog
          title="DELETE"
          :visible.sync="showModalDelete"
          width="30%"
          center
        >
          <span class="text-align-center">Are you sure to delete this working time record?</span>
          <span slot="footer" class="dialog-footer">
            <el-button @click="showModalDelete = false">Cancel</el-button>
            <el-button type="danger" @click="deleteWorkingRecord()">Confirm</el-button>
          </span>
        </el-dialog>

      </div>
    </div>
  </div>
</template>

<script>
import { getWokingTimeDetailById, editWorkingTimeById, deleteWorkingTimeById } from '../../api/working_time';
import { MakeToast } from '../../utils/toast_message';
export default {
  name: 'WorkingTimeManagement',
  data() {
    return {
      dataWorkingTimeRecord: {
        user_id: '',
        date: '',
        in_time: '',
        out_time: '',
        type_date: '',
        remark: '',
      },
      showModalDelete: false,
      dateRangeOptions1: {
        firstDayOfWeek: 5,
      },
      listWorkingType: [
        { id: 1, name: 'Working' },
        { id: 2, name: 'Remote' },
        { id: 3, name: 'Day off' },
        { id: 4, name: 'Special day off' },
      ],
      rules: {
        date: [
          { required: true, message: 'Please pick a date', trigger: 'change' },
        ],
        in_time: [
          { required: true, message: 'Please pick a time in', trigger: 'change' },
        ],
        out_time: [
          { required: true, message: 'Please pick a time out', trigger: 'change' },
        ],
      },
    };
  },
  watch: {
    'dataWorkingTimeRecord.type_date': function() {
      const isRequired = this.dataWorkingTimeRecord.type_date !== 1;
      this.rules.out_time[0].required = isRequired;
    },
  },
  created() {
    this.getWorkingRecordById();
  },
  methods: {
    submitForm(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          this.editWorkingTime();
        } else {
          return false;
        }
      });
    },
    resetForm(formName) {
      this.$refs[formName].resetFields();
    },
    rowWorkingStyle({ row, rowIndex }) {
      return { 'cursor': 'pointer' };
    },
    listWorkingRecord() {
      this.$router.push({ path: `/working-time/index` });
    },
    async getWorkingRecordById() {
      const id = this.$route.params.id;
      await getWokingTimeDetailById({ id })
        .then((response) => {
          if (response.code === 200) {
            const res = { ...response };
            response.data.result.date = res.data.result.in_time?.split(' ')[0];
            // 2 line above are needed because reference will mess the data

            this.dataWorkingTimeRecord = response.data.result;
            this.dataWorkingTimeRecord.in_time = response.data.result.in_time?.split(' ')[1];
            this.dataWorkingTimeRecord.out_time = response.data.result.out_time?.split(' ')[1];
          }
        })
        .catch(() => {
          this.dataWorkingTimeRecord = [];
        });
    },
    async editWorkingTime() {
      const id = this.$route.params.id;
      const { user_id, date, remark, type_date } = this.dataWorkingTimeRecord;
      let { in_time, out_time } = this.dataWorkingTimeRecord;
      in_time = `${date} ${in_time}`;
      out_time = out_time ? `${date} ${out_time}` : null;

      const DATA = { user_id, date, in_time, out_time, type_date, remark };

      console.log('file: detail.vue:252 / DATA:  ===>', DATA);
      await editWorkingTimeById({ id }, DATA)
        .then((response) => {
          if (response.code === 200) {
            MakeToast({
              variant: 'success',
              title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
              content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_EDIT_SUCCESSFULLY'),
            });
            this.$router.push({ path: `/working-time/index` });
          } else {
            MakeToast({
              variant: 'danger',
              title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_FAILED'),
              content: response.message,
            });
          }
        })
        .catch((error) => {
          MakeToast({
            variant: 'danger',
            title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_FAILED'),
            content: error.message,
          });
        });
    },
    async deleteWorkingRecord() {
      const id = this.$route.params.id;
      await deleteWorkingTimeById({ id })
        .then((response) => {
          if (response.code === 200) {
            MakeToast({
              variant: 'success',
              title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
              content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_DELETE_SUCCESSFULLY'),
            });
            this.$router.push({ path: `/working-time/index` });
          } else {
            MakeToast({
              variant: 'danger',
              title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_FAILED'),
              content: response.message,
            });
          }
        })
        .catch((error) => {
          MakeToast({
            variant: 'danger',
            title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_FAILED'),
            content: error.message,
          });
        });
    },
    updateStatusHeader() {
      const item = this.listWorkingType.find(item => this.dataWorkingTimeRecord.type_date === item.id);
      return item ? item.name : '';
    },
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
.line-bottom {
  width: 95%;
  height: 1px;
  color: rgba(63, 63, 63, 0.4);
  margin: 0 auto;
}
.cursor-pointer {
  cursor: pointer;
}
.use-management-title-table {
  padding: 0 45px;
}
.line {
  width: 100%;
  height: 1px;
  color: rgba(63, 63, 63, 0.4);
  margin: 15px auto;
}
::v-deep .title-add-working .el-dialog__title, .title-working {
  font-weight: 600;
  font-size: 32px;
  line-height: 48px;
  color: #000000;
}
.back-list {
	color: #0070C9;
	font-weight: 400;
	font-size: 23px;
	margin: 0px;
}
.icon-back-list {
	font-weight: 600;
}
::v-deep .btn-add-custom {
  background: #0070C9;
  border-radius: 5px;
  width: 110px;
	font-size: 20px;
}
::v-deep .p-card-body {
	padding: 35px 30px;
}
::v-deep .title-record {
	font-weight: 600;
	font-size: 35px;
	margin: 0px;
  margin-top: 5rem;
}
::v-deep .delete-record {
	color: #C90000;
	font-weight: 400;
	font-size: 23px;
	margin: 0px;
	padding: 0px 10px;
}
::v-deep .textarea-style textarea {
	background-color: #F9F9F9;
	border: 1px solid rgba(63, 63, 63, 0.4);
}
.inputDate {
	width: 15%;
	display: block;
}
::v-deep .inputDate {
	text-align: center;
	font-size: 18px;
	color: #000000;
	font-weight: 400;
  margin: 0;
}
.inputTime{
	width: 6%;
	display: block;
}
::v-deep .inputTime .el-input__inner {
	padding: unset;
	width: 100%;
	text-align: center;
	font-size: 18px;
	color: #000000;
	font-weight: 400;
	border: 1px solid rgba(63, 63, 63, 0.4);
	border-radius: 5px;
}
.title-time {
	font-weight: 500;
	font-size: 20px;
	color: #000000;
	margin-bottom: 5px;
}
::v-deep .working-record {
  display: flex;
  width: 100%;
  flex-wrap: wrap;
  flex-direction: row;
  justify-content: space-around;
  text-align: left;
}
::v-deep .header-working-record {
  width: calc(100% / 4);
  height: 40px;
  margin: 0;
  font-size: 20px;
}
.fw-5 {
  font-weight: 500;
}
::v-deep .el-input.is-disabled .el-input__inner{
  background-color: unset;
  color: #000000;
  cursor: text;
  border: unset;
  text-align: center;
  font-size: 18px;
  font-weight: unset;
}
::v-deep .disable-date-custom .el-input__prefix {
  display: none;
}
</style>
