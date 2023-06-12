<template>
  <div>
    <div class="container-fluid w-90">
      <div class="container-fluid-body mt-5 mb-5">
        <div class="use-management-title">
          <div class="card-body p-5">
            <div class="d-flex justify-content-between">
              <div class="basic">
                <h1 class="pl-3 title-info">{{ $t('LANGUAGES.TEXT_EMPLOYEE_MANAGER') }}</h1>
              </div>
            </div>
          </div>
        </div>
        <hr>
        <div class="use-management-title">
          <div class="card-body p-5">
            <div class="d-flex justify-content-sm-between">
              <div class="basic">
                <div>
                  <h2>
                    <b-icon-plus-circle-fill style="font-size: 10rem; color: rgb(0, 0, 255); opacity: 1;" />
                  </h2>
                </div>
              </div>
              <div>
                <span>
                  <b-icon-search />
                </span>
                <span>
                  <b-form-select
                    v-model="role_id_selected"
                    :options="roles_select"
                    value-field="id"
                    text-field="name"
                    class="col-6"
                  />
                </span>
                <span>
                  <button class="btn btn-warning">
                  {{ $t('LANGUAGES.TEXT_BUTTON_SIGN_UP') }}
                </button>
                </span>
              </div>
            </div>
          </div>
        </div>

        <div class="use-management-title-table mt-5 px-3">
          <div class="card-body">
            <b-table
              id="my-table"
              class="text-center w-100  mb-0"
              :items="listUser ? listUser : []"
              :fields="role_id === headQuarter ? fields : fields2"
              responsive="sm"
              :current-page="pagination.current_page"
              show-empty
            >
              <template #cell(edit)="edit">
                <b-button
                  :id="'btn-edit-'+ edit.item.id"
                  class="btn btn-edit fs-14"
                  dusk="btn-edit"
                  @click="goToEditScreen(edit.item.id)"
                >{{ $t('LANGUAGES.TEXT_EDIT') }}</b-button>
              </template>
              <template #cell(delete)="info">
                <b-button
                  :id="'btn-remove-'+ info.item.id"
                  class="btn btn-delete fs-14"
                  @click="confirmationForm(info.item)"
                >{{ $t('LANGUAGES.TEXT_DELETE') }}</b-button>
              </template>
              <template #empty="">
                {{ $t('LANGUAGES.TEXT_NO_DATA') }}
              </template>
            </b-table>
          </div>
        </div>

        <div class="use-management-pagianation">
          <div class="card-body pagianation">
            <b-pagination
              v-model="pagination.current_page"
              :per-page="pagination.per_page"
              :total-rows="pagination.total_records"
              aria-controls="my-table"
              :disabled="pagination.isDisable"
            />
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
import { getAllUser, deleteOneUser } from '../../api/user';
import { MakeToast } from '../../utils/toast_message';
import * as CONFIGS from '../../configs/index';
import { getAllRole } from '../../api/role';
export default {
  name: 'UserManagement',
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
      role_id_selected: null,
      roles_select: [],
      fields: [
        { key: 'name', label: this.$t('LANGUAGES.TEXT_USER_NAME') },
        { key: 'email', label: this.$t('LANGUAGES.TEXT_EMAIL') },
        { key: 'roles.name', label: this.$t('LANGUAGES.TEXT_AUTHORITY') },
        { key: 'company_branchs.name', label: this.$t('LANGUAGES.TEXT_BRANCH') },
        { key: 'edit', label: this.$t('LANGUAGES.TEXT_EDIT') },
        { key: 'delete', label: this.$t('LANGUAGES.TEXT_DELETE') },
      ],
      fields2: [
        { key: 'name', label: this.$t('LANGUAGES.TEXT_USER_NAME') },
        { key: 'email', label: this.$t('LANGUAGES.TEXT_EMAIL') },
        { key: 'edit', label: this.$t('LANGUAGES.TEXT_EDIT') },
        { key: 'delete', label: this.$t('LANGUAGES.TEXT_DELETE') },
      ],
    };
  },

  computed: {
    role_id() {
      return this.$store.getters.role_id;
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
    this.getListAllUser();
  },
  methods: {
    openLoading() {
      this.$store.dispatch('loading/setLoading', true);
    },
    closeLoading() {
      this.$store.dispatch('loading/setLoading', false);
    },
    async getListAllUser() {
      this.pagination.isDisable = true;
      const PARAMS = {
        page: this.pagination.current_page,
        per_page: this.pagination.per_page,
      };
      this.openLoading();
      await getAllRole().then((response) => {
        if (response.code === 200){
          console.log('data role la ' + response.data);
          this.roles_select = response.data;
          // response.data.forEach((element) => {
          //   element.roles_select.value = element.id;
          //   element.roles_select.text = element.name;
          // });
        }
      }).catch((error) => {
        this.closeLoading();
        MakeToast({
          variant: 'warning',
          title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
          content: error.message,
        });
      });
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
    goToEditScreen(id) {
      this.$router.push({ path: `/user/edit/${id}` }, (onAbort) => {});
    },
    toCreatePage() {
      this.$router.push('/user/create');
    },
    confirmationForm(item) {
      this.infoModel = item;
      this.$bvModal.show('bv-modal-delete');
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
  padding: 13px 100px;
}
.btn-sign:hover {
  background-color: #d57700;
  border-color: #c87000;
}
::v-deep table .b-table {
  width: 100% !important;
}
::v-deep .table {
  width: 100%;
}
table#__BVID__46 {
  width: 100% !important;
}
::v-deep table#__BVID__15 {
  width: 100% !important;
  border-left: 0.9px solid #888888;
}
::v-deep .table tbody {
  border: 0.9px solid #888888;
}
::v-deep .table thead th {
  border: 0.9px solid #888888;
}
::v-deep .table td {
  background: #ffffff !important;
  border-top: 0 !important;
  border-right: 0.9px solid #888888;
  border-bottom: 0.9px solid #888888;
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
  background-color: transparent !important;
  color: #0f68b1;
  border: 1px solid #0f68b1 !important;
}
::v-deep .btn-accept {
    background: #0f68b1;
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

</style>
