<template>
  <div>
    <div class="container-fluid w-90">
      <div class="container-fluid-body mt-5 mb-5">
        <div class="use-management-title">
          <div class="card-body p-5">
            <div class="d-flex justify-content-between align-items-center">
              <div class="basic">
                <h1 class="title">VIAM RDS</h1>
              </div>
            </div>
          </div>
        </div>
        <hr class="line-bottom">
        <div class="use-management-title-table mt-5">
          <div class="fill d-flex justify-content-end">
            <div class="select-custom">
              <el-select
                v-model="rds_id"
                placeholder="Select"
                class="el-select-custom"
                value=""
              >
                <el-option
                  class="el-option-custom"
                  label="Select RDS"
                  value=""
                />
                <el-option
                  v-for="item in listRDS ?? []"
                  :key="item.id"
                  :label="item.name"
                  :value="item.id"
                />
              </el-select>
            </div>

            <div class="select-custom">
              <el-select
                v-model="database_name"
                :disabled="!rds_id"
                placeholder="Select"
                class="el-select-custom"
                value=""
              >
                <el-option
                  class="el-option-custom text-default"
                  label="Select database"
                  value=""
                />
                <el-option
                  v-for="item in listDatabases ?? []"
                  :key="item"
                  :label="item"
                  :value="item"
                />
              </el-select>
            </div>
          </div>
          <b-table
            id="my-table"
            class="text-center w-100 bg-dx-grey-blur mb-0"
            :items="listViamRDS"
            :fields="fields"
            responsive="sm"
            show-empty
          >
            <template #cell(data)>
              <a
                id="file_name_data_point"
                class="bg-white text-dark fs-14 digitaco-point"
                @click="&quot;&quot;;"
              >{{ data.item.employee }}</a>
            </template>
            <template #cell(config_rds)="data">
              <u
                id="file_name_data_driving"
                class="bg-white text-dark fs-14 digitaco-driving cursor-pointer text-blue"
                @click="goToEditScreen(data.item.id)"
              >
                {{ data.item.config_rds }}
              </u>
            </template>
            <template #cell(status)="data">
              <div class="select-custom">
                <el-select
                  v-model="data.item.status"
                  placeholder=""
                  :class="data.item.status === 'denied' ? 'el-select-custom text-colour-blue' : 'el-select-custom text-colour-red'"
                  @change="handleChangeStatus(data.item)"
                >
                  <el-option
                    v-for="item in listStatus ?? []"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  />
                </el-select>
              </div>
            </template>
          </b-table>
        </div>

        <!-- <div class="use-management-pagianation">
          <div class="card-body pagianation">
            <el-pagination
              background
              layout="prev, pager, next"
              class="d-flex justify-content-center"
              :page-size="pagination.per_page"
              :total="pagination.total_records"
              :current-page.sync="pagination.current_page"
              @current-change="getListAllUser"
            />
          </div>
        </div> -->
      </div>
    </div>
  </div>
</template>

<script>
import {
  getListViamRds,
  getListRDS,
  getListDatabases,
} from '../../api/viamUser';
import { MakeToast } from '../../utils/toast_message';
// import { ValidationObserver, ValidationProvider } from 'vee-validate';

export default {
  name: 'ViamRDSManagement',
  // components: {
  //   ValidationObserver,
  //   ValidationProvider,
  // },
  data() {
    return {
      pagination: {
        current_page: 1,
        per_page: 20,
        total_records: 0,
        isDisable: false,
      },

      form: {
        name: '',
        description: '',
      },
      openModalAdd: false,
      listViamRDS: [
        // {
        //   id: 1,
        //   employee: 'Nawa',
        //   config_rds: 'Data(1)/Structure(1)/Administration(1)',
        //   status: 'denied',
        // },
        // {
        //   id: 2,
        //   employee: 'Nawa 2',
        //   config_rds: 'Data(0)/Structure(0)/Administration(0)',
        //   status: 'denied',
        // },
        // {
        //   id: 3,
        //   employee: 'Nawa 3',
        //   config_rds: 'Data(0)/Structure(0)/Administration(0)',
        //   status: 'active',
        // },
      ],
      listRDS: [],
      listDatabases: [],
      listStatus: [
        {
          id: 'denied',
          name: 'Denied',
        },
        {
          id: 'active',
          name: 'Active',
        },
      ],

      database_name: '',
      rds_id: '',
      selectedStatus: '',

      fields: [
        { key: 'employee', label: 'Employee', class: 'getting_date' },
        {
          key: 'config_rds',
          label: 'Config RDS',
          class: 'file_name_data_point',
        },
        { key: 'status', label: 'Status', class: 'file_name_data_driving' },
      ],
    };
  },
  computed: {
    currChange() {
      return this.pagination.current_page;
    },
  },
  watch: {
    currChange() {
      this.getListAllUser();
    },
    rds_id(newVal) {
      if (newVal) {
        this.getListDatabases();
      }
    },
    database_name(newVal) {
      if (newVal) {
        this.getListViamRds();
      }
    },
  },
  created() {
    this.getListRDS();
  },
  methods: {
    openLoading() {
      this.$store.dispatch('loading/setLoading', true);
    },
    closeLoading() {
      this.$store.dispatch('loading/setLoading', false);
    },
    async getListViamRds() {
      this.openLoading();
      //   this.pagination.isDisable = true;
      //   const PARAMS = {
      //     page: this.pagination.current_page,
      //     per_page: this.pagination.per_page,
      //   };
      await getListViamRds(this.rds_id, this.database_name)
        .then((response) => {
          if (response.code === 200) {
            this.listViamRDS = response.data.result.map((item) => {
              return {
                id: item.id,
                employee: item.username,
                config_rds: `Data(${item.count_data})/Structure(${item.count_structure})/Administration(${item.count_administration})`,
                status: item.status ? 'active' : 'denied',
              };
            });

            console.log('this.listViamRDS ==>', this.listViamRDS);
            // this.$store.dispatch('app/saveListUSer', listUser);
            // this.pagination.total_records =
            //     response.data.pagination.total_records;
            // this.pagination.current_page = response.data.pagination.current_page;
            // this.pagination.isDisable = false;
          }
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

    async getListRDS() {
      this.openLoading();
      const URL = '/rds_manager';
      //   this.pagination.isDisable = true;
      //   const PARAMS = {
      //     page: this.pagination.current_page,
      //     per_page: this.pagination.per_page,
      //   };
      await getListRDS(URL)
        .then((response) => {
          if (response.code === 200) {
            this.listRDS = response.data;

            // this.$store.dispatch('app/saveListUSer', listUser);
            // this.pagination.total_records =
            //     response.data.pagination.total_records;
            // this.pagination.current_page = response.data.pagination.current_page;
            // this.pagination.isDisable = false;
          }
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

    async getListDatabases() {
      // this.openLoading();
      const URL = `${this.rds_id}`;

      //   this.pagination.isDisable = true;
      //   const PARAMS = {
      //     page: this.pagination.current_page,
      //     per_page: this.pagination.per_page,
      //   };
      await getListDatabases(URL)
        .then((response) => {
          if (response.code === 200) {
            this.listDatabases = response.data;

            // this.$store.dispatch('app/saveListUSer', listUser);
            // this.pagination.total_records =
            //     response.data.pagination.total_records;
            // this.pagination.current_page = response.data.pagination.current_page;
            // this.pagination.isDisable = false;
          }
          // this.closeLoading();
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
    changePage(page) {
      // console.log('Page ban vua chon', page);
    },
    // copy cua Yen
    rowWorkingStyle({ row, rowIndex }) {
      return { cursor: 'pointer' };
    },

    handleEditViamRDS(val) {
      // this.openModalAddRole = true;
      // this.openModalAddRole = true;
      this.$router.push({ path: `/viam-rds/edit/${val.id}` });
    },

    goToEditScreen(val) {
      this.$router.push({ path: `/viam-rds/edit/${val.id}` }, (onAbort) => {});
    },

    handleChangeStatus(item) {
      if (item.status === 'active') {
        // Thực hiện mở để chọn
        this.$router.push({ path: `/viam-rds/edit/${item.id}` });
      } else {
        // thực hiện call denied xóa quyền
        console.log('Denied');
      }
    },
  },
};
</script>
<style scoped>
#screen-title {
  position: flex;
  text-align: center;
  margin-top: 50px;
}
.form-tag {
  position: relative;
}
::v-deep .dropdown-menu {
  position: absolute;
  left: 0px;
  width: 100%;
  top: 95%;
  z-index: 999999999;
  max-height: 180px;
  overflow-y: auto;
}
::v-deep .el-tag + .el-tag {
  margin-left: 10px;
}
::v-deep .b-form-tags-button {
  display: none;
}
::v-deep .el-select {
  width: 100%;
}
::v-deep .el-textarea__inner {
  height: 100px;
  resize: none;
}
::v-deep .el-dialog__body {
  padding: 10px 20px;
}

::v-deep table .b-table {
  width: 100% !important;
}
table#__BVID__46 {
  width: 100% !important;
}
::v-deep table#__BVID__15 {
  width: 100% !important;
  border-left: 0.9px solid #888888;
}
::v-deep .table {
  width: 100%;
}
::v-deep .table thead {
  background: none;
}
/* ::v-deep .table tbody {
   border: 0.9px solid #888888;
   } */
/* ::v-deep .table thead th {
   border: 0.9px solid #888888;
   } */
::v-deep .table td {
  /*background: #ffffff !important;*/
  /*border-top: 0 !important;*/
  /*border-right: 0.9px solid #888888;*/
  /*border-top: 0.9px solid #888888;*/
  line-height: 30px;
}
.btn {
  border: 0 !important;
}

.btn-secondary:hover {
  border: none !important;
}
.btn-delete {
  background: #e9240a;
}
.btn-delete:hover {
  background-color: #cc1800;
}
.style-modal {
  border-bottom: 1px solid #dee2e6;
}
.buttons-control {
  text-align: center;
  margin-top: 20px;
  margin-bottom: 20px;
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
.btn-close {
  background: #0f68b1;
}
::v-deep .btn-accept {
  background-color: transparent !important;
  color: #0f68b1;
  border: 1px solid #0f68b1 !important;
}
.btn-accept:hover {
  box-shadow: 0 5px 11px 0 rgb(0 0 0 / 18%), 0 4px 15px 0 rgb(0 0 0 / 15%);
  background: #0f68b1 !important;
  transition: all 0.2s ease-in-out;
}
.btn-close:hover {
  box-shadow: 0 5px 11px 0 rgb(0 0 0 / 18%), 0 4px 15px 0 rgb(0 0 0 / 15%);
  background-color: transparent !important;
  transition: all 0.2s ease-in-out;
  color: #0f68b1;
  border: 1px solid #0f68b1 !important;
}
::v-deep #bv-modal-delete___BV_modal_body_ {
  padding: 0 !important;
}
::v-deep #bv-modal-delete___BV_modal_content_ {
  border: 0 !important;
  border-radius: 0 !important;
}
.style-modal h4 {
  font-weight: 300 !important;
  margin-bottom: 0px !important;
}
::v-deep thead {
  background: #e5e5e5;
}
/* ::v-deep #my-table th {
   background: #e5e5e5;
   } */
::v-deep .style-title-modal {
  background: #0f68b1;
}
.style-title-modal h4 {
  font-size: 18px;
}
.style-modal h2 {
  margin: 25px 0px;
  color: red;
  font-size: 23px;
}
.text-error {
  line-height: normal;
  word-break: break-word;
  overflow-wrap: break-word;
  word-wrap: break-word;
  -webkit-hyphens: auto;
  -ms-hyphens: auto;
  hyphens: auto;
  color: red;
  font-size: 12px;
}
.email-link {
  text-decoration: none;
  transition: color 0.3s ease;
  cursor: pointer;
}
.email-link:hover {
  color: blue;
}
.image-dropzone {
  border: 2px solid #ccc;
  padding: 20px;
  text-align: center;
  background: rgb(245 246 247);
  /*width: 800px;*/
}
.image-dropzone p {
  margin: 0;
}
.image-preview {
  display: table;
  flex-wrap: wrap;
  height: 200px;
  margin: 15px;
}
.preview-item {
  display: inline-block;
  margin: 10px;
}
.preview-item img {
  width: 180px;
  height: 200px;
}
.preview-item button {
  margin-top: 5px;
}
.check_with_or_without_mask {
  border-bottom: 4px solid;
}
/*copy tu Yen*/
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
.fill {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 90%;
  margin: 0 auto;
}
.custom-icon-down {
  color: #0070c9;
  font-size: 26px;
  font-weight: 600;
}
.box-search {
  margin-left: 214px;
}
::v-deep .box-search .el-input__inner {
  border: 1px solid rgba(63, 63, 63, 0.4);
  border-radius: 5px;
  padding-left: 40px;
}
::v-deep .box-search .el-icon-search {
  color: #3f3f3f;
  font-weight: bolder;
  font-size: 20px;
}
::v-deep .box-search ::placeholder {
  color: #8a8a8a;
}
::v-deep .box-search .el-icon-close {
  margin-left: 10px;
  font-size: 25px;
  color: #8a8a8a;
}
.use-management-title-table {
  padding: 0 45px;
}
.cursor-pointer {
  cursor: pointer;
}
.el-select-custom {
  width: 175px;
  margin: 0 20px;
}
::v-deep .el-select-custom .el-input__inner {
  border: unset;
  border-radius: unset;
  font-size: 16px;
  font-weight: 500;
  text-align: center;
  color: #0070c9
}

::v-deep .el-select-custom.text-colour-blue .el-input__inner  {
  background-color: #fce4d6;
  color: #000 !important
}

::v-deep .el-select-custom.text-colour-red .el-input__inner  {
  background-color: #D6FCD8 ;
  color: #000 !important
}

::v-deep .el-select-custom .el-input .el-select__caret {
  font-weight: bolder;
  font-size: 20px;
  margin-top: 3px;
}
::v-deep .title-add-working .el-dialog__title,
.title-create-employee {
  font-weight: 600;
  font-size: 32px;
  line-height: 28px;
  color: #000000;
}
.title-body {
  font-weight: 600;
  font-size: 32px;
  line-height: 28px;
  color: #000000;
  margin-bottom: 20px;
}
.bg-gray {
  background-color: #eee;
}
.el-select-custom {
  width: 175px;
  margin: 0 20px;
}
</style>
