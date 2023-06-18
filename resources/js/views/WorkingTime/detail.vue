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
          <p class="back-list cursor-pointer"> <i class="el-icon-arrow-left icon-back-list"></i> All Working Records </p>
					<div class="card-body p-card-body">
						<!-- Working Record -->
            <div class="d-flex justify-content-between align-items-center">
              <div class="basic">
                <h1 class="title-record">Working Record</h1>
              </div>
              <div class="basic">
                <el-button class="btn-add-custom" type="primary">Save</el-button>
              </div>
            </div>
            <hr class="line">
						<template class="table-record">
							<el-table
								:data="listWorkingTimes"
								style="width: 100%"
								:row-class-name="rowWorkingStyle">
								<el-table-column
									prop="id"
									label="No"
									align="center">
								</el-table-column>
								<el-table-column
									prop="name"
									label="Employee name"
									align="center">
								</el-table-column>
								<el-table-column
									prop="type"
									label="Registration type"
									align="center">
								</el-table-column>
							</el-table>
						</template>

						<!-- Working Time -->
						<div class="d-flex justify-content-between align-items-center mt-3">
              <div class="basic">
                <h1 class="title-record">Working Time</h1>
              </div>
              <div class="basic"></div>
            </div>
            <hr class="line">
						<div class="time-line">
							<p class="title-time">In Time</p>
							<div class="d-flex justify-content-start mb-3">
								<el-input class="inputDate" v-model="inDate"></el-input>
								<el-input class="inputTime" v-model="inTime"></el-input>
							</div>

							<p class="title-time">Out Time</p>
							<div class="d-flex justify-content-start mb-3">
								<el-input class="inputDate" v-model="outDate"></el-input>
								<el-input class="inputTime" v-model="outTime"></el-input>
							</div>
						</div>

						<!-- Remark -->
						<div class="d-flex justify-content-between align-items-center">
              <div class="basic">
                <h1 class="title-record">Remark</h1>
              </div>
              <div class="basic"></div>
            </div>
            <hr class="line">
						<el-input
							type="textarea"
							:rows="5"
							v-model="textarea"
							class="textarea-style">
						</el-input>
          </div>
          <p class="delete-record cursor-pointer"> Delete Working Record </p>
        </div>

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
      listWorkingTimes: [
				{ id: '1', name: 'IKeda Kohei ', type: 'ipad' }
			],
			textarea: '',
			inDate: 'September 20, 2023',
			inTime: '8:30',
			outDate: 'September 20, 2023',
			outTime: '18:30',
    };
  },
  created() {

  },
  methods: {
		rowWorkingStyle({ row, rowIndex }) {
      return 'border-style-table';
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
}
::v-deep .delete-record {
	color: #C90000;
	font-weight: 400;
	font-size: 23px;
	margin: 0px;
	padding: 0px 10px;
}
::v-deep .border-style-table td{
	box-shadow: none !important;
	border-color:transparent !important;
}
::v-deep table{
	border:1px solid #F9F9F9;
	box-shadow: none;
}
::v-deep .textarea-style textarea {
	background-color: #F9F9F9;
	border: 1px solid rgba(63, 63, 63, 0.4);
}
.inputDate {
	width: 15%;
	display: block;
}
::v-deep .inputDate .el-input__inner {
	padding: unset;
	width: 100%;
	border: unset;
	text-align: center;
	font-size: 18px;
	color: #000000;
	font-weight: 400;
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
.time-line {
	padding: 15px 0px;
}
</style>
