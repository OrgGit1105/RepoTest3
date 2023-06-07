<template>
  <div>
    <div class="main-page container-fluid w-90">
      <div class="justify-content-start p-5">
        <div class="basic"><h1 class="pl-3 title-info text-dx-blue-light-2">{{ $t('LANGUAGES.TEXT_USER_CREATE') }}</h1></div>
      </div>
      <b-col lg="6" md="8" sm="12" style="margin: auto">
        <b-form
          class="py-5 px-4"
          @submit="onSubmit($event)"
          @reset="onReset($event)"
        >
          <div>
            <!-- 1 -->
            <b-form-group id="input-group-3">
              <b-row>
                <b-col lg="3">
                  <label class="label-name">{{ $t('LANGUAGES.TEXT_USER_NAME') }}</label>
                </b-col>
                <b-col lg="9">
                  <b-form-input
                    id="txtUserName"
                    v-model="form.username"
                    dusk="username"
                    :state="error.username"
                    :placeholder="$t('LANGUAGES.TEXT_PLACEHOLDER_ENTER_USER_NAME')"
                    :formatter="formatText"
                    @input="handleChangeForm($event, 'username')"
                  />
                  <b-form-invalid-feedback :state="error.username">
                    {{ $t('LANGUAGES.ERROR_PLEASE_ENTER_USER_NAME') }}
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
                    type="email"
                    :state="error.email"
                    :placeholder="$t('LANGUAGES.TEXT_PLACEHOLDER_ENTER_EMAIL')"
                    :formatter="formatText"
                    @input="handleChangeForm($event, 'email')"
                  />
                  <b-form-invalid-feedback :state="error.email">
                    {{ $t('LANGUAGES.ERROR_PLEASE_ENTER_EMAIL') }}
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
                    id="input-5"
                    v-model="form.password"
                    dusk="password"
                    :state="error.password"
                    :placeholder="$t('LANGUAGES.TEXT_PLACEHOLDER_ENTER_PASSWORD')"
                    :formatter="formatText"
                    @input="handleChangeForm($event, 'password')"
                  />
                  <b-form-invalid-feedback :state="error.password">
                    {{ $t('LANGUAGES.TEXT_PLACEHOLDER_ENTER_PASSWORD') }}
                  </b-form-invalid-feedback>
                </b-col>
              </b-row>
            </b-form-group>
            <!-- 4 -->
            <b-form-group
              v-if="roleId === headQuarter"
              id="input-group-1"
              size=""
            >
              <b-row>
                <b-col lg="3">
                  <label class="label-name">{{ $t('LANGUAGES.TEXT_AUTHORITY') }}</label>
                </b-col>
                <b-col lg="9">
                  <b-form-select
                    id="txtAuthority"
                    v-model="form.role_id"
                    dusk="role_id"
                    :state="error.role_id"
                    :options="authorityList"
                    @change="handleChangeForm($event, 'role_id')"
                  />
                  <b-form-invalid-feedback :state="error.role_id">
                    {{ $t('LANGUAGES.ERROR_PLEASE_SELECT_AUTHORITY') }}
                  </b-form-invalid-feedback>
                </b-col>
              </b-row>
            </b-form-group>
            <!-- 5 -->
            <b-form-group v-if="roleId === headQuarter" id="input-group-2">
              <b-row>
                <b-col lg="3">
                  <label class="label-name">{{ $t('LANGUAGES.TEXT_DEPARTMENT') }}</label>
                </b-col>
                <b-col lg="9">
                  <b-form-select
                    id="txtDepartment"
                    v-model="form.department_id"
                    dusk="department_id"
                    :state="error.department_id"
                    :options="branchList"
                    :disabled="author"
                    @change="handleChangeForm($event, 'department_id')"
                  />
                  <b-form-invalid-feedback :state="error.department_id">
                    {{ $t('LANGUAGES.ERROR_PLEASE_SELECT_DEPARTMENT') }}
                  </b-form-invalid-feedback>
                </b-col>
              </b-row>
            </b-form-group>
          </div>
          <div class="d-flex btn-submit py-5 px-5">
            <b-button
              class="fs-18 px-5 btn"
              @click="returnToIndex()"
            >{{ $t('LANGUAGES.TEXT_BUTTON_RETURN') }}</b-button>
            <b-button
              class="btn_submit fs-18 px-5 btn"
              type="submit"
              @click="onSubmit($event)"
            >{{ $t('LANGUAGES.TEXT_BUTTON_SUBMIT') }}</b-button>
          </div>
        </b-form>
      </b-col>
    </div>
  </div>
</template>

<script>
import * as CONFIGS from '../../configs/index';
import * as UserApi from '../../api/user';
import { regEmail } from '../../utils/regexr';
import { MakeToast } from '../../utils/toast_message';
import * as CompanyBranchApi from '../../api/company_branch';
export default {
  name: 'CreateUser',

  data() {
    return {
      headQuarter: CONFIGS.UserRoleId.HEAD_QUARTER,
      department: CONFIGS.UserRoleId.DEPARTMENT,
      status: 'not_accepted',
      isSuccess: false,
      form: {
        role_id: '',
        department_id: '',
        username: '',
        email: '',
        password: '',
      },
      author: true,
      branchList: [],
      authorityList: CONFIGS.AuthorityList,
      error: {
        role_id: null,
        department_id: null,
        username: null,
        email: null,
        password: null,
      },
    };
  },
  computed: {
    roleId() {
      // console.log('Manh', this.$store.getters.role_id);
      return this.$store.getters.role_id;
    },
    companyBranch() {
      return this.$store.getters.listBranch;
    },
  },
  watch: {
  },
  created() {
    if (this.roleId === this.headQuarter) {
      this.getAllCompanyBranch();
    }
  },
  methods: {
    checkvalidate() {
      if (this.form.username === '') {
        this.error.username = false;
      }
      if (!this.form.email || !this.checkEmail(this.form.email)) {
        this.error.email = false;
      }
      if (!this.form.password || this.form.password.length < 8) {
        this.error.password = false;
      }
      if (this.form.role_id === '' && this.roleId === this.headQuarter) {
        this.error.role_id = false;
      }
      if (this.form.department_id === '' && this.roleId === this.headQuarter && this.form.role_id === this.department) {
        this.error.department_id = false;
      }
      if (this.form.department_id === '' && this.form.role_id === this.headQuarter) {
        this.error.department_id = null;
      }
      if (this.roleId !== this.headQuarter) {
        delete this.error['role_id'];
        delete this.error['department_id'];
      }
      if (this.form.role_id === this.headQuarter && !this.form.department_id) {
        delete this.error['department_id'];
      }
      if (Object.keys(this.error).every((k) => this.error[k] === false)) {
        return false;
      }
      if (Object.keys(this.error).every((k) => this.error[k] === null) && this.roleId === this.headQuarter) {
        return false;
      }
      if (Object.keys(this.error).every((k) => this.error[k] === true)) {
        return true;
      }
    },

    handleChangeForm(event, field) {
      const newValue = event;
      // const listValue = [1, 2, 3];
      const departmentList = [];
      // console.log('newvalue', newValue);
      switch (field) {
        case 'role_id':
          if (newValue) {
            this.error.role_id = true;
            this.branchList = [];
            if (newValue === 2){
              const departmentByRole = this.companyBranch.filter(x => x.role_id === newValue);
              departmentByRole.length > 0 && departmentByRole.map(item => {
                departmentList.push({ value: item.id, text: item.name });
              });
              this.branchList = departmentList;
              this.author = false;
              this.error.department_id = null;
              this.form.department_id = '';
            } else {
              this.branchList = [];
              this.author = true;
              this.form.department_id = '';
              this.error.department_id = null;
            }
          } else {
            this.error.role_id = false;
            this.author = true;
            this.form.department_id = '';
          }
          break;
        case 'department_id':
          this.error.department_id = null;
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
    async onSubmit(e) {
      e.preventDefault();
      this.isSuccess = false;
      this.checkvalidate();
      if (this.roleId !== this.headQuarter) {
        delete this.form['role_id'];
        delete this.form['department_id'];
      }
      if (this.checkvalidate() === true) {
        this.openLoading();
        // console.log('Form gui di', this.form);
        await UserApi.postOneUser(this.form)
          .then((response) => {
            this.closeLoading();
            if (response.code === 200) {
              this.isSuccess = true;
              MakeToast({
                variant: 'success',
                title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
                content: this.$t('LANGUAGES.TEXT_TOAST_CONTENT_CREATE_USER_SUCCESSFULLY'),
              });
              this.returnToIndex();
            }
            if (response.code === 422) {
              MakeToast({
                variant: 'warning',
                title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
                content: response.message,
              });
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
      } else {
        e.stopPropagation();
      }
    },
    returnToIndex() {
      this.$router.push('/user/index');
    },
    onReset(e) {
      e.preventDefault();
      this.form = {};
      this.error = {};
      this.status = 'not_accepted';
    },
    openLoading() {
      this.$store.dispatch('loading/setLoading', true);
    },
    closeLoading() {
      this.$store.dispatch('loading/setLoading', false);
    },
    checkEmail(email) {
      return regEmail.test(email);
    },
    async getAllCompanyBranch() {
      await CompanyBranchApi.getAllCompanyBranch()
        .then((response) => {
          if (response.code === 200) {
            console.log('listCompany===>', response);
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
.label-name {
  font-size: 16px;
    padding-top: 9px;
    font-weight: 500;
}
.form-control {
  border: 1px solid #ced4da;
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
/* ::v-deep select:first-child:disabled {
  color: blue;
}
::v-deep option {
  color: #111111;
}
::v-deep option[value=""][disabled] {
  display: none !important;
  color: blue;
}
select:required:invalid { color: red; }
::v-deep select {
  color: red !important;
}
::v-deep select#txtInterViewDeparute {
    color: blue !important;
}
select:focus {
  color: #9e9e9e;
}
option {
  color: black;
}
option:first-of-type {
  color: #9e9e9e;
} */

::v-deep select:required:invalid {
  color: #6f737c;
}
::v-deep  option[value=""][disabled] {
 display: none !important;
  color: blue !important;
}
::v-deep option {
  color: black;
}
::v-deep .custom-select {
  color: #6b727a !important;
}

</style>

