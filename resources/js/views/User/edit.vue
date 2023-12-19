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
            </div>
          </div>
        </div>
        <hr class="line-bottom">
        <div class="use-management-title-table mt-5">
          <p class="back-list cursor-pointer" @click="listEmployees()"> <i class="el-icon-arrow-left icon-back-list" /> All Employees </p>
          <div class="card-body p-card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div class="basic">
                <h1 class="title-record m-0">Employee</h1>
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
                    <div class="header-employee-edit">
                      <p>Employee name</p>
                      <ValidationProvider
                        v-slot="{ errors }"
                        name="name"
                        rules="required"
                      >
                        <b-input-group>
                          <b-form-input
                            id="nameEmployee"
                            v-model="formEdit.name"
                            class="p-0"
                          />
                        </b-input-group>
                        <div class="text-error">
                          {{ errors[0] }}
                        </div>
                      </ValidationProvider>
                    </div>
                    <div class="header-employee-edit">
                      <p>Email</p>
                      <ValidationProvider
                        v-slot="{ errors }"
                        name="email"
                        rules="required|email"
                      >
                        <b-input-group>
                          <b-form-input
                            id="emailEmployee"
                            v-model="formEdit.email"
                            class="p-0"
                          />
                        </b-input-group>
                        <div class="text-error">
                          {{ errors[0] }}
                        </div>
                      </ValidationProvider>
                    </div>
                  </div>
                  <div class="employee-edit">
                    <div class="header-employee-edit">
                      <p>Gender</p>
                      <el-select
                        id="genderEmployee"
                        v-model="formEdit.gender"
                        placeholder="Please select gender"
                      >
                        <el-option
                          v-for="item in listGender"
                          :key="item.id"
                          :label="item.name"
                          :value="item.id"
                        />
                      </el-select>
                    </div>
                    <div class="header-employee-edit">
                      <p>Birthday</p>
                      <el-date-picker
                        v-model="formEdit.birthday"
                        type="date"
                        placeholder="Please select birthday"
                        format="yyyy/MM/dd"
                        value-format="yyyy-MM-dd"
                      />
                    </div>
                  </div>
                  <div class="employee-edit">
                    <div class="header-employee-edit">
                      <p>Address</p>
                      <b-input-group>
                        <b-form-input
                          id="address"
                          v-model="formEdit.address"
                          class="p-0"
                        />
                      </b-input-group>
                    </div>
                    <div class="header-employee-edit">
                      <p>Telephone</p>
                      <b-input-group>
                        <b-form-input
                          id="telephone"
                          v-model="formEdit.telephone"
                          class="p-0"
                        />
                      </b-input-group>
                    </div>
                  </div>
                  <div class="employee-edit">
                    <div class="header-employee-edit">
                      <p class="header-employee-edit fw-5">Entry Date</p>
                      <el-date-picker
                        v-model="formEdit.entry_date"
                        type="date"
                        placeholder="Please select entry date"
                        format="yyyy/MM/dd"
                        value-format="yyyy-MM-dd"
                        disabled="disabled"
                      />
                    </div>
                    <div class="header-employee-edit">
                      <p class="header-employee-edit fw-5">Slack Id</p>
                      <b-input-group>
                        <b-form-input
                          id="slack_id"
                          v-model="formEdit.slack_id"
                          class="p-0"
                        />
                      </b-input-group>
                    </div>
                  </div>
                  <div class="employee-edit">
                    <div class="header-employee-edit">
                      <p class="header-employee-edit fw-5">Skype Id</p>
                      <b-input-group>
                        <b-form-input
                          id="skype_id"
                          v-model="formEdit.skype_id"
                          class="p-0"
                        />
                      </b-input-group>
                    </div>
                    <div class="header-employee-edit">
                      <p class="header-employee-edit fw-5">Github Id</p>
                      <b-input-group>
                        <b-form-input
                          id="github_id"
                          v-model="formEdit.github_id"
                          class="p-0"
                        />
                      </b-input-group>
                    </div>
                  </div>
                  <div class="employee-edit" style="justify-content: start; width: 200%">
                    <div class="header-employee-edit">
                      <p class="header-employee-edit fw-5">Paid Off</p>
                      <b-input-group>
                        <b-form-input
                          id="paidOff"
                          v-model="formEdit.paidOff"
                          class="p-0"
                          disabled="disabled"
                        />
                      </b-input-group>
                    </div>
                  </div>
                </div>
              </h4>
              <h4 class="mb-0 font-weight-normal">
                <div class="d-flex justify-content-between align-items-center">
                  <div class="basic">
                    <h1 class="title-face">Face Data</h1>
                  </div>
                  <div class="basic" />
                </div>
                <hr class="line">
                <div>
                  <div class="image-dropzone" @dragover.prevent @drop="handleDrop">
                    <div style="border-bottom: 2px solid;display: flex; gap: 1rem">
                      <div
                        :class="{check_with_or_without_mask: withoutMask}"
                        style="display: flex; gap: 1rem;cursor: pointer;border-right: 2px solid"
                        @click="checkWithoutMask()"
                      >
                        <b-icon-emoji-smile />
                        <div style="margin-right: 20px">
                          <p>Without mask</p>
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
                          width: 1em;
                          height: 1em;"
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
                          <p>With mask</p>
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

                <div>
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="basic">
                      <h1 class="title-face">VIAM User</h1>
                    </div>
                  </div>
                  <hr class="line">
                  <ValidationProvider
                    v-slot="{ errors }"
                    name="role"
                    rules="required"
                  >
                    <el-select id="viam_user_id" v-model="userInfo.viam_user" placeholder="Please select VIAM user">
                      <el-option
                        v-for="item in listRoles"
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
              </h4>
              <h4 class="mb-0 font-weight-normal">
                <div class="d-flex justify-content-between align-items-center">
                  <div class="basic">
                    <h1 class="title-face">Retirement</h1>
                  </div>
                </div>
                <hr class="line">
                <div>
                  <el-date-picker
                    v-model="formEdit.retirement_date"
                    type="date"
                    placeholder="Please select retirement date"
                    format="yyyy/MM/dd"
                    value-format="yyyy-MM-dd"
                  />
                </div>
              </h4>
              <h4 class="mb-0 font-weight-normal" style="margin-top: 15px; border-bottom: 1px solid rgba(0, 0, 0, 0.15);">
                <div class="d-flex justify-content-between align-items-center">
                  <div class="basic">
                    <h1 class="title-face">Password</h1>
                  </div>
                </div>
                <hr class="line">
                <div>
                  <ValidationProvider
                    v-slot="{ errors }"
                    name="password"
                    vid="password"
                    rules="min:8"
                  >
                    <label for="password" style="font-size: 16px;">Password:</label>
                    <b-input-group>
                      <b-form-input
                        id="password"
                        v-model="formEdit.password"
                        type="password"
                      />
                    </b-input-group>
                    <div class="text-error">
                      {{ errors[0] }}
                    </div>
                  </ValidationProvider>
                </div>
                <div style="margin-bottom: 15px;">
                  <ValidationProvider
                    v-slot="{ errors }"
                    name="password_confirm"
                    rules="confirmed:password|min:8"
                  >
                    <label for="password_confirm" style="font-size: 16px;">Password(Confirm) :</label>
                    <b-input-group>
                      <b-form-input
                        id="password_confirm"
                        v-model="formEdit.password_confirmation"
                        type="password"
                      />
                    </b-input-group>
                    <div class="text-error">
                      {{ errors[0] }}
                    </div>
                  </ValidationProvider>
                </div>
              </h4>
              <p class="delete-record cursor-pointer mt-5" @click="showModalDelete= true"> Delete Employee </p>
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
      <span class="text-align-center">Are you sure to delete this employee?</span>
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
import { getAllRole } from '../../api/viamUser';
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
        gender: '',
        birthday: '',
        address: '',
        telephone: '',
        entry_date: '',
        slack_id: '',
        skype_id: '',
        github_id: '',
        paidOff: '',
        password: '',
        password_confirmation: '',
        viam_user_id: '',
        retirement_date: '',
      },
      listGender: [
        { id: 0, name: 'male' },
        { id: 1, name: 'female' },
      ],
      id: this.$route.params.id,
      userInfo: {},
      author: true,
      selectedWithMaskFiles: [],
      selectedWithoutMaskFiles: [],
      withoutMask: true,
      withMask: false,
      linkFilesWithoutMask: [],
      linkFilesWithMask: [],
      linkFileDelete: [],
      validateFile: false,
      messageErrorFile: [],
      showModalDelete: false,
      waitEdit: false,
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
          content: '1. ' + error.message,
        });
      });
    },
    async getUserInfo() {
      this.openLoading();
      try {
        const response = await UserApi.getOneUser(this.id);
        this.userInfo = {
          viam_user: response.data.viam_user.name,
        };
        this.formEdit = {
          name: response.data.name,
          email: response.data.email,
          gender: response.data.gender,
          birthday: response.data.birthday,
          address: response.data.address,
          telephone: response.data.telephone,
          entry_date: response.data.entry_date,
          slack_id: response.data.slack_id,
          skype_id: response.data.skype_id,
          github_id: response.data.github_id,
          password: '',
          password_confirmation: '',
          viam_user: response.data.viam_user.name,
          role_id: response.data.role_id,
          retirement_date: response.data.retirement_date ? this.formatTimeStamp(response.data.retirement_date) : null,
        };
        this.closeLoading();
      } catch (error) {
        this.closeLoading();
        MakeToast({
          variant: 'warning',
          title: this.$t('LANGUAGES.TEXT_TOAST_TITLE_WARNING'),
          content: '2. ' + error.message,
        });
      }
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
            content: '3. ' + error.message,
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
                        content: '4. ' + error.message,
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
                      content: '5. ' + error.message,
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
                      content: '6. ' + error.message,
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
              content: '7. ' + error.message,
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
      this.messageErrorFile = [];
      if (this.linkFilesWithoutMask.length === 0 && this.selectedWithoutMaskFiles.length === 0){
        this.validateFile = true;
        this.messageErrorFile.push('Image without mask must one image');
      }

      if (this.linkFilesWithMask.length === 0 && this.linkFilesWithoutMask.length === 0 && this.selectedWithoutMaskFiles.length === 0 && this.selectedWithMaskFiles.length === 0){
        this.validateFile = true;
        this.messageErrorFile.push('Pleas choose image');
      }
      if (this.messageErrorFile.length === 0){
        this.validateFile = false;
      }
    },
    async checkImage() {
      let dem = 0;
      this.waitEdit = true;
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
      this.waitEdit = false;
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
        if (this.linkFilesWithoutMask !== []){
          this.linkFilesWithoutMask.forEach((element) => {
            this.linkFileDelete.push(element);
          });
          this.linkFilesWithoutMask.splice(0, this.linkFilesWithoutMask.length);
        }
      }
      if (this.withMask){
        this.selectedWithMaskFiles.splice(0, this.selectedWithMaskFiles.length);
        if (this.linkFilesWithMask !== []){
          this.linkFilesWithMask.forEach((element) => {
            this.linkFileDelete.push(element);
          });
          this.linkFilesWithMask.splice(0, this.linkFilesWithMask.length);
        }
      }
      this.checkNumImage();
    },
    // showModalDelete(){
    //   this.$bvModal.show('bv-modal-delete');
    // },
    // hideModalDelete() {
    //   this.$bvModal.hide('bv-modal-delete');
    // },
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
      this.$router.push({ path: `/user/index` });
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
  display: flex;
  width: 100%;
  flex-wrap: nowrap;
  flex-direction: row;
  justify-content: space-around;
  text-align: left;
  gap: 10px;
}
::v-deep .header-employee-edit {
  width: calc(100% / 2);
  height: 40px;
  margin: 0;
  font-size: 20px;
}
.cover-employee-edit {
  display: flex;
  gap: 60px;
  flex-direction: column;
}
::v-deep .el-select {
  width: 100%;
}
::v-deep .el-date-editor {
  width: 100%;
}
</style>

