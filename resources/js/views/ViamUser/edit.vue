<template>
  <div>
    <div class="container-fluid w-90">
      <div class="container-fluid-body mt-5 mb-5">
        <div class="use-management-title">
          <div class="card-body p-5">
            <div class="d-flex justify-content-between align-items-center">
              <div class="basic">
                <h1 class="title">{{ $t('LANGUAGES.TEXT_VIAM_USER') }}</h1>
              </div>
            </div>
          </div>
        </div>
        <hr class="line-bottom">
        <div class="use-management-title-table mt-5">
          <p class="back-list cursor-pointer" @click="listEmployees()"> <i class="el-icon-arrow-left icon-back-list" /> All VIAM User </p>
          <div class="card-body p-card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div class="basic">
                <h1 class="title-record m-0">User</h1>
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
              ref="obsEditEmployee"
              tag="div"
            >
              <h4 class="mb-0 font-weight-normal">
                <div class="cover-employee-edit">
                  <div class="employee-edit">
                    <p class="header-employee-edit-name fw-5">User name</p>
                    <div class="header-employee-edit">
                      <ValidationProvider
                        v-slot="{ errors }"
                        name="name"
                        rules="required"
                      >
                        <b-input-group>
                          <b-form-input
                            id="nameEmployee"
                            v-model="formEdit.name"
                            class="border-0 pl-2"
                          />
                        </b-input-group>
                        <div class="text-error">
                          {{ errors[0] }}
                        </div>
                      </ValidationProvider>
                    </div>
                    <div>
                      <p class="header-employee-edit-name fw-5">VIAM Policy</p>
                      <div class="form-tag">
                        <b-form-tags
                          v-model="selectedTagPolicy"
                          placeholder="入力してください"
                          @focus="showDropdownPolicy = true"
                          @blur="hideDropdownPolicy"
                          @remove="onTagRemoveEdit"
                        />

                        <div v-if="showDropdownPolicy" class="dropdown-menu" style="display:block;">
                          <b-dropdown-item
                            v-for="(tag, index) in availableTags"
                            :key="index"
                            @click="addTagPolicy(tag)"
                          >
                            {{ tag.name }}
                          </b-dropdown-item>
                        </div>
                      </div>
                    </div>
                    <div>
                      <p class="header-employee-edit-name fw-5">Description</p>
                      <div>
                        <el-input
                          v-model="formEdit.description"
                          type="textarea"
                          :rows="2"
                          placeholder=""
                          class="no-resize"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </h4>
              <p class="delete-record cursor-pointer mt-5" @click="showModalDelete= true"> Delete User </p>
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
      <span class="text-align-center">Are you sure to delete this Viam?</span>
      <span slot="footer" class="dialog-footer">
        <el-button @click="showModalDelete= false">Cancel</el-button>
        <el-button type="danger" @click="submitDelete()">Confirm</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import * as CONFIGS from '../../configs/index';
import * as UserApi from '../../api/user';
import * as ImageApi from '../../api/image_face';
import { MakeToast } from '../../utils/toast_message';
import { ValidationObserver, ValidationProvider } from 'vee-validate';
import { deleteOneUser } from '../../api/user';

export default {
  name: 'EditViamUser',
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
        description: '',
      },
      id: this.$route.params.id,
      userInfo: {},
      author: true,
      selectedWithMaskFiles: [],
      selectedWithoutMaskFiles: [],
      withoutMask: true,
      withMask: false,
      nameEmployee: '',
      linkFilesWithoutMask: [],
      linkFilesWithMask: [],
      linkFileDelete: [],
      validateFile: false,
      messageErrorFile: [],
      showModalDelete: false,
      waitEdit: false,

      selectedTagPolicy: [],
      selectedTagPolicy_id: [],
      showDropdownPolicy: false,
      availableTags: [
        { id: 1, name: 'Tag 1' },
        { id: 2, name: 'Tag 2' },
        { id: 3, name: 'Tag 3' },
      ],
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
    selectedTagPolicy(newTags) {
      // Cập nhật selectedTagsId dựa trên newTags
      this.selectedTagPolicy_id = newTags.map(tagName => {
        const foundTag = this.availableTags.find(tag => tag.name === tagName);
        return foundTag ? foundTag.id : null;
      }).filter(id => id !== null);
    },
  },
  created() {
    this.getUserInfo();
  },

  methods: {
    openLoading() {
      this.$store.dispatch('loading/setLoading', true);
    },
    closeLoading() {
      this.$store.dispatch('loading/setLoading', false);
    },
    addTagPolicy(tag) {
      if (!this.selectedTagPolicy.includes(tag)) {
        this.selectedTagPolicy.push(tag.name);
        this.selectedTagPolicy_id.push(tag.id);
        console.log('this.selectedTagPolicy_id1111111', this.selectedTagPolicy_id);
      }
      this.showDropdownPolicy = false;
    },
    hideDropdownPolicy() {
      setTimeout(() => {
        this.showDropdownPolicy = false;
      }, 200);
    },
    onTagRemoveEdit(removedTagName) {
      // Tìm đối tượng tag trong availableTags dựa trên tên
      const tagToRemove = this.availableTags.find(tag => tag.name === removedTagName);

      // Nếu tìm thấy, xóa id của nó khỏi selectedTagPolicy_id
      if (tagToRemove) {
        const indexToRemove = this.selectedTagPolicy_id.indexOf(tagToRemove.id);
        if (indexToRemove !== -1) {
          this.selectedTagPolicy_id.splice(indexToRemove, 1);
        }
      }

      console.log('Tag removed:', removedTagName);
    },
    async getUserInfo() {
      this.openLoading();
      await UserApi.getOneUser(this.id)
        .then((response) => {
          // MakeToast({
          //   variant: 'success',
          //   title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
          //   content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_GET_USER_INFO_SUCCESSFULLY'),
          // });
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
    async onSubmit(e) {
      e.preventDefault();
      const isValid = await this.$refs.obsEditEmployee.validate();
      if (isValid === true && !this.validateFile) {
        this.waitEdit = true;
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
                }
              }
              // Kiểm tra selectedWithoutMaskFiles
              if (this.selectedWithoutMaskFiles.length !== 0){
                const image = new FormData();
                for (let i = 0; i < this.selectedWithoutMaskFiles.length; i++) {
                  const file = this.selectedWithoutMaskFiles[i];
                  image.append('file[]', file);
                }
                image.append('type', 'WithoutMask');
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
              }

              // Kiểm tra selectedWithMaskFiles
              if (this.selectedWithMaskFiles.length !== 0){
                const image = new FormData();
                for (let i = 0; i < this.selectedWithMaskFiles.length; i++) {
                  const file = this.selectedWithMaskFiles[i];
                  image.append('file[]', file);
                }
                image.append('type', 'WithMask');
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
              }
              this.waitEdit = false;
              await this.$router.push('/user/index');
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
              content: error.message,
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

    checkWithoutMask(){
      this.withoutMask = true;
      this.withMask = false;
    },
    checkWithMask(){
      this.withoutMask = false;
      this.withMask = true;
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
    listEmployees(){
      this.$router.push({ path: `/viam-user/index` });
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
    .form-tag {
        position: relative;
    }
    ::v-deep .dropdown-menu {
        position: absolute;
        left: 0px;
        width: 100%;
        top: 91.7%;
        z-index: 999999999;
        max-height: 180px;
        overflow-y: auto;
    }
    ::v-deep .no-resize {
        resize: none;
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
      height: 40px;
      /* margin: 0; */
      margin-top: 30px;
      font-size: 20px;
    }
    </style>

