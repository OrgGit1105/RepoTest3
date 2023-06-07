<template>
  <div>
    <div class="main-page">
      <div class="justify-content-start p-5"><div class="basic"> <h1 class="pl-3 title-info">{{ $t('LANGUAGES.TEXT_USER_EDIT') }}</h1></div></div>
      <b-col lg="6" md="8" sm="12" style="margin:auto;">
        <b-form class="py-5 px-4" @submit="onSubmit($event)">
          <div>
            <!-- 1 -->
            <b-form-group>
              <b-row>
                <b-col lg="3">
                  <label class="label-name">{{ $t('LANGUAGES.TEXT_USER_NAME') }}</label>
                </b-col>
                <b-col lg="9">
                  <b-form-input
                    id="txtUserName"
                    v-model="form.username"
                    dusk="username"
                    :placeholder="$t('LANGUAGES.TEXT_PLACEHOLDER_ENTER_USER_NAME')"
                    :state="error.username"
                    :formatter="formatText"
                    @input="handleChangeForm($event,'username')"
                  />
                  <b-form-invalid-feedback :state="error.username">
                    {{ $t('LANGUAGES.ERROR_PLEASE_ENTER_INPUT_USER_NAME') }}
                  </b-form-invalid-feedback>
                </b-col>
              </b-row>
            </b-form-group>
            <!-- 2 -->
            <b-form-group id="input-group-4" class="prediction-item">
              <b-row>
                <b-col lg="3">
                  <label class="label-name">{{ $t('LANGUAGES.TEXT_EMAIL') }}</label>
                </b-col>
                <b-col lg="9">
                  <b-form-input
                    id="txtEmail"
                    v-model="form.email"
                    dusk="email"
                    type="text"
                    :placeholder="$t('LANGUAGES.TEXT_PLACEHOLDER_ENTER_EMAIL')"
                    :state="error.email"
                    :formatter="formatText"
                    @input="handleChangeForm($event,'email')"
                  />
                  <b-form-invalid-feedback :state="error.email">
                    {{ $t('LANGUAGES.ERROR_EMAIL_MUST_BE_AN_EMAIL_AND_NOT_NULL') }}
                  </b-form-invalid-feedback>
                </b-col>
              </b-row>
            </b-form-group>
            <!-- 3 -->
            <b-form-group id="input-group-5">
              <b-row>
                <b-col lg="3">
                  <label class="label-name">{{ $t('LANGUAGES.TEXT_PASSWORD') }}</label>
                </b-col>
                <b-col lg="9">
                  <b-form-input
                    id="txtPassword"
                    v-model="form.password"
                    dusk="password"
                    :placeholder="$t('LANGUAGES.TEXT_PLACEHOLDER_ENTER_PASSWORD')"
                    :state="error.password"
                    :formatter="formatText"
                    @input="handleChangeForm($event,'password')"
                  />
                  <b-form-invalid-feedback :state="error.password">
                    {{ $t('LANGUAGES.ERROR_YOUR_PASSWORD_MUST_BE_GREATER_THAN_8_CHARACTERS_AND_LESS_THAN_16') }}
                  </b-form-invalid-feedback>
                </b-col>
              </b-row>
            </b-form-group>
            <!-- 4 -->
            <b-form-group v-if="roleId === headQuarter">
              <b-row>
                <b-col lg="3">
                  <label class="label-name">{{ $t('LANGUAGES.TEXT_AUTHORITY') }}</label>
                </b-col>
                <b-col lg="9">
                  <b-form-select
                    id="slAuthority"
                    v-model="form.role_id"
                    dusk="role_id"
                    placeholder="Please Select Authority"
                    :options="authorityOption"
                    :state="error.role_id"
                    @change="handleChangeForm($event,'role_id')"
                  />
                  <b-form-invalid-feedback :state="error.role_id">
                    Please Select Authority
                  </b-form-invalid-feedback>
                </b-col>
              </b-row>
            </b-form-group>
            <!-- 5 -->
            <b-form-group v-if="roleId === headQuarter">
              <b-row>
                <b-col lg="3">
                  <label class="label-name">{{ $t('LANGUAGES.TEXT_DEPARTMENT') }}</label>
                </b-col>
                <b-col lg="9">
                  <b-form-select
                    id="slDepartment"
                    v-model="form.department_id"
                    :options="branchList"
                    dusk="department_id"
                    placeholder="Please Select Department"
                    :state="error.department_id"
                    :disabled="author"
                    @change="handleChangeForm($event,'department_id')"
                  />
                  <b-form-invalid-feedback :state="error.department_id">
                    {{ $t('LANGUAGES.ERROR_PLEASE_SELECT_DEPARTMENT') }}
                  </b-form-invalid-feedback>
                </b-col>
              </b-row>
            </b-form-group>
          </div>
          <div class="d-flex btn-submit py-5 px-5"> <b-button variant="warning" class="fs-15 px-5" @click="returnToIndex()">{{ $t('LANGUAGES.TEXT_BUTTON_RETURN') }}</b-button> <b-button variant="warning" class="fs-15 px-5" type="submit" dusk="submit">{{ $t('LANGUAGES.TEXT_BUTTON_SUBMIT') }}</b-button>  </div>
        </b-form>
      </b-col>
    </div>
  </div>
</template>

<script>
import * as CONFIGS from '../../configs/index';
import { regEmail } from '../../utils/regexr';
import * as UserApi from '../../api/user';
import { MakeToast } from '../../utils/toast_message';
import * as CompanyBranchApi from '../../api/company_branch';
export default {
  name: 'EditUser',

  data() {
    return {
      headQuarter: CONFIGS.UserRoleId.HEAD_QUARTER,
      authorityOption: CONFIGS.AuthorityList,
      branchList: [],
      form: {
        role_id: '',
        department_id: '',
        username: '',
        email: '',
        password: '',
      },
      error: {
        role_id: null,
        department_id: null,
        username: null,
        email: null,
        password: null,
      },
      id: this.$route.params.id,
      userInfo: {},
      author: true,
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
  },
  created() {
    this.getAllCompanyBranch();
    this.getUserInfo();
  },

  methods: {
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
          MakeToast({
            variant: 'success',
            title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
            content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_GET_USER_INFO_SUCCESSFULLY'),
          });
          if (this.roleId === this.headQuarter) {
            this.getAllCompanyBranch();
          }
          // console.log('Data return', response.data);
          this.form = response.data;
          // const listValue = [1, 2, 3];
          const departmentList = [];
          // console.log('Manh', response.data.company_branchs);
          if (response.data.role_id === 2){
            this.author = false;
            const departmentByRole = this.companyBranch.filter(x => x.role_id === response.data.role_id);
            departmentByRole.length > 0 && departmentByRole.map(item => {
              departmentList.push({ value: item.id, text: item.name });
            });
            // console.log('departmentByRole ==>', departmentByRole);
            this.branchList = departmentList;
            // console.log('branchList ==>', this.branchList);
            this.form.department_id = response.data.company_branchs.id;
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

    returnToIndex() {
      this.$router.push('/user/index');
    },
    checkValidate() {
      if (this.form.role_id === '' && this.roleId === this.headQuarter) {
        this.error.role_id = false;
      } else if (this.form.department_id === '' && this.roleId === this.headQuarter && this.form.role_id === 2) {
        this.error.department_id = false;
      } else if (this.form.department_id === null && this.roleId === this.headQuarter && this.form.role_id === 2) {
        this.error.department_id = false;
      } else if (this.form.username === '') {
        this.error.username = false;
      } else if (!this.form.email || !this.checkEmail(this.form.email)) {
        this.error.email = false;
      } else if (this.form.password && this.form.password.length < 8) {
        this.error.password = false;
      } else {
        return true;
      }
    },
    checkEmail(email) {
      return regEmail.test(email);
    },
    handleChangeForm(event, field) {
      const newValue = event;
      // const listValue = [1, 2, 3];
      const departmentList = [];
      switch (field) {
        case 'role_id':
          if (newValue) {
            this.error.role_id = true;
            this.branchList = [];
            if (newValue === 2){
              // for (let i = 0; i < listValue.length; i++) {
              //   departmentList.push({ value: this.companyBranch[listValue[i]].id, text: this.companyBranch[listValue[i]].name });
              // }
              const departmentByRole = this.companyBranch.filter(x => x.role_id === newValue);
              departmentByRole.length > 0 && departmentByRole.map(item => {
                departmentList.push({ value: item.id, text: item.name });
              });
              this.branchList = departmentList;
              this.author = false;
            } else {
              this.author = true;
              this.form.department_id = '';
              this.branchList = [];
              this.error.department_id = null;
              // delete this.error['department_id'];
            }
          } else {
            this.branchList = [];
            this.author = true;
            this.form.department_id = '';
          }
          break;
        case 'department_id':
          if (newValue) {
            this.error.department_id = true;
          } else {
            this.error.department_id = false;
          }
          break;
        case 'username':
          if (newValue.length > 0) {
            this.error.username = true;
          } else {
            this.error.username = false;
          }
          break;
        case 'email':
          if (newValue.length === 0 || !this.checkEmail(newValue)) {
            this.error.email = false;
          } else {
            this.error.email = true;
          }
          break;
        case 'password':
          if (newValue.length > 7 && newValue.length < 17) {
            this.error.password = true;
          } else {
            this.error.password = false;
          }
          break;
        default:
          break;
      }
    },
    async getAllCompanyBranch() {
      await CompanyBranchApi.getAllCompanyBranch()
        .then((response) => {
          if (response.code === 200) {
            // console.log('listCompany===>', response);
            const listBranch = response.data.result;
            this.$store.dispatch('app/saveListBranch', listBranch);
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
    onSubmit(e) {
      e.preventDefault();
      this.checkValidate();
      if (this.checkValidate() === true){
        const EDIT_DATA = {
          role_id: this.form.role_id,
          department_id: this.form.department_id,
          username: this.form.username,
          email: this.form.email,
        };
        if (this.form.password) {
          EDIT_DATA.password = this.form.password;
          // console.log('Co chay vao day');
        }
        // console.log('Form edit gui di', EDIT_DATA);
        this.openLoading();
        UserApi.putOneUser(this.id, EDIT_DATA)
          .then((response) => {
            if (response.code === 200) {
              this.closeLoading();
              MakeToast({
                variant: 'success',
                title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
                content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_EDIT_USER_SUCCESSFULLY'),
              });
              this.returnToIndex();
            }
          })
          .catch((error) => {
            this.closeLoading();
            MakeToast({
              variant: 'warning',
              title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
              content: error.message,
            });
          });
      }
    },
    formatText(e) {
      return String(e).substring(0, 255);
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
</style>

