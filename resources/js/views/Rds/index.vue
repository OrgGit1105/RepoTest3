<template>
  <div>
    <div class="container-fluid w-90">
      <div class="container-fluid-body mt-5 mb-5">
        <div class="use-management-title">
          <div class="card-body p-5">
            <div class="d-flex justify-content-between align-items-center">
              <div class="basic">
                <h1 class="title">{{ $t('LANGUAGES.TEXT_RDS_MANAGEMENT') }}</h1>
              </div>
              <div class="basic" />
            </div>
          </div>
        </div>
        <hr class="line-bottom">
        <div class="use-management-title-table mt-5">
          <div class="fill">
            <i class="el-icon-circle-plus-outline custom-icon-add cursor-pointer" @click="createForm()" />
            <div class="box-search align-items-center" :class="displayBoxSearch">
              <el-input
                v-model="name_search"
                placeholder="search by email"
                prefix-icon="el-icon-search"
                @keyup.native="getListRDS()"
              />
              <i class="el-icon-close cursor-pointer" @click="closeInputSearch()" />
            </div>
          </div>
          <hr class="line">
          <div class="">
            <el-table
              :data="listRds"
              style="width: 100%"
              @current-change="goToEditScreen"
            >
              <el-table-column
                prop="id"
                label="No"
                align="center"
              />
              <el-table-column
                prop="name"
                label="Name RDS"
                align="center"
              >
                <template slot-scope="scope">
                  <span>
                    {{ scope.row.name }}
                  </span>
                </template>
              </el-table-column>
              <el-table-column
                prop="endpoint"
                label="URL endpoint"
                align="center"
              >
                <template slot-scope="scope">
                  {{ scope.row.url_end_point }}
                </template>
              </el-table-column>
              <el-table-column
                prop="username"
                label="Username"
                align="center"
              >
                <template slot-scope="scope">
                  {{ scope.row.username }}
                </template>
              </el-table-column>
              <el-table-column
                prop="ec2_ip_address"
                label="EC2 IP address"
                align="center"
              >
                <template slot-scope="scope">
                  {{ scope.row.ec2_ip_address }}
                </template>
              </el-table-column>
              <el-table-column
                prop="ec2_username"
                label="EC2 Username"
                align="center"
              >
                <template slot-scope="scope">
                  {{ scope.row.ec2_username }}
                </template>
              </el-table-column>
            </el-table>
          </div>
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
              @current-change="getListRDS"
            />
          </div>
        </div> -->

        <!-- Modal -->
        <el-dialog
          class="title-add-working"
          title="Add RDS"
          :visible.sync="openModalAdd"
          :close-on-click-modal="false"
          width="50%"
          @click="hideCreateModal()"
        >
          <ValidationObserver
            ref="obsAddRDS"
            tag="div"
          >
            <ValidationProvider
              v-slot="{ errors }"
              name="name"
              rules="required"
            >
              <label for="name">Server name</label>
              <el-input id="name" v-model="formCreate.name" />
              <div class="text-error">
                {{ errors[0] }}
              </div>
            </ValidationProvider>
            <ValidationProvider
              v-slot="{ errors }"
              name="url_end_point"
              rules="required"
            >
              <label for="url_end_point" class="mt-3">URL endpoint</label>
              <el-input id="url_end_point" v-model="formCreate.url_end_point" />
              <div class="text-error">
                {{ errors[0] }}
              </div>
            </ValidationProvider>
            <ValidationProvider
              v-slot="{ errors }"
              name="username"
              rules="required"
            >
              <label for="username" class="mt-3">Username</label>
              <el-input id="username" v-model="formCreate.username" />
              <div class="text-error">
                {{ errors[0] }}
              </div>
            </ValidationProvider>
            <ValidationProvider
              v-slot="{ errors }"
              name="password"
              vid="password"
              rules="required|min:8"
            >
              <label for="passwordEmployee" class="mt-3">Password</label>
              <el-input id="passwordEmployee" v-model="formCreate.password" type="password" show-password />
              <div class="text-error">
                {{ errors[0] }}
              </div>
            </ValidationProvider>
            <ValidationProvider
              v-slot="{ errors }"
              name="file_id"
              rules="required"
            >
              <label for="file_id" class="mt-3">Upload file</label>
              <div>
                <input
                  ref="fileInput"
                  type="file"
                  @change="handleFileSelect"
                >
              </div>
              <div class="text-error">
                {{ errors[0] }}
              </div>
            </ValidationProvider>
            <ValidationProvider
              v-slot="{ errors }"
              name="ec2_ip_address"
              rules="required"
            >
              <label for="ec2_ip_address" class="mt-3">EC2 IP address</label>
              <el-input id="ec2_ip_address" v-model="formCreate.ec2_ip_address" />
              <div class="text-error">
                {{ errors[0] }}
              </div>
            </ValidationProvider>
            <ValidationProvider
              v-slot="{ errors }"
              name="ec2_username"
              rules="required"
            >
              <label for="ec2_username" class="mt-3">EC2 Username</label>
              <el-input id="ec2_username" v-model="formCreate.ec2_username" />
              <div class="text-error">
                {{ errors[0] }}
              </div>
            </ValidationProvider>
          </ValidationObserver>
          <span slot="footer" class="dialog-footer">
            <el-button class="btn-cancle-custom" @click="hideCreateModal()">Cancel</el-button>
            <template v-if="!waitCreate">
              <el-button class="btn-add-custom" type="primary" @click="submitCreate()">Add</el-button>
            </template>
            <template v-if="waitCreate">
              <el-button class="btn-add-custom" type="primary">...</el-button>
            </template>
          </span>
        </el-dialog>

        <!-- Modal delete -->
        <b-modal id="bv-modal-delete" hide-footer hide-header>
          <header class="style-title-modal p-3 text-white">
            <h4>{{ $t('LANGUAGES.TEXT_MODAL_DELETE_USER') }}</h4>
          </header>
          <div>
            <div class="d-block text-center p-4 style-modal">
              <h4 class="text-center mb-0 font-weight-normal">
                {{ $t('LANGUAGES.TEXT_DO_YOU_WANT_TO_DELETE_USER_NAME') }}
              </h4>
              <h2>{{ infoModel.username }}</h2>
            </div>
          </div>
          <div class="justify-content-end d-flex p-3">
            <b-button
              class="mt-3 w-25 fs-12 btn btn-accept"
              squared
              @click="submitDelete(infoModel.id)"
            >{{ $t('LANGUAGES.TEXT_BUTTON_YES') }}
            </b-button>
            <b-button
              class="mt-3 ml-3 w-25 fs-12 btn btn-close"
              squared
              @click="hideModal()"
            >{{ $t('LANGUAGES.TEXT_BUTTON_CLOSE') }}
            </b-button>
          </div>
        </b-modal>
      </div>
    </div>
  </div>
</template>

<script>
import { deleteOneUser } from '../../api/user';
import { MakeToast } from '../../utils/toast_message';
import { getAllRDS, postOneRDS, uploadFileHandler } from '../../api/viamUser';
import * as CONFIGS from '../../configs/index';
import { ValidationObserver, ValidationProvider } from 'vee-validate';

export default {
  name: 'UserManagement',
  components: {
    ValidationObserver,
    ValidationProvider,
  },
  data() {
    return {
      pagination: {
        current_page: 1,
        per_page: 20,
        total_records: 0,
        isDisable: false,
      },
      headQuarter: CONFIGS.UserRoleId.HEAD_QUARTER,
      infoModel: {},
      name_search: null,
      formCreate: {
        name: '',
        url_end_point: '',
        username: '',
        password: '',
        file_id: '',
        ec2_ip_address: '',
        ec2_username: '',
      },
      openModalAdd: false,
      waitCreate: false,
      displayBoxSearch: 'd-none',
      displaySearch: 'd-block',
      listRds: [],
    };
  },
  computed: {
    currChange() {
      return this.pagination.current_page;
    },
  },
  watch: {
    currChange() {
      this.getListRDS();
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

    async getListRDS() {
      this.pagination.isDisable = true;
      const PARAMS = {
        page: '',
        per_page: '',
      };
      await getAllRDS(PARAMS)
        .then((response) => {
          if (response.code === 200) {
            this.listRds = response.data.map((item) => {
              return {
                id: item.id,
                name: item.name,
                url_end_point: item.url_end_point,
                username: item.username,
                ec2_ip_address: item.ec2_ip_address,
                ec2_username: item.ec2_username,
              };
            });
            // this.$store.dispatch('app/savelistRds', listRds);
            // this.pagination.total_records = response.data.pagination.total_records;
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
    goToEditScreen(val) {
      this.$router.push({ path: `/rds/edit/${val.id}` }, (onAbort) => {});
    },
    createForm() {
      this.openModalAdd = true;
      // this.$bvModal.show('bv-modal-create');
    },
    confirmationForm(item) {
      this.infoModel = item;
      this.$bvModal.show('bv-modal-delete');
    },
    hideCreateModal() {
      this.formCreate = {
        name: '',
        url_end_point: '',
        username: '',
        password: '',
        file_id: '',
        ec2_ip_address: '',
        ec2_username: '',
      };
      this.openModalAdd = false;
    },
    hideModal() {
      this.$bvModal.hide('bv-modal-delete');
    },
    changePage(page) {
      // console.log('Page ban vua chon', page);
    },
    submitDelete(id) {
      if (id) {
        deleteOneUser(id).then(() => {
          this.hideModal();
          MakeToast({
            variant: 'success',
            title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
            content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_DELETE_USER_SUCCESSFULLY'),
          });
          this.getListRDS();
        });
      }
    },
    async submitCreate() {
      // const isValid = await this.$refs.obsAddRds.validate();
      const isValid = true;
      console.log('isValid isValid ===>', isValid);
      if (!isValid) {
        return;
      } else {
        this.waitCreate = true;
        console.log('this.formCreate===>', this.formCreate);
        await postOneRDS(this.formCreate).then(async(response) => {
          const toastSuccessMessage = [];
          const toastFalseMessage = [];
          if (response.code === 200) {
            this.formCreate = {
              name: '',
              url_end_point: '',
              username: '',
              password: '',
              file_id: '',
              ec2_ip_address: '',
              ec2_username: '',
            };
            this.waitCreate = false;
            this.openModalAdd = false;
            toastSuccessMessage.forEach((element) => {
              MakeToast({
                variant: 'success',
                title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
                content: element,
              });
            });
            toastFalseMessage.forEach((element) => {
              MakeToast({
                variant: 'success',
                title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
                content: element,
              });
            });
            await this.getListRDS();
          } else {
            this.openModalAdd = false;
            this.waitCreate = false;
            MakeToast({
              variant: 'warning',
              title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
              content: response.message,
            });
          }
        }).catch((error) => {
          this.openModalAdd = false;
          MakeToast({
            variant: 'warning',
            title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
            content: error.message,
          });
        });
      }
    },
    closeInputSearch() {
      this.displayBoxSearch = 'd-none';
      this.displaySearch = 'd-block';
    },
    async handleFileSelect(event) {
      const file = event.target.files[0];
      const formData = new FormData();
      if (!file) {
        return 0;
      }
      formData.append('file', file); // Make the request to the POST /single-file URL
      try {
        await uploadFileHandler(formData).then(async(response) => {
          if (response.code === 200) {
            this.formCreate.file_id = response.data.id;
          }
        }).catch((error) => {
          MakeToast({
            variant: 'warning',
            title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
            content: error.message,
          });
        });
      } catch (error) {
        console.log('error===>', error);
      }
    },
  },
};
</script>

<style scoped>
::v-deep .custom-toast {
  z-index: 2001 !important; /* Sử dụng !important để đảm bảo nó ghi đè lên các giá trị mặc định */
}
#screen-title {
  display: flex;
  text-align: center;
  margin-top: 50px;
}

.title-info {
  border-left: 9px solid #fb9a09;
  text-transform: uppercase;
  color: #3189bb;
  font-size: 25px;
}

.btn-action {
  min-width: 85px;
}

.btn-sign {
  background-color: #fb9a09;
  border: 1px solid #fb9a09;
  font-size: 17px;
  color: white;
  /*padding: 13px 100px;*/
}

.btn-sign:hover {
  background-color: #d57700;
  border-color: #c87000;
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

.btn:hover {
  color: #ffffff;
}

.btn-secondary:hover {
  border: none !important;
}

.btn-edit {
  background: #fb8c00;
}

.btn-delete {
  background: #e9240a;
}

.btn-edit:hover {
  background-color: #dd7f04;
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
  color: #0070C9;
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

.use-management-title-table {
  padding: 0 45px;
}

.cursor-pointer {
  cursor: pointer;
}

.custom-icon-add {
  color: #0070C9;
  font-size: 30px;
  font-weight: bolder;
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

.select-custom .el-select-custom {
  color: #0070C9;
}

::v-deep .el-select-custom .el-input .el-select__caret {
  color: #0070C9;
  font-weight: bolder;
  font-size: 20px;
  margin-top: 3px;
}

::v-deep .el-select-custom .b-form-select .el-select__caret {
  color: #0070C9;
  font-weight: bolder;
  font-size: 20px;
  margin-top: 3px;
}

el-select {
  color: #0070C9 !important;
}

::v-deep .title-add-working .el-dialog__title, .title-create-employee {
  font-weight: 600;
  font-size: 32px;
  line-height: 48px;
  color: #000000;
}

::v-deep .el-select {
  width: 100%;
}

::v-deep .el-date-editor {
  width: 100%;
}

.avatar-uploader .el-upload {
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
  }
  .avatar-uploader .el-upload:hover {
    border-color: #409EFF;
  }
  .avatar-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 178px;
    height: 178px;
    line-height: 178px;
    text-align: center;
  }
  .avatar {
    width: 178px;
    height: 178px;
    display: block;
  }
</style>
