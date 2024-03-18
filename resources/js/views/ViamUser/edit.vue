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
          <p class="back-list cursor-pointer" @click="listUserViam()"> <i class="el-icon-arrow-left icon-back-list" /> All VIAM User </p>
          <div class="card-body p-card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div class="basic">
                <h1 class="title-record m-0">User</h1>
              </div>
              <div class="basic">
                <el-button class="btn-add-custom" type="primary" @click="onSubmit($event)">Save</el-button>
              </div>
            </div>
            <hr class="line">
            <h4 class="mb-0 font-weight-normal">
              <div class="cover-employee-edit">
                <div class="employee-edit">
                  <p class="header-employee-edit-name fw-5">Username</p>
                  <ValidationObserver
                    ref="obsEditEmployee"
                    tag="div"
                    class="header-employee-edit"
                  >
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
                  </ValidationObserver>
                  <validation-observer
                    ref="obsEditpolicy"
                    tag="div"
                  >
                    <ValidationProvider
                      v-slot="{ errors }"
                      name="policy"
                      rules="required"
                    >
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
                        <div class="text-error">
                          {{ errors[0] }}
                        </div>
                      </div>
                    </ValidationProvider>
                  </validation-observer>
                  <!-- Add Rds -->
                  <validation-observer
                    ref="obsEditpolicy"
                    tag="div"
                  >
                    <ValidationProvider
                      name="policy"
                      rules="required"
                    >
                      <div class="d-flex align-items-center">
                        <p class="mt-4 w-0 fw-5">RDS</p>
                        <i class="el-icon-circle-plus-outline custom-icon-add cursor-pointer" @click="handleAddRds()" />
                      </div>
                      <div v-for="item in RDS_FAKE" :key="item.id" class="form-rds">
                        <div class="rds-container">
                          <div class="d-flex justify-content-between align-items-center">
                            <div class="text-blue-400">{{ item.name }}</div>
                            <i class="el-icon-close text-blue-400 cursor-pointer" @click="handleDeleteRds(item.id)" />
                          </div>
                          <div v-for="element in item.selected" :key="element.id">
                            <div class="pl-4 d-flex align-items-center">
                              <i class="el-icon-close cursor-pointer" @click="handleDeleteRdsRole(item.id, element.id)" />
                              <div class="pl-1">{{ element.name }}</div>
                            </div>
                            <div v-for="ele in element.selected_child" :key="ele.id" class="pl-5 d-flex align-items-center">
                              {{ ele.name }}
                            </div>
                          </div>
                        </div>
                      </div>
                    </ValidationProvider>
                  </validation-observer>
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

    <!-- Modal -->
    <el-dialog
      class="title-add-working"
      title="Add new RDS"
      :visible.sync="openModalAdd"
      width="60%"
      @click="hideCreateModal()"
    >
      <div class="container">
        <el-tabs type="card" closable>
          <el-tab-pane label="Config">
            <div class="container">
              <form>
                <div class="form-section">
                  <div>
                    <label for="max-queries">RDS (*)</label>
                    <el-row :gutter="20">
                      <el-col :span="12">
                        <el-select
                          v-model="value"
                          multiple
                          filterable
                          allow-create
                          default-first-option
                          placeholder="Choose tags for your article"
                        >
                          <el-option
                            v-for="item in [{label: '', value: ''}]"
                            :key="item.value"
                            :label="item.label"
                            :value="item.value"
                          />
                        </el-select>
                      </el-col>
                    </el-row>
                  </div>

                  <el-row :gutter="20" class="mt-5">
                    <el-col :span="24">
                      <h3>Global privileges</h3>
                      <div>
                        <el-checkbox v-model="checkAllData" :indeterminate="isIndeterminateData" @change="handlecheckAllChangeData">Data</el-checkbox>
                        <el-checkbox-group v-model="checkedData" class="pl-4" @change="handleCheckedChangeData">
                          <el-checkbox v-for="data in GLOBAL_PRIVILEGES_DATA" :key="data" :label="data">{{ data }}</el-checkbox>
                        </el-checkbox-group>
                      </div>
                      <div>
                        <el-checkbox v-model="checkAllStructure" :indeterminate="isIndeterminateStructure" @change="handlecheckAllChangeStructure">Structure</el-checkbox>
                        <el-checkbox-group v-model="checkedStructure" class="pl-4" @change="handleCheckedChangeStructure">
                          <el-checkbox v-for="data in GLOBAL_PRIVILEGES_STRUCTURE" :key="data" :label="data">{{ data }}</el-checkbox>
                        </el-checkbox-group>
                      </div>
                      <div>
                        <el-checkbox v-model="checkAllAdministrator" :indeterminate="isIndeterminateAdministrator" @change="handlecheckAllChangeAdministrator">Administrator</el-checkbox>
                        <el-checkbox-group v-model="checkedAdministrator" class="pl-4" @change="handleCheckedChangeAdministrator">
                          <el-checkbox v-for="data in GLOBAL_PRIVILEGES_ADMINISTRATOR" :key="data" :label="data">{{ data }}</el-checkbox>
                        </el-checkbox-group>
                      </div>
                    </el-col>
                    <el-col :span="24" class="mt-5">
                      <h3>Resource limits</h3>
                      <div>
                        <label for="max-queries">MAX QUERIES PER HOUR: </label>
                        <el-input id="nameEmployee" />
                      </div>
                      <div>
                        <label for="max-queries">MAX UPDATES PER HOUR: </label>
                        <el-input id="nameEmployee" />
                      </div>
                      <div>
                        <label for="max-queries">MAX CONNECTIONS PER HOUR:  </label>
                        <el-input id="nameEmployee" />
                      </div>
                      <div>
                        <label for="max-queries">MAX USER CONNECTIONS:  </label>
                        <el-input id="nameEmployee" />
                      </div>
                    </el-col>
                  </el-row>
                </div>
                <div class="form-footer d-flex justify-content-center">
                  <el-button type="danger" plain>{{ $t('LANGUAGES.TEXT_BUTTON_CANCEL') }}</el-button>
                  <el-button type="primary">{{ $t('LANGUAGES.TEXT_BUTTON_SAVE') }}</el-button>
                </div>
              </form>
            </div>
          </el-tab-pane>
          <el-tab-pane label="Database">
            <div class="container">
              <div class="split-screen">
                <el-row :gutter="20">
                  <el-col :span="12">
                    <!-- Nội dung ở phần thứ nhất -->
                    <div class="content-left">
                      <ul>
                        <li
                          v-for="item in items"
                          :key="item.id"
                          :class="{ active: item.selected }"
                          @click="toggleSelection(item)"
                        >
                          {{ item.name }}
                        </li>
                      </ul>
                    </div>
                  </el-col>
                  <el-col :span="12">
                    <!-- Nội dung ở phần thứ hai -->
                    <div class="content-right">
                      <div>
                        <div class="bg-gray pl-2">
                          <el-checkbox v-model="checkAllDataTab" :indeterminate="isIndeterminateDataTab" @change="handlecheckAllChangeDataTab">Data</el-checkbox>
                        </div>
                        <el-checkbox-group v-model="checkedDataTab" class="pl-4 vertical-checkbox-group" @change="handleCheckedChangeDataTab">
                          <el-checkbox v-for="data in GLOBAL_PRIVILEGES_DATA" :key="data" class="vertical-checkbox" :label="data">{{ data }}</el-checkbox>
                        </el-checkbox-group>
                      </div>
                      <div>
                        <div class="bg-gray pl-2">
                          <el-checkbox v-model="checkAllStructureTab" :indeterminate="isIndeterminateStructureTab" @change="handlecheckAllChangeStructureTab">Structure</el-checkbox>
                        </div>
                        <el-checkbox-group v-model="checkedStructureTab" class="pl-4 vertical-checkbox-group" @change="handleCheckedChangeStructureTab">
                          <el-checkbox v-for="data in GLOBAL_PRIVILEGES_STRUCTURE" :key="data" class="vertical-checkbox" :label="data">{{ data }}</el-checkbox>
                        </el-checkbox-group>
                      </div>
                      <div>
                        <div class="bg-gray pl-2">
                          <el-checkbox v-model="checkAllAdministratorTab" :indeterminate="isIndeterminateAdministratorTab" @change="handlecheckAllChangeAdministratorTab">Adminstration</el-checkbox>
                        </div>
                        <el-checkbox-group v-model="checkedAdministratorTab" class="pl-4 vertical-checkbox-group" @change="handleCheckedChangeAdministratorTab">
                          <el-checkbox v-for="data in GLOBAL_PRIVILEGES_ADMINISTRATOR" :key="data" class="vertical-checkbox" :label="data">{{ data }}</el-checkbox>
                        </el-checkbox-group>
                      </div>
                    </div>
                  </el-col>
                </el-row>
              </div>
            </div>
          </el-tab-pane>
        </el-tabs>
      </div>
    </el-dialog>
  </div>
</template>

<script>

import * as UserApi from '../../api/viamUser';
import * as CONFIGS from '../../configs';
import { MakeToast } from '../../utils/toast_message';
import { ValidationObserver, ValidationProvider } from 'vee-validate';
import { deleteOneUser } from '../../api/viamUser';
import { getAllPolicy } from '../../api/viampolicy';

export default {
  name: 'EditViamUser',
  components: {
    ValidationObserver,
    ValidationProvider,
  },
  data() {
    return {
      formEdit: {
        name: '',
        description: '',
      },
      id: this.$route.params.id,
      showModalDelete: false,
      openModalAdd: false,
      selectedTagPolicy: [],
      selectedTagPolicy_id: [],
      showDropdownPolicy: false,
      availableTags: [],

      checkAllData: false,
      checkedData: [],
      isIndeterminateData: true,

      checkAllStructure: false,
      checkedStructure: [],
      isIndeterminateStructure: true,

      checkAllAdministrator: false,
      checkedAdministrator: [],
      isIndeterminateAdministrator: true,

      checkAllDataTab: false,
      checkedDataTab: [],
      isIndeterminateDataTab: true,

      checkAllStructureTab: false,
      checkedStructureTab: [],
      isIndeterminateStructureTab: true,

      checkAllAdministratorTab: false,
      checkedAdministratorTab: [],
      isIndeterminateAdministratorTab: true,

      GLOBAL_PRIVILEGES_DATA: CONFIGS.GLOBAL_PRIVILEGES_DATA,
      GLOBAL_PRIVILEGES_STRUCTURE: CONFIGS.GLOBAL_PRIVILEGES_STRUCTURE,
      GLOBAL_PRIVILEGES_ADMINISTRATOR: CONFIGS.GLOBAL_PRIVILEGES_ADMINISTRATOR,
      items: [
        { id: 1, name: 'atmtc-centlex-dev', selected: false },
        { id: 2, name: 'atmtc-dev', selected: false },
        { id: 3, name: 'atmtc-arata-develop', selected: false },
        { id: 4, name: 'cck', selected: false },
        { id: 4, name: 'cck-dev', selected: false },
        { id: 4, name: 'cck-production', selected: false },
        { id: 4, name: 'llm-inc1', selected: false },
        { id: 4, name: 'llm-inc2', selected: false },
        { id: 4, name: 'llm-inc3', selected: false },
        { id: 4, name: 'llm-inc4', selected: false },
        { id: 4, name: 'llm-inc5', selected: false },
        { id: 4, name: 'llm-inc6', selected: false },
        { id: 4, name: 'llm-inc7', selected: false },
        { id: 4, name: 'llm-inc8', selected: false },
      ],
      RDS_FAKE: [
        {
          id: 1,
          name: 'atmtc_center_Dev 1',
          selected: [
            {
              name: 'atmtc-dev 1', id: 1,
              selected_child: [
                { name: 'Data ( 2 selected roles)', id: 1 },
                { name: 'Data ( 2 selected roles)', id: 2 },
                { name: 'Data ( 3 selected roles)', id: 3 }],
            },
            {
              name: 'atmtc-dev 1.1', id: 11,
              selected_child: [
                { name: 'Data ( 2.1 selected roles)', id: 1 },
                { name: 'Data ( 2.1 selected roles)', id: 2 },
                { name: 'Data ( 3.1 selected roles)', id: 3 }],
            },
          ],
        },
        {
          id: 2,
          name: 'atmtc_center_Dev 2',
          selected: [
            {
              name: 'atmtc-dev 2', id: 2,
              selected_child: [
                { name: 'Data ( 12 selected roles)', id: 11 },
                { name: 'Data ( 12 selected roles)', id: 12 },
                { name: 'Data ( 13 selected roles)', id: 13 }],
            },
          ],
        },
      ],
    };
  },
  watch: {
    selectedTagPolicy(newTags) {
      this.selectedTagPolicy_id = newTags.map(tagName => {
        const foundTag = this.availableTags.find(tag => tag.name === tagName);
        return foundTag ? foundTag.id : null;
      }).filter(id => id !== null);
    },
  },
  created() {
    this.initData();
    // this.getListPolicy();
    // this.getUserInfo();
  },

  methods: {
    async initData() {
      this.openLoading();
      await this.getListPolicy();
      await this.getUserInfo();
      this.closeLoading();
    },
    openLoading() {
      this.$store.dispatch('loading/setLoading', true);
    },
    closeLoading() {
      this.$store.dispatch('loading/setLoading', false);
    },
    async addTagPolicy(tag) {
      if (!this.selectedTagPolicy.includes(tag)) {
        this.selectedTagPolicy.push(tag.name);
        this.selectedTagPolicy_id.push(tag.id);
      }
      await this.$refs.obsEditpolicy.validate();
      this.showDropdownPolicy = false;
    },
    hideDropdownPolicy() {
      setTimeout(() => {
        this.showDropdownPolicy = false;
      }, 200);
    },
    onTagRemoveEdit(removedTagName) {
      const tagToRemove = this.availableTags.find(tag => tag.name === removedTagName);
      if (tagToRemove) {
        const indexToRemove = this.selectedTagPolicy_id.indexOf(tagToRemove.id);
        if (indexToRemove !== -1) {
          this.selectedTagPolicy_id.splice(indexToRemove, 1);
        }
      }
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
              this.$router.push('/viam-user/index');
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
              this.$router.push('/viam-user/index');
            } else {
              this.showModalDelete = false;
              MakeToast({
                variant: 'warning',
                title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
                content: response.message,
              });
              this.$router.push('/viam-user/index');
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
    listUserViam(){
      this.$router.push({ path: `/viam-user/index` });
    },
    async getListPolicy() {
      const url = `/policy-option`;
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
    handleAddRds(){
      this.openModalAdd = true;
    },

    handlecheckAllChangeData(val) {
      this.checkedData = val ? this.GLOBAL_PRIVILEGES_DATA : [];
      this.isIndeterminateData = false;
    },

    handlecheckAllChangeStructure(val) {
      this.checkedStructure = val ? this.GLOBAL_PRIVILEGES_STRUCTURE : [];
      this.isIndeterminateStructure = false;
    },

    handlecheckAllChangeAdministrator(val) {
      this.checkedAdministrator = val ? this.GLOBAL_PRIVILEGES_ADMINISTRATOR : [];
      this.isIndeterminateAdministrator = false;
    },

    handlecheckAllChangeDataSecond(val) {
      this.checkedDataSecond = val ? this.GLOBAL_PRIVILEGES_DATA : [];
      this.isIndeterminateSecond = false;
    },

    handleCheckedChangeData(value) {
      const checkedCount = value.length;
      this.checkAllData = checkedCount === this.GLOBAL_PRIVILEGES_DATA.length;
      this.isIndeterminateData = checkedCount > 0 && checkedCount < this.GLOBAL_PRIVILEGES_DATA.length;
    },

    handleCheckedChangeStructure(value) {
      const checkedCount = value.length;
      this.checkAllStructure = checkedCount === this.GLOBAL_PRIVILEGES_STRUCTURE.length;
      this.isIndeterminateStructure = checkedCount > 0 && checkedCount < this.GLOBAL_PRIVILEGES_STRUCTURE.length;
    },

    handleCheckedChangeAdministrator(value) {
      const checkedCount = value.length;
      this.checkAllAdministrator = checkedCount === this.GLOBAL_PRIVILEGES_ADMINISTRATOR.length;
      this.isIndeterminateAdministrator = checkedCount > 0 && checkedCount < this.GLOBAL_PRIVILEGES_ADMINISTRATOR.length;
    },

    handlecheckAllChangeDataTab(val) {
      this.checkedDataTab = val ? this.GLOBAL_PRIVILEGES_DATA : [];
      this.isIndeterminateDataTab = false;
    },

    handlecheckAllChangeStructureTab(val) {
      this.checkedStructureTab = val ? this.GLOBAL_PRIVILEGES_STRUCTURE : [];
      this.isIndeterminateStructureTab = false;
    },

    handlecheckAllChangeAdministratorTab(val) {
      this.checkedAdministratorTab = val ? this.GLOBAL_PRIVILEGES_ADMINISTRATOR : [];
      this.isIndeterminateAdministratorTab = false;
    },

    handlecheckAllChangeDataSecondTab(val) {
      this.checkedDataSecondTab = val ? this.GLOBAL_PRIVILEGES_DATA : [];
      this.isIndeterminateSecondTab = false;
    },

    handleCheckedChangeDataTab(value) {
      const checkedCount = value.length;
      this.checkAllDataTab = checkedCount === this.GLOBAL_PRIVILEGES_DATA.length;
      this.isIndeterminateDataTab = checkedCount > 0 && checkedCount < this.GLOBAL_PRIVILEGES_DATA.length;
    },

    handleCheckedChangeStructureTab(value) {
      const checkedCount = value.length;
      this.checkAllStructureTab = checkedCount === this.GLOBAL_PRIVILEGES_STRUCTURE.length;
      this.isIndeterminateStructureTab = checkedCount > 0 && checkedCount < this.GLOBAL_PRIVILEGES_STRUCTURE.length;
    },

    handleCheckedChangeAdministratorTab(value) {
      const checkedCount = value.length;
      this.checkAllAdministratorTab = checkedCount === this.GLOBAL_PRIVILEGES_ADMINISTRATOR.length;
      this.isIndeterminateAdministratorTab = checkedCount > 0 && checkedCount < this.GLOBAL_PRIVILEGES_ADMINISTRATOR.length;
    },
    toggleSelection(item) {
      item.selected = !item.selected;
    },
    handleDeleteRds(id){
      const index = this.RDS_FAKE.findIndex(item => item.id === id);
      if (index !== -1) {
        this.RDS_FAKE.splice(index, 1);
      }
    },
    handleDeleteRdsRole(itemId, elementId) {
      const parentIndex = this.RDS_FAKE.findIndex(item => item.id === itemId);
      if (parentIndex !== -1) {
        const childIndex = this.RDS_FAKE[parentIndex].selected.findIndex(element => element.id === elementId);
        if (childIndex !== -1) {
          this.RDS_FAKE[parentIndex].selected.splice(childIndex, 1);
        }
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
    .form-tag {
        position: relative;
    }
    ::v-deep .b-form-tags-button {
        display: none;
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

.vertical-checkbox {
  display: block;
}

.bg-gray {
  background-color: #eee;
}

.content-left {
  overflow-y: scroll;
  height: 600px;
}

.custom-icon-add {
  color: #0070C9;
  font-size: 30px;
  font-weight: bolder;
  margin-top: 8px;
  margin-left: 8px;
}

.cursor-pointer {
  cursor: pointer;
}
</style>

