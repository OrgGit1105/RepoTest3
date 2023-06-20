<template>
  <div>
    <div class="container-fluid w-90">
      <div class="container-fluid-body mt-5 mb-5">
        <div class="use-management-title">
          <div class="card-body p-8">
            <div class="d-flex justify-content-between mb-0" style="border-bottom: 1px solid rgba(0, 0, 0, 0.25);">
              <div class="basic">
                <h1 class="font-weight-bold display-4">{{ $t('LANGUAGES.TEXT_EMPLOYEE_MANAGEMENT') }}</h1>
              </div>
              <!--              <div>-->
              <!--                <button class="btn btn-sign text-uppercase" @click="toCreatePage">-->
              <!--                  {{ $t('LANGUAGES.TEXT_BUTTON_SIGN_UP') }}-->
              <!--                </button>-->
              <!--              </div>-->
            </div>
          </div>
        </div>
        <div class="use-management-title-table">
          <div class="card-body">
            <ValidationObserver
              ref="obsEditEmployee"
              tag="div"
            >
              <h4 class="mb-0 font-weight-normal">
                <div>
                  <a href="/user/index" style="display: flex;width: 12%;"><b-icon-chevron-left /><h4>All Employee</h4></a>
                </div>
                <div>
                  <ValidationProvider
                    v-slot="{ errors }"
                    name="name"
                    rules="required"
                  >
                    <b-input-group>
                      <b-form-input
                        id="nameEmployee"
                        v-model="formEdit.name"
                        class="border-0 border-bottom-important"
                        style="font-size: 40px"
                      />
                      <span class="col-1">
                        <b-button variant="primary" class="submit_button" style="width: 110px" @click="onSubmit($event)">Save</b-button>
                      </span>
                    </b-input-group>
                    <div class="text-error">
                      {{ errors[0] }}
                    </div>
                  </ValidationProvider>
                </div>
                <div style="margin-bottom: 15px;">
                  <ValidationProvider
                    v-slot="{ errors }"
                    name="email"
                    rules="required|email"
                  >
                    <label for="emailEmployee" style="font-size: 16px;">Email:</label>
                    <b-input-group>
                      <b-form-input
                        id="emailEmployee"
                        v-model="formEdit.email"
                        class="border-0 border-bottom-important col-11"
                      />
                    </b-input-group>
                    <div class="text-error">
                      {{ errors[0] }}
                    </div>
                  </ValidationProvider>
                </div>
              </h4>
              <h4 class="mb-0 font-weight-normal" style="margin-top: 15px;">
                <header class="line-form">
                  <h4>Face Data</h4>
                </header>
                <!--                <div style="margin-bottom: 15px;">-->
                <!--                  <label for="linkFace" style="font-size: 16px;">Link:</label>-->
                <!--                  <b-input-group>-->
                <!--                    <b-form-input-->
                <!--                      id="linkFace"-->
                <!--                      class="border-0 border-bottom-important"-->
                <!--                    />-->
                <!--                  </b-input-group>-->
                <!--                </div>-->
                <div class="input-group">
                  <div class="image-dropzone" @dragover.prevent @drop="handleDrop">
                    <div style="border-bottom: 2px solid;display: flex; gap: 1rem">
                      <div
                        :class="{check_with_or_without_mask: withoutMask}"
                        style="display: flex; gap: 1rem;cursor: pointer;border-right: 2px solid"
                        @click="checkWithoutMask()"
                      >
                        <b-icon-emoji-smile style="margin-top: 10px; height: 55%" />
                        <div style="margin-right: 20px">
                          <p>Face image</p>
                          <p>without mask</p>
                        </div>
                      </div>
                      <div
                        :class="{check_with_or_without_mask: withMask}"
                        style="display: flex; gap: 1rem;cursor: pointer"
                        @click="checkWithMask()"
                      >
                        <b-icon-emoji-frown style="margin-top: 10px; height: 55%" />
                        <div style="margin-right: 20px">
                          <p>Face image</p>
                          <p>with mask</p>
                        </div>
                      </div>
                    </div>
                    <div
                      style="overflow-x: auto;
                      white-space: nowrap;"
                    >
                      <input
                        ref="fileInput"
                        type="file"
                        multiple
                        style="display: none;"
                        @change="handleFileSelect"
                      >
                      <div class="image-preview">
                        <span v-if="linkFilesWithoutMask !== [] && withoutMask">
                          <div v-for="(file, index) in linkFilesWithoutMask" :key="index" class="preview-item">
                            <img :src="file.file">
                            <b-icon-x-circle
                              style="display: block;
                          float: right;
                          position: relative;
                          top: -9px;
                          right: 8px;
                          height: 17px;
                          cursor: pointer"
                              @click="removeLinkFile(file,index)"
                            >Remove
                            </b-icon-x-circle>
                          </div>
                        </span>
                        <span v-if="linkFilesWithMask !== [] && withMask">
                          <div v-for="(file, index) in linkFilesWithMask" :key="index" class="preview-item">
                            <img :src="file.file">
                            <b-icon-x-circle
                              style="display: block;
                          float: right;
                          position: relative;
                          top: -9px;
                          right: 8px;
                          height: 17px;
                          cursor: pointer"
                              @click="removeLinkFile(file,index)"
                            >Remove
                            </b-icon-x-circle>
                          </div>
                        </span>
                        <div v-for="(file, index) in selectedFiles" :key="index" class="preview-item">
                          <img :src="convertFileToUrl(file)">
                          <b-icon-x-circle
                            style="display: block;
                          float: right;
                          position: relative;
                          top: -9px;
                          right: 8px;
                          height: 17px;
                          cursor: pointer"
                            @click="removeFile(index)"
                          >Remove
                          </b-icon-x-circle>
                        </div>
                      </div>
                    </div>
                    <div style="display: flex;font-size: large; gap: 1rem">
                      <div style="color: blue;cursor: pointer;" @click="openFilePicker">Select File</div>
                      <div>|</div>
                      <div style="cursor: pointer;" @click="removeFileAll">Delete all</div>
                    </div>
                  </div>
                  <div v-if="validateFile" class="text-error">
                    {{ messageErrorFile }}
                  </div>
                </div>

                <div style="margin-top: 15px;margin-bottom: 15px;">
                  <header class="line-form">
                    <h4>Role</h4>
                  </header>
                  <ValidationProvider
                    v-slot="{ errors }"
                    name="role"
                    rules="required"
                  >
                    <b-form-checkbox-group
                      id="role_id_create"
                      v-model="formEdit.role_id"
                      :options="listRoles ?? []"
                      value-field="id"
                      text-field="name"
                    />
                    <div class="text-error">
                      {{ errors[0] }}
                    </div>
                  </ValidationProvider>
                </div>
              </h4>
              <h4 class="mb-0 font-weight-normal" style="margin-top: 15px;">
                <header class="line-form">
                  <h4>Retirement</h4>
                </header>
                <div style="margin-bottom: 15px;">
                  <b-input-group>
                    <b-form-input
                      v-model="formEdit.retirement_date"
                      type="date"
                    />
                  </b-input-group>
                </div>
              </h4>
              <h4 class="mb-0 font-weight-normal" style="margin-top: 35px;">
                <header>
                  <h4 class="text-error" style="font-size: 20px; cursor: pointer" @click="showModalDelete()">Delete Employee</h4>
                </header>
              </h4>
              <!--              <h4 class="mb-0 font-weight-normal" style="margin-top: 15px; border-bottom: 1px solid rgba(0, 0, 0, 0.15);">-->
              <!--                <header>-->
              <!--                  <h4>Password</h4>-->
              <!--                </header>-->
              <!--                <div>-->
              <!--                  <ValidationProvider-->
              <!--                    v-slot="{ errors }"-->
              <!--                    name="password"-->
              <!--                    vid="password"-->
              <!--                    rules="required"-->
              <!--                  >-->
              <!--                    <label for="password" style="font-size: 16px;">Password:</label>-->
              <!--                    <b-input-group>-->
              <!--                      <b-form-input-->
              <!--                        id="password"-->
              <!--                        v-model="formEdit.password"-->
              <!--                        type="password"-->
              <!--                      />-->
              <!--                    </b-input-group>-->
              <!--                    <div class="text-error">-->
              <!--                      {{ errors[0] }}-->
              <!--                    </div>-->
              <!--                  </ValidationProvider>-->
              <!--                </div>-->
              <!--                <div style="margin-bottom: 15px;">-->
              <!--                  <ValidationProvider-->
              <!--                    v-slot="{ errors }"-->
              <!--                    name="password_confirm"-->
              <!--                    rules="required|confirmed:password"-->
              <!--                  >-->
              <!--                    <label for="password_confirm" style="font-size: 16px;">Password(Confirm) :</label>-->
              <!--                    <b-input-group>-->
              <!--                      <b-form-input-->
              <!--                        id="password_confirm"-->
              <!--                        v-model="formEdit.password_confirmation"-->
              <!--                        type="password"-->
              <!--                      />-->
              <!--                    </b-input-group>-->
              <!--                    <div class="text-error">-->
              <!--                      {{ errors[0] }}-->
              <!--                    </div>-->
              <!--                  </ValidationProvider>-->
              <!--                </div>-->
              <!--              </h4>-->
            </ValidationObserver>
          </div>
        </div>
      </div>
    </div>
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
          <h2>{{ nameEmployee }}</h2>
        </div>
      </div>
      <div class="justify-content-end d-flex p-3">
        <b-button
          class="mt-3 w-25 fs-12 btn btn-accept"
          squared
          @click="submitDelete()"
        >{{ $t('LANGUAGES.TEXT_BUTTON_YES') }}</b-button>
        <b-button
          class="mt-3 ml-3 w-25 fs-12 btn btn-close"
          squared
          @click="hideModalDelete()"
        >{{ $t('LANGUAGES.TEXT_BUTTON_CLOSE') }}</b-button>
      </div>
    </b-modal>
  </div>
</template>

<script>
import * as CONFIGS from '../../configs/index';
import * as UserApi from '../../api/user';
import * as ImageApi from '../../api/image_face';
import { MakeToast } from '../../utils/toast_message';
import { ValidationObserver, ValidationProvider } from 'vee-validate';
import { getAllRole } from '../../api/role';
import { deleteOneUser } from '../../api/user';
import { getImageByUserId } from '../../api/image_face';

export default {
  name: 'EditUser',
  components: {
    ValidationObserver,
    ValidationProvider,
  },
  data() {
    return {
      headQuarter: CONFIGS.UserRoleId.HEAD_QUARTER,
      authorityOption: CONFIGS.AuthorityList,
      branchList: [],
      formEdit: {
        name: '',
        email: '',
        // password: '',
        // password_confirmation: '',
        role_id: '',
        retirement_date: '',
      },
      id: this.$route.params.id,
      userInfo: {},
      author: true,
      selectedFiles: [],
      withoutMask: true,
      withMask: false,
      nameEmployee: '',
      linkFilesWithoutMask: [],
      linkFilesWithMask: [],
      linkFileDelete: [],
      validateFile: false,
      messageErrorFile: [],
    };
  },
  computed: {
    roleId() {
      return this.$store.getters.role_id;
    },
    companyBranch() {
      return this.$store.getters.listBranch;
    },
    listRoles() {
      return this.$store.getters.listRoles;
    },
  },
  watch: {
    companyBranch() {
    },
  },
  created() {
    this.getListRole();
    this.getUserInfo();
    this.getImageByUserId();
  },

  methods: {
    openLoading() {
      this.$store.dispatch('loading/setLoading', true);
    },
    closeLoading() {
      this.$store.dispatch('loading/setLoading', false);
    },
    async getListRole(){
      this.openLoading();
      await getAllRole().then((response) => {
        if (response.code === 200){
          this.$store.dispatch('app/saveListRoles', response.data);
          this.closeLoading();
        }
      }).catch((error) => {
        this.closeLoading();
        MakeToast({
          variant: 'warning',
          title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
          content: error.message,
        });
      });
    },
    async getUserInfo() {
      this.openLoading();
      await UserApi.getOneUser(this.id)
        .then((response) => {
          MakeToast({
            variant: 'success',
            title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
            content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_GET_USER_INFO_SUCCESSFULLY'),
          });
          this.nameEmployee = response.data.name;
          this.formEdit = {
            name: response.data.name,
            email: response.data.email,
            // password: '',
            // password_confirmation: '',
            role_id: response.data.role_id,
            retirement_date: response.data.retirement_date ? this.formatTimeStamp(response.data.retirement_date) : null,
          };
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
    async getImageByUserId(){
      this.openLoading();
      await getImageByUserId(this.id)
        .then((response) => {
          response.data.forEach((element) => {
            if (element.type === 'WithoutMask'){
              this.linkFilesWithoutMask.push({
                id: element.id,
                file: element.file,
                type: element.type,
                face_rekognition_id: element.face_rekognition_id,
              });
            }
            if (element.type === 'WithMask'){
              this.linkFilesWithMask.push({
                id: element.id,
                file: element.file,
                type: element.type,
                face_rekognition_id: element.face_rekognition_id,
              });
            }
          });
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
    formatTimeStamp(date){
      const datePart = date.split(' ')[0]; // Extract the date part from the received value
      const parts = datePart.split('-');
      const year = parts[0];
      const month = parts[1];
      const day = parts[2];
      return `${year}-${month}-${day}`;
    },
    async onSubmit(e) {
      e.preventDefault();
      this.checkNumImage();
      const isValid = await this.$refs.obsEditEmployee.validate();
      if (isValid === true && !this.validateFile) {
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
        await UserApi.putOneUser(this.id, this.formEdit)
          .then(async(response) => {
            if (response.code === 200) {
              // this.closeLoading();
              MakeToast({
                variant: 'success',
                title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
                content: 'Edit employee success',
              });
              if (this.linkFileDelete.length !== 0){
                for (const element of this.linkFileDelete) {
                  await ImageApi.deleteImageByUserId(element.id)
                    .then((response) => {
                      if (response.code === 200){
                        MakeToast({
                          variant: 'success',
                          title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
                          content: `Delete image employee with link ${element.file} success`,
                        });
                      } else {
                        MakeToast({
                          variant: 'warning',
                          title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
                          content: response.message_content,
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
                }
              }

              if (this.selectedFiles.length !== 0){
                let typeImage = '';
                if (this.withoutMask){
                  typeImage = 'WithoutMask';
                }
                if (this.withMask){
                  typeImage = 'WithMask';
                }

                const image = new FormData();

                // Lặp qua danh sách các file đã chọn để upload
                for (let i = 0; i < this.selectedFiles.length; i++) {
                  const file = this.selectedFiles[i];
                  image.append('file[]', file);
                }

                // Thêm các trường dữ liệu khác vào FormData
                image.append('type', typeImage);
                image.append('user_id', this.id);

                await ImageApi.createImage(image)
                  .then((response) => {
                    if (response.code === 200){
                      MakeToast({
                        variant: 'success',
                        title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
                        content: 'Add image employee success',
                      });
                    } else {
                      MakeToast({
                        variant: 'warning',
                        title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
                        content: response.message_content,
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
              }
              await this.$router.push('/user/index');
            } else {
              // this.closeLoading();
              MakeToast({
                variant: 'warning',
                title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
                content: response.message_content,
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
    removeLinkFile(file, index){
      if (file.type === 'WithoutMask'){
        this.linkFileDelete.push(file);
        this.linkFilesWithoutMask.splice(index, 1);
      }
      if (file.type === 'WithMask'){
        this.linkFileDelete.push(file);
        this.linkFilesWithMask.splice(index, 1);
      }
      this.checkNumImage();
    },
    checkNumImage(){
      if (this.linkFilesWithoutMask.length === 0 && this.selectedFiles.length === 0){
        this.validateFile = true;
        this.messageErrorFile.push('Image with mask must one image');
      }

      if (this.linkFilesWithMask.length === 0 && this.linkFilesWithoutMask.length === 0 && this.selectedFiles.length === 0){
        this.validateFile = true;
        this.messageErrorFile.push('Pleas choose image');
      }
    },
    checkWithoutMask(){
      if (!this.withoutMask){
        this.selectedFiles.splice(0, this.selectedFiles.length);
      }
      this.withoutMask = true;
      this.withMask = false;
    },
    checkWithMask(){
      if (!this.withMask){
        this.selectedFiles.splice(0, this.selectedFiles.length);
      }
      this.withoutMask = false;
      this.withMask = true;
    },
    handleDrop(event) {
      event.preventDefault();
      const files = event.dataTransfer.files;
      for (let i = 0; i < files.length; i++) {
        const file = files[i];
        // const fileURL = URL.createObjectURL(file);
        this.selectedFiles.push(file);
      }
    },
    openFilePicker() {
      this.$refs.fileInput.click();
    },
    handleFileSelect(event) {
      const files = event.target.files;
      for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (this.isImageFile(file)) {
          this.selectedFiles.push(file);
        }
      }
      this.validateFile = false;
      this.checkNumImage();
    },
    isImageFile(file) {
      const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
      return allowedExtensions.test(file.name);
    },
    convertFileToUrl(file){
      return URL.createObjectURL(file);
    },
    removeFile(index) {
      this.selectedFiles.splice(index, 1);
      this.checkNumImage();
    },
    chooseFiles() {
      this.$refs.fileInput.click();
    },
    removeFileAll(){
      this.selectedFiles.splice(0, this.selectedFiles.length);
      if (this.linkFilesWithoutMask !== []){
        this.linkFilesWithoutMask.forEach((element) => {
          this.linkFileDelete.push(element);
        });
        this.linkFilesWithoutMask.splice(0, this.linkFilesWithoutMask.length);
      }
      if (this.linkFilesWithMask !== []){
        this.linkFilesWithMask.forEach((element) => {
          this.linkFileDelete.push(element);
        });
        this.linkFilesWithMask.splice(0, this.linkFilesWithMask.length);
      }
      this.checkNumImage();
    },
    showModalDelete(){
      this.$bvModal.show('bv-modal-delete');
    },
    hideModalDelete() {
      this.$bvModal.hide('bv-modal-delete');
    },
    async submitDelete() {
      if (this.id) {
        await deleteOneUser(this.id).then(() => {
          MakeToast({
            variant: 'success',
            title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
            content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_DELETE_USER_SUCCESSFULLY'),
          });
          this.$router.push('/user/index');
        });
      }
    },
  },
};
</script>

<style scoped>

.main-page {
  width: 98%;
  margin: 0 auto;
}
.title-info {
  border-left: 9px solid #fb9a09;
  color: #3189bb;
  font-size: 25px;
}
.label-name{
  font-size: 17px;
  padding-top: 9px;
}
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
.border-bottom-important{
  border-bottom: 1px solid rgba(0, 0, 0, 1) !important;
  border-radius: unset;
}
.image-dropzone {
  border: 2px solid #ccc;
  padding: 20px;
  text-align: center;
  background: rgb(245 246 247);
  width: 800px;
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
.check_with_or_without_mask{
  border-bottom: 4px solid;
}
.line-form{
  border-bottom: 1px solid rgba(0, 0, 0, 0.15);
  margin-bottom: 10px;
}
.submit_button:hover{
  background: #0f68b1 !important;
}
</style>

