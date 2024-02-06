<template>
  <div>
    <div class="container-fluid w-90">
      <div class="container-fluid-body mt-5 mb-5">
        <div class="use-management-title">
          <div class="card-body p-5">
            <div class="d-flex justify-content-between align-items-center">
              <div class="basic">
                <h1 class="title">{{ $t('LANGUAGES.TEXT_VIAM_POLICY') }}</h1>
              </div>
            </div>
          </div>
        </div>
        <hr class="line-bottom">
        <div class="use-management-title-table mt-5">
          <p class="back-list cursor-pointer" @click="listPolicy()"> <i class="el-icon-arrow-left icon-back-list" /> All VIAM Policies </p>
          <div class="card-body p-card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div class="basic">
                <h1 class="title-record m-0">Policy</h1>
              </div>
              <div class="basic">
                <el-button class="btn-add-custom" type="primary" @click="onSubmit($event)">Save</el-button>
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
                    <p class="header-employee-edit-name fw-5">Policy name</p>
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
                    <p class="header-employee-edit-name fw-5">Type</p>
                    <div class="header-employee-edit">
                      <ValidationProvider
                        v-slot="{ errors }"
                        name="type"
                        rules="required"
                      >
                        <el-select id="typeEmployee" v-model="formEdit.type" placeholder="Please select Type">
                          <el-option
                            v-for="item in listType"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id"
                          />
                        </el-select>
                        <div class="text-error">
                          {{ errors[0] }}
                        </div>
                      </ValidationProvider>
                    </div>
                    <div v-if="formEdit.type === 3 || formEdit.type === 4">
                      <ValidationProvider
                        v-slot="{ errors }"
                        name="Instance"
                        rules="required"
                      >
                        <p class="header-employee-edit-name fw-5">Instance</p>
                        <div class="header-employee-edit">
                          <el-input id="instance_id" v-model="formEdit.instance_id" @focus="focusInput" @blur="blurInput" />
                          <div class="text-error">
                            {{ errors[0] }}
                          </div>
                          <div v-if="mesage_istance_err" class="text-error">
                            instance_id does not exist
                          </div>
                        </div>
                      </ValidationProvider>
                    </div>
                    <div v-if="formEdit.project_name || checkInstance && formEdit.type === 4">
                      <ValidationProvider
                        v-slot="{ errors }"
                        name="Project Name"
                        rules="required"
                      >
                        <p class="header-employee-edit-name fw-5">Project Name</p>
                        <div class="header-employee-edit">
                          <el-select id="typeEmployee" v-model="formEdit.project_name" placeholder="Please select Type">
                            <el-option
                              v-for="item in OptionName"
                              :key="item.id"
                              :label="item.name"
                              :value="item.id"
                            />
                          </el-select>
                          <div class="text-error">
                            {{ errors[0] }}
                          </div>
                        </div>
                      </ValidationProvider>
                    </div>
                  </div>
                </div>
              </h4>
              <p class="delete-record cursor-pointer mt-5" @click="showModalDelete= true"> Delete Policy </p>
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
      <span class="text-align-center">Are you sure to delete this Policy?</span>
      <span slot="footer" class="dialog-footer">
        <el-button @click="showModalDelete= false">Cancel</el-button>
        <el-button type="danger" @click="submitDelete()">Confirm</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import * as CONFIGS from '../../configs/index';
import * as UserApi from '../../api/viampolicy';
import { MakeToast } from '../../utils/toast_message';
import { ValidationObserver, ValidationProvider } from 'vee-validate';
import { deleteOneUser } from '../../api/viampolicy';

export default {
  name: 'EditViam',
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
        type: '',
        instance_id: null,
        project_name: null,
      },
      listType: [
        { id: 1, name: 'V-FACE' },
        { id: 2, name: 'AWS' },
        { id: 3, name: 'EC2-ADMIN' },
        { id: 4, name: 'EC2-DEPLOY' },
        { id: 5, name: 'GITHUB' },
      ],
      id: this.$route.params.id,
      userInfo: {},
      author: true,
      withoutMask: true,
      withMask: false,
      showModalDelete: false,
      checkInstance: false,
      OptionName: [],
      mesage_istance_err: false,
    };
  },
  computed: {
    roleId() {
      return this.$store.getters.role_id;
    },
    companyBranch() {
      return this.$store.getters.listBranch;
    },
  },
  watch: {
    companyBranch() {
    },
    'formEdit.type'(newType) {
      if (newType !== 3 && newType !== 4) {
        this.formEdit.instance_id = null;
        this.formEdit.project_name = null;
      }
    },
  },
  created() {
    this.getUserInfo();
    console.log('aaaa', this.formEdit.project_name);
  },
  // mounted() {
  //   this.getUserInfo().then(() => {
  //     if (this.formEdit.instance_id) {
  //       this.blurInput();
  //     }
  //   });
  // },
  methods: {
    openLoading() {
      this.$store.dispatch('loading/setLoading', true);
    },
    closeLoading() {
      this.$store.dispatch('loading/setLoading', false);
    },
    focusInput(){
      this.checkInstance = true;
    },
    async blurInput(){
      if (this.formEdit.instance_id){
        const OPTION = [];
        const PARAMS = {
          instance_id: this.formEdit.instance_id,
        };
        const { code, data } = await UserApi.getIstance(PARAMS);
        if (code === 200){
          console.log('data istance', data);
          this.mesage_istance_err = false;
          this.OptionName = [];
          if (data){
            data.map(item => {
              console.log('item', item);
              OPTION.push({
                id: item,
                name: item,
              });
            });
            this.OptionName.push(...OPTION);
          }
          this.checkInstance = true;
        } else {
          this.mesage_istance_err = false;
          this.OptionName = [];
        }
      }
      console.log('first', this.formEdit.instance_id);
    },
    async getUserInfo() {
      this.openLoading();
      await UserApi.getOneUser(this.id)
        .then((response) => {
          console.log('response edit', response);
          this.formEdit = {
            name: response.data.name,
            type: response.data.type,
            instance_id: response.data.instance_id,
            project_name: response.data.project_name,
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
      if (isValid === true) {
        console.log('dddd', this.formEdit);
        await UserApi.putOneUser(this.id, this.formEdit)
          .then(async(response) => {
            console.log('first response', response);
            if (response.code === 200) {
              // this.closeLoading();
              MakeToast({
                variant: 'success',
                title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
                content: 'Edit policy success',
              });
              this.$router.push('/viam/index');
            } else {
              // this.closeLoading();
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
        await deleteOneUser(this.id)
          .then(async(response) => {
            if (response.code === 200){
              this.showModalDelete = false;
              MakeToast({
                variant: 'success',
                title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
                content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_DELETE_USER_SUCCESSFULLY'),
              });
              this.$router.push('/viam/index');
            } else {
              this.showModalDelete = false;
              MakeToast({
                variant: 'warning',
                title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
                content: response.message,
              });
              this.$router.push('/viam/index');
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
    listPolicy(){
      this.$router.push({ path: `/viam/index` });
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
    width: 100%;
    height: 40px;
    margin: 0px;
    font-size: 20px;
  }
  ::v-deep .header-employee-edit-name{
    width: 100%;
    height: 40px;
    /* margin: 0; */
    margin-top: 30px;
    font-size: 20px;
  }
  ::v-deep .el-select {
    width: 100%;
  }
  </style>

