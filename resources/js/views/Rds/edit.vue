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
            </div>
          </div>
        </div>
        <hr class="line-bottom">
        <div class="use-management-title-table mt-5">
          <p class="back-list cursor-pointer" @click="backToList()"> <i class="el-icon-arrow-left icon-back-list" /> All RDS </p>
          <div class="card-body p-card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div class="basic">
                <h1 class="title-record m-0">RDS</h1>
              </div>
              <div class="basic">
                <template v-if="!waitEdit">
                  <el-button class="btn-add-custom" type="primary" @click="onSubmit($event)">Save</el-button>
                </template>
                <template v-if="waitEdit">
                  <el-button class="btn-add-custom" type="primary">...</el-button>
                </template>
              </div>
            </div>
            <hr class="line">
            <ValidationObserver
              ref="obsEditRds"
              tag="div"
            >
              <h4 class="mb-0 font-weight-normal">
                <div class="cover-employee-edit">
                  <div class="employee-edit" style="justify-content: start">
                    <div style="flex: 1">
                      <p class="header-employee-edit fw-5">Name RDS</p>
                      <ValidationProvider
                        v-slot="{ errors }"
                        name="name"
                        rules="required"
                      >
                        <b-input-group>
                          <b-form-input
                            id="name"
                            v-model="formEdit.name"
                            class="p-1"
                          />
                        </b-input-group>
                        <div class="text-error">
                          {{ errors[0] }}
                        </div>
                      </ValidationProvider>
                    </div>
                  </div>
                  <div class="employee-edit" style="justify-content: start">
                    <div style="flex: 1">
                      <p class="header-employee-edit fw-5">URL enpoint</p>
                      <ValidationProvider
                        v-slot="{ errors }"
                        name="url_end_point"
                        rules="required"
                      >
                        <b-input-group>
                          <b-form-input
                            id="url_end_point"
                            v-model="formEdit.url_end_point"
                            class="p-1"
                          />
                        </b-input-group>
                        <div class="text-error">
                          {{ errors[0] }}
                        </div>
                      </ValidationProvider>

                    </div>
                  </div>
                  <div class="employee-edit" style="justify-content: start">
                    <div style="flex: 1">
                      <p class="header-employee-edit fw-5">Username</p>
                      <ValidationProvider
                        v-slot="{ errors }"
                        name="username"
                        rules="required"
                      >
                        <b-input-group>
                          <b-form-input
                            id="username"
                            v-model="formEdit.username"
                            class="p-1"
                          />
                        </b-input-group>
                        <div class="text-error">
                          {{ errors[0] }}
                        </div>
                      </ValidationProvider>
                    </div>
                  </div>
                  <div class="employee-edit" style="justify-content: start">
                    <div style="flex: 1">
                      <p class="header-employee-edit fw-5">Password</p>
                      <ValidationProvider
                        v-slot="{ errors }"
                        name="password"
                        rules="required"
                      >
                        <b-input-group>
                          <el-input
                            id="password"
                            v-model="formEdit.password"
                            type="password"
                            show-password
                          />
                        </b-input-group>
                        <div class="text-error">
                          {{ errors[0] }}
                        </div>
                      </ValidationProvider>
                    </div>
                  </div>
                  <div class="employee-edit" style="justify-content: start">
                    <div style="flex: 1">
                      <p class="header-employee-edit fw-5">Upload file</p>
                      <ValidationProvider
                        v-slot="{ errors }"
                        name="file_id"
                        rules="required"
                      >
                        <b-input-group>
                          <button class="button-upload" :style="{ display: checkUploadFileSuccess ? 'none' : '' }" @click="openFileInput">Choose File</button>
                          <span :style="{ display: checkUploadFileSuccess ? 'none' : '' }">{{ fileName }}</span>
                          <input ref="fileInput" type="file" :style="{ display: checkUploadFileSuccess ? '' : 'none' }" @change="handleFileSelect">
                          <div class="text-error">
                            {{ errors[0] }}
                          </div>
                        </b-input-group>
                      </ValidationProvider>

                    </div>
                  </div>
                  <div class="employee-edit" style="justify-content: start">
                    <div style="flex: 1">
                      <p class="header-employee-edit fw-5">EC2 IP address</p>
                      <ValidationProvider
                        v-slot="{ errors }"
                        name="ec2_ip_address"
                        rules="required"
                      >
                        <b-input-group>
                          <b-form-input
                            id="ec2_ip_address"
                            v-model="formEdit.ec2_ip_address"
                            class="p-1"
                          />
                        </b-input-group>
                        <div class="text-error">
                          {{ errors[0] }}
                        </div>
                      </ValidationProvider>

                    </div>
                  </div>
                  <div class="employee-edit" style="justify-content: start">
                    <div style="flex: 1">
                      <p class="header-employee-edit fw-5">EC2 Username</p>
                      <ValidationProvider
                        v-slot="{ errors }"
                        name="ec2_username"
                        rules="required"
                      >
                        <b-input-group>
                          <b-form-input
                            id="ec2_username"
                            v-model="formEdit.ec2_username"
                            class="p-1"
                          />
                        </b-input-group>
                        <div class="text-error">
                          {{ errors[0] }}
                        </div>
                      </ValidationProvider>
                    </div>
                  </div>
                  <div class="employee-edit" style="justify-content: start">
                    <div style="flex: 1">
                      <p class="header-employee-edit fw-5">PHPAdmin URL</p>
                      <ValidationProvider
                        v-slot="{ errors }"
                        name="phpmyadmin_url"
                        rules="required"
                      >
                        <b-input-group>
                          <b-form-input
                            id="phpmyadmin_url"
                            v-model="formEdit.phpmyadmin_url"
                            class="p-1"
                          />
                        </b-input-group>
                        <div class="text-error">
                          {{ errors[0] }}
                        </div>
                      </ValidationProvider>
                    </div>
                  </div>
                </div>
              </h4>
              <p class="delete-record cursor-pointer mt-5" @click="showModalDelete= true"> Delete RDS </p>
            </ValidationObserver>
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
      <span class="text-align-center">Are you sure to delete this RDS?</span>
      <span slot="footer" class="dialog-footer">
        <el-button @click="showModalDelete= false">Cancel</el-button>
        <el-button type="danger" @click="submitDelete()">Confirm</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { getOneRds, uploadFileHandler, updateOneRds, deleteOneRds } from '../../api/viamUser';
import { MakeToast } from '../../utils/toast_message';
import { ValidationObserver, ValidationProvider } from 'vee-validate';

export default {
  name: 'EditRds',
  components: {
    ValidationObserver,
    ValidationProvider,
  },
  data() {
    return {
      formEdit: {
        name: '',
        url_end_point: '',
        username: '',
        password: '',
        file_id: '',
        ec2_ip_address: '',
        ec2_username: '',
        phpmyadmin_url: '',
      },
      id: this.$route.params.id,
      showModalDelete: false,
      waitEdit: false,
      fileName: '',
      checkUploadFileSuccess: false,
    };
  },
  computed: {
  },
  watch: {
  },
  created() {
    this.getRdsInfo();
  },

  methods: {
    openLoading() {
      this.$store.dispatch('loading/setLoading', true);
    },
    closeLoading() {
      this.$store.dispatch('loading/setLoading', false);
    },
    async getRdsInfo() {
      this.openLoading();
      try {
        const response = await getOneRds(this.id);
        this.formEdit = {
          name: response.data.name,
          url_end_point: response.data.url_end_point,
          username: response.data.username,
          password: response.data.password,
          file_id: response.data.file_id,
          ec2_ip_address: response.data.ec2_ip_address,
          ec2_username: response.data.ec2_username,
          phpmyadmin_url: response.data.phpmyadmin_url,
        };
        this.fileName = response.data.file.file_name;
        this.closeLoading();
      } catch (error) {
        this.closeLoading();
        MakeToast({
          variant: 'warning',
          title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
          content: '2. ' + error.message,
        });
      }
    },

    async onSubmit(event) {
      event.preventDefault();
      // const isValid = await this.$refs.obsEditRds.validate();
      const isValid = true;
      if (isValid) {
        // const EDIT_DATA = {
        //   role_id: this.form.role_id,
        //   department_id: this.form.department_id,
        //   username: this.form.username,
        //   email: this.form.email,
        // };
        // if (this.form.password) {
        //   EDIT_DATA.password = this.form.password;
        //   // console.log('Co chay vao day');
        // }
        // // console.log('Form edit gui di', EDIT_DATA);
        // this.openLoading();
        this.waitEdit = true;
        await updateOneRds(this.id, this.formEdit)
          .then(async(response) => {
            if (response.code === 200) {
              // this.closeLoading();
              MakeToast({
                variant: 'success',
                title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
                content: 'Edit employee success',
              });

              this.waitEdit = false;
              await this.$router.push('/rds/index');
            } else {
              // this.closeLoading();
              MakeToast({
                variant: 'warning',
                title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
                content: response.message,
              });
              this.waitEdit = false;
            }
          })
          .catch((error) => {
            MakeToast({
              variant: 'warning',
              title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
              content: '7. ' + error.message,
            });
          });
        this.waitEdit = false;
      } else {
        MakeToast({
          variant: 'warning',
          title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
          content: 'Still error',
        });
        this.waitEdit = false;
      }
    },

    async submitDelete() {
      if (this.id) {
        await deleteOneRds(this.id).then(() => {
          MakeToast({
            variant: 'success',
            title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
            content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_DELETE_USER_SUCCESSFULLY'),
          });
          this.$router.push('/rds/index');
        });
      }
    },

    async handleFileSelect(event) {
      const file = event.target.files[0];
      const formData = new FormData();
      if (!file) {
        return 0;
      }
      formData.append('file', file); // Make the request to the POST /single-file URL
      await uploadFileHandler(formData).then(async(response) => {
        if (response.code === 200) {
          this.formEdit.file_id = response.data.id;
          this.checkUploadFileSuccess = true;
        } else {
          MakeToast({
            variant: 'warning',
            title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
            content: response.message,
          });
        }
      }).catch((error) => {
        MakeToast({
          variant: 'warning',
          title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
          content: error.message,
        });
      });
    },

    openFileInput() {
      this.$refs.fileInput.click();
    },

    backToList(){
      this.$router.push({ path: `/rds/index` });
    },
  },
};
</script>

<style scoped>
::v-deep .btn-warning {
  color: #fff !important;
  background: #fb9a09;
}
.btn {
  border: 0 !important;
  margin: 0px 10px;
}
.btn-submit {
  justify-content: center;
}
.btn:hover {
  color: #fff !important;
  background: #ef8f00 !important;
}
.btn:active {
  background: #fb8c00 !important;
}
.btn-secondary {
  background: #fb9a09 !important;
}
::v-deep select:first-child:disabled {
  color: #6f737c;
}
::v-deep option {
  color: #111111;
}
::v-deep option[value=""][disabled] {
  display: none !important;
  color: #6f737c;
}
select:required:invalid { color: #6f737c; }
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
  display: flex;
  width: 100%;
  flex-wrap: nowrap;
  flex-direction: row;
  justify-content: space-around;
  text-align: left;
  gap: 10px;
}
::v-deep .header-employee-edit {
  width: calc(100% / 2);
  height: 40px;
  margin: 0;
  font-size: 20px;
}
.cover-employee-edit {
  display: flex;
  gap: 60px;
  flex-direction: column;
}
::v-deep .el-select {
  width: 100%;
}
::v-deep .el-date-editor {
  width: 100%;
}

.button-upload {
  border: 1px solid #111;
  border-radius: 3px;
  font-size: 24px;
  width: 138px;
  padding: 1px 6px;
  height: 35px;
  margin-right: 6px;
}
</style>

