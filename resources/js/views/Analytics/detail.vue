<template>
  <div>
    <div class="container-fluid w-90">
      <div class="container-fluid-body mt-5 mb-5">
        <div class="use-management-title">
          <div class="card-body p-5">
            <div class="d-flex justify-content-between align-items-center">
              <div class="basic">
                <h1 class="title">Analytics</h1>
              </div>
            </div>
            <hr class="line">
            <p class="back-list cursor-pointer all-analytics" @click="listAllAnalytic()"> 
              <i class="el-icon-arrow-left icon-back-list"></i> All Analytics 
            </p>
            <div class="use-management-title-table mt-3">
              <div class="employee-name">
                <strong>Employee name</strong>
                <div>{{ nameEmployee }}</div>
              </div>
              <div class="fill mt-5">
                <h1>Emotion Statistics</h1>
                <div class="d-flex justify-content-end align-items-center all-date">
                  <p class="back-list cursor-pointer" @click="getAllEmmotions"> All date 
                    <i class="el-icon-arrow-down icon-back-list ml-1"></i>
                  </p>
                  <p class="back-list cursor-pointer">
                    <i class="el-icon-download custom-icon-down cursor-pointer ml-3"></i>
                  </p>
                </div>
              </div>
            <hr class="line">
              <template class="">
                <el-table
                  :data="emmotionStatistics"
                  style="width: 100%"
                >
                  <el-table-column
                    prop="time"
                    label="Date"
                    width="350"
                    align="center"
                    :formatter="formatDate"
                  />
                  <el-table-column
                    prop="happy"
                    label="Happy"
                    width="250"
                    align="center"
                  />
                  <el-table-column
                    prop="sad"
                    label="Sad"
                    align="center"
                  />
                  <el-table-column
                    prop="angry"
                    label="Angry"
                    align="center"
                  />
                  <el-table-column
                    prop="confused"
                    label="Confused"
                    align="center"
                  />
                  <el-table-column
                    prop="disgusted"
                    label="Disgusted"
                    align="center"
                  />
                  <el-table-column
                    prop="surprised"
                    label="Surprised"
                    align="center"
                  />
                  <el-table-column
                    prop="calm"
                    label="Calm"
                    align="center"
                  />
                  <el-table-column
                    prop="fear"
                    label="Fear"
                    align="center"
                  />
                </el-table>
              </template>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import {getEmotions} from '../../api/analytic';
  import * as UserApi from '../../api/user';
  export default {
    name: 'AnalyticsManagement',
    data() {
      return {
        emmotionStatistics: [],
        nameEmployee: '',
        search:'',
        pagination: {
          current_page: 1,
          per_page: 20,
          total_records: 0,
          isDisable: false,
        },
      };
    },
    created() {
      this.getEmmotionStatistics();
      this.getUser();
    },
    methods: {
      async getEmmotionStatistics() {
        let PARAMS = {
          user_id: this.$route.params.id,
          search: this.search,
          per_page: this.pagination.per_page,
          page: this.pagination.current_page,
        };
        await getEmotions(PARAMS).then((response) => {
          if (response.code === 200) {
            this.emmotionStatistics = response.data.result;
          }
        }).catch((error) => {
          this.getEmmotionStatistics = [];
        });
      },
      async getUser() {
        const id = this.$route.params.id;
        await UserApi.getOneUser(id)
          .then((response) => {
            this.nameEmployee = response.data.name;
          })
          .catch((error) => {
            this.nameEmployee = ''
          });
      },
      formatDate(row, column) {
        const date = new Date(row.time);
        const year = date.getFullYear();
        const month = date.getMonth() + 1;
        const day = date.getDate();
        return `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
      }, 
      getAllEmmotions(){
        this.search = 'all';
        this.getEmmotionStatistics();
      }, 
      listAllAnalytic(){
      this.$router.push({ path: `/analytics/index` });
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
.line-bottom {
  width: 95%;
  height: 1px;
  color: rgba(63, 63, 63, 0.4);
  margin: 0 auto;
}
.el-icon-download {
  color: #0070C9;
  font-size: 21px;
  font-weight: 600;
}
.all-date {
  cursor: pointer;
  color: #0070C9;
  font-size: 16px;
  font-weight: 500;
}
.el-icon-arrow-left {
  color: #0070C9;
  font-size:23px;
  font-weight: 600;
}
.el-icon-arrow-down {
  color: #0070C9;
  font-size: 19px;
  font-weight: 600;
}
.employee-name {
  font-size: 20px;
}
.all-analytics {
  cursor: pointer;
  color: #0070C9;
  font-size: 23px;
  font-weight: 400;
}
</style>
