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
          <div class="fill">
            <i class="el-icon-circle-plus-outline custom-icon-add cursor-pointer" @click="createForm()" />
          </div>
          <hr class="line">
          <div class="">
            <el-table
              :data="listViamUser ? listViamUser : []"
              style="width: 100%"
              :row-style="rowWorkingStyle"
              @current-change="goToEditScreen"
            >
              <el-table-column
                prop="id"
                label="No"
                align="center"
              />
              <el-table-column
                prop="name"
                label="User"
                align="center"
              />
              <el-table-column
                prop="policy_list"
                label="Viam Policy"
                align="center"
              />
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
              @current-change="getListAllUser"
            />
          </div>
        </div> -->

        <!-- Modal -->
        <el-dialog class="title-add-working" title="Add VIAM User" :visible.sync="openModalAdd" width="40%" @click="hideCreateModal()">
          <div class="title-body">User</div>
          <ValidationObserver
            ref="obsAddEmployee"
            tag="div"
          >
            <ValidationProvider
              v-slot="{ errors }"
              name="name"
              rules="required"
            >
              <label for="nameEmployee">User Name</label>
              <el-input id="nameEmployee" v-model="form.name" />
              <div class="text-error">
                {{ errors[0] }}
              </div>
            </ValidationProvider>
            <!-- <ValidationProvider
              v-slot="{ errors }"
              name="policy"
              rules="required"
            >
              <label for="tagsPolicy" class="mt-3">VIAM Policy</label>
              <div id="tagsPolicy" class="form-tag">
                <b-form-tags
                  id="tagsPolicy-tags"
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
                <div class="text-error">
                  {{ errors[0] }}
                </div>
              </div>
            </ValidationProvider> -->
          </ValidationObserver>

          <ValidationObserver
            ref="obsAddEmployeeTagsPolicy"
            tag="div"
          >
            <ValidationProvider
              v-slot="{ errors }"
              name="policy"
              rules="required"
            >
              <label for="tagsPolicy" class="mt-3">VIAM Policy</label>
              <div class="form-tag">
                <b-form-tags
                  id="tagsPolicy"
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
                <div class="text-error">
                  {{ errors[0] }}
                </div>
              </div>
            </ValidationProvider>
          </ValidationObserver>
          <div>
            <label for="emailEmployee" class="mt-3">Description</label>
            <div>
              <el-input
                v-model="form.description"
                type="textarea"
                :rows="2"
                placeholder=""
                class="no-resize"
              />
            </div>
          </div>
          <span slot="footer" class="dialog-footer mt-3">
            <el-button class="btn-cancle-custom" @click="hideCreateModal()">Cancel</el-button>
            <template v-if="!waitCreate">
              <el-button class="btn-add-custom" type="primary" @click="submitCreate()">Add</el-button>
            </template>
            <template v-if="waitCreate">
              <el-button class="btn-add-custom" type="primary">...</el-button>
            </template>
          </span>
        </el-dialog>

      </div>
    </div>
  </div>
</template>

<script>
import { deleteOneUser, getAllUser, postOneUser } from '../../api/viamUser';
import { MakeToast } from '../../utils/toast_message';
import * as CONFIGS from '../../configs/index';
import { ValidationObserver, ValidationProvider } from 'vee-validate';
import { getAllPolicy } from '../../api/viampolicy';

export default {
  name: 'ViamUserManagement',
  components: {
    ValidationObserver,
    ValidationProvider,
  },
  data() {
    return {
      // userList: [],
      pagination: {
        current_page: 1,
        per_page: 20,
        total_records: 0,
        isDisable: false,
      },
      headQuarter: CONFIGS.UserRoleId.HEAD_QUARTER,
      infoModel: {},
      listViamUser: [],
      role_id_selected: '',
      name_search: null,
      form: {
        name: '',
        description: '',
      },
      listType: [
        { id: 1, name: 'V-Face' },
        { id: 2, name: 'AWS' },
        { id: 3, name: 'Google' },
      ],
      availableTags: [],
      selectedTagPolicy: [],
      selectedTagPolicy_id: [],
      showDropdownPolicy: false,
      selectedWithMaskFiles: [],
      selectedWithoutMaskFiles: [],
      withoutMask: true,
      openModalAdd: false,
      waitCreate: false,
      displayBoxSearch: 'd-none',
      displaySearch: 'd-block',
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
    selectedTagPolicy(newTags) {
      // Cập nhật selectedTagsId dựa trên newTags
      this.selectedTagPolicy_id = newTags.map(tagName => {
        const foundTag = this.availableTags.find(tag => tag.name === tagName);
        return foundTag ? foundTag.id : null;
      }).filter(id => id !== null);
    },
  },
  created() {
    this.getListAllUser();
    this.getListPolicy();
  },
  methods: {
    async addTagPolicy(tag) {
      if (!this.selectedTagPolicy.includes(tag)) {
        this.selectedTagPolicy.push(tag.name);
        this.selectedTagPolicy_id.push(tag.id);
      }
      this.showDropdownPolicy = false;
      await this.$refs.obsAddEmployeeTagsPolicy.validate();
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
    openLoading() {
      this.$store.dispatch('loading/setLoading', true);
    },
    closeLoading() {
      this.$store.dispatch('loading/setLoading', false);
    },
    async getListAllUser() {
      this.openLoading();
      const URL = '/viam_user';
      //   this.pagination.isDisable = true;
      //   const PARAMS = {
      //     page: this.pagination.current_page,
      //     per_page: this.pagination.per_page,
      //   };
      await getAllUser(URL)
        .then((response) => {
          if (response.code === 200) {
            this.listViamUser = response.data;
            console.log('listUser===>', this.listViamUser);
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
    goToEditScreen(val) {
      this.$router.push({ path: `/viam-user/edit/${val.id}` }, (onAbort) => {});
    },
    toCreatePage() {
      this.$router.push('/user/create');
    },
    createForm(){
      this.form = {
        name: '',
        description: '',
      };
      this.selectedTagPolicy = [];
      this.selectedTagPolicy_id = [];
      this.openModalAdd = true;
      // this.$bvModal.show('bv-modal-create');
    },
    confirmationForm(item) {
      this.infoModel = item;
      this.$bvModal.show('bv-modal-delete');
    },
    hideCreateModal(){
      this.form = {
        name: '',
        description: '',
      };
      this.selectedTagPolicy = [];
      this.selectedTagPolicy_id = [];
      this.openModalAdd = false;
    },
    hideModal() {
      this.$bvModal.hide('bv-modal-delete');
    },
    changePage(page){
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
          this.getListAllUser();
        });
      }
    },
    async submitCreate() {
      const isValid = await this.$refs.obsAddEmployee.validate();
      const isValidTagsPolicy = await this.$refs.obsAddEmployeeTagsPolicy.validate();
      console.log('isValid', isValid);
      if (isValid && isValidTagsPolicy) {
        this.waitCreate = true;
        const DATA = {
          name: this.form.name,
          description: this.form.description,
          policy_id: this.selectedTagPolicy_id,
        };
        await postOneUser(DATA).then(async(response) => {
          if (response.code === 200) {
            this.form = {
              name: '',
              description: '',
            };
            this.selectedTagPolicy_id = [];
            this.selectedTagPolicy = [];
            this.waitCreate = false;
            this.openModalAdd = false;

            MakeToast({
              variant: 'success',
              title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
              content: 'create viam user success',
            });
            await this.getListAllUser();
          } else {
            MakeToast({
              variant: 'warning',
              title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
              content: response.message,
            });
            this.waitCreate = false;
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

    checkWithoutMask(){
      this.withoutMask = true;
      this.withMask = false;
    },
    checkWithMask(){
      this.withoutMask = false;
      this.withMask = true;
    },

    // copy cua Yen
    rowWorkingStyle({ row, rowIndex }) {
      return { 'cursor': 'pointer' };
    },
    async getListPolicy() {
      const url = `/policy`;
      await getAllPolicy(url)
        .then((response) => {
          if (response.code === 200) {
            const data = response.data;
            if (data.length > 0) {
              const TEM = [];
              data.map(item => {
                TEM.push({
                  id: item.id,
                  name: item.name,
                });
              });
              this.availableTags.push(...TEM);
            }
          }
        })
        .catch((error) => {
          MakeToast({
            variant: 'warning',
            title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
            content: error.message,
          });
        });
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
    .check_with_or_without_mask{
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
    .box-search{
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
    .select-custom .el-select-custom{
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
    el-select{
      color: #0070C9 !important;
    }
    ::v-deep .title-add-working .el-dialog__title, .title-create-employee {
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
    </style>

