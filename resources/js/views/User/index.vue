<template>
  <div>
    <div class="container-fluid w-90">
      <div class="container-fluid-body mt-5 mb-5">
        <div class="use-management-title">
          <div class="card-body p-5">
            <div class="d-flex justify-content-between align-items-center">
              <div class="basic">
                <h1 class="title">{{ $t('LANGUAGES.TEXT_EMPLOYEE_MANAGEMENT') }}</h1>
              </div>
              <div class="basic">
                <!--                <button class="btn btn-date d-flex align-items-center" @click="toCreatePage">-->
                <!--                  <b-icon class="text-btn" icon="chevron-left" />-->
                <!--                  <span class="text-btn">3月20日 -  4月18日</span>-->
                <!--                  <b-icon class="text-btn" icon="chevron-right" />-->
                <!--                </button>-->
              </div>
            </div>
          </div>
        </div>
        <hr class="line-bottom">
        <div class="use-management-title-table mt-5">
          <div class="fill">
            <i class="el-icon-circle-plus-outline custom-icon-add cursor-pointer" @click="createForm()" />
            <!--            <div class="box-search align-items-center">-->
            <!--              &lt;!&ndash;              <el-input&ndash;&gt;-->
            <!--              &lt;!&ndash;                placeholder="検索"&ndash;&gt;-->
            <!--              &lt;!&ndash;                prefix-icon="el-icon-search"&ndash;&gt;-->
            <!--              &lt;!&ndash;                v-model="input2">&ndash;&gt;-->
            <!--              &lt;!&ndash;              </el-input>&ndash;&gt;-->
            <!--              &lt;!&ndash;              <i class="el-icon-close cursor-pointer" @click="closeInputSearch()"></i>&ndash;&gt;-->
            <!--            </div>-->
            <div class="d-flex justify-content-end align-items-center">
              <img class="icon-search cursor-pointer" :src="require(`../../assets/images/icon-search.png`)">
              <template class="select-custom">
                <el-select v-model="role_id_selected" placeholder="Select" class="el-select-custom" value="" @change="getListAllUser()">
                  <el-option
                    class="el-option-custom"
                    label="All Role"
                    value=""
                  />
                  <el-option
                    v-for="role in listRoles ?? [] "
                    :key="role.id"
                    :label="role.name"
                    :value="role.id"
                  />
                </el-select>
              </template>
            </div>
          </div>
          <hr class="line">
          <div class="">
            <el-table
              :data="listUser ? listUser : []"
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
                label="Name"
                align="center"
              >
                <template slot-scope="scope">
                  <span>
                    {{ scope.row.name }}
                  </span>
                </template>
              </el-table-column>
              <el-table-column
                prop="retirement_date"
                label=""
                align="center"
                width="90"
              >
                <template slot-scope="scope">
                  <span v-if="checkDateRetired(scope.retirement_date)" style="color: red;">
                    Retired
                  </span>
                </template>
              </el-table-column>
              <el-table-column
                prop="email"
                label="Email"
                align="center"
              />
              <el-table-column
                prop="role.name"
                label="Role"
                align="center"
              />
            </el-table>
            <!--            <b-table-->
            <!--              id="my-table"-->
            <!--              class="text-center w-100 mb-0"-->
            <!--              :items="listUser ? listUser : []"-->
            <!--              :fields="fields"-->
            <!--              responsive="sm"-->
            <!--              :current-page="pagination.current_page"-->
            <!--              show-empty-->
            <!--            >-->
            <!--              <template #cell(retirement_date)="row">-->
            <!--                <span v-if="checkDateRetired(row.item.retirement_date)" style="color: red;">-->
            <!--                  Retirement-->
            <!--                </span>-->
            <!--              </template>-->
            <!--              <template #cell(email)="row">-->
            <!--                <div class="email-link" @click="goToEditScreen(row.item.id)">{{ row.item.email }}</div>-->
            <!--              </template>-->
            <!--              &lt;!&ndash;              <template #cell(edit)="edit">&ndash;&gt;-->
            <!--              &lt;!&ndash;                <b-button&ndash;&gt;-->
            <!--              &lt;!&ndash;                  :id="'btn-edit-'+ edit.item.id"&ndash;&gt;-->
            <!--              &lt;!&ndash;                  class="btn btn-edit fs-14"&ndash;&gt;-->
            <!--              &lt;!&ndash;                  dusk="btn-edit"&ndash;&gt;-->
            <!--              &lt;!&ndash;                  @click="goToEditScreen(edit.item.id)"&ndash;&gt;-->
            <!--              &lt;!&ndash;                >{{ $t('LANGUAGES.TEXT_EDIT') }}</b-button>&ndash;&gt;-->
            <!--              &lt;!&ndash;              </template>&ndash;&gt;-->
            <!--              &lt;!&ndash;              <template #cell(delete)="info">&ndash;&gt;-->
            <!--              &lt;!&ndash;                <b-button&ndash;&gt;-->
            <!--              &lt;!&ndash;                  :id="'btn-remove-'+ info.item.id"&ndash;&gt;-->
            <!--              &lt;!&ndash;                  class="btn btn-delete fs-14"&ndash;&gt;-->
            <!--              &lt;!&ndash;                  @click="confirmationForm(info.item)"&ndash;&gt;-->
            <!--              &lt;!&ndash;                >{{ $t('LANGUAGES.TEXT_DELETE') }}</b-button>&ndash;&gt;-->
            <!--              &lt;!&ndash;              </template>&ndash;&gt;-->
            <!--              <template #empty="">-->
            <!--                {{ $t('LANGUAGES.TEXT_NO_DATA') }}-->
            <!--              </template>-->
            <!--            </b-table>-->
          </div>
        </div>

        <div class="use-management-pagianation">
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
        </div>
        <!--        <div class="use-management-pagianation">-->
        <!--          <div class="card-body pagianation">-->
        <!--            <b-pagination-->
        <!--              v-model="pagination.current_page"-->
        <!--              :per-page="pagination.per_page"-->
        <!--              :total-rows="pagination.total_records"-->
        <!--              aria-controls="my-table"-->
        <!--              :disabled="pagination.isDisable"-->
        <!--            />-->
        <!--          </div>-->
        <!--        </div>-->

        <!-- Modal -->
        <el-dialog class="title-add-working" title="Add Employee" :visible.sync="openModalAdd" width="50%" @click="hideCreateModal()">
          <ValidationObserver
            ref="obsAddEmployee"
            tag="div"
          >
            <ValidationProvider
              v-slot="{ errors }"
              name="name"
              rules="required"
            >
              <label for="nameEmployee">Employee Name</label>
              <el-input id="nameEmployee" v-model="formCreate.name"/>
              <div class="text-error">
                {{ errors[0] }}
              </div>
            </ValidationProvider>
            <ValidationProvider
              v-slot="{ errors }"
              name="email"
              rules="required|email"
            >
              <label for="emailEmployee">Employee Email</label>
              <el-input id="emailEmployee" v-model="formCreate.email"/>
              <div class="text-error">
                {{ errors[0] }}
              </div>
            </ValidationProvider>
            <hr class="line">
            <p class="title-create-employee mb-3">Face Data</p>
            <div style="margin-bottom: 15px;">
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
                    <template v-if="withoutMask">
                      <div v-for="(file, index) in selectedWithoutMaskFiles" :key="index" class="preview-item">
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
                    </template>
                    <template v-if="withMask">
                      <div v-for="(file, index) in selectedWithMaskFiles" :key="index" class="preview-item">
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
                    </template>
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
            <hr class="line">
            <ValidationProvider
              v-slot="{ errors }"
              name="role"
              rules="required"
            >
              <label class="title-create-employee" for="roleEmployee">Role</label>
              <br>
              <el-radio-group id="roleEmployee" v-model="formCreate.role_id">
                <el-radio
                  v-for="element in listRoles"
                  :key="element.id"
                  :label="element.id"
                >
                  {{ element.name }}
                </el-radio>
              </el-radio-group>
              <div class="text-error">
                {{ errors[0] }}
              </div>
            </ValidationProvider>
            <hr class="line">
            <p class="label-custom title-create-employee">Password</p>
            <ValidationProvider
              v-slot="{ errors }"
              name="password"
              vid="password"
              rules="required|min:8"
            >
              <label for="passwordEmployee">Password</label>
              <el-input id="passwordEmployee" v-model="formCreate.password" type="password" show-password />
              <div class="text-error">
                {{ errors[0] }}
              </div>
            </ValidationProvider>
            <ValidationProvider
              v-slot="{ errors }"
              name="password_confirmation"
              rules="required|confirmed:password|min:8"
            >
              <label for="password_confirmationEmployee">Password (Confirm)</label>
              <el-input id="password_confirmationEmployee" v-model="formCreate.password_confirmation" type="password" show-password />
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

        <!-- Modal create -->
        <!--        <b-modal id="bv-modal-create" size="lg" hide-footer hide-header @hidden="hideCreateModal()">-->
        <!--          <div>-->
        <!--            <ValidationObserver-->
        <!--              ref="obsAddEmployee"-->
        <!--              tag="div"-->
        <!--            >-->
        <!--              <h4 class="mb-0 font-weight-normal" style="border-bottom: 1px solid rgba(0, 0, 0, 0.15);">-->
        <!--                <header>-->
        <!--                  <h4>Add Employee</h4>-->
        <!--                </header>-->
        <!--                <div>-->
        <!--                          <ValidationProvider-->
        <!--                            v-slot="{ errors }"-->
        <!--                            name="name"-->
        <!--                            rules="required"-->
        <!--                  >-->
        <!--                    <label for="nameEmployee" style="font-size: 16px;">Name:</label>-->
        <!--                    <b-input-group>-->
        <!--                      <b-form-input-->
        <!--                        id="nameEmployee"-->
        <!--                        v-model="formCreate.name"-->
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
        <!--                    name="email"-->
        <!--                    rules="required|email"-->
        <!--                  >-->
        <!--                    <label for="emailEmployee" style="font-size: 16px;">Email:</label>-->
        <!--                    <b-input-group>-->
        <!--                      <b-form-input-->
        <!--                        id="emailEmployee"-->
        <!--                        v-model="formCreate.email"-->
        <!--                      />-->
        <!--                    </b-input-group>-->
        <!--                            <div class="text-error">-->
        <!--                              {{ errors[0] }}-->
        <!--                            </div>-->
        <!--                  </ValidationProvider>-->
        <!--                </div>-->
        <!--              </h4>-->
        <!--              <h4 class="mb-0 font-weight-normal" style="margin-top: 15px; border-bottom: 1px solid rgba(0, 0, 0, 0.15);">-->
        <!--                <header>-->
        <!--                  <h4>Face Data</h4>-->
        <!--                </header>-->
        <!--                        <div style="margin-bottom: 15px;">-->
        <!--                          <div class="image-dropzone" @dragover.prevent @drop="handleDrop">-->
        <!--                            <div style="border-bottom: 2px solid;display: flex; gap: 1rem">-->
        <!--                              <div-->
        <!--                                :class="{check_with_or_without_mask: withoutMask}"-->
        <!--                                style="display: flex; gap: 1rem;cursor: pointer;border-right: 2px solid"-->
        <!--                                @click="checkWithoutMask()"-->
        <!--                              >-->
        <!--                                <b-icon-emoji-smile style="margin-top: 10px; height: 55%" />-->
        <!--                                <div style="margin-right: 20px">-->
        <!--                                  <p>Face image</p>-->
        <!--                                  <p>without mask</p>-->
        <!--                                </div>-->
        <!--                              </div>-->
        <!--                              <div-->
        <!--                                :class="{check_with_or_without_mask: withMask}"-->
        <!--                                style="display: flex; gap: 1rem;cursor: pointer"-->
        <!--                                @click="checkWithMask()"-->
        <!--                              >-->
        <!--                                <b-icon-emoji-frown style="margin-top: 10px; height: 55%" />-->
        <!--                                <div style="margin-right: 20px">-->
        <!--                                  <p>Face image</p>-->
        <!--                                  <p>with mask</p>-->
        <!--                                </div>-->
        <!--                              </div>-->
        <!--                            </div>-->
        <!--                            <div-->
        <!--                              style="overflow-x: auto;-->
        <!--                              white-space: nowrap;"-->
        <!--                            >-->
        <!--                              <input-->
        <!--                                ref="fileInput"-->
        <!--                                type="file"-->
        <!--                                multiple-->
        <!--                                style="display: none;"-->
        <!--                                @change="handleFileSelect"-->
        <!--                              >-->
        <!--                              <div class="image-preview">-->
        <!--                                <div v-for="(file, index) in selectedFiles" :key="index" class="preview-item">-->
        <!--                                  <img :src="convertFileToUrl(file)">-->
        <!--                                  <b-icon-x-circle-->
        <!--                                    style="display: block;-->
        <!--                                  float: right;-->
        <!--                                  position: relative;-->
        <!--                                  top: -9px;-->
        <!--                                  right: 8px;-->
        <!--                                  height: 17px;-->
        <!--                                  cursor: pointer"-->
        <!--                                    @click="removeFile(index)"-->
        <!--                                  >Remove-->
        <!--                                  </b-icon-x-circle>-->
        <!--                                </div>-->
        <!--                              </div>-->
        <!--                            </div>-->
        <!--                            <div style="display: flex;font-size: large; gap: 1rem">-->
        <!--                              <div style="color: blue;cursor: pointer;" @click="openFilePicker">Select File</div>-->
        <!--                              <div>|</div>-->
        <!--                              <div style="cursor: pointer;" @click="removeFileAll">Delete all</div>-->
        <!--                            </div>-->
        <!--                          </div>-->
        <!--                          <div v-if="validateFile" class="text-error">-->
        <!--                            {{ messageErrorFile }}-->
        <!--                          </div>-->
        <!--                        </div>-->
        <!--              </h4>-->
        <!--              <h4 class="mb-0 font-weight-normal" style="margin-top: 15px; border-bottom: 1px solid rgba(0, 0, 0, 0.15);">-->
        <!--                <header>-->
        <!--                  <h4>Role</h4>-->
        <!--                </header>-->
        <!--                        <div style="margin-bottom: 15px;">-->
        <!--                          <ValidationProvider-->
        <!--                            v-slot="{ errors }"-->
        <!--                            name="role"-->
        <!--                            rules="required"-->
        <!--                          >-->
        <!--                            <label for="role_id_create" style="font-size: 16px;">Role:</label>-->
        <!--                            <b-form-checkbox-group-->
        <!--                              id="role_id_create"-->
        <!--                              v-model="formCreate.role_id"-->
        <!--                              :options="listRoles ?? []"-->
        <!--                              value-field="id"-->
        <!--                              text-field="name"-->
        <!--                            />-->
        <!--                            <div class="text-error">-->
        <!--                              {{ errors[0] }}-->
        <!--                            </div>-->
        <!--                          </ValidationProvider>-->
        <!--                        </div>-->
        <!--              </h4>-->
        <!--              <h4 class="mb-0 font-weight-normal" style="margin-top: 15px; border-bottom: 1px solid rgba(0, 0, 0, 0.15);">-->
        <!--                <header>-->
        <!--                  <h4>Password</h4>-->
        <!--                </header>-->
        <!--                <div>-->
<!--                          <ValidationProvider-->
<!--                            v-slot="{ errors }"-->
<!--                            name="password"-->
<!--                            vid="password"-->
<!--                            rules="required"-->
        <!--                  >-->
        <!--                    <label for="password" style="font-size: 16px;">Password:</label>-->
        <!--                    <b-input-group>-->
        <!--                      <b-form-input-->
        <!--                        id="password"-->
        <!--                        v-model="formCreate.password"-->
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
        <!--                        v-model="formCreate.password_confirmation"-->
        <!--                        type="password"-->
        <!--                      />-->
        <!--                    </b-input-group>-->
        <!--                    <div class="text-error">-->
        <!--                      {{ errors[0] }}-->
        <!--                    </div>-->
        <!--                  </ValidationProvider>-->
        <!--                </div>-->
        <!--              </h4>-->
        <!--            </ValidationObserver>-->
        <!--          </div>-->
        <!--          <div class="justify-content-end d-flex p-3">-->
        <!--            <b-button-->
        <!--              class="mt-3 w-25 fs-12 btn btn-accept"-->
        <!--              squared-->
        <!--              @click="submitCreate()"-->
        <!--            >Save</b-button>-->
        <!--            <b-button-->
        <!--              class="mt-3 ml-3 w-25 fs-12 btn btn-close"-->
        <!--              squared-->
        <!--              @click="hideCreateModal()"-->
        <!--            >{{ $t('LANGUAGES.TEXT_BUTTON_CLOSE') }}</b-button>-->
        <!--          </div>-->
        <!--        </b-modal>-->
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
            >{{ $t('LANGUAGES.TEXT_BUTTON_YES') }}</b-button>
            <b-button
              class="mt-3 ml-3 w-25 fs-12 btn btn-close"
              squared
              @click="hideModal()"
            >{{ $t('LANGUAGES.TEXT_BUTTON_CLOSE') }}</b-button>
          </div>
        </b-modal>
      </div>
    </div>
  </div>
</template>

<script>
import { deleteOneUser, getAllUser, postOneUser } from '../../api/user';
import { MakeToast } from '../../utils/toast_message';
import * as CONFIGS from '../../configs/index';
import { getAllRole } from '../../api/role';
import * as ImageApi from '../../api/image_face';
import { ValidationObserver, ValidationProvider } from 'vee-validate';

export default {
  name: 'UserManagement',
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
      fields: [
        { key: 'name', label: this.$t('LANGUAGES.TEXT_USER_NAME') },
        { key: 'retirement_date', label: '', class: 'col-1' },
        { key: 'email', label: this.$t('LANGUAGES.TEXT_EMAIL') },
        { key: 'role.name', label: 'Role' },
        // { key: 'company_branchs.name', label: this.$t('LANGUAGES.TEXT_BRANCH') },
        // { key: 'edit', label: this.$t('LANGUAGES.TEXT_EDIT') },
        // { key: 'delete', label: this.$t('LANGUAGES.TEXT_DELETE') },
      ],
      role_id_selected: '',
      name_search: null,
      formCreate: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role_id: '',
        status: 1,
      },
      selectedWithMaskFiles: [],
      selectedWithoutMaskFiles: [],
      withoutMask: true,
      withMask: false,
      validateFile: false,
      messageErrorFile: [],
      openModalAdd: false,
      waitCreate: false,
    };
  },
  computed: {
    role_id() {
      return this.$store.getters.role_id;
    },
    listRoles() {
      return this.$store.getters.listRoles;
    },
    listUser() {
      return this.$store.getters.listUser;
    },
    currChange() {
      return this.pagination.current_page;
    },
  },
  watch: {
    currChange() {
      this.getListAllUser();
    },
  },
  created() {
    this.getListRole();
    this.getListAllUser();
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
    async getListAllUser() {
      this.pagination.isDisable = true;
      const PARAMS = {
        page: this.pagination.current_page,
        per_page: this.pagination.per_page,
        role_id: this.role_id_selected,
        name: this.name_search,
      };
      await getAllUser(PARAMS)
        .then((response) => {
          if (response.code === 200) {
            const listUser = response.data.result;
            // console.log('listUser===>', listUser);
            this.$store.dispatch('app/saveListUSer', listUser);
            this.pagination.total_records =
            response.data.pagination.total_records;
            this.pagination.current_page = response.data.pagination.current_page;
            this.pagination.isDisable = false;
            // listUser.forEach((element) => {
            //   element.roles.name = this.convertRoles(
            //     element.roles.name);
            // });
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
      this.$router.push({ path: `/user/edit/${val.id}` }, (onAbort) => {});
    },
    toCreatePage() {
      this.$router.push('/user/create');
    },
    createForm(){
      this.openModalAdd = true;
      // this.$bvModal.show('bv-modal-create');
    },
    confirmationForm(item) {
      this.infoModel = item;
      this.$bvModal.show('bv-modal-delete');
    },
    hideCreateModal(){
      this.formCreate = {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role_id: '',
        status: 1,
      };
      if (this.withoutMask){
        this.selectedWithoutMaskFiles.splice(0, this.selectedWithoutMaskFiles.length);
      }
      if (this.withMask){
        this.selectedWithMaskFiles.splice(0, this.selectedWithMaskFiles.length);
      }
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
          MakeToast({
            variant: 'success',
            title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
            content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_DELETE_USER_SUCCESSFULLY'),
          });
          this.hideModal();
          this.getListAllUser();
        });
      }
    },
    convertRoles(roles) {
      switch (roles) {
        case 'Headquater_Role':
          return this.$t('LANGUAGES.TEXT_HEAD_QUARTER_ROLE');
        case 'Department_Role':
          return this.$t('LANGUAGES.TEXT_HEAD_DEPARTMENT_ROLE');
        default:
      }
    },
    async submitCreate() {
      this.checkNumImage();
      const isValid = await this.$refs.obsAddEmployee.validate();
      if (!isValid && this.validateFile) {
        MakeToast({
          variant: 'warning',
          title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
          content: 'Still error',
        });
      } else {
        this.waitCreate = true;
        await postOneUser(this.formCreate).then(async(response) => {
          if (response.code === 200) {
            MakeToast({
              variant: 'success',
              title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
              content: 'Create employee success',
            });
            // Kiểm tra selectedWithoutMaskFiles
            if (this.selectedWithoutMaskFiles.length !== 0){
              const image = new FormData();
              for (let i = 0; i < this.selectedWithoutMaskFiles.length; i++) {
                const file = this.selectedWithoutMaskFiles[i];
                image.append('file[]', file);
              }
              image.append('type', 'WithoutMask');
              image.append('user_id', response.data.id);

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
              image.append('user_id', response.data.id);

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
            this.formCreate = {
              name: '',
              email: '',
              password: '',
              password_confirmation: '',
              role_id: '',
              status: 1,
            };
            this.waitCreate = false;
            this.openModalAdd = false;
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
    checkDateRetired(date){
      if (date == null){
        return false;
      }
      const dateRetired = new Date(this.formatTimeStamp(date)).getTime();
      const dateNow = new Date().getTime();
      return dateRetired > dateNow;
    },
    formatTimeStamp(date){
      const datePart = date.split(' ')[0]; // Extract the date part from the received value
      const parts = datePart.split('-');
      const year = parts[0];
      const month = parts[1];
      const day = parts[2];
      return `${year}-${month}-${day}`;
    },
    checkWithoutMask(){
      this.withoutMask = true;
      this.withMask = false;
    },
    checkWithMask(){
      this.withoutMask = false;
      this.withMask = true;
    },
    handleDrop(event) {
      event.preventDefault();
      const files = event.dataTransfer.files;
      for (let i = 0; i < files.length; i++) {
        const file = files[i];
        // const fileURL = URL.createObjectURL(file);
        if (this.withoutMask){
          this.selectedWithoutMaskFiles.push(file);
        }
        if (this.withMask){
          this.selectedWithMaskFiles.push(file);
        }
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
          if (this.withoutMask){
            this.selectedWithoutMaskFiles.push(file);
          }
          if (this.withMask){
            this.selectedWithMaskFiles.push(file);
          }
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
      if (this.withoutMask){
        this.selectedWithoutMaskFiles.splice(index, 1);
      }
      if (this.withMask){
        this.selectedWithMaskFiles.splice(index, 1);
      }
      this.checkNumImage();
    },
    chooseFiles() {
      this.$refs.fileInput.click();
    },
    removeFileAll(){
      if (this.withoutMask){
        this.selectedWithoutMaskFiles.splice(0, this.selectedWithoutMaskFiles.length);
      }
      if (this.withMask){
        this.selectedWithMaskFiles.splice(0, this.selectedWithMaskFiles.length);
      }
      this.checkNumImage();
    },
    checkNumImage(){
      this.messageErrorFile = [];
      if (this.selectedWithoutMaskFiles.length === 0){
        this.validateFile = true;
        this.messageErrorFile.push('Image without mask must one image');
      }

      if (this.selectedWithoutMaskFiles.length === 0 && this.selectedWithMaskFiles.length === 0){
        this.validateFile = true;
        this.messageErrorFile.push('Pleas choose image');
      }
    },
    // copy cua Yen
    rowWorkingStyle({ row, rowIndex }) {
      return { 'cursor': 'pointer' };
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
::v-deep .table tbody {
  /*border: 0.9px solid #888888;*/
}
::v-deep .table thead th {
  /*border: 0.9px solid #888888;*/
}
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
  line-height: 48px;
  color: #000000;
}
</style>
