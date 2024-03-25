<template>
  <div>
    <div class="container-fluid w-90">
      <div class="container-fluid-body mt-5 mb-5">
        <div class="use-management-title">
          <div class="card-body p-5">
            <div class="d-flex justify-content-between align-items-center">
              <div class="basic">
                <h1 class="title">{{ 'Setting RDS Role' }}</h1>
              </div>
            </div>
          </div>
        </div>
        <hr class="line-bottom">
        <div class="use-management-title-table mt-5">
          <p class="back-list cursor-pointer" @click="backToList()"> <i class="el-icon-arrow-left icon-back-list" /> All VIAM RDS </p>
          <div class="card-body p-card-body">
            <div>
              <h3>Databases</h3>
              <div class="d-flex justify-content-end">
                <el-button type="danger" plain>Cancel</el-button>
                <el-button type="primary" @click="handleSave">Save</el-button>
              </div>
            </div>
            <hr class="line">
            <el-row :gutter="20" class="mt-5">
              <el-col :span="8">
                <div>
                  <el-checkbox v-model="checkAllDataTab" :indeterminate="isIndeterminateDataTab" @change="handlecheckAllChangeDataTab">Data</el-checkbox>
                  <el-checkbox-group v-model="checkedDataTab" class="pl-4 d-flex flex-column" @change="handleCheckedChangeDataTab">
                    <el-checkbox v-for="data in DATABASE_DATA" :key="data.id" :label="data.id">{{ data.name }}</el-checkbox>
                  </el-checkbox-group>
                </div>
              </el-col>
              <el-col :span="8">
                <div class="d-flex flex-column">
                  <el-checkbox v-model="checkAllStructureTab" :indeterminate="isIndeterminateStructureTab" @change="handlecheckAllChangeStructureTab">Structure</el-checkbox>
                  <el-checkbox-group v-model="checkedStructureTab" class="pl-4 d-flex flex-column" @change="handleCheckedChangeStructureTab">
                    <el-checkbox v-for="data in DATABASE_STRUCTURE" :key="data.id" :label="data.id">{{ data.name }}</el-checkbox>
                  </el-checkbox-group>
                </div>
              </el-col>
              <el-col :span="8">
                <div class="d-flex flex-column">
                  <el-checkbox v-model="checkAllAdministratorTab" :indeterminate="isIndeterminateAdministratorTab" @change="handlecheckAllChangeAdministratorTab">Administrator</el-checkbox>
                  <el-checkbox-group v-model="checkedAdministratorTab" class="pl-4 d-flex flex-column" @change="handleCheckedChangeAdministratorTab">
                    <el-checkbox v-for="data in DATABASE_ADMINISTRATOR" :key="data.id" :label="data.id">{{ data.name }}</el-checkbox>
                  </el-checkbox-group>
                </div>
              </el-col>
            </el-row>
            <p class="delete-record cursor-pointer mt-5" @click="showModalDelete= true"> Delete RDS </p>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal delete -->
    <el-dialog
      title="DELETE"
      :visible.sync="showModalDelete"
      width="30%"
      center
    >
      <span class="text-align-center">Are you sure to delete this Viam?</span>
      <span slot="footer" class="dialog-footer">
        <el-button @click="showModalDelete= false">Cancel</el-button>
        <el-button type="danger" @click="submitDelete()">Confirm</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>

import * as UserApi from '../../api/viamUser';
import * as CONFIGS from '../../configs';
import { MakeToast } from '../../utils/toast_message';
// import { ValidationObserver, ValidationProvider } from 'vee-validate';
import { createRdsRole, deleteOneUser } from '../../api/viamUser';

export default {
  name: 'EditViamRDSManagement',
  // components: {
  //   ValidationObserver,
  //   ValidationProvider,
  // },
  data() {
    return {
      formEdit: {
        name: '',
        description: '',
      },
      id: this.$route.params.id,
      showModalDelete: false,

      checkAllDataTab: false,
      checkedDataTab: [],
      isIndeterminateDataTab: true,

      checkAllStructureTab: false,
      checkedStructureTab: [],
      isIndeterminateStructureTab: true,

      checkAllAdministratorTab: false,
      checkedAdministratorTab: [],
      isIndeterminateAdministratorTab: true,

      DATABASE_DATA: CONFIGS.DATABASES.data,
      DATABASE_STRUCTURE: CONFIGS.DATABASES.structure,
      DATABASE_ADMINISTRATOR: CONFIGS.DATABASES.administrator,
    };
  },
  watch: {
  },
  created() {
    this.initData();
    // this.getListPolicy();
    // this.getUserInfo();
  },

  methods: {
    async initData() {
      this.openLoading();
      // await this.getUserInfo();
      this.closeLoading();
    },
    openLoading() {
      this.$store.dispatch('loading/setLoading', true);
    },
    closeLoading() {
      this.$store.dispatch('loading/setLoading', false);
    },
    async getUserInfo() {
      this.openLoading();
      await UserApi.getOneUser(this.id)
        .then((response) => {
          this.formEdit = {
            name: response.data.name,
            description: response.data.description,
          };
          this.selectedTagPolicy_id = response.data.policies.map(item => item.id);
          this.selectedTagPolicy = response.data.policies.map(item => item.name);
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
    async onSubmit(e) {
      e.preventDefault();
      this.openLoading();
      const isValid = await this.$refs.obsEditEmployee.validate();
      const isValidpolicy = await this.$refs.obsEditpolicy.validate();
      if (isValid && isValidpolicy) {
        const DATA = {
          name: this.formEdit.name,
          policy_id: this.selectedTagPolicy_id,
          description: this.formEdit.description,
        };
        console.log('data', DATA);
        await UserApi.putOneUser(this.id, DATA)
          .then(async(response) => {
            if (response.code === 200) {
              this.closeLoading();
              MakeToast({
                variant: 'success',
                title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
                content: 'Edit viam user success',
              });
              this.$router.push('/viam-rds/index');
            } else {
              this.closeLoading();
              MakeToast({
                variant: 'warning',
                title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
                content: response.message,
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
      } else {
        MakeToast({
          variant: 'warning',
          title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
          content: 'Still error',
        });
      }
    },

    async submitDelete() {
      if (this.id) {
        await deleteOneUser(this.id)
          .then(async(response) => {
            if (response.code === 200){
              this.showModalDelete = false;
              MakeToast({
                variant: 'success',
                title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
                content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_DELETE_USER_SUCCESSFULLY'),
              });
              this.$router.push('/viam-rds/index');
            } else {
              this.showModalDelete = false;
              MakeToast({
                variant: 'warning',
                title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
                content: response.message,
              });
              this.$router.push('/viam-rds/index');
            }
          }).catch((error) => {
            MakeToast({
              variant: 'warning',
              title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
              content: error.message,
            });
          });
      }
    },
    backToList(){
      this.$router.push({ path: `/viam-rds/index` });
    },

    handlecheckAllChangeDataTab(val) {
      this.checkedDataTab = val ? this.DATABASE_DATA.map(item => item.id) : [];
      this.isIndeterminateDataTab = false;
    },

    handlecheckAllChangeStructureTab(val) {
      this.checkedStructureTab = val ? this.DATABASE_STRUCTURE.map(item => item.id) : [];
      this.isIndeterminateStructureTab = false;
    },

    handlecheckAllChangeAdministratorTab(val) {
      this.checkedAdministratorTab = val ? this.DATABASE_ADMINISTRATOR.map(item => item.id) : [];
      this.isIndeterminateAdministratorTab = false;
    },

    handleCheckedChangeDataTab(value) {
      const checkedCount = value.length;
      this.checkAllDataTab = checkedCount === this.DATABASE_DATA.length;
      this.isIndeterminateDataTab = checkedCount > 0 && checkedCount < this.DATABASE_DATA.length;
    },

    handleCheckedChangeStructureTab(value) {
      const checkedCount = value.length;
      this.checkAllStructureTab = checkedCount === this.DATABASE_STRUCTURE.length;
      this.isIndeterminateStructureTab = checkedCount > 0 && checkedCount < this.DATABASE_STRUCTURE.length;
    },

    handleCheckedChangeAdministratorTab(value) {
      const checkedCount = value.length;
      this.checkAllAdministratorTab = checkedCount === this.DATABASE_ADMINISTRATOR.length;
      this.isIndeterminateAdministratorTab = checkedCount > 0 && checkedCount < this.DATABASE_ADMINISTRATOR.length;
    },
    toggleSelection(item) {
      item.selected = !item.selected;
    },
    handleResetPopup(){
      this.checkAllDataTab = false;
      this.checkedDataTab = [];
      this.isIndeterminateDataTab = true;
      this.checkAllStructureTab = false;
      this.checkedStructureTab = [];
      this.isIndeterminateStructureTab = true;
      this.checkAllAdministratorTab = false;
      this.checkedAdministratorTab = [];
      this.isIndeterminateAdministratorTab = true;
    },
    async handleSave(){
      this.openLoading();
      const params = {
        user_id: +this.$route.params.id,
        rds_manager_id: this.$store.getters.rdsSelectedId,
        database_name: this.$store.getters.databaseSelectedId,
        permission: [...this.checkedDataTab, ...this.checkedStructureTab, ...this.checkedAdministratorTab],
      };
      console.log('params===>', params);
      await createRdsRole(params).then((response) => {
        if (response.code === 200){
          console.log(':stuck_out_tongue_closed_eyes:');
        }
      });
    },
  },
};
</script>

<style scoped>
  .btn {
    border: 0 !important;
    margin: 0px 10px;
  }
  ::v-deep option {
    color: #111111;
  }
  ::v-deep option[value=""][disabled] {
    display: none !important;
    color: #6f737c;
  }
  .submit_button:hover{
    background: #0f68b1 !important;
  }
    /*copy cua Yen*/
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
    .use-management-title-table {
      padding: 0 45px;
    }
    .line {
      width: 100%;
      height: 1px;
      color: rgba(63, 63, 63, 0.4);
      margin: 15px auto;
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
    ::v-deep .title-face {
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
    }
    ::v-deep .btn-add-custom {
      background: #0070C9;
      border-radius: 5px;
      width: 110px;
      font-size: 20px;
    }
    ::v-deep .employee-edit {
      width: 100%;
      flex-wrap: wrap;
      flex-direction: row;
      justify-content: space-around;
      text-align: left;
    }
    ::v-deep .header-employee-edit {
      width: calc(100% / 2);
      height: 40px;
      margin: 0px;
      font-size: 20px;
    }
    ::v-deep .header-employee-edit-name{
      width: calc(100% / 2);
      /* margin: 0; */
      margin-top: 30px;
      font-size: 20px;
    }
    .custom-icon {
      height: 48px;
      font-size: 18px;
    }

    .rds-container {
      background-color: #E8F2FC;
      padding: 1rem;
      font-size: 1rem;
      margin-bottom: 1rem;
    }

    .text-blue-400 {
      color: #409EFF
    }

  .container {
    margin: auto;
  }
  .form-section {
    margin-bottom: 20px;
  }
  .checkbox-group {
    margin-bottom: 10px;
  }
  .input-group {
    margin-bottom: 10px;
  }
  .form-section label {
    display: block;
    margin-bottom: 5px;
  }
  .form-section input[type="checkbox"] {
    margin-right: 5px;
  }
  .form-section input[type="text"] {
    width: 100%;
    padding: 5px;
    margin-bottom: 5px;
  }
  .form-footer {
    text-align: right;
  }
  button {
    padding: 10px 20px;
    margin-left: 5px;
  }

  ul {
    list-style-type: none; /* Ẩn dấu chấm danh sách */
    padding: 0; /* Xóa khoảng cách dỡ dang giữa các phần tử */
  }

  li {
    font-size: 18px; /* Kích thước chữ của các phần tử li */
    margin-bottom: 1px; /* Khoảng cách giữa các phần tử li */
    padding: 8px;
  }

  .active {
    background-color: #0070C9; /* Màu xanh cho đối tượng được chọn */
    color: #fff
  }

  .vertical-checkbox-group {
  display: flex;
  flex-direction: column;
}

.cursor-pointer {
  cursor: pointer;
}
</style>

