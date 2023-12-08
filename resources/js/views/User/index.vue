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
            <div class="box-search align-items-center" :class="displayBoxSearch">
              <el-input
                v-model="name_search"
                placeholder="search by email"
                prefix-icon="el-icon-search"
                @keyup.native="getListAllUser()"
              />
              <i class="el-icon-close cursor-pointer" @click="closeInputSearch()" />
            </div>
            <div class="d-flex justify-content-end align-items-center">
              <img :class="displaySearch" class="icon-search cursor-pointer" :src="require(`../../assets/images/icon-search.png`)" @click="openInputSearch()">
              <div class="select-custom">
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
              </div>
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
              <label for="nameEmployee">Name</label>
              <el-input id="nameEmployee" v-model="formCreate.name" />
              <div class="text-error">
                {{ errors[0] }}
              </div>
            </ValidationProvider>
            <ValidationProvider
              v-slot="{ errors }"
              name="email"
              rules="required|email"
            >
              <label for="emailEmployee" class="mt-3">Email</label>
              <el-input id="emailEmployee" v-model="formCreate.email" />
              <div class="text-error">
                {{ errors[0] }}
              </div>
            </ValidationProvider>
            <ValidationProvider
              v-slot="{ errors }"
              name="gender"
              rules="required"
            >
              <label for="genderEmployee" class="mt-3">Gender</label>
              <el-input id="genderEmployee" v-model="formCreate.gender" />
              <div class="text-error">
                {{ errors[0] }}
              </div>
            </ValidationProvider>
            <ValidationProvider
              v-slot="{ errors }"
              name="birthday"
              rules="required"
            >
              <label for="birthdayEmployee" class="mt-3">Birthday</label>
              <el-input id="birthdayEmployee" v-model="formCreate.birthday" />
              <div class="text-error">
                {{ errors[0] }}
              </div>
            </ValidationProvider>
            <ValidationProvider
              v-slot="{ errors }"
              name="address"
              rules="required"
            >
              <label for="addressEmployee" class="mt-3">Address</label>
              <el-input id="addressEmployee" v-model="formCreate.address" />
              <div class="text-error">
                {{ errors[0] }}
              </div>
            </ValidationProvider>
            <ValidationProvider
              v-slot="{ errors }"
              name="telephone"
              rules="required"
            >
              <label for="telephoneEmployee" class="mt-3">Tel</label>
              <el-input id="telephoneEmployee" v-model="formCreate.telephone" />
              <div class="text-error">
                {{ errors[0] }}
              </div>
            </ValidationProvider>
            <ValidationProvider
              v-slot="{ errors }"
              name="entryDate"
              rules="required"
            >
              <label for="entryDateEmployee" class="mt-3">Entry Date</label>
              <el-input id="entryDateEmployee" v-model="formCreate.entryDate" />
              <div class="text-error">
                {{ errors[0] }}
              </div>
            </ValidationProvider>
            <ValidationProvider
              v-slot="{ errors }"
              name="slackId"
              rules="required"
            >
              <label for="slackIdEmployee" class="mt-3">Slack Id</label>
              <el-input id="slackIdEmployee" v-model="formCreate.slackId" />
              <div class="text-error">
                {{ errors[0] }}
              </div>
            </ValidationProvider>
            <ValidationProvider
              v-slot="{ errors }"
              name="skypeId"
              rules="required"
            >
              <label for="skypeIdEmployee" class="mt-3">Skype Id</label>
              <el-input id="skypeIdEmployee" v-model="formCreate.skypeId" />
              <div class="text-error">
                {{ errors[0] }}
              </div>
            </ValidationProvider>
            <ValidationProvider
              v-slot="{ errors }"
              name="githubId"
              rules="required"
            >
              <label for="githubIdEmployee" class="mt-3">Github Id</label>
              <el-input id="githubIdEmployee" v-model="formCreate.githubId" />
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
                    style="display: flex; gap: 0.5rem;cursor: pointer;border-right: 2px solid"
                    @click="checkWithoutMask()"
                  >
                    <b-icon-emoji-smile style="margin-top: 10px; height: 1.5rem; width: 1.5rem" />
                    <div style="margin-right: 20px; display: flex; flex-direction: column">
                      <div style="display: flex;">
                        <p>Face image</p>
                      </div>
                      <div style="display: flex; padding-left: 0.6rem">
                        <p>Without mask</p>
                      </div>
                    </div>
                  </div>
                  <div
                    :class="{check_with_or_without_mask: withMask}"
                    style="display: flex; gap: 1rem;cursor: pointer"
                    @click="checkWithMask()"
                  >
                    <svg
                      version="1.0"
                      xmlns="http://www.w3.org/2000/svg"
                      viewBox="0 0 459 400"
                      preserveAspectRatio="xMidYMid meet"
                      style="display: inline-block;
                          overflow: visible;
                          vertical-align: -0.15em;
                          width: 1.5em;
                          height: 1.5em;
                          margin-top: 10px"
                    >
                      <g
                        transform="translate(0.000000,460.000000) scale(0.100000,-0.100000)"
                        fill="#000000"
                        stroke="none"
                      >
                        <path
                          d="M2010 4554 c-150 -21 -314 -59 -448 -105 -129 -44 -362 -154 -460
                            -218 -67 -44 -77 -48 -66 -28 4 7 3 9 -2 4 -5 -5 -9 -12 -9 -17 0 -8 -80 -70
                            -91 -70 -3 0 -2 5 1 10 10 16 -1 11 -16 -7 -12 -15 -12 -16 1 -8 13 8 13 7 1
                            -8 -16 -20 -36 -23 -25 -4 4 6 -2 3 -14 -7 -26 -23 -29 -34 -4 -15 14 11 13
                            10 -2 -9 -20 -23 -42 -31 -30 -9 15 23 -111 -91 -191 -175 -85 -87 -152 -166
                            -200 -233 -44 -60 -15 -30 56 60 75 94 214 240 284 298 28 23 -7 -15 -76 -83
                            -172 -170 -331 -376 -408 -528 -12 -23 -26 -40 -31 -37 -6 4 -7 -1 -3 -11 4
                            -12 3 -15 -5 -10 -8 5 -10 1 -5 -10 4 -12 3 -15 -5 -10 -8 5 -10 1 -5 -10 4
                            -12 3 -15 -5 -10 -8 5 -10 1 -5 -10 4 -12 3 -15 -5 -10 -7 4 -10 2 -7 -6 3 -8
                            -8 -43 -24 -78 -122 -271 -184 -578 -184 -910 1 -365 68 -656 227 -985 106
                            -218 204 -362 394 -575 l84 -95 -87 90 c-48 50 -121 135 -162 190 -85 113
                            -111 143 -64 73 98 -146 313 -376 449 -481 95 -74 74 -50 -38 43 -56 45 -98
                            85 -96 88 3 2 31 -19 63 -48 82 -75 243 -193 345 -252 50 -28 85 -55 81 -61
                            -3 -6 -1 -7 5 -3 14 9 103 -35 95 -47 -4 -6 -1 -7 6 -3 7 5 40 -4 74 -20 149
                            -66 382 -132 592 -166 106 -17 504 -17 610 0 210 34 443 100 592 166 34 16 67
                            25 74 20 7 -4 10 -3 6 3 -8 12 81 56 95 47 6 -4 8 -3 5 3 -4 6 31 33 81 61
                            102 59 263 177 345 252 32 29 60 50 63 48 2 -3 -40 -43 -96 -88 -112 -93 -133
                            -117 -38 -43 136 105 351 335 449 481 47 70 21 40 -64 -73 -41 -55 -114 -140
                            -162 -190 l-87 -90 89 100 c176 200 278 346 373 535 143 285 214 544 240 870
                            28 341 -43 753 -181 1060 -16 35 -27 70 -24 78 3 8 0 10 -7 6 -8 -5 -9 -2 -5
                            10 5 11 3 15 -5 10 -8 -5 -9 -2 -5 10 5 11 3 15 -5 10 -8 -5 -9 -2 -5 10 5 11
                            3 15 -5 10 -8 -5 -9 -2 -5 10 4 10 3 15 -3 11 -5 -3 -19 14 -31 37 -77 152
                            -236 358 -408 528 -69 68 -103 106 -76 83 70 -58 209 -204 284 -298 71 -90
                            100 -120 56 -60 -48 67 -115 146 -200 233 -80 84 -206 198 -191 175 12 -22
                            -10 -14 -30 9 -19 22 -19 23 1 8 20 -15 20 -14 1 8 -20 23 -32 30 -21 12 10
                            -17 -12 -11 -26 7 -10 11 -10 14 -1 9 18 -11 15 -1 -5 15 -11 10 -14 10 -9 2
                            4 -7 5 -13 2 -13 -11 0 -91 62 -91 70 0 5 -4 12 -9 17 -5 5 -6 3 -2 -4 11 -20
                            1 -16 -66 28 -94 61 -329 173 -447 214 -128 44 -269 80 -380 97 -61 9 -77 14
                            -56 18 16 3 -90 6 -236 7 -186 1 -295 -3 -369 -13z m485 -154 c640 -60 1213
                            -404 1568 -940 87 -131 194 -350 241 -495 46 -140 80 -285 69 -295 -4 -4 -235
                            -138 -511 -298 l-504 -290 -51 39 c-30 22 -80 48 -117 59 -63 20 -89 20 -911
                            18 l-845 -3 -78 -38 -77 -39 -517 292 c-284 161 -521 296 -526 301 -23 21 74
                            318 164 504 143 298 343 546 602 748 112 87 258 179 276 172 7 -2 10 0 7 5
                            -13 20 266 141 438 189 103 29 306 67 397 74 101 8 271 6 375 -3z m-2276
                            -1897 c1 4 16 1 34 -8 17 -8 24 -10 14 -3 -46 31 -48 38 -7 15 151 -81 180
                            -100 173 -109 -3 -7 -1 -8 6 -4 9 6 705 -377 733 -403 3 -3 -3 -35 -14 -70
                            l-21 -63 27 -372 c31 -424 39 -493 66 -545 l19 -38 -55 -26 c-31 -15 -60 -24
                            -66 -21 -7 4 -8 2 -4 -4 4 -7 -37 -33 -115 -72 l-121 -61 -93 94 c-332 339
                            -537 769 -595 1247 -11 96 -13 363 -3 436 6 42 8 45 14 24 4 -14 7 -22 8 -17z
                            m4190 -180 c1 -98 -3 -216 -9 -263 -57 -460 -241 -862 -553 -1204 -98 -108
                            -139 -140 -167 -131 -11 4 -17 11 -14 17 5 7 2 8 -6 3 -16 -10 -171 65 -163
                            78 4 6 1 7 -7 2 -7 -4 -40 6 -81 25 l-68 33 24 48 c32 63 39 117 71 548 22
                            300 25 373 15 415 -6 28 -8 52 -4 54 4 1 222 126 483 277 261 151 475 275 476
                            275 1 0 3 -80 3 -177z m-1234 -310 c81 -43 118 -107 117 -203 0 -36 -12 -222
                            -27 -415 -24 -324 -28 -354 -51 -402 -28 -59 -64 -89 -131 -109 -68 -21 -1498
                            -21 -1566 0 -67 20 -103 50 -131 109 -23 48 -27 78 -51 402 -33 436 -34 470
                            -11 515 34 65 82 104 151 121 17 4 397 7 845 6 814 -2 815 -2 855 -24z m-1750
                            -1268 l60 -30 815 0 815 0 50 23 c41 19 54 21 77 11 15 -6 25 -15 22 -20 -3
                            -5 1 -6 8 -4 8 3 76 -24 153 -60 l139 -67 -100 -65 c-620 -409 -1415 -462
                            -2082 -138 -118 57 -263 143 -330 196 l-23 18 91 46 c50 25 96 43 102 39 7 -4
                            8 -3 4 4 -4 7 19 24 61 46 37 19 70 34 73 32 3 -1 32 -15 65 -31z"
                        />
                        <path
                          d="M1308 3268 c15 -6 14 -7 -3 -7 -11 -1 -44 -15 -74 -31 -61 -34 -65
                            -35 -55 -17 4 6 -14 -8 -40 -33 -26 -25 -52 -54 -58 -65 -6 -11 12 5 41 35
                            l53 55 -45 -53 c-75 -87 -107 -198 -86 -298 6 -33 12 -69 13 -81 0 -11 5 -19
                            10 -15 5 3 7 -2 3 -11 -3 -9 6 -31 22 -54 28 -38 91 -100 77 -75 -11 18 9 15
                            25 -5 12 -15 12 -16 -1 -8 -8 4 -12 4 -7 0 4 -5 15 -10 25 -11 9 -2 42 -12 72
                            -23 71 -26 159 -26 230 0 30 11 63 21 72 23 10 1 21 6 25 11 5 4 1 4 -7 0 -13
                            -8 -13 -7 -1 8 16 20 36 23 25 5 -4 -7 5 -2 21 12 39 35 86 101 79 111 -3 5 3
                            31 12 56 42 111 13 256 -73 355 l-45 53 53 -55 c29 -30 47 -46 41 -35 -6 11
                            -32 40 -58 65 -26 25 -44 39 -40 33 10 -18 6 -17 -55 17 -30 16 -63 30 -74 31
                            -17 0 -18 1 -3 7 10 4 -30 7 -87 7 -57 0 -97 -3 -87 -7z m167 -177 c49 -22 90
                            -65 80 -82 -5 -8 -4 -10 3 -5 34 21 43 -123 12 -183 -71 -141 -278 -141 -350
                            -1 -22 43 -26 122 -9 167 37 98 166 149 264 104z"
                        />
                        <path
                          d="M3118 3268 c15 -6 14 -7 -3 -7 -11 -1 -44 -15 -74 -31 -61 -34 -65
                            -35 -55 -17 4 6 -14 -8 -40 -33 -26 -25 -52 -54 -58 -65 -6 -11 12 5 41 35
                            l53 55 -45 -53 c-75 -87 -107 -198 -86 -298 6 -33 12 -69 13 -81 0 -11 5 -19
                            10 -15 5 3 7 -2 3 -11 -3 -9 6 -31 22 -54 28 -38 91 -100 77 -75 -11 18 9 15
                            25 -5 12 -15 12 -16 -1 -8 -8 4 -12 4 -7 0 4 -5 15 -10 25 -11 9 -2 42 -12 72
                            -23 71 -26 159 -26 230 0 30 11 63 21 72 23 10 1 21 6 25 11 5 4 1 4 -7 0 -13
                            -8 -13 -7 -1 8 16 20 36 23 25 5 -14 -25 49 37 77 75 16 23 25 45 22 54 -4 9
                            -2 14 3 11 5 -4 10 4 10 15 1 12 7 48 13 81 21 100 -11 211 -86 298 l-45 53
                            53 -55 c29 -30 47 -46 41 -35 -6 11 -32 40 -58 65 -26 25 -44 39 -40 33 10
                            -18 6 -17 -55 17 -30 16 -63 30 -74 31 -17 0 -18 1 -3 7 10 4 -30 7 -87 7 -57
                            0 -97 -3 -87 -7z m167 -177 c49 -22 90 -65 80 -82 -5 -8 -4 -10 3 -5 17 10 32
                            -35 32 -95 0 -209 -279 -270 -374 -82 -28 55 -15 196 16 177 7 -5 8 -3 3 5
                            -19 32 89 101 160 101 22 0 58 -9 80 -19z"
                        />
                        <path
                          d="M1893 2213 c230 -2 604 -2 830 0 227 1 39 2 -418 2 -456 0 -642 -1-412 -2z"
                        />
                        <path
                          d="M1902 2023 c219 -2 577 -2 795 0 219 1 40 2 -397 2 -437 0 -616 -1-398 -2z"
                        />
                        <path
                          d="M1913 693 c213 -2 561 -2 775 0 213 1 38 2 -388 2 -426 0 -601 -1-387 -2z"
                        />
                      </g>
                    </svg>
                    <div style="margin-right: 20px">
                      <div style="margin-right: 20px; display: flex; flex-direction: column">
                        <div style="display: flex;">
                          <p>Face image</p>
                        </div>
                        <div style="display: flex; padding-left: 0.6rem">
                          <p>Without mask</p>
                        </div>
                      </div>
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
              name="viamUser"
              rules="required"
            >
              <label class="title-create-employee" for="viamUser">VIAM User</label>
              <br>
              <el-radio-group id="roleEmployee" v-model="formCreate.role_id">
                <el-radio
                  v-for="element in listRoles"
                  :key="element.id"
                  :label="element.id"
                  style="font-weight: 400"
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
              <label for="password_confirmationEmployee" class="mt-3">Password (Confirm)</label>
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
      displayBoxSearch: 'd-none',
      displaySearch: 'd-block',
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
        email: this.name_search,
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
          const toastSuccessMessage = [];
          const toastFalseMessage = [];
          if (response.code === 200) {
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
                    toastSuccessMessage.push('Add image without mask employee success');
                  } else {
                    toastFalseMessage.push(response.message);
                  }
                })
                .catch((error) => {
                  toastFalseMessage.push(error.message);
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
                    toastSuccessMessage.push('Add image with mask employee success');
                  } else {
                    toastFalseMessage.push(response.message);
                  }
                })
                .catch((error) => {
                  toastFalseMessage.push(error.message);
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
            toastSuccessMessage.forEach((element) => {
              MakeToast({
                variant: 'success',
                title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
                content: element,
              });
            });
            toastFalseMessage.forEach((element) => {
              MakeToast({
                variant: 'success',
                title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_SUCCESS'),
                content: element,
              });
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
      this.validateFile = false;
      this.checkNumImage();
      this.checkImage();
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
      this.checkImage();
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
      this.checkImage();
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
      if (this.messageErrorFile.length === 0){
        this.validateFile = false;
      }
    },
    async checkImage() {
      let dem = 0;
      this.waitCreate = true;
      for (const item of this.selectedWithoutMaskFiles) {
        const file = new FormData();
        file.append('file', item);
        await ImageApi.checkImage(file).then((response) => {
          if (response.code === 200){
            if (response.data.checkImage === false){
              this.messageErrorFile.push('Image must only one person');
              dem++;
            }
          } else {
            this.messageErrorFile.push(response.message);
            this.validateFile = true;
          }
        }).catch((error) => {
          this.messageErrorFile.push(error.getMessage());
          this.validateFile = true;
        });
      }
      for (const item of this.selectedWithMaskFiles) {
        const file = new FormData();
        file.append('file', item);
        await ImageApi.checkImage(file).then((response) => {
          if (response.code === 200){
            if (response.data.checkImage === false){
              this.messageErrorFile.push('Image must only one person');
              dem++;
            }
          } else {
            this.messageErrorFile.push(response.message);
            this.validateFile = true;
          }
        }).catch((error) => {
          this.messageErrorFile.push(error.getMessage());
          this.validateFile = true;
        });
      }
      if (dem > 0){
        this.validateFile = true;
      } else {
        this.validateFile = false;
      }
      this.waitCreate = false;
    },
    // copy cua Yen
    rowWorkingStyle({ row, rowIndex }) {
      return { 'cursor': 'pointer' };
    },
    openInputSearch() {
      this.displayBoxSearch = 'd-flex';
      this.displaySearch = 'd-none';
    },
    closeInputSearch() {
      this.displayBoxSearch = 'd-none';
      this.displaySearch = 'd-block';
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
  line-height: 48px;
  color: #000000;
}
</style>
