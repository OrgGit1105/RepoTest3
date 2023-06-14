import Cookies from 'js-cookie';

import { getLanguage } from '../../utils/getLang';

const state = {
  language: getLanguage(),
  listEnrollment: [],
  listUser: [],
  dataManagement: [],
  listInfo: {},
  listEmployee: [],
  listEvaluation: {},
  listEnrollmentSimilar: [],
  listBranch: [],
  branchUser: {},
  pagination: {},
  listDataDigitaco: [],
  detailDataDigitaco: [],
  employeeDetailList: [],
  employeeDetailInfo: {},
  listRetirement: {},
  monthYear: {},
  listRoles: [],
};

const mutations = {
  SET_LANGUAGE: (state, language) => {
    state.language = language;
    Cookies.set('language', language);
  },
  SET_LIST_ENROLLMENT: (state, listEnrollment) => {
    state.listEnrollment = listEnrollment;
  },
  SET_LIST_USER: (state, listUser) => {
    // console.log('mutations', state);
    state.listUser = listUser;
  },
  SET_DATA_MANAGEMENT: (state, dataManagement) => {
    state.dataManagement = dataManagement;
  },
  SET_LIST_EMPLOYEE: (state, listEmployee) => {
    state.listEmployee = listEmployee;
  },
  SET_LIST_EVALUATION: (state, listEvaluation) => {
    state.listEvaluation = listEvaluation;
  },
  SET_LIST_INFO: (state, listInfo) => {
    state.listInfo = listInfo;
  },
  SET_LIST_COMPANY_BRANCH: (state, listBranch) => {
    state.listBranch = listBranch;
  },
  SET_BRANCH_USER: (state, branchUser) => {
    state.branchUser = branchUser;
  },
  SET_PAGINATION: (state, pagination) => {
    state.pagination = pagination;
  },
  SET_LIST_DATA_DIGITACO: (state, listDataDigitaco) => {
    state.listDataDigitaco = listDataDigitaco;
  },
  SET_DETAIL_DATA_DIGITACO: (state, detailDataDigitaco) => {
    state.detailDataDigitaco = detailDataDigitaco;
  },
  SET_EMPLOYEE_DETAIL_LIST: (state, employeeDetailList) => {
    state.employeeDetailList = employeeDetailList;
  },
  SET_EMPLOYEE_DETAIL_INFO: (state, employeeDetailInfo) => {
    state.employeeDetailInfo = employeeDetailInfo;
  },
  SET_LIST_RETIREMENT: (state, listRetirement) => {
    state.listRetirement = listRetirement;
  },
  SET_MONTH_YEAR: (state, monthYear) => {
    state.monthYear = monthYear;
  },
  SET_LIST_ROLES: (state, listRoles) => {
    state.listRoles = listRoles;
  },
};

const actions = {
  setLanguage({ commit }, language) {
    commit('SET_LANGUAGE', language);
  },
  saveListUSer({ commit }, listUser) {
    // console.log('Da chay vao store', listUser);
    commit('SET_LIST_USER', listUser);
  },
  saveListEnrollment({ commit }, listEnrollment) {
    commit('SET_LIST_ENROLLMENT', listEnrollment);
  },
  saveDataManagement({ commit }, dataManagement) {
    commit('SET_DATA_MANAGEMENT', dataManagement);
  },
  saveListEmployee({ commit }, listEmployee) {
    commit('SET_LIST_EMPLOYEE', listEmployee);
  },
  saveListEvaluation({ commit }, listEvaluation) {
    commit('SET_LIST_EVALUATION', listEvaluation);
  },
  saveListInfo({ commit }, listInfo) {
    commit('SET_LIST_INFO', listInfo);
  },
  saveListBranch({ commit }, listBranch) {
    commit('SET_LIST_COMPANY_BRANCH', listBranch);
  },
  saveBranchUser({ commit }, branchUser) {
    commit('SET_BRANCH_USER', branchUser);
  },
  savePagination({ commit }, pagination) {
    commit('SET_PAGINATION', pagination);
  },
  saveDataDigitaco({ commit }, listDataDigitaco) {
    commit('SET_LIST_DATA_DIGITACO', listDataDigitaco);
  },
  saveDetailDataDigitaco({ commit }, detailDataDigitaco) {
    commit('SET_DETAIL_DATA_DIGITACO', detailDataDigitaco);
  },
  saveEmployeeDetailList({ commit }, employeeDetailList) {
    commit('SET_EMPLOYEE_DETAIL_LIST', employeeDetailList);
  },
  saveEmployeeDetailInfo({ commit }, employeeDetailInfo) {
    commit('SET_EMPLOYEE_DETAIL_INFO', employeeDetailInfo);
  },
  saveListRetirement({ commit }, listRetirement) {
    commit('SET_LIST_RETIREMENT', listRetirement);
  },
  saveMonthYear({ commit }, monthYear) {
    commit('SET_MONTH_YEAR', monthYear);
  },
  saveListRoles({ commit }, listRoles) {
    commit('SET_LIST_ROLES', listRoles);
  },
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
};
